<?php

namespace App\Http\Controllers\Users;

use App\Http\Controllers\Controller;
use App\Jobs\ProcessArticlePublishing;
use App\Models\Settings\AuditLogEntry;
use App\Models\News\CarouselItem;
use App\Models\Settings\CoreSettings;
use App\Models\Publications\MeetingMinutes;
use App\Models\News\News;
use App\Models\Users\StaffMember;
use App\Models\Users\User;
use Auth;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class ImageUploadController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */

    public function imageUpload()
    {
        return view('dashboard.image');
    }

    public function imageUploadPost()
    {
        request()->validate([
            'image' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);

        $imageName = time().'.'.request()->image->getClientOriginalExtension();
        request()->image->move(public_path('images/uploads'), $imageName);

        return back()
            ->with('success','Well congrats buddy. Hopefully you\'re happy because your image has been "successfully" uploaded (and you thought it wouldn\'t eh?). It\'s nice to know that you know how to click about <b>*3 times*</b>. Find your "amazing" image at: https://site-dev.winnipegfir.ca/images/uploads/'. $imageName);
        }
}
