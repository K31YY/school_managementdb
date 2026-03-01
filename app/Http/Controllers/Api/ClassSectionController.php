<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ClassSection;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ClassSectionController extends Controller
{
    public function index()
    {
        // ទាញយកថ្នាក់រៀនជាមួយឆ្នាំសិក្សា និងបង្ហាញតែថ្នាក់ដែលមិនទាន់លុប
        $sections = ClassSection::with('academicYear')
            ->where('IsDeleted', 0)
            ->orderBy('SectionID', 'desc')
            ->get();
            
        return response()->json(['success' => true, 'data' => $sections]);
    }

    public function store(Request $request)
    {
        // កែសម្រួល Key ឱ្យត្រូវតាម Table
        $validator = Validator::make($request->all(), [
            'SectionName' => 'required|string|max:255',
            'YearID'      => 'required|exists:tblacademicyears,YearID',
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'errors' => $validator->errors()], 422);
        }

        // បញ្ចូលទិន្នន័យដោយប្រើ Key ត្រឹមត្រូវតាម Database
        $sec = ClassSection::create([
            'SectionName' => $request->SectionName,
            'YearID'      => $request->YearID,
            'IsDeleted'   => 0 // កំណត់តម្លៃដើម
        ]);

        return response()->json(['success' => true, 'data' => $sec], 201);
    }

    public function show($id)
    {
        $section = ClassSection::with('academicYear')->find($id);

        if (!$section || $section->IsDeleted == 1) {
            return response()->json(['success' => false, 'message' => 'រកមិនឃើញថ្នាក់រៀននេះទេ'], 404);
        }
        return response()->json(['success' => true, 'data' => $section]);
    }

    public function update(Request $request, $id)
    {
        $section = ClassSection::find($id);
        if (!$section) return response()->json(['success' => false, 'message' => 'រកមិនឃើញទិន្នន័យ'], 404);

        // អនុញ្ញាតឱ្យ Update គ្រប់ Field ដែលមានក្នុង fillable
        $section->update($request->all());
        return response()->json(['success' => true, 'data' => $section]);
    }

    public function destroy($id)
    {
        $section = ClassSection::find($id);
        if (!$section) return response()->json(['success' => false, 'message' => 'រកមិនឃើញទិន្នន័យ'], 404);

        // ប្រើ Soft Delete ប្តូរ IsDeleted ទៅជា ១
        $section->update(['IsDeleted' => 1]);
        return response()->json(['success' => true, 'message' => 'លុបថ្នាក់រៀនជោគជ័យ']);
    }
}
