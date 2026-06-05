<?php

namespace App\Console\Commands;

use App\Services\VatsimBookingService;
use Carbon\Carbon;
use Illuminate\Console\Command;

class PurgeExpiredBookings extends Command
{
    protected $signature   = 'bookings:purge-expired';
    protected $description = 'Delete CZWG bookings whose end time has passed (UTC).';

    public function handle(): void
    {
        $service = new VatsimBookingService;
        $result  = $service->getBookings(['sort' => 'end', 'sort_dir' => 'asc']);

        if ($result['status'] !== 'ok') {
            return;
        }

        $validPrefixes = ['CYQT', 'CYWG', 'CYAV', 'CYPG', 'CYXE', 'CYQR', 'CYMJ', 'WPG'];
        $now           = Carbon::now('UTC');

        foreach ($result['data'] as $booking) {
            $prefix = explode('_', $booking['callsign'] ?? '', 2)[0];

            if (!in_array($prefix, $validPrefixes)) {
                continue;
            }

            if (Carbon::parse($booking['end'], 'UTC')->lt($now)) {
                $service->deleteBooking($booking['id']);
            }
        }
    }
}
