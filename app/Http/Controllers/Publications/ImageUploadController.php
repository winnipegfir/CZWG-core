<?php

namespace App\Http\Controllers\Publications;

use App\Http\Controllers\Controller;
use Auth;

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
