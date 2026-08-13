<?php

namespace App\Http\Controllers\Management;

use App\Http\Controllers\Controller;
use App\Models\Subject;
use Illuminate\Http\Request;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\IOFactory;

class SubjectController extends Controller
{
    public function index()
    {
        $subjects = Subject::orderBy('subject_id', 'DESC')->get();
        return view('management.subject', compact('subjects'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'subject_name' => 'required|string|max:255',
            'subject_status' => 'required|in:0,1',
        ]);

        Subject::create([
            'subject_name' => $request->subject_name,
            'subject_status' => $request->subject_status,
        ]);

        return back()->with('success', 'Subject created successfully.');
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'subject_name' => 'required|string|max:255',
            'subject_status' => 'required|in:0,1',
        ]);

        $subject = Subject::findOrFail($id);
        $subject->subject_name = $request->subject_name;
        $subject->subject_status = $request->subject_status;
        $subject->save();

        return back()->with('success', 'Subject updated successfully.');
    }

    public function destroy($id)
    {
        $subject = Subject::findOrFail($id);
        $subject->delete();
        return back()->with('success', 'Subject deleted successfully.');
    }

    public function exportExcel()
    {
        $subjects = Subject::orderBy('subject_id', 'desc')->get();
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        $headers = ['Subject ID', 'Name', 'Status'];
        $sheet->fromArray($headers, NULL, 'A1');

        $data = [];
        foreach ($subjects as $subject) {
            $data[] = [
                $subject->subject_id,
                $subject->subject_name,
                $subject->subject_status,
            ];
        }

        if (!empty($data)) {
            $sheet->fromArray($data, NULL, 'A2');
        }

        foreach (range('A', 'C') as $columnID) {
            $sheet->getColumnDimension($columnID)->setAutoSize(true);
        }

        $writer = new Xlsx($spreadsheet);
        $fileName = 'subjects_' . date('Y-m-d_H-i-s') . '.xlsx';

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

                $subjectId = !empty($row[0]) ? $row[0] : null;
                $data = [
                    'subject_name' => $row[1] ?? '',
                    'subject_status' => isset($row[2]) ? $row[2] : 1,
                ];

                if ($subjectId) {
                    $subject = Subject::find($subjectId);
                    if ($subject) {
                        $subject->update($data);
                        $successCount++;
                    } else {
                        $errorCount++;
                    }
                } else {
                    Subject::create($data);
                    $successCount++;
                }
            }

            return redirect()->back()->with('success', "Import completed. Success: $successCount, Errors: $errorCount");

        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Error during import: ' . $e->getMessage());
        }
    }
}
