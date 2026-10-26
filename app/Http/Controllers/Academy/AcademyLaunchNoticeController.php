<?php

namespace App\Http\Controllers\Academy;

use App\Http\Controllers\Controller;
use App\Services\AcademyLaunchNotice;
use Illuminate\Http\Request;

class AcademyLaunchNoticeController extends Controller
{
    public function update(Request $request, AcademyLaunchNotice $notice)
    {
        $validated = $request->validate(['action' => 'required|in:show,dismiss']);

        return response()->json([
            'show' => $notice->record($request->user(), $validated['action']),
        ])->header('Cache-Control', 'no-store, private');
    }
}
