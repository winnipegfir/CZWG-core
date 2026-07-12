<?php

namespace App\Http\Controllers;

use Auth;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
    public function index()
    {
        $notifications = Auth::user()->notifications()->paginate(25);

        return view('dashboard.notifications.index', compact('notifications'));
    }

    public function poll(Request $request)
    {
        $user = Auth::user();

        return response()->json([
            'unread_count' => $user->unreadNotifications()->count(),
            'items' => $user->notifications()->latest()->take(8)->get()->map(function ($n) {
                return [
                    'id' => $n->id,
                    'title' => $n->data['title'] ?? '',
                    'body' => $n->data['body'] ?? '',
                    'icon' => $n->data['icon'] ?? 'fa-bell',
                    'url' => $n->data['url'] ?? '#',
                    'read' => $n->read_at !== null,
                    'created_at' => $n->created_at->diffForHumans(),
                ];
            }),
        ]);
    }

    public function open($id)
    {
        $notification = Auth::user()->notifications()->where('id', $id)->firstOrFail();
        $notification->markAsRead();

        return redirect($notification->data['url'] ?? route('dashboard.index'));
    }

    public function readAll()
    {
        Auth::user()->unreadNotifications->markAsRead();

        return redirect()->back();
    }

    public function destroy(Request $request, $id)
    {
        $notification = Auth::user()->notifications()->where('id', $id)->firstOrFail();
        $notification->delete();

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json(['ok' => true]);
        }

        return redirect()->back();
    }
}
