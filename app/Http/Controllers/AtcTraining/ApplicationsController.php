<?php

namespace App\Http\Controllers\AtcTraining;

use App\Http\Controllers\Controller;
use App\Models\AtcTraining\Application;
use App\Models\Settings\AuditLogEntry;
use App\Models\Settings\CoreSettings;
use App\Mail\ApplicationAcceptedStaffEmail;
use App\Mail\ApplicationAcceptedUserEmail;
use App\Mail\ApplicationDeniedUserEmail;
use App\Mail\ApplicationStartedStaffEmail;
use App\Mail\ApplicationStartedUserEmail;
use App\Mail\ApplicationWithdrawnEmail;
use App\Models\Training\RosterMember;
use App\Models\Users\User;
use App\Models\Users\UserNotification;
use App\Models\AtcTraining\Student;
use Auth;
use Flash;
use Illuminate\Http\File as HttpFile;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Mail;

class ApplicationsController extends Controller
{
    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function startApplicationProcess()
    {
        //Is there an existing application?
        $existingApplication = Application::where('user_id', Auth::id())->where('status', 0)->first();

        //Redirects
        if (Auth::user()->rating_id < 2) {
            //user is in a prohibited rating (OBS and S1)
            return view('dashboard.application.start')->with('allowed', 'false');
        } elseif ($existingApplication != null) {
            //user already has an application
            return view('dashboard.application.start')->with('allowed', 'pendingApplication');
        } else {
            //hooray they can apply
            return view('dashboard.application.start')->with('allowed', 'true');
        }
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     * @throws \Illuminate\Validation\ValidationException
     */
    public function submitApplication(Request $request)
    {
        $messages = [
            'applicant_statement.required' => 'You need to write why you wish to control at Winnipeg.',
        ];

        //Validate form
        $validator = Validator::make($request->all(), [
            'applicant_statement' => 'required',
        ], $messages);

        if ($validator->fails()) {
            return redirect()->back()->withInput()->withErrors($validator, 'applicationErrors');
        }

        //Create model and save it
        $application = new Application();
        $application->application_id = Str::random(8);
        $application->user_id = Auth::id();
        $application->submitted_at = date('Y-m-d H:i:s');
        $application->applicant_statement = $request->get('applicant_statement');
        $application->save();

        //Send new application email to staff
        Mail::to(CoreSettings::where('id', 1)->firstOrFail()->emailfirchief)->cc([CoreSettings::where('id', 1)->first()->emaildepfirchief, CoreSettings::where('id', 1)->first()->emailcinstructor])->send(new ApplicationStartedStaffEmail($application));
        Mail::to(Auth::user()->email)->send(new ApplicationStartedUserEmail($application));

        //Return user to the applications detail page
        return redirect()->route('application.view', $application->application_id)->with('success', 'Application submitted! It should be processed within 72 hours. If you do not get a response, please send a ticket to the FIR Chief. Thanks for applying to Winnipeg!');
    }

    public function withdrawApplication($application_id)
    {
        //Find application from URL params
        $application = Application::where('application_id', $application_id)->firstOrFail();

        //Check if someone is being dumb
        if ($application->user != Auth::user()) {
            abort(403);
        } elseif ($application->status != 0) {
            abort(403, 'You cannot withdraw an already withdrawn or processed application!');
        }

        //Set application to withdrawn status and set processed
        $application->status = 3;
        $application->processed_at = date('Y-m-d H:i:s');
        $application->processed_by = Auth::id();
        $application->save();



        //Notify staff
        Mail::to(CoreSettings::where('id', 1)->firstOrFail()->emailfirchief)->cc(CoreSettings::where('id', 1)->first()->emaildepfirchief, CoreSettings::where('id', 1)->first()->emailcinstructor)->send(new ApplicationWithdrawnEmail($application));

        //Return user to applications details page
        return redirect()->route('application.view', $application->application_id)->with('info', 'Application withdrawn.');
    }

    public function viewApplication($application_id)
    {
        //Find application from URL params
        $application = Application::where('application_id', $application_id)->firstOrFail();

        if ($application->user != Auth::user()) {
            abort(403);
        }

        //Return user to applications details page
        return view('dashboard.application.view', compact('application'));
    }

    public function viewApplications()
    {
        //Fetch all applications
        //$applications = Application::where('user_id', Auth::user()->id)->get();
        $applications = Auth::user()->applications;

        //Return user to applications details page
        return view('dashboard.application.list', compact('applications'));
    }

    public function viewAllApplications()
    {
      $pendingapplications = Application::where('status', '0')->get();
      $acceptedapplications = Application::where('status', '2')->get();
      $deniedapplications = Application::where('status', '1')->get();

      return view('dashboard.training.applications.viewall', compact('pendingapplications', 'acceptedapplications', 'deniedapplications'));
    }

    public function joinWinnipeg() {
      $waitlist = Student::where('status', '0')->get();
        return view('joinwinnipeg', compact('waitlist'));
    }
}
