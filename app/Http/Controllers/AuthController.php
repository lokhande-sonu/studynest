<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

use App\Models\Management;
use App\Mail\ManagementResetPasswordMail;


class AuthController extends Controller
{

    public function showLogin(){

        return view('management/auth/login');
    }


    public function login(Request $request)
    {
        // Validate the request data
        $request->validate([
            'email' => 'required|string',
            'password' => 'required',
        ],[
            'email.required' => 'Please enter your email address or username',
            'password.required' => 'Please enter your password',
        ]);
    
        // Check if the input is email or username
        $loginField = filter_var($request->email, FILTER_VALIDATE_EMAIL) ? 'email' : 'username';

        // Attempt to authenticate the user using the 'management' guard
        if (Auth::guard('management')->attempt([$loginField => $request->email, 'password' => $request->password, 'status' => 1])) {
            // Authentication successful
            $request->session()->regenerate();

            $management = Auth::guard('management')->user();
            
            // Store user data in session for backward compatibility
            $request->session()->put('uid', $management->m_id); 
            $request->session()->put('name', $management->name);   
            $request->session()->put('username', $management->username);     
            $request->session()->put('user_photo', $management->profile_photo); 
            $request->session()->put('role', $management->role);  
    
            return redirect()->intended('management/dashboard')->with('success', 'Login successful. Welcome to StudyNest Admin Panel!');
        } else {
            // Authentication failed
            return redirect()->back()->withInput()->with('error', 'Invalid email/username or password. Please try again.');
        }
    }  
    
    public function showForgotPassword(){

        return view('management/auth/forgot-password');
    }
    
    public function forgotPassword(Request $request)
    {
        $request->validate([
            'email' => 'required|email'
        ]);
    
        $user = Management::where('email', $request->email)->first();
    
        if (!$user) {
            return back()->with('error', 'Email address not registered.');
        }
    
        // Generate reset token
        $token = Str::random(64);
    
        // Store hashed token
        $user->api_token = hash('sha256', $token);
        $user->save();
    
        // Reset link
        $resetLink = route('show-reset-password', [
            'email' => $user->email,
            'token' => $token
        ]);
    
        // Send mail
        Mail::to($user->email)
            ->send(new ManagementResetPasswordMail($user->email, $resetLink));
    
        return redirect()->route('show-login')->with('success', 'Password reset link has been sent to your email.');
    }
    
    public function showResetPassword(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'token' => 'required'
        ]);
    
        $user = Management::where('email', $request->email)->first();
    
        if (
            !$user ||
            !$user->api_token ||
            !hash_equals($user->api_token, hash('sha256', $request->token))
        ) {
            return redirect()->route('show-forgot-password')
                ->with('error', 'Invalid or expired reset link.');
        }
    
        return view('management.auth.reset-password', [
            'email' => $request->email,
            'token' => $request->token
        ]);
    }

    public function resetPassword(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'token' => 'required',
            'password' => 'required|min:6|confirmed'
        ]);
    
        $user = Management::where('email', $request->email)->first();
    
        if (
            !$user ||
            !$user->api_token ||
            !hash_equals($user->api_token, hash('sha256', $request->token))
        ) {
            return back()->with('error', 'Invalid or expired reset token.');
        }
    
        // Update password & clear token
        $user->password = Hash::make($request->password);
        $user->api_token = null;
        $user->save();
    
        return redirect()->route('show-login')
            ->with('success', 'Password reset successfully. Please login.');
    }



    public function logout(Request $request){
        // Logout from the management guard
        Auth::guard('management')->logout();
        
        // Clear session data for backward compatibility
        $request->session()->forget(['uid', 'name', 'username', 'user_photo', 'role']);
        // Session::flush(); // Do not flush to keep customer session alive
        
        return redirect('management/login')->with('success','You have been successfully logged out.');
    }
    
}
