<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Customer;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Session;
use Carbon\Carbon;

class WebsiteAuthController extends Controller
{
    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required'
        ]);

        if ($validator->fails()) {
            if ($request->ajax()) {
                return response()->json([
                    'status' => false,
                    'message' => 'Validation failed',
                    'errors' => $validator->errors()
                ], 422);
            }
            return redirect()->back()
                ->withErrors($validator)
                ->withInput()
                ->with('error', 'Please check your input');
        }

        $credentials = [
            'cust_email' => $request->email,
            'password' => $request->password
        ];

        if (Auth::attempt($credentials)) {
            $request->session()->regenerate();

            DB::table('customer_login_attempts')->insert([
                'cust_id' => Auth::id(),
                'email' => $request->email,
                'ip_address' => $request->ip(),
                'successful' => 1,
                'created_at' => now(),
            ]);

            if ($request->ajax()) {
                session()->flash('success', 'Logged in successfully');
                return response()->json([
                    'status' => true,
                    'message' => 'Logged in successfully',
                    'redirect' => url()->previous()
                ]);
            }
            return redirect()->back()->with('success', 'Logged in successfully');
        }

        DB::table('customer_login_attempts')->insert([
            'cust_id' => null,
            'email' => $request->email,
            'ip_address' => $request->ip(),
            'successful' => 0,
            'created_at' => now(),
        ]);

        if ($request->ajax()) {
            return response()->json([
                'status' => false,
                'message' => 'Invalid email or password'
            ], 401);
        }

        return redirect()->back()->with('error', 'Invalid email or password');
    }

    public function sendOtp(Request $request)
    {
        // OTP bypass - auto success (email setup pending)
        return response()->json(['success' => true, 'message' => 'OTP sent successfully']);

        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:tbl_customers,cust_email',
            'password' => 'required|min:6',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => $validator->errors()->first()
            ], 422);
        }

        // Generate 6-digit OTP
        $otp = str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);

        // Store in session
        Session::put('registration_otp', [
            'otp' => $otp,
            'email' => $request->email,
            'name' => $request->name,
            'password' => $request->password,
            'expires_at' => Carbon::now()->addMinutes(10)
        ]);

        // Send OTP email
        try {
            $this->sendOtpEmail($request->email, $otp, $request->name);

            return response()->json([
                'success' => true,
                'message' => 'OTP sent to your email'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to send OTP. Please try again.'
            ], 500);
        }
    }

    protected function sendOtpEmail($email, $otp, $name)
    {
        $subject = 'Your StudyNest Verification Code';
        $message = "<html>
        <body style='font-family: Arial, sans-serif; background-color: #f4f4f4; padding: 20px;'>
            <div style='max-width: 600px; margin: 0 auto; background-color: #ffffff; padding: 40px; border-radius: 8px;'>
                <h2 style='color: #333; text-align: center;'>StudyNest Email Verification</h2>
                <p style='color: #666; font-size: 16px;'>Hello {$name},</p>
                <p style='color: #666; font-size: 16px;'>Your verification code is:</p>
                <div style='text-align: center; padding: 30px; background-color: #f8f9fa; border-radius: 8px; margin: 20px 0;'>
                    <h1 style='color: #007bff; font-size: 36px; letter-spacing: 8px; margin: 0;'>{$otp}</h1>
                </div>
                <p style='color: #666; font-size: 14px;'>This code will expire in 10 minutes.</p>
                <p style='color: #999; font-size: 12px; margin-top: 30px;'>If you didn't request this code, please ignore this email.</p>
            </div>
        </body>
        </html>";

        // Use PHP mail function or Laravel Mail
        Mail::send([], [], function ($mail) use ($email, $subject, $message) {
            $mail->to($email)
                ->subject($subject)
                ->html($message);
        });
    }

    public function register(Request $request)
    {
        // OTP bypass - skip OTP verification
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:tbl_customers,cust_email',
            'password' => 'required|min:6',
            'mobile' => 'required|digits:10|unique:tbl_customers,cust_mobile',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => $validator->errors()->first()
            ], 422);
        }

        // Skip OTP verification - direct registration
        try {
            $customer = Customer::create([
                'cust_name' => $request->name,
                'cust_email' => $request->email,
                'cust_password' => Hash::make($request->password),
                'cust_mobile' => $request->mobile,
                'cust_address' => $request->address ?? '',
                'cust_city' => $request->city ?? '',
                'cust_state' => $request->state ?? '',
                'cust_country' => $request->country ?? 'India',
                'cust_pincode' => $request->pincode ?? 0,
                'cust_gender' => $request->gender ?? 'Other',
                'cust_status' => 1,
                'cust_created_at' => now(),
            ]);

            // Clear session
            Session::forget('registration_otp');

            Auth::login($customer);

            return response()->json([
                'success' => true,
                'message' => 'Registration successful! Welcome ' . $customer->cust_name
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Something went wrong: ' . $e->getMessage()
            ], 500);
        }
    }

    public function sendForgotPasswordOtp(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => $validator->errors()->first()
            ], 422);
        }

        // Check if email exists
        $customer = Customer::where('cust_email', $request->email)->first();
        if (!$customer) {
            return response()->json([
                'success' => false,
                'message' => 'Email address not registered.'
            ], 404);
        }

        // Generate 6 digit OTP
        $otp = str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);

        // Store OTP in session
        Session::put('forgot_password_otp', [
            'email' => $request->email,
            'otp' => $otp,
            'expires_at' => Carbon::now()->addMinutes(10)
        ]);

        // Send OTP email
        $this->sendOtpEmail($request->email, $otp, $customer->cust_name ?? 'User');

        return response()->json([
            'success' => true,
            'message' => 'OTP sent successfully'
        ]);
    }

    public function resetPasswordWithOtp(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'otp' => 'required|string|size:6',
            'password' => 'required|min:6|confirmed'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => $validator->errors()->first()
            ], 422);
        }

        // Verify OTP from session
        $otpData = Session::get('forgot_password_otp');

        if (!$otpData) {
            return response()->json([
                'success' => false,
                'message' => 'OTP expired. Please request a new one.'
            ], 400);
        }

        if ($otpData['otp'] !== $request->otp) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid OTP'
            ], 400);
        }

        if (Carbon::now()->isAfter($otpData['expires_at'])) {
            Session::forget('forgot_password_otp');
            return response()->json([
                'success' => false,
                'message' => 'OTP expired. Please request a new one.'
            ], 400);
        }

        // Verify email matches
        if ($otpData['email'] !== $request->email) {
            return response()->json([
                'success' => false,
                'message' => 'Email mismatch'
            ], 400);
        }

        try {
            $customer = Customer::where('cust_email', $request->email)->first();
            if (!$customer) {
                return response()->json([
                    'success' => false,
                    'message' => 'User not found'
                ], 404);
            }

            // Update password
            $customer->cust_password = Hash::make($request->password);
            $customer->cust_updated_at = now();
            $customer->save();

            // Clear session
            Session::forget('forgot_password_otp');

            return response()->json([
                'success' => true,
                'message' => 'Password reset successful! Please login.'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Something went wrong: ' . $e->getMessage()
            ], 500);
        }
    }

    public function logout(Request $request)
    {
        Auth::guard('web')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect()->route('website.index')->with('success', 'Logged out successfully');
    }
}
