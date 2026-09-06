<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        $decks = [
            'introduction-to-air-traffic-services' => [
                'air-traffic-services' => 'https://docs.google.com/presentation/d/e/2PACX-1vR65P8H5MgaCXlkUQwfmoR1pQSRGRsCR6y4FFz-IzrcamIu8Msc9QgLP7EUTQCR5iHT5gpAdaMsKnsM/pubembed?start=false&loop=false&delayms=3000',
                'the-atis' => 'https://docs.google.com/presentation/d/e/2PACX-1vRYZOhkbILEkTn7u26ABv9psyYXV4XFEz-SGUT1kmvD8MHO1SAXItpf-w8jYk9eNPIWR5pd4ZlmOuWi/pubembed?start=false&loop=false&delayms=3000',
                'vatsimisms' => 'https://docs.google.com/presentation/d/e/2PACX-1vStSGj3ScH9kaun4sBckwdLCPG5TuYB0sK0VmWPTgSIlH48VaUgEid1q7H06wK0GEK1JVDngJG2q_Je/pubembed?start=false&loop=false&delayms=3000',
            ],
            'introduction-to-aviation' => [
                'the-basics' => 'https://docs.google.com/presentation/d/e/2PACX-1vShPHiKEx-CA13o7_HIXZbx2EpzrsioZ70uo5bVdqk-4iAXlX87ZaI1-ppe4BUi6ANLC92y0zVAVYbx/pubembed?start=false&loop=false&delayms=3000',
                'radio-telecommunication' => 'https://docs.google.com/presentation/d/e/2PACX-1vTjA-hbvVM7S4phmXcmQRXPyrQmp3-vtOapOCmf7Sv7w3IMivM3gSqvIHGQZQhz4wpxPFapxMmuVfrc/pubembed?start=false&loop=false&delayms=3000',
                'flight-planning' => 'https://docs.google.com/presentation/d/e/2PACX-1vQH3Z_irRcRaLyczXjVjkhvUpKj6m2YWjQrwRfw_KWBal7bczcnCwoQz9CwSI8DsCz5cH_kaPB0b8SW/pubembed?start=false&loop=false&delayms=3000',
                'airspace' => 'https://docs.google.com/presentation/d/e/2PACX-1vSg0X12eaXJWNdgkXG0h0SZmpoFrp_xtCbDixb6i4cnR-BAo1Q-567xSJihVRSYXXVuXBEoj2Qw6K6v/pubembed?start=false&loop=false&delayms=3000',
                'aviation-weather' => 'https://docs.google.com/presentation/d/e/2PACX-1vQHb_APiZa-W1PWdIJkcxdPNdWzZu23InWvJO5mjs74UGP2-1KL03Xu62SzgWZ-gUAeZuMZwyoU6tS0/pubembed?start=false&loop=false&delayms=3000',
            ],
            'software-setup' => [
                'initial-program-installation' => 'https://docs.google.com/presentation/d/e/2PACX-1vS6JoJ2paepEo26_6C813n2ddrmu4TJTM-wz7HQ8qTAC6v50VYdEVBXu5BTVxzggLydcVC7NuohCcQY/pubembed?start=false&loop=false&delayms=3000',
                'euroscope-basic-functions' => 'https://docs.google.com/presentation/d/e/2PACX-1vQ8CfCFX1QNSt1ZpCtRgpHgtcF4sZmrpkHH1MOtwaZ-5TrtOirMLNsoICZ2JnFeMXCdc2MsLBFf6ym-/pubembed?start=false&loop=false&delayms=3000',
                'external-client-walkthrough' => 'https://docs.google.com/presentation/d/e/2PACX-1vQwj3Sy8DZLRto9mwSrsWyhrfn9NqynLpVtFeivgdn3gm9GEVdMWw3M6FQpQmHnjI7UFx-fWnM3QIz8/pubembed?start=false&loop=false&delayms=3000',
                'communication-platforms' => 'https://docs.google.com/presentation/d/e/2PACX-1vSqc57fl5dpDpCGcZm35k5q1JTpYk3pTfgdLrf9rwDIC0FP0DAheBM2dzhrirdapyTC5IXGkzgAOXK2/pubembed?start=false&loop=false&delayms=3000',
            ],
            'clearance-delivery' => [
                'flight-plans' => 'https://docs.google.com/presentation/d/e/2PACX-1vRYF9Iqlw_qzkJblQgdLFFcRghgfV4cwAwUnxRbgYR0gfvb5TMha7BTLMuy_2QwYcxU_9Pul9_sLlGG/pubembed?start=false&loop=false&delayms=3000',
                'issuing-airways' => 'https://docs.google.com/presentation/d/e/2PACX-1vQ_xlJ0SiOZFsJGtRVuTOETE0GYNCqBQgeeLSesKn-L2zG_XV44VffmhumScyRb4rJ8_wYttlXznVUi/pubembed?start=false&loop=false&delayms=3000',
            ],
            'ground' => [
                'basic-ground-operations' => 'https://docs.google.com/presentation/d/e/2PACX-1vSHIvbO1nLb8S-vkFdXOPJ44mNCRjTGK-VliNYWGbDvTQoiNxtP8A2dDQb7oXV7PGDr6okk0qyy9YmB/pubembed?start=false&loop=false&delayms=3000',
                'advanced-ground-operations' => 'https://docs.google.com/presentation/d/e/2PACX-1vTQfTaS6V9bZZ3k9WICOjOiszXL89u0mETVhNL4PeSt0NfN9c5s7tk9JCHmXfbbQFqA3GP76AoAlwvg/pubembed?start=false&loop=false&delayms=3000',
            ],
            'tower' => [
                'winnipeg-airspace-familiarization' => 'https://docs.google.com/presentation/d/e/2PACX-1vRnG4yhaSCw7Xk7-7GHyVyWT6RbbeAeIgUjKqiuQNDCUtbdj6POycDy2tvBqTimgX7cWtHZpewzv8_q/pubembed?start=false&loop=false&delayms=3000',
                'ifr-tower-control' => 'https://docs.google.com/presentation/d/e/2PACX-1vTAJ07fXEgXXpTIvctk8ALmmQ5l9T9Mk_ymbYysTkae_lGHJzZdwmFlnyDu5Iufcxf5oLdzAlvP3Yuw/pubembed?start=false&loop=false&delayms=3000',
                'vfr-tower-control' => 'https://docs.google.com/presentation/d/e/2PACX-1vRQRbvWGUayoktu7SF0MJGiEh7ijM9VusWQSyhQR39Ajaq3ekloX8Y4rYiqhvBzb4R7jqkt31tnhgdQ/pubembed?start=false&loop=false&delayms=3000',
                'st-andrews-tower' => 'https://docs.google.com/presentation/d/e/2PACX-1vQT6k14vRUXkooeeJKD-GuVe-X8BHRQ5XZ6i_CALU90_u4BrFG2MWP1dRxS-9q0HfdA65YHFGyFvKoB/pubembed?start=false&loop=false&delayms=3000',
            ],
            'advanced-tower' => [
                'changing-flight-rules' => 'https://docs.google.com/presentation/d/e/2PACX-1vQC30KVan51zBG3B3I9qiJXammgvWhgPz2InIJMVijOUGNKOit8oCQBymgy__HD-hZRlxgK3wECixGt/pubembed?start=false&loop=false&delayms=3000',
                'visual-contact-approaches' => 'https://docs.google.com/presentation/d/e/2PACX-1vQsikxVc2H6mu3DMm_XsruEUOAD0CS01LGq1cYR1gn2uOqqbWbR9FP5rilUWdbU2KbBTgcZvi1xuTdO/pubembed?start=false&loop=false&delayms=3000',
                'special-operations' => 'https://docs.google.com/presentation/d/e/2PACX-1vSiwqAqqOow4AfWc4ht00qbydEC3TlC_Bzm4D8ckF9Mc4jwB3K1I50rapitq73k41MlKINqZf-vElQf/pubembed?start=false&loop=false&delayms=3000',
                'specialty-towers' => 'https://docs.google.com/presentation/d/e/2PACX-1vQ8HHV_jsdlOaF2bhYz016bDUMaMT6rDrM-GNByv1K0v8x_4fXRZxo4dKRZWplZkYFvkUo4VTkPWznH/pubembed?start=false&loop=false&delayms=3000',
            ],
        ];

        foreach ($decks as $courseSlug => $modules) {
            $courseId = DB::table('academy_courses')->where('slug', $courseSlug)->value('id');
            if (! $courseId) continue;

            foreach ($modules as $moduleSlug => $url) {
                DB::table('academy_modules')
                    ->where('course_id', $courseId)
                    ->where('slug', $moduleSlug)
                    ->update(['google_slides_url' => $url, 'updated_at' => now()]);
            }
        }
    }

    public function down(): void
    {
        // Keep URLs if rolled back; administrators may have edited them after deployment.
    }
};
