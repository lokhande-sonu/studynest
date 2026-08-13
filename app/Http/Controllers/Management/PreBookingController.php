<?php

namespace App\Http\Controllers\Management;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\PreBooking;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class PreBookingController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $prebookings = PreBooking::with(['school', 'class'])->orderBy('id', 'desc')->get();
        return view('management.prebooking.index', compact('prebookings'));
    }

    /**
     * Export prebooking data to Excel.
     */
    public function exportExcel()
    {
        $prebookings = PreBooking::with(['school', 'class'])->orderBy('id', 'desc')->get();

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // Header row
        $headers = ['ID', 'Customer Name', 'Mobile', 'Email', 'School', 'Class', 'Token Amount', 'Payment Status', 'Date'];
        $sheet->fromArray($headers, NULL, 'A1');

        // Data rows
        $data = [];
        foreach ($prebookings as $booking) {
            $data[] = [
                $booking->id,
                $booking->customer_name,
                $booking->customer_mobile,
                $booking->customer_email,
                $booking->school->sch_name ?? 'N/A',
                $booking->class->class_name ?? 'N/A',
                $booking->token_amount,
                ucfirst($booking->payment_status),
                $booking->created_at->format('d/m/Y h:i A'),
            ];
        }

        if (!empty($data)) {
            $sheet->fromArray($data, NULL, 'A2');
        }

        // Auto-size columns
        foreach (range('A', 'I') as $columnID) {
            $sheet->getColumnDimension($columnID)->setAutoSize(true);
        }

        $writer = new Xlsx($spreadsheet);
        $fileName = 'prebooking_' . date('Y-m-d_H-i-s') . '.xlsx';

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment; filename="' . urlencode($fileName) . '"');
        $writer->save('php://output');
        exit;
    }

    /**
     * Update the status of the specified resource.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function updateStatus(Request $request, $id)
    {
        $prebooking = PreBooking::findOrFail($id);
        
        if ($request->has('status')) {
            $prebooking->status = $request->status;
        }
        
        if ($request->has('payment_status')) {
            $prebooking->payment_status = $request->payment_status;
        }

        $prebooking->save();

        return redirect()->back()->with('success', 'Status updated successfully.');
    }
}
