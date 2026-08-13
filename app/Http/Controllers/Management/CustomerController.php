<?php

namespace App\Http\Controllers\Management;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Barryvdh\DomPDF\Facade\Pdf;

class CustomerController extends Controller
{
    /**
     * Display a listing of the customers.
     */
    public function index(Request $request)
    {
        // Simple search logic if needed, or just paginate
        // The view has a search input but it seems to be client-side JS based (data-sa-search-input)
        // We will just return all customers or paginate
        $customers = Customer::orderBy('cust_id', 'desc')->get();
        
        // Check if we are on the report route
        if ($request->routeIs('management.customer-report.index')) {
            return view('management.customer-report', compact('customers'));
        }

        return view('management.customer', compact('customers'));
    }

    /**
     * Display the specified customer.
     */
    public function show($id)
    {
        $customer = Customer::with(['orders' => function($query) {
            $query->orderBy('order_id', 'desc')->take(5);
        }])->findOrFail($id);

        return view('management.customer-profile', compact('customer'));
    }

    /**
     * Update the specified customer in storage.
     */
    public function update(Request $request, $id)
    {
        $customer = Customer::findOrFail($id);
    
        // Validation
        $request->validate([
            'cust_name'   => 'required|string|max:255',
            'cust_email'  => 'required|email|max:255',
            'cust_mobile' => 'required|string|max:20',
            'cust_gender' => 'nullable|in:Male,Female',
            'cust_state'  => 'nullable|string|max:100',
            'cust_city'   => 'nullable|string|max:100',
            'cust_pincode'=> 'nullable|digits_between:4,8',
            'cust_status' => 'nullable|in:0,1',
            'cust_address'=> 'nullable|string',
            'cust_profile_photo' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
        ]);
    
        /* ----------------------------
           Handle profile photo upload
        -----------------------------*/
        if ($request->hasFile('cust_profile_photo')) {
    
            $uploadPath = env('CUSTOMER_PROFILE_PHOTO', 'uploads/customer-profile-photo');
    
            // Delete old photo
            if ($customer->cust_profile_photo) {
                $oldPath = public_path($uploadPath . '/' . $customer->cust_profile_photo);
                if (File::exists($oldPath)) {
                    File::delete($oldPath);
                }
            }
    
            // Upload new photo
            $photoName = time() . '_' . $request->cust_profile_photo->getClientOriginalName();
            $request->cust_profile_photo->move(public_path($uploadPath), $photoName);
    
            $customer->cust_profile_photo = $photoName;
        }
    
        /* ----------------------------
           Update customer fields
        -----------------------------*/
        $customer->update([
            'cust_name'    => $request->cust_name,
            'cust_email'   => $request->cust_email,
            'cust_mobile'  => $request->cust_mobile,
            'cust_gender'  => $request->cust_gender,
            'cust_state'   => $request->cust_state,
            'cust_city'    => $request->cust_city,
            'cust_pincode' => $request->cust_pincode,
            'cust_status'  => $request->cust_status,
            'cust_address' => $request->cust_address,
            'cust_updated_at' => $request->cust_updated_at,
        ]);
    
        return redirect()->back()->with('success', 'Customer updated successfully.');
    }

    /**
     * Update the status of the specified customer.
     */
    public function updateStatus(Request $request, $id)
    {
        $customer = Customer::findOrFail($id);
        
        // Toggle or set status based on request
        // The route suggests a PUT with maybe a status field, or just a toggle link
        // Assuming 'status' is passed or we just toggle if it's a specific action
        // But usually it's explicitly passed.
        
        if ($request->has('status')) {
            $customer->cust_status = $request->status;
            $customer->cust_updated_at = now();

            $customer->save();
             return redirect()->back()->with('success', 'Customer status updated successfully.');
        }

        return redirect()->back()->with('error', 'Status not provided.');
    }

    public function customerReport(Request $request)
    {
        $query = Customer::query();
    
        // Registration date filter
        if ($request->filled('from_date')) {
            $query->whereDate('cust_created_at', '>=', $request->from_date);
        }
    
        if ($request->filled('to_date')) {
            $query->whereDate('cust_created_at', '<=', $request->to_date);
        }
    
        // Account status filter
        if ($request->filled('cust_status')) {
            $query->where('cust_status', $request->cust_status);
        }
    
        $customers = $query->orderBy('cust_id', 'desc')->get();
    
        return view('management.customer-report', compact('customers'));
    }
    
    public function exportCustomerReportExcel(Request $request)
    {
        $query = Customer::query();
    
        if ($request->filled('from_date')) {
            $query->whereDate('cust_created_at', '>=', $request->from_date);
        }
    
        if ($request->filled('to_date')) {
            $query->whereDate('cust_created_at', '<=', $request->to_date);
        }
    
        if ($request->filled('cust_status')) {
            $query->where('cust_status', $request->cust_status);
        }
    
        $customers = $query->orderBy('cust_id', 'desc')->get();
    
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
    
        // Header
        $sheet->fromArray([
            ['ID', 'Name', 'Mobile', 'Email', 'Registered On', 'Gender', 'City', 'Status']
        ], null, 'A1');
    
        $row = 2;
        foreach ($customers as $customer) {
            $sheet->fromArray([
                $customer->cust_id,
                $customer->cust_name,
                $customer->cust_mobile,
                $customer->cust_email,
                optional($customer->cust_created_at)->format('d/m/Y h:i A'),
                ucfirst($customer->cust_gender ?? 'N/A'),
                $customer->cust_city ?? 'N/A',
                $customer->cust_status == 1 ? 'Active' : 'Blocked',
            ], null, 'A' . $row);
    
            $row++;
        }
    
        $fileName = 'customers-report-' . date('Y-m-d') . '.xlsx';
    
        return response()->streamDownload(function () use ($spreadsheet) {
            (new Xlsx($spreadsheet))->save('php://output');
        }, $fileName);
    }
    
    public function exportCustomerReportPdf(Request $request)
    {
        $query = Customer::query();
    
        if ($request->filled('from_date')) {
            $query->whereDate('cust_created_at', '>=', $request->from_date);
        }
    
        if ($request->filled('to_date')) {
            $query->whereDate('cust_created_at', '<=', $request->to_date);
        }
    
        if ($request->filled('cust_status')) {
            $query->where('cust_status', $request->cust_status);
        }
    
        $customers = $query->orderBy('cust_id', 'desc')->get();
    
        $pdf = Pdf::loadView('management.exports.customer-report-pdf', compact('customers'))
            ->setPaper('a4', 'landscape');
    
        return $pdf->download('customers-report-' . date('Y-m-d') . '.pdf');
    }
    

}
