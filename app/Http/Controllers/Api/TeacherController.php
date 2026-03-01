<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Teacher;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class TeacherController extends Controller
{
    public function index()
    {
        // ទាញយកគ្រូដែលមិនទាន់លុប រួមជាមួយព័ត៌មាន User (Username, Role)
        $teachers = Teacher::with('user')
            ->where('IsDeleted', 0)
            ->orderBy('TeacherID', 'desc')
            ->get();

        return response()->json(['success' => true, 'data' => $teachers]);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'TeacherName' => 'required|string|max:255',
            'UserID'      => 'nullable|exists:tblusers,UserID', // ផ្ទៀងផ្ទាត់ជាមួយ Table Users
            'Email'       => 'nullable|email|unique:tblteachers,Email',
            'Photo'       => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'errors' => $validator->errors()], 422);
        }

        // ចាត់ចែងការរក្សារុករូបភាព
        $path = null;
        if ($request->hasFile('Photo')) {
            $path = $request->file('Photo')->store('teachers', 'public');
        } else {
            $path = 'teachers/default.png';
        }

        // បង្កើតទិន្នន័យគ្រូ
        $teacher = Teacher::create([
            'UserID'      => $request->UserID,
            'TeacherName' => $request->TeacherName,
            'Gender'      => $request->Gender,
            'DOB'         => $request->DOB,
            'Phone'       => $request->Phone,
            'Email'       => $request->Email,
            'Specialty'   => $request->Specialty,
            'Address'     => $request->Address,
            'StartDate'   => $request->StartDate,
            'EndDate'     => $request->EndDate,
            'Certificate' => $request->Certificate,
            'Photo'       => $path,
            'IsDeleted'   => 0, // តម្លៃដើមគឺមិនទាន់លុប
        ]);

        return response()->json(['success' => true, 'data' => $teacher], 201);
    }

    public function show($id)
    {
        // ទាញទិន្នន័យគ្រូម្នាក់ រួមជាមួយ User Relation
        $teacher = Teacher::with('user')->find($id);
        
        if (!$teacher || $teacher->IsDeleted == 1) {
            return response()->json(['message' => 'រកមិនឃើញទិន្នន័យគ្រូទេ'], 404);
        }
        return response()->json(['success' => true, 'data' => $teacher]);
    }

    public function update(Request $request, $id)
    {
        $teacher = Teacher::find($id);
        if (!$teacher) return response()->json(['message' => 'រកមិនឃើញទិន្នន័យ'], 404);

        $data = $request->except('Photo');

        if ($request->hasFile('Photo')) {
            // លុបរូបចាស់ចោល
            if ($teacher->Photo && $teacher->Photo !== 'teachers/default.png') {
                Storage::disk('public')->delete($teacher->Photo);
            }
            $data['Photo'] = $request->file('Photo')->store('teachers', 'public');
        }

        $teacher->update($data);

        return response()->json(['success' => true, 'data' => $teacher]);
    }

    public function destroy($id)
    {
        $teacher = Teacher::find($id);
        if (!$teacher) return response()->json(['message' => 'រកមិនឃើញទិន្នន័យ'], 404);

        // ប្រើ Soft Delete តាមរចនាសម្ព័ន្ធ Table
        $teacher->update(['IsDeleted' => 1]);
        return response()->json(['success' => true, 'message' => 'លុបគ្រូជោគជ័យ']);
    }
}
