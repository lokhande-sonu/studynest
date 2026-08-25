<?php

namespace App\Http\Controllers\Management;

use App\Http\Controllers\Controller;
use App\Models\School;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\IOFactory;

class SchoolController extends Controller
{
    public function index()
    {
        $schools = School::orderBy('sch_id', 'DESC')->get();
        return view('management.school', compact('schools'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'sch_name' => 'required|string|max:255',
            'sch_mobile' => 'required|string|max:20',
            'sch_email' => 'required|email|max:255',
            'sch_address' => 'required|string',
            'sch_desc' => 'required|string',
            'sch_city' => 'required|string|max:100',
            'sch_status' => 'required|in:0,1',
            'is_verified' => 'nullable|in:0,1',
            'sch_logo' => 'required|image|mimes:jpg,jpeg,png,webp'
        ]);

        $folder = 'uploads/school-logo';
        if (!file_exists(public_path($folder))) {
            mkdir(public_path($folder), 0777, true);
        }

        $photoName = time() . '.' . $request->sch_logo->extension();
        $request->sch_logo->move(public_path($folder), $photoName);

        School::create([
            'sch_name' => $request->sch_name,
            'sch_mobile' => $request->sch_mobile,
            'sch_email' => $request->sch_email,
            'sch_address' => $request->sch_address,
            'sch_desc' => $request->sch_desc,
            'sch_city' => $request->sch_city,
            'sch_status' => $request->sch_status,
            'is_verified' => $request->has('is_verified') ? 1 : 0,
            'sch_logo' => $photoName,
        ]);

        return back()->with('success', 'School created successfully.');
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'sch_name' => 'required|string|max:255',
            'sch_mobile' => 'required|string|max:20',
            'sch_email' => 'required|email|max:255',
            'sch_address' => 'required|string',
            'sch_desc' => 'required|string',
            'sch_city' => 'required|string|max:100',
            'sch_status' => 'required|in:0,1',
            'is_verified' => 'nullable|in:0,1',
            'sch_logo' => 'nullable|image|mimes:jpg,jpeg,png,webp'
        ]);

        $school = School::findOrFail($id);
        $folder = 'uploads/school-logo';

        $school->sch_name = $request->sch_name;
        $school->sch_mobile = $request->sch_mobile;
        $school->sch_email = $request->sch_email;
        $school->sch_address = $request->sch_address;
        $school->sch_desc = $request->sch_desc;
        $school->sch_city = $request->sch_city;
        $school->sch_status = $request->sch_status;
        $school->is_verified = $request->has('is_verified') ? 1 : 0;

        if ($request->hasFile('sch_logo')) {
            $oldPath = public_path($folder . '/' . $school->sch_logo);
            if (File::exists($oldPath)) {
                File::delete($oldPath);
            }

            $newName = time() . '.' . $request->sch_logo->extension();
            $request->sch_logo->move(public_path($folder), $newName);
            $school->sch_logo = $newName;
        }

        $school->save();

        return back()->with('success', 'School updated successfully.');
    }

    public function destroy($id)
    {
        $school = School::findOrFail($id);
        $school->delete();
        return back()->with('success', 'School deleted successfully.');
    }

    public function exportExcel()
    {
        $schools = School::orderBy('sch_id', 'desc')->get();
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        $headers = ['School ID', 'Name', 'Mobile', 'Email', 'Address', 'Description', 'City', 'Status', 'Verified'];
        $sheet->fromArray($headers, NULL, 'A1');

        $data = [];
        foreach ($schools as $school) {
            $data[] = [
                $school->sch_id,
                $school->sch_name,
                $school->sch_mobile,
                $school->sch_email,
                $school->sch_address,
                $school->sch_desc,
                $school->sch_city,
                $school->sch_status,
                $school->is_verified,
            ];
        }

        if (!empty($data)) {
            $sheet->fromArray($data, NULL, 'A2');
        }

        foreach (range('A', 'I') as $columnID) {
            $sheet->getColumnDimension($columnID)->setAutoSize(true);
        }

        $writer = new Xlsx($spreadsheet);
        $fileName = 'schools_' . date('Y-m-d_H-i-s') . '.xlsx';

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment; filename="' . urlencode($fileName) . '"');
        $writer->save('php://output');
        exit;
    }

    public function importExcel(Request $request)
    {
        $request->validate([
            'import_file' => 'required|mimes:xlsx,xls,csv'
        ]);

        try {
            $file = $request->file('import_file');
            $spreadsheet = IOFactory::load($file->getRealPath());
            $sheet = $spreadsheet->getActiveSheet();
            $rows = $sheet->toArray();

            // Skip header row
            unset($rows[0]);

            $successCount = 0;
            $errorCount = 0;

            foreach ($rows as $row) {
                if (empty($row[1])) continue; // Skip if name is empty

                $schoolId = !empty($row[0]) ? $row[0] : null;

                $data = [
                    'sch_name' => $row[1] ?? '',
                    'sch_mobile' => $row[2] ?? '',
                    'sch_email' => $row[3] ?? '',
                    'sch_address' => $row[4] ?? '',
                    'sch_desc' => $row[5] ?? '',
                    'sch_city' => $row[6] ?? '',
                    'sch_status' => isset($row[7]) ? $row[7] : 1,
                    'is_verified' => isset($row[8]) ? $row[8] : 0,
                ];

                if ($schoolId) {
                    $school = School::find($schoolId);
                    if ($school) {
                        $school->update($data);
                        $successCount++;
                    } else {
                        $errorCount++;
                    }
                } else {
                    School::create($data);
                    $successCount++;
                }
            }

            return redirect()->back()->with('success', "Import completed. Success: $successCount, Errors: $errorCount");

        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Error during import: ' . $e->getMessage());
        }
    }
}
