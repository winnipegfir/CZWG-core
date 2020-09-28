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
            'file' => 'required|mimes:jpeg,png,jpg,gif,svg,pdf|max:2048',
        ]);

        $fileName = time().'.'.request()->file->getClientOriginalExtension();
        request()->file->move(public_path('images/uploads'),$fileName);

        return back()
            ->with('success','File uploaded to: <a href="https://winnipegfir.ca/images/uploads/'.$fileName.'">https://winnipegfir.ca/images/uploads/'.$fileName.'</a>');
        }
}
