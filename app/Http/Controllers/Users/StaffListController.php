<?php

namespace App\Http\Controllers\Users;

use App\Http\Controllers\Controller;
use App\Models\AtcTraining\Instructor;
use App\Models\Users\StaffGroup;
use App\Models\Users\StaffMember;
use App\Models\Users\User;
use Illuminate\Http\Request;

class StaffListController extends Controller
{
    public function index()
    {
        $staff = StaffMember::all();

        // Instructor list
        $instructors_temp = Instructor::all(); // Temp
        $instructors = []; // Actual

        // Sort assessors to top of array
        foreach ($instructors_temp as $instructor) {
            if ($instructor->qualification == 'Assessor') {
                array_push($instructors, $instructor);
            }
        }

        // Sort the instructors at the bottom of the array
        foreach ($instructors_temp as $instructor) {
            if ($instructor->qualification == 'Instructor') {
                array_push($instructors, $instructor);
            }
        }

        $groups = StaffGroup::all();

        return view('staff', compact('staff', 'instructors', 'groups'));
    }

    public function editIndex()
    {
        $staff = StaffMember::all();
        $users = User::all();
        $groups = StaffGroup::all();

        return view('dashboard.staff.index', compact('staff', 'users', 'groups'));
    }

    public function addStaffMember(Request $request)
    {
        $this->validate($request, [
            'position' => 'required',
            'shortform' => 'required',
            'group' => 'required',
        ]);
        $user = User::whereId($request->get('newstaff'))->first();
        $addstaff = StaffMember::create([
            'user_id' => $request->input('newstaff'),
            'position' => $request->input('position'),
            'shortform' => $request->input('shortform'),
            'group_id' => $request->input('group'),
            'group' => 'Staff',
            'description' => 'Create Description',
            'email' => 'user@user.com',
        ]);

        return redirect()->back()->with('success', 'Staff member '.$addstaff->shortform.' created!');
    }

    public function editStaffMember(Request $request, $id)
    {
        //Grab staff object
        $staff = StaffMember::whereId($id)->firstOrFail();

        //Check user given is a user

        $user = User::whereId($request->input('cid'))->first();

        if ($user === null) {
            return redirect()->back()->withInput()->with('error', 'CID for staff member '.$staff->shortform.' invalid!');
        }

        //Ok we have a user.. assign them!
        $staff->user_id = $user->id;

        //Update description and email
        $staff->description = $request->get('description');
        $staff->email = $request->get('email');

        //Save it
        $staff->save();

        //Return!
        return redirect()->back()->with('success', 'Staff member '.$staff->position.' saved!');
    }

    public function deleteStaffMember(Request $request, $id)
    {
        //Grab staff object
        $staff = StaffMember::whereId($id)->firstOrFail();

        //Delete it
        $staff->delete();

        //Return!
        return redirect()->back()->with('success', 'Staff member '.$staff->position.' deleted!');
    }
}
