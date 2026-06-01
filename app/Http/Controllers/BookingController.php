<?php

namespace App\Http\Controllers;

use App\Services\VatsimBookingService;
use Auth;
use Carbon\Carbon;
use Illuminate\Http\Request;

class BookingController extends Controller
{
    public function index()
    {
        $result = (new VatsimBookingService)->getBookings([
            'callsign' => config('services.vatsim_bookings.callsign_prefix'),
            'sort'     => 'start',
            'sort_dir' => 'asc',
        ]);

        $bookings = collect($result['data'] ?? [])
            ->filter(fn($b) => Carbon::parse($b['end'])->isFuture())
            ->values();

        $myBookings = Auth::check()
            ? $bookings->where('cid', Auth::id())->values()
            : collect();

        return view('bookings.index', compact('bookings', 'myBookings'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'airspace' => 'required|string|max=20',
            'position' => 'required|string|max=10',
            'start'    => 'required|date',
            'end'      => 'required|date|after:start',
        ]);

        $callsign = strtoupper($request->airspace) . '_' . strtoupper($request->position);

        $result = (new VatsimBookingService)->createBooking([
            'callsign' => $callsign,
            'cid'      => Auth::id(),
            'type'     => 'booking',
            'start'    => Carbon::parse($request->start)->format('Y-m-d H:i:s'),
            'end'      => Carbon::parse($request->end)->format('Y-m-d H:i:s'),
        ]);

        if ($result['status'] === 'ok') {
            return redirect()->route('bookings.index')->withSuccess("Booking created for {$callsign}.");
        }

        return redirect()->back()->withError('Could not create booking. Please try again.');
    }

    public function update(Request $request, int $id)
    {
        $request->validate([
            'airspace' => 'required|string|max:20',
            'position' => 'required|string|max:10',
            'start'    => 'required|date',
            'end'      => 'required|date|after:start',
        ]);

        $callsign = strtoupper($request->airspace) . '_' . strtoupper($request->position);

        $result = (new VatsimBookingService)->updateBooking($id, [
            'callsign' => $callsign,
            'cid'      => Auth::id(),
            'start'    => Carbon::parse($request->start)->format('Y-m-d H:i:s'),
            'end'      => Carbon::parse($request->end)->format('Y-m-d H:i:s'),
        ]);

        if ($result['status'] === 'ok') {
            return redirect()->route('bookings.index')->withSuccess("Booking updated for {$callsign}.");
        }

        return redirect()->back()->withError('Could not update booking. Please try again.');
    }

    public function destroy(int $id)
    {
        $success = (new VatsimBookingService)->deleteBooking($id);

        if ($success) {
            return redirect()->route('bookings.index')->withSuccess('Booking cancelled.');
        }

        return redirect()->back()->withError('Could not cancel booking. Please try again.');
    }
}
