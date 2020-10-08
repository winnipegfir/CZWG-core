<?php

namespace App\Http\Controllers\Settings;

use App\Http\Controllers\Controller;
use App\Models\Settings\AuditLogEntry;
use App\Models\Settings\CoreSettings;
use App\Models\Settings\RotationImage;
use App\Models\Settings\HomepageImages;
use App\Notifications\MaintenanceNotification;
use Artisan;
use Auth;
use Illuminate\Http\Request;

class SettingsController extends Controller
{
    public function index()
    {
        return view('admin.settings.index');
    }

    /*
    Site info
    */
    public function siteInformation()
    {
        //Get the settings
        $coreSettings = CoreSettings::find(1);

        //Return the view
        return view('admin.settings.siteinformation', compact('coreSettings'));
    }

    /*
    Save site info
    */
    public function saveSiteInformation(Request $request)
    {
        //Get the settings
        $coreSettings = CoreSettings::find(1);

        //Save changes
        $coreSettings->sys_name = $request->get('sys_name');
        $coreSettings->release = $request->get('release');
        $coreSettings->sys_build = $request->get('sys_build');
        $coreSettings->copyright_year = $request->get('copyright_year');
        $coreSettings->save();

        //Return the view
        return view('admin.settings.siteinformation', compact('coreSettings'))->with('success', 'Settings saved');
    }

    /*
    Emails
    */
    public function emails()
    {
        //Get settings
        $coreSettings = CoreSettings::find(1);

        //Return the view
        return view('admin.settings.emails', compact('coreSettings'));
    }

    /*
    Save emails
    */
    public function saveEmails(Request $request)
    {
        //Get the settings
        $coreSettings = CoreSettings::find(1);

        //Save changes
        $coreSettings->emailfirchief = $request->get('emailfirchief');
        $coreSettings->emaildepfirchief = $request->get('emaildepfirchief');
        $coreSettings->emailcinstructor = $request->get('emailcinstructor');
        $coreSettings->emaileventc = $request->get('emaileventc');
        $coreSettings->emailfacilitye = $request->get('emailfacilitye');
        $coreSettings->emailwebmaster = $request->get('emailwebmaster');
        $coreSettings->save();

        //Return the view
        return view('admin.settings.emails', compact('coreSettings'))->with('success', 'Emails saved');
    }

    /*
    Audit log
    */
    public function auditLog()
    {
        $entriesall = AuditLogEntry::all();
        $entries = $entriesall->sortByDesc('created_at');

        return view('admin.settings.auditlog', compact('entries'));
    }

    public function banner() {
        $banner = CoreSettings::find(1);

        return view('admin.settings.banner', compact('banner'));
    }

    public function bannerEdit(Request $request) {
        //Get the settings
        $coreSettings = CoreSettings::find(1);

        if ($request->get('bannerMessage') == null) {
            $bannerMessage = "";
        } else {
            $bannerMessage = $request->get('bannerMessage');
        }

        if ($request->get('bannerLink') == null) {
            $bannerLink = "";
        } else {
            $bannerLink = $request->get('bannerLink');
        }

        if ($request->get('bannerMode') == null) {
            $bannerMode = "";
            $bannerMessage = "";
            $bannerLink = "";
        } else {
            $bannerMode = $request->get('bannerMode');
        }

        $coreSettings->banner = $bannerMessage;
        $coreSettings->bannerMode = $bannerMode;
        $coreSettings->bannerLink = $bannerLink;
        $coreSettings->save();


        return back()->withSuccess('The banner has been updated!');
    }

    public function imagesIndex() {
        $images = HomepageImages::all();

        return view('admin.settings.homepageimages', compact('images'));
    }

    public function uploadImage(Request $request) {
        $this->validate($request, [
            'URL' => 'required',
            'nameCredit' => 'required'
        ]);

        $image = new HomepageImages();
        $image->url = $request->URL;
        $image->credit = $request->nameCredit;
        $image->save();

        return back()->withSuccess('Image uploaded successfully!');
    }

    public function deleteImage($id) {
        $image = HomepageImages::where('id', $id)->first();
        $image->delete();

        return back()->withSuccess('Image deleted successfully!');
    }
}
