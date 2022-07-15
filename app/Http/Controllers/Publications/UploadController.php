<?php

namespace App\Http\Controllers\Publications;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Storage;

class UploadController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function upload()
    {
        return view('dashboard.upload');
    }

    public function uploadPost()
    {
        request()->validate([
            'file' => 'required|max:2048',
        ]);

        $fileName = time().'.'.request()->file->getClientOriginalExtension();
        Storage::putFileAs(
            'public/files/uploads', request()->file, $fileName
        );

        return back()
            ->with('success', 'File uploaded to: <a href='.config('app.url').'/storage/files/uploads/'.$fileName.'>'.config('app.url').'/storage/files/uploads/'.$fileName.'</a>');
    }
}
