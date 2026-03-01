<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Student;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Exception; // បន្ថែម Exception

class StudentController extends Controller
{
    // ១. បង្ហាញបញ្ជីសិស្ស
    public function index()
    {
        $students = Student::with('user')
            ->where('IsDeleted', 0)
            ->orderBy('StuID', 'desc')
            ->get();

        return response()->json(['success' => true, 'data' => $students]);
    }

    // ២. បញ្ចូលទិន្នន័យ (កែសម្រួលថ្មីដើម្បីការពារ Error 500)
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'StuNameKH' => 'required|string|max:255',
            'StuNameEN' => 'required|string|max:255',
            'Gender'    => 'required',
            'Email'     => 'nullable|email|unique:tblstudents,Email',
            'Photo'     => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'errors' => $validator->errors()], 422);
        }

        try {
            // ចាត់ចែងរូបភាព
            $path = 'students/default.png';
            if ($request->hasFile('Photo')) {
                $path = $request->file('Photo')->store('students', 'public');
            }

            // បញ្ចូលទិន្នន័យដោយមានការត្រួតពិនិត្យ (Default Value)
            $student = Student::create([
                // ប្រសិនបើ UserID មិនមាន ត្រូវដាក់ null (លុះត្រាតែ Database អនុញ្ញាត)
                'UserID'        => $request->UserID ?? null, 
                'StuName'       => $request->StuName,
                'StuNameKH'     => $request->StuNameKH,
                'StuNameEN'     => $request->StuNameEN,
                'Gender'        => $request->Gender,
                'DOB'           => $request->DOB,
                'POB'           => $request->POB,
                'Address'       => $request->Address,
                'Phone'         => $request->Phone,
                'Email'         => $request->Email,
                'Promotion'     => $request->Promotion,
                'Photo'         => $path,
                'FatherName'    => $request->FatherName,
                'FatherJob'     => $request->FatherJob,
                'MotherName'    => $request->MotherName,
                'MotherJob'     => $request->MotherJob,
                'FamilyContact' => $request->FamilyContact,
                'Status'        => $request->Status ?? 1, // ប្តូរមកលេខ ១ តាម structure
                'IsDeleted'     => 0,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'បញ្ចូលទិន្នន័យសិស្សជោគជ័យ',
                'data' => $student
            ], 201);

        } catch (Exception $e) {
            // ប្រសិនបើមាន Error វានឹងប្រាប់មូលហេតុពិតប្រាកដ (ឧទាហរណ៍៖ Foreign Key Error)
            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ], 500);
        }
    }

    // ៣. បង្ហាញព័ត៌មានលម្អិត
    public function show($id)
    {
        $student = Student::with(['user', 'attendances', 'studies', 'requests'])->find($id);

        if (!$student || $student->IsDeleted == 1) {
            return response()->json(['success' => false, 'message' => 'រកមិនឃើញទិន្នន័យសិស្សទេ'], 404);
        }

        return response()->json(['success' => true, 'data' => $student]);
    }

    // ៤. កែប្រែទិន្នន័យ
    public function update(Request $request, $id)
    {
        $student = Student::find($id);
        if (!$student) {
            return response()->json(['success' => false, 'message' => 'រកមិនឃើញទិន្នន័យ'], 404);
        }


        try {
            $data = $request->all();
            if ($request->hasFile('Photo')) {
                if ($student->Photo && $student->Photo !== 'students/default.png') {
                    Storage::disk('public')->delete($student->Photo);
                }
                $data['Photo'] = $request->file('Photo')->store('students', 'public');
            }

            $student->update($data);
            return response()->json(['success' => true, 'data' => $student]);
        } catch (Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    // ៥. លុបទិន្នន័យ (Soft Delete)
    public function destroy($id)
    {
        $student = Student::find($id);
        if (!$student) {
            return response()->json(['success' => false, 'message' => 'រកមិនឃើញទិន្នន័យ'], 404);
        }

        $student->update(['IsDeleted' => 1]);
        return response()->json(['success' => true, 'message' => 'លុបទិន្នន័យសិស្សជោគជ័យ']);
    }
}
