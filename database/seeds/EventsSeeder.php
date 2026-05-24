<?php

use Illuminate\Database\Seeder;

class EventsSeeder extends Seeder
{
    public function run()
    {
        $events = [
            [
                'name' => 'Fly-In Friday: CYWG Spotlight',
                'start_timestamp' => '2026-06-06 23:00:00',
                'end_timestamp' => '2026-06-07 03:00:00',
                'user_id' => 1,
                'description' => "## Fly-In Friday: CYWG Spotlight\n\nJoin us for our monthly **Fly-In Friday** event, this time shining the spotlight on **Winnipeg James Armstrong Richardson International Airport (CYWG)**!\n\nFull ATC staffing will be online from ground through to centre, making this the perfect opportunity to fly in or out of Winnipeg.\n\n### Details\n\n- **Date:** Friday, June 6, 2026\n- **Time:** 2300z – 0300z\n- **Airport:** CYWG – Winnipeg International\n\n### Recommended Routes\n\n- CYYZ → CYWG\n- CYVR → CYWG\n- CYEG → CYWG\n\nAll skill levels welcome. See you on the network!",
                'image_url' => 'https://cdn.discordapp.com/attachments/598024548301930496/762594915552985108/unknown.png',
                'controller_applications_open' => true,
                'departure_icao' => null,
                'arrival_icao' => 'CYWG',
                'slug' => 'fly-in-friday-cywg-spotlight-jun-2026',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Cross the Pond Westbound 2026',
                'start_timestamp' => '2026-06-14 06:00:00',
                'end_timestamp' => '2026-06-14 20:00:00',
                'user_id' => 1,
                'description' => "## Cross the Pond Westbound 2026\n\nWinnipeg FIR is proud to be a **participating facility** in this year's Cross the Pond Westbound event! Hundreds of pilots will be crossing the Atlantic and making their way into Canada — including many routing through Winnipeg airspace.\n\n### What to Expect\n\n- High traffic volumes throughout the event window\n- Full ATC coverage across all positions\n- Coordination with adjacent facilities (Gander, Moncton, Toronto, Edmonton)\n\n### For Controllers\n\nController applications are open to all certified Winnipeg FIR members. Priority will be given to higher ratings for busier positions. Please review the CTP briefing package in the Publications section before applying.\n\n### For Pilots\n\nBook your slot on the official CTP website. Routes into CYWG and onwards are highly encouraged!",
                'image_url' => null,
                'controller_applications_open' => true,
                'departure_icao' => null,
                'arrival_icao' => 'CYWG',
                'slug' => 'cross-the-pond-westbound-2026',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Thunder Bay Bash: CYQT Feature Event',
                'start_timestamp' => '2026-06-27 22:00:00',
                'end_timestamp' => '2026-06-28 02:00:00',
                'user_id' => 1,
                'description' => "## Thunder Bay Bash\n\nThis month we're taking the party to **Thunder Bay (CYQT)**! Located on the northern shore of Lake Superior, CYQT is one of the gems of the Winnipeg FIR and doesn't get nearly enough traffic.\n\n### Event Info\n\n- **Date:** Friday, June 27, 2026\n- **Time:** 2200z – 0200z\n- **Airport:** CYQT – Thunder Bay International\n\n### Why Thunder Bay?\n\nCYQT serves as an important hub for Northwestern Ontario with regular turboprop and regional jet traffic. It's a great airport for pilots who want a more relaxed, manageable approach compared to the big hubs.\n\n### ATC Coverage\n\n- Thunder Bay Radio / FSS\n- Winnipeg Centre (CWG) for IFR separation\n\nCome fly somewhere different — Thunder Bay is waiting!",
                'image_url' => null,
                'controller_applications_open' => false,
                'departure_icao' => null,
                'arrival_icao' => 'CYQT',
                'slug' => 'thunder-bay-bash-cyqt-jun-2026',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        foreach ($events as $event) {
            DB::table('events')->insert($event);
        }
    }
}
