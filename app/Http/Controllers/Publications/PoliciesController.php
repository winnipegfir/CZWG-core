<?php

namespace App\Http\Controllers\Publications;

use App\Http\Controllers\Controller;
use App\Models\Settings\AuditLogEntry;
use App\Mail\EmailAnnouncementEmail;
use App\Models\News\News;
use App\Models\Publications\Policy;
use App\Models\Publications\PolicySection;
use App\Models\Users\User;
use Auth;
use function GuzzleHttp\Promise\queue;
use Illuminate\Http\Request;
use Mail;

class PoliciesController extends Controller
{
    public function index()
    {
        if (Auth::check() == false || Auth::user()->permissions < 2) {
            $policySections = PolicySection::all();

            return view('policies', compact('policySections'));
        } else {
            $policySections = PolicySection::all();
            $nullPolicies = Policy::where('section_id', null)->get();
            $allPolicies = Policy::all();

            return view('policies', compact('nullPolicies', 'policySections', 'allPolicies'));
        }
    }

    public function addPolicy(Request $request)
    {
        $this->validate($request, [
            'name' => 'required',
            'section' => 'required',
            'details' => 'required',
            'link' => 'required',
            'embed' => 'required',
            'staff_only' => 'required',
            'email' => 'required',
        ]);

        if ($request->get('staff_only') == 1 && $request->get('email') != 'none') {
            return redirect()->route('policies')->with('error', 'A private policy cannot be released publicly via email or a news article.')->withInput();
        }

        if ($request->get('section') == -1) {
            return back()->with('error', 'You need to select a section!');
        }

        if($request->get('date')) {
            $date = $request->get('date');
        } else {
            $date = date('Y-m-d');
        }

        $policy = new Policy([
            'section_id' => $request->get('section'),
            'name' => $request->get('name'),
            'details' => $request->get('details'),
            'link' => $request->get('link'),
            'embed' => $request->get('embed'),
            'staff_only' => $request->get('staff_only'),
            'author' => Auth::user()->id,
            'releaseDate' => $date,
        ]);

        $policy->save();

        if ($request->get('email') == 'all') {
            $news = new News([
                'title' => 'New Policy: '.$policy->name,
                'content' => "The '".$policy->name."' policy for CZQO has been released. Read it on the policies page.",
                'date' => date('Y-m-d'),
                'type' => 'Email',
                'user_id' => Auth::user()->id,
            ]);
            $news->save();
            $users = User::all();
            foreach ($users as $user) {
                $data = [];
                $data['content'] = $news->content;
                $data['title'] = $news->title;
                $data['fname'] = Auth::user()->fname;
                $data['lname'] = Auth::user()->lname;
                $data['receivingname'] = $user->fname;
                Mail::to($user->email)->send(new EmailAnnouncementEmail($data), function ($message) use ($data) {
                    $message->subject('Winnipeg News: '.$data['title']);
                });
            }
        } elseif ($request->get('email') == 'emailcert') {
            $news = new News([
                'title' => 'New Policy: '.$policy->name,
                'content' => "The '".$policy->name."' policy for Winnipeg has been released. Read it on the policies page.",
                'date' => date('Y-m-d'),
                'type' => 'CertifiedOnly',
                'user_id' => Auth::user()->id,
            ]);
            $news->save();
            $users = User::all();
            foreach ($users as $user) {
                if ($user->permissions >= 1) {
                    $data = [];
                    $data['content'] = $news->content;
                    $data['title'] = $news->title;
                    $data['fname'] = Auth::user()->fname;
                    $data['lname'] = Auth::user()->lname;
                    $data['receivingname'] = $user->fname;
                    Mail::to($user->email)->send(new EmailAnnouncementEmail($data), function ($message) use ($data) {
                        $message->subject('Winnipeg Controller News: '.$data['title']);
                    });
                }
            }
        } elseif ($request->get('email') == 'newsonly') {
            $slug = str_replace(' ', '-', $policy->name);

            $news = new News([
                'title' => 'New Policy: '.$policy->name,
                'content' => "The '".$policy->name."' policy for Winnipeg has been released. Read it on the policies page.",
                'published' => date('Y-m-d'),
                'type' => 'NoEmail',
                'user_id' => Auth::user()->id,
                'slug' =>$slug
            ]);
            $news->save();
        }
        $entry = new AuditLogEntry([
            'user_id' => Auth::user()->id,
            'affected_id' => 1,
            'action' => 'CREATE POLICY '.'('.$policy->id.')',
            'time' => date('Y-m-d H:i:s'),
            'private' => 0,
        ]);
        $entry->save();

        return redirect()->route('policies')->with('success', 'Policy '.$policy->name.' added!');
    }

    public function editPolicy(Request $request, $id)
    {
        $this->validate($request, [
            'name' => 'required',
            'section' => 'required',
            'details' => 'required',
            'link' => 'required',
            'embed' => 'required',
            'staff_only' => 'required',
        ]);

        if ($request->get('section') == -1) {
            return back()->with('error', 'You need to select a section!');
        }

        if($request->get('date')) {
            $date = $request->get('date');
        } else {
            $date = date('Y-m-d');
        }

        $policy = Policy::where('id', $id)
            ->update(['section_id' => $request->get('section'), 'name' => $request->get('name'), 'details' => $request->get('details'), 'link' => $request->get('link'), 'embed' => $request->get('embed'), 'staff_only' => $request->get('staff_only'), 'author' => Auth::user()->id, 'releaseDate' => $date]);

        $entry = new AuditLogEntry([
            'user_id' => Auth::user()->id,
            'affected_id' => 1,
            'action' => 'EDIT POLICY '.'('.$id.')',
            'time' => date('Y-m-d H:i:s'),
            'private' => 0,
        ]);
        $entry->save();

        return redirect()->route('policies')->with('success', 'Policy <text class="font-weight-bold">'.$request->get('name').'</text> edited!');
    }

    public function deletePolicy($id)
    {
        $policy = Policy::where('id', $id)->firstOrFail();
        $entry = new AuditLogEntry([
            'user_id' => Auth::user()->id,
            'affected_id' => 1,
            'action' => 'DELETE POLICY '.$policy->name.'('.$policy->id.')',
            'time' => date('Y-m-d H:i:s'),
            'private' => 0,
        ]);
        $entry->save();
        $policy->delete();

        return redirect()->route('policies')->with('success', 'Policy deleted.');
    }


    public function addPolicySection(Request $request)
    {
        $this->validate($request, [
            'name' => 'required',
        ]);

        $policySection = new PolicySection([
            'section_name' => $request->get('name'),
        ]);

        $policySection->save();

        return redirect()
            ->route('policies')
            ->with('success', 'New policy section: ' . $request->get('name') . ' created!');
    }

    public function deletePolicySection($id)
    {
        $section = PolicySection::where('id', $id)->firstOrFail();
        $policies = Policy::all();

        foreach($policies as $p) {
            if($p->section_id == $id) {
                Policy::where('id', $p->id)
                    ->update(['section_id' => null, 'staff_only' => 1]);
            }
        }

        $entry = new AuditLogEntry([
            'user_id' => Auth::user()->id,
            'affected_id' => 1,
            'action' => 'DELETE POLICY SECTION ' .$section->section_name . '('.$section->id.')',
            'time' => date('Y-m-d H:i:s'),
            'private' => 0,
        ]);
        $entry->save();
        $section->delete();

        return redirect()->route('policies')->with('success', 'Policy section ' . $section->section_name . ' deleted.');
    }
}
