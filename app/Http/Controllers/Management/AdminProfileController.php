<?php

namespace App\Http\Controllers\Management;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\File;
use App\Models\Management;

class AdminProfileController extends Controller
{
    /**
     * Show admin profile
     */
    public function index(Request $request)
    {
        $admin = Auth::guard('management')->user();

        if (!$admin) {
            return redirect()->route('management.login');
        }

        return view('management.admin-profile', compact('admin'));
    }

    /**
     * Update profile details
     */
    public function update(Request $request)
    {
        $admin = Auth::guard('management')->user();
        if (!$admin) {
            return redirect()->route('management.login');
        }

        $request->validate([
            'name'   => 'required|string|max:255',
            'email'  => 'required|email|max:255|unique:tbl_managements,email,'.$admin->m_id.',m_id',
            'mobile' => 'required|string|max:15',
            'profile_photo' => 'nullable|image|mimes:jpeg,jpg,png,gif|max:2048',
        ]);

        try {
            $admin->name = $request->name;
            $admin->email = $request->email;
            $admin->mobile = $request->mobile;

            // Handle profile photo
            if ($request->hasFile('profile_photo')) {
                $file = $request->file('profile_photo');
                $filename = time().'_'.$file->getClientOriginalName();
                $filePath = 'uploads/management-profile-photo/';

                // Create folder if not exists
                if (!File::exists(public_path($filePath))) {
                    File::makeDirectory(public_path($filePath), 0755, true);
                }

                // Delete old photo
                if ($admin->profile_photo && File::exists(public_path($filePath.$admin->profile_photo))) {
                    File::delete(public_path($filePath.$admin->profile_photo));
                }

                $file->move(public_path($filePath), $filename);
                $admin->profile_photo = $filename;
            }

            $admin->save();

            return redirect()->back()->with('success', 'Profile updated successfully.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Something went wrong: '.$e->getMessage());
        }
    }

    /**
     * Update password
     */
    public function updatePassword(Request $request)
    {
        $data = $request->all();
    
        // Get the authenticated admin
        $admin = Auth::guard('management')->user();
    
        // Backward compatibility: check session if guard not available
        if (!$admin && $request->session()->has('uid')) {
            $userId = $request->session()->get('uid');
            $admin = Management::where(['m_id' => $userId, 'status' => '1'])->first();
        }
    
        // Check if admin exists and old password matches
        if (!$admin || !Hash::check($data['current_password'], $admin->password)) {
            return redirect()->back()->with([
                'error' => 'Please enter a valid current password.',
                'active_tab' => 'change-password'
            ]);
        }
    
        // Check if new password and confirmation match
        if ($data['new_password'] !== $data['new_password_confirmation']) {
            return redirect()->back()->with([
                'error' => 'Confirm Password does not match. Please re-enter!',
                'active_tab' => 'change-password'
            ]);
        }
    
        try {
            // Update password
            $admin->password = Hash::make($data['new_password']);
            $admin->save();
    
            // Logout admin after password change
            Auth::guard('management')->logout();
            $request->session()->forget(['uid', 'name', 'username', 'user_photo', 'role']);
            
            return redirect()->route('show-login')->with('success', 'Password updated successfully. Please login again.');
        } catch (\Exception $e) {
            return redirect()->back()->with([
                'error' => 'Something went wrong: '.$e->getMessage(),
                'active_tab' => 'change-password'
            ]);
        }
    }

}
