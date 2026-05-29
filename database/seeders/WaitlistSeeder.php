<?php

namespace Database\Seeders;

use App\Models\AtcTraining\Student;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class WaitlistSeeder extends Seeder
{
    public function run()
    {
        $students = [
            ['cid' => 1468146, 'visitor' => true,  'date' => '2024-03-27'],
            ['cid' => 986717,  'visitor' => false, 'date' => '2024-06-25'],
            ['cid' => 1152129, 'visitor' => true,  'date' => '2024-11-10'],
            ['cid' => 1596254, 'visitor' => true,  'date' => '2024-11-11'],
            ['cid' => 1217788, 'visitor' => false, 'date' => '2024-12-01'],
            ['cid' => 1203303, 'visitor' => false, 'date' => '2024-12-20'],
            ['cid' => 1886641, 'visitor' => false, 'date' => '2025-01-19'],
            ['cid' => 1176713, 'visitor' => true,  'date' => '2025-01-19'],
            ['cid' => 1528001, 'visitor' => true,  'date' => '2025-02-04'],
            ['cid' => 1396338, 'visitor' => false, 'date' => '2025-03-10'],
            ['cid' => 1803156, 'visitor' => false, 'date' => '2025-04-22'],
            ['cid' => 1668116, 'visitor' => false, 'date' => '2025-05-21'],
            ['cid' => 1916791, 'visitor' => false, 'date' => '2025-05-21'],
            ['cid' => 1943842, 'visitor' => false, 'date' => '2025-08-31'],
            ['cid' => 1093966, 'visitor' => false, 'date' => '2025-09-10'],
            ['cid' => 1442675, 'visitor' => true,  'date' => '2025-10-17'],
            ['cid' => 1928434, 'visitor' => false, 'date' => '2025-11-04'],
            ['cid' => 1743981, 'visitor' => false, 'date' => '2025-11-25'],
            ['cid' => 1730737, 'visitor' => false, 'date' => '2025-12-26'],
            ['cid' => 1707671, 'visitor' => false, 'date' => '2025-12-26'],
            ['cid' => 1937950, 'visitor' => false, 'date' => '2026-01-08'],
            ['cid' => 1948438, 'visitor' => false, 'date' => '2026-01-20'],
            ['cid' => 1846372, 'visitor' => false, 'date' => '2026-02-06'],
            ['cid' => 1928453, 'visitor' => false, 'date' => '2026-02-06'],
            ['cid' => 1826457, 'visitor' => false, 'date' => '2026-03-21'],
            ['cid' => 1674262, 'visitor' => false, 'date' => '2026-03-21'],
            ['cid' => 1661565, 'visitor' => false, 'date' => '2026-03-21'],
            ['cid' => 1775910, 'visitor' => false, 'date' => '2026-04-22'],
        ];

        $skipped = [];

        DB::statement('SET FOREIGN_KEY_CHECKS=0;');

        foreach ($students as $s) {
            $existing = Student::where('user_id', $s['cid'])->first();
            if ($existing) {
                $skipped[] = $s['cid'];
                continue;
            }

            Student::create([
                'user_id'          => $s['cid'],
                'status'           => 0,
                'instructor_id'    => null,
                'entry_type'       => $s['visitor'] ? 'New Visitor' : 'New Student',
                'waitlist_added_at' => Carbon::parse($s['date']),
                'last_status_change' => Carbon::parse($s['date']),
                'created_at'       => Carbon::parse($s['date']),
                'updated_at'       => Carbon::parse($s['date']),
            ]);
        }

        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        $this->command->info('Seeded ' . (count($students) - count($skipped)) . ' students.');
        if ($skipped) {
            $this->command->warn('Skipped (already exist): ' . implode(', ', $skipped));
        }
    }
}
