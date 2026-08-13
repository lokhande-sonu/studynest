<?php

namespace App\Http\Controllers\Management;

use App\Http\Controllers\Controller;
use App\Models\Classes;
use Illuminate\Http\Request;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\IOFactory;

class ClassController extends Controller
{
    public function index()
    {
        $classes = Classes::orderBy('class_id', 'DESC')->get();
        return view('management.class', compact('classes'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'class_name' => 'required|string|max:255',
            'class_status' => 'required|in:0,1',
        ]);

        Classes::create([
            'class_name' => $request->class_name,
            'class_status' => $request->class_status,
        ]);

        return back()->with('success', 'Class created successfully.');
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'class_name' => 'required|string|max:255',
            'class_status' => 'required|in:0,1',
        ]);

        $class = Classes::findOrFail($id);
        $class->class_name = $request->class_name;
        $class->class_status = $request->class_status;
        $class->save();

        return back()->with('success', 'Class updated successfully.');
    }

    public function destroy($id)
    {
        $class = Classes::findOrFail($id);
        $class->delete();
        return back()->with('success', 'Class deleted successfully.');
    }

    public function exportExcel()
    {
        $classes = Classes::orderBy('class_id', 'desc')->get();
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        $headers = ['Class ID', 'Name', 'Status'];
        $sheet->fromArray($headers, NULL, 'A1');

        $data = [];
        foreach ($classes as $class) {
            $data[] = [
                $class->class_id,
                $class->class_name,
                $class->class_status,
            ];
        }

        if (!empty($data)) {
            $sheet->fromArray($data, NULL, 'A2');
        }

        foreach (range('A', 'C') as $columnID) {
            $sheet->getColumnDimension($columnID)->setAutoSize(true);
        }

        $writer = new Xlsx($spreadsheet);
        $fileName = 'classes_' . date('Y-m-d_H-i-s') . '.xlsx';

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

            unset($rows[0]);

            $successCount = 0;
            $errorCount = 0;

            foreach ($rows as $row) {
                if (empty($row[1])) continue;

                $classId = !empty($row[0]) ? $row[0] : null;
                $data = [
                    'class_name' => $row[1] ?? '',
                    'class_status' => isset($row[2]) ? $row[2] : 1,
                ];

                if ($classId) {
                    $class = Classes::find($classId);
                    if ($class) {
                        $class->update($data);
                        $successCount++;
                    } else {
                        $errorCount++;
                    }
                } else {
                    Classes::create($data);
                    $successCount++;
                }
            }

            return redirect()->back()->with('success', "Import completed. Success: $successCount, Errors: $errorCount");

        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Error during import: ' . $e->getMessage());
        }
    }
}
