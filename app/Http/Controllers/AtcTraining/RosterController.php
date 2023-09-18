<?php

namespace App\Http\Controllers\AtcTraining;

use App\Http\Controllers\Controller;
use App\Models\AtcTraining\RosterMember;
use App\Models\Network\SessionLog;
use App\Models\Users\User;
use Illuminate\Http\Request;

class RosterController extends Controller
{
    public function showPublic()
    {
        $roster = RosterMember::where('visit', '0')->get()->sortBy('cid');
        $visitroster = RosterMember::where('visit', '1')->get()->sortBy('cid');

        return view('roster', compact('roster', 'visitroster'));
    }

    public function index()
    {
        $roster = RosterMember::where('visit', '0')->get()->sortBy('cid');
        $visitroster2 = RosterMember::where('visit', '1')->get()->sortBy('cid');
        $users = User::all();

        return view('dashboard.roster.index', compact('roster', 'visitroster2', 'users'));
    }

    public function deleteController($id)
    {
        $roster = RosterMember::findorFail($id);
        $session = SessionLog::where('roster_member_id', $id)->get();

        foreach ($session as $s) {
            $s->delete();
        }
        $roster->delete();

        return redirect('/dashboard/roster')->withSuccess('Successfully deleted from roster!');
    }

    public function addController(Request $request)
    {
        //here we are getting the data from the table
        $users = User::findOrFail($request->input('newcontroller'));
        $rosterMember = RosterMember::where('cid', $users->id)->first();
        if ($rosterMember == null) {
            RosterMember::create([
                'cid' => $users->id,
                'user_id' => $users->id,
                'full_name' => $users->fullName('FL'),
                'status' => 'home',
                'visit' => '0',
            ]);
        } else {
            return redirect()->back()->withErrors('Member: '.$users->fullName('FL').' CID: '.$users->id.' is already on the roster!');
        }

        return redirect('/dashboard/roster')->withSuccess('Successfully added '.$users->fullName('FL').' CID: '.$users->id.' to roster!');
    }

    public function addVisitController(Request $request)
    {
        //here we are getting the data from the table
        $users = User::findOrFail($request->input('newcontroller'));
        $rosterMember = RosterMember::where('cid', $users->id)->first();
        if ($rosterMember == null) {
            RosterMember::create([
                'cid' => $users->id,
                'user_id' => $users->id,
                'full_name' => $users->fullName('FL'),
                'status' => 'visit',
                'visit' => 1,
            ]);
        } else {
            return redirect()->back()->withErrors('Member: '.$users->fullName('FL').' CID: '.$users->id.' is already on the roster!');
        }

        return redirect('/dashboard/roster')->withSuccess('Successfully added '.$users->fullName('FL').' CID: '.$users->id.' to roster!');
    }

    public function editControllerForm($cid)
    {
        $roster = RosterMember::where('cid', $cid)->first();

        return view('dashboard.roster.edituser', compact('roster'))->with('cid', $cid);
    }

    public function editController(Request $request, $cid)
    {
        $roster = RosterMember::where('cid', $cid)->first();
        if ($roster != null) {
            $roster->del = $request->input('del');
            $roster->gnd = $request->input('gnd');
            $roster->twr = $request->input('twr');
            $roster->dep = $request->input('dep');
            $roster->app = $request->input('app');
            $roster->ctr = $request->input('ctr');
            $roster->remarks = $request->input('remarks');
            if ($request->input('rating_hours') == 'true') {
                $roster->rating_hours = 0;
            }
            $roster->active = $request->input('active');
            $roster->save();
        }

        return redirect('/dashboard/roster')->withSuccess('Successfully edited!');
    }
}
