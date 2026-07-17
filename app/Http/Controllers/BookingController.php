<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Network\NetworkController;
use App\Services\VatsimBookingService;
use Auth;
use Carbon\Carbon;
use Illuminate\Http\Request;

class BookingController extends Controller
{
    private function ownsBooking(VatsimBookingService $service, int $id): bool
    {
        $result = $service->getBookings(['cid' => Auth::id()]);
        return collect($result['data'] ?? [])->contains('id', $id);
    }

    public function index()
    {
        $result = (new VatsimBookingService)->getBookings([
            'sort'     => 'start',
            'sort_dir' => 'asc',
        ]);

        $bookings = collect($result['data'] ?? [])
            ->filter(fn($b) =>
                Carbon::parse($b['end'])->isFuture() &&
                collect(NetworkController::HOME_FIR_PREFIXES)->contains(fn($prefix) => str_starts_with($b['callsign'], $prefix))
            )
            ->values();

        $myBookings = Auth::check()
            ? $bookings->where('cid', Auth::id())->values()
            : collect();

        $positionPrefixes = ['CYQT', 'CYWG', 'CYAV', 'CYPG', 'CYXE', 'CYQR', 'CYMJ', 'WPG'];

        return view('bookings.index', compact('bookings', 'myBookings', 'positionPrefixes'));
    }

    public function store(Request $request)
    {
        $validPrefixes = ['CYQT', 'CYWG', 'CYAV', 'CYPG', 'CYXE', 'CYQR', 'CYMJ', 'WPG'];

        $request->validate([
            'airspace' => ['required', 'string', 'max:20', 'in:' . implode(',', $validPrefixes)],
            'position' => 'required|string|max:10|alpha',
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

        $service = new VatsimBookingService;
        if (!$this->ownsBooking($service, $id)) {
            abort(403);
        }

        $callsign = strtoupper($request->airspace) . '_' . strtoupper($request->position);

        $result = $service->updateBooking($id, [
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
        $service = new VatsimBookingService;
        if (!$this->ownsBooking($service, $id)) {
            abort(403);
        }

        $success = $service->deleteBooking($id);

        if ($success) {
            return redirect()->route('bookings.index')->withSuccess('Booking cancelled.');
        }

        return redirect()->back()->withError('Could not cancel booking. Please try again.');
    }
}
