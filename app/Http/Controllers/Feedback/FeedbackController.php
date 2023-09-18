<?php

namespace App\Http\Controllers\Feedback;

use App\Http\Controllers\Controller;
use App\Models\AtcTraining\RosterMember;
use App\Models\Feedback\ControllerFeedback;
use App\Models\Feedback\WebsiteFeedback;
use App\Models\Settings\AuditLogEntry;
use App\Models\Settings\CoreSettings;
use App\Models\Users\User;
use App\Notifications\Feedback\NewControllerFeedback;
use App\Notifications\Feedback\NewOperationsFeedback;
use App\Notifications\Feedback\NewWebsiteFeedback;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Validator;

class FeedbackController extends Controller
{
    public function index()
    {
        $controller_feedback = ControllerFeedback::all()->sortByDesc('created_at');
        $website_feedback = WebsiteFeedback::all()->sortByDesc('created_at');
        $controller_feedback_attention = ControllerFeedback::where('approval', 0)->get();

        return view('feedback.index', compact('controller_feedback', 'website_feedback', 'controller_feedback_attention'));
    }

    public function yourFeedback()
    {
        $feedback = ControllerFeedback::where('approval', 2)->get();

        return view('feedback.yourfeedback', compact('feedback'));
    }

    public function viewControllerFeedback($id)
    {
        $submitter = User::where('id', ControllerFeedback::where('id', $id)->firstOrFail()->user_id)->firstOrFail();
        $controller = User::where('id', ControllerFeedback::where('id', $id)->firstOrFail()->controller_cid)->firstOrFail();
        $feedback = ControllerFeedback::where('id', $id)->firstOrFail();

        return view('feedback.controllerview', compact('id', 'submitter', 'controller', 'feedback'));
    }

    public function approveControllerFeedback($id)
    {
        $feedback = ControllerFeedback::where('id', $id)->firstOrFail();

        if ($feedback->approval == 2) {
            return redirect()->back()->withErrors('Feedback ID#'.$id.' has already been approved!');
        }

        $feedback->approval = 2;
        $feedback->save();

        return redirect()->back()->withSuccess('Feedback ID#'.$id.' has been approved!');
    }

    public function denyControllerFeedback($id)
    {
        $feedback = ControllerFeedback::where('id', $id)->firstOrFail();

        if ($feedback->approval == 1) {
            return redirect()->back()->withErrors('Controller Feedback ID#'.$id.' has already been denied!');
        }

        $feedback->approval = 1;
        $feedback->save();

        return redirect()->back()->withSuccess('Controller Feedback ID#'.$id.' has been denied!');
    }

    public function editControllerFeedback(Request $request, $id)
    {
        $feedback = ControllerFeedback::where('id', $id)->firstOrFail();
        $feedback->content = $request->get('content');
        $feedback->save();

        $log = new AuditLogEntry();
        $log->user_id = Auth::user()->id;
        $log->affected_id = $feedback->controller_cid;
        $log->action = 'Edited Controller Feedback ID#'.$id;
        $log->time = date('Y-m-d H:i:s');
        $log->private = 0;
        $log->save();

        return redirect()->back()->withSuccess('Controller Feedback ID#'.$feedback->id.' has been edited!');
    }

    public function deleteControllerFeedback($id)
    {
        $feedback = ControllerFeedback::where('id', $id)->firstOrFail();
        $feedback->delete();

        return redirect()->to(route('staff.feedback.index'))->withSuccess('Controller Feedback ID#'.$id.' has been deleted!');
    }

    public function viewWebsiteFeedback($id)
    {
        $submitter = User::where('id', WebsiteFeedback::where('id', $id)->firstOrFail()->user_id)->firstOrFail();
        $feedback = WebsiteFeedback::where('id', $id)->firstOrFail();

        return view('feedback.websiteview', compact('id', 'submitter', 'feedback'));
    }

    public function deleteWebsiteFeedback($id)
    {
        $feedback = WebsiteFeedback::where('id', $id)->firstOrFail();
        $feedback->delete();

        return redirect()->to(route('staff.feedback.index'))->withSuccess('Website Feedback ID#'.$id.' has been deleted!');
    }

    public function create()
    {
        $controllers = RosterMember::all()->sortBy('cid');

        return view('feedback.create', compact('controllers'));
    }

    public function createPost(Request $request)
    {
        //dd($request->all());
        //Define validator messages
        $messages = [
            'feedbackType.required' => 'You need to select a type of feedback.',
        ];

        //Validate
        $validator = Validator::make($request->all(), [
            'feedbackType' => 'required',
            'content' => 'required',
        ], $messages);

        if ($request->get('feedbackType') == '0') {
            $validator->errors()->add('type', 'You need to select a feedback type.');
        }
        //If it's controller feedback then...
        elseif ($request->get('feedbackType') == 'controller') {
            //If they dont have the controller CID
            if ($request->get('controllerCid') == '0') {
                $validator->after(function ($validator) {
                    $validator->errors()->add('controllerCid', 'You need to provide the Controller\'s Name/CID.');
                });
            }
            if ($request->get('position') == null) {
                $validator->after(function ($validator) {
                    $validator->errors()->add('position', 'You need to specify the Controller\'s position.');
                });
            }
        } else { /*Otherwise*/
            //No subject
            if ($request->get('subject') == null) {
                $validator->after(function ($validator) {
                    $validator->errors()->add('subject', 'You need to fill in the subject field.');
                });
            }
        }
        //dd($validator->errors());
        //Redirect if fails
        if ($validator->fails()) {
            return redirect()->back()->withInput()->withErrors($validator, 'createFeedbackErrors');
        }

        //Otherwise...
        switch ($request->get('feedbackType')) {
            case 'website':
                $feedback = new WebsiteFeedback([
                    'user_id' => Auth::id(),
                    'subject' => $request->get('subject'),
                    'content' => $request->get('content'),
                ]);
                $feedback->save();
                Notification::route('mail', CoreSettings::find(1)->emailwebmaster)->notify(new NewWebsiteFeedback($feedback));
                break;
            case 'operations':
                $feedback = new OperationsFeedback([
                    'user_id' => Auth::id(),
                    'subject' => $request->get('subject'),
                    'content' => $request->get('content'),
                ]);
                $feedback->save();
                Notification::route('mail', CoreSettings::find(1)->emailfacilitye)->notify(new NewOperationsFeedback($feedback));
                break;
            case 'controller':
                $feedback = new ControllerFeedback([
                    'user_id' => Auth::id(),
                    'controller_cid' => $request->get('controllerCid'),
                    'position' => $request->get('position'),
                    'content' => $request->get('content'),
                ]);
                $feedback->save();
                Notification::route('mail', CoreSettings::find(1)->emailfirchief)->notify(new NewControllerFeedback($feedback));
                Notification::route('mail', CoreSettings::find(1)->emaildepfirchief)->notify(new NewControllerFeedback($feedback));
                break;
        }

        return view('feedback.sent');
    }
}
