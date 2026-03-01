<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\AcademicYear;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class AcademicYearController extends Controller
{
    // ១. បង្ហាញបញ្ជីឆ្នាំសិក្សាទាំងអស់
    public function index()
    {
        return response()->json([
            'success' => true,
            // កែពី AcademicYearID មក YearID តាម Table
            'data' => AcademicYear::where('IsDeleted', 0)
                ->orderBy('YearID', 'desc')
                ->get()
        ]);
    }

    // ២. បញ្ចូលឆ្នាំសិក្សាថ្មី
    public function store(Request $request)
    {
        // កែ Key ឱ្យត្រូវនឹង Column "YearName"
        $validator = Validator::make($request->all(), [
            'YearName'  => 'required|unique:tblacademicyears,YearName',
            'StartDate' => 'required|date',
            'EndDate'   => 'required|date',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false, 
                'errors'  => $validator->errors()
            ], 422);
        }

        // បញ្ចូលទិន្នន័យ (ប្រើ $fillable ពី Model)
        $data = AcademicYear::create([
            'YearName'    => $request->YearName,
            'StartDate'   => $request->StartDate,
            'EndDate'     => $request->EndDate,
            'Description' => $request->Description,
            'IsDeleted'   => 0, // តម្លៃដើមគឺមិនទាន់លុប
        ]);

        return response()->json([
            'success' => true, 
            'data'    => $data
        ], 201);
    }

    // ៣. បង្ហាញឆ្នាំសិក្សាតាម ID
    public function show($id)
    {
        $year = AcademicYear::find($id);
        if (!$year || $year->IsDeleted == 1) {
            return response()->json(['message' => 'រកមិនឃើញឆ្នាំសិក្សានេះទេ'], 404);
        }
        return response()->json(['success' => true, 'data' => $year]);
    }

    // ៤. កែប្រែទិន្នន័យឆ្នាំសិក្សា
    public function update(Request $request, $id)
    {
        $year = AcademicYear::find($id);
        if (!$year) {
            return response()->json(['message' => 'រកមិនឃើញទិន្នន័យ'], 404);
        }

        // អនុញ្ញាតឱ្យ Update តាមរយៈ fillable
        $year->update($request->all());

        return response()->json([
            'success' => true, 
            'data'    => $year
        ]);
    }

    // ៥. លុបឆ្នាំសិក្សា (Soft Delete)
    public function destroy($id)
    {
        $year = AcademicYear::find($id);
        if (!$year) {
            return response()->json(['message' => 'រកមិនឃើញទិន្នន័យ'], 404);
        }

        // ប្តូរ IsDeleted ទៅជា 1 (មិនលុបចេញពី DB ទេ)
        $year->update(['IsDeleted' => 1]);

        return response()->json([
            'success' => true, 
            'message' => 'លុបឆ្នាំសិក្សាជោគជ័យ'
        ]);
    }
}
