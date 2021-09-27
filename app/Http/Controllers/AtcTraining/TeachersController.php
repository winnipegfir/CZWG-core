<?php

namespace App\Http\Controllers\AtcTraining;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Teacher;

class TeachersController extends Controller
{
    public function store(Request $request) {
        $teacher = new Teacher;
        $teacher->user_cid = $request->input('newteacher');
        $teacher->is_local = $request->has('is_local');
        $teacher->is_radar = $request->has('is_radar');
        $teacher->is_enroute = $request->has('is_enroute');
        $teacher->is_instructor = $request->has('is_instructor');
        $teacher->save();

        return redirect()->route('instructors');
    }

    public function delete($id) {
        $teacher = Teacher::whereId($id)->firstOrFail();
        $teacher->delete();

        return redirect('/instructors')->withSuccess('Teacher Removed!');
    }
}
