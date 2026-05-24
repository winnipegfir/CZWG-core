<?php

use Illuminate\Database\Seeder;

class NewsSeeder extends Seeder
{
    public function run()
    {
        $articles = [
            [
                'title' => 'Welcome to the New Winnipeg FIR Website',
                'user_id' => 1,
                'show_author' => false,
                'image' => 'https://cdn.discordapp.com/attachments/598024548301930496/762594915552985108/unknown.png',
                'summary' => 'We\'re excited to launch the redesigned Winnipeg FIR member portal with a fresh new look and improved features.',
                'content' => "## Welcome!\n\nWe are thrilled to announce the launch of the redesigned **Winnipeg FIR** member portal. The new site brings a modern look and feel with improved navigation, faster load times, and better mobile support.\n\n### What's New\n\n- Redesigned dashboard with live network statistics\n- Improved event management and controller applications\n- Streamlined ATC training portal\n- Better feedback and ticketing system\n\nWe hope you enjoy the new experience. As always, if you encounter any issues, please submit a ticket through the support portal.\n\n*– Winnipeg FIR Staff*",
                'published' => '2026-05-01 12:00:00',
                'edited' => null,
                'visible' => true,
                'email_level' => 0,
                'certification' => 0,
                'slug' => 'welcome-to-the-new-winnipeg-fir-website',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'title' => 'Spring 2026 Training Waitlist Now Open',
                'user_id' => 1,
                'show_author' => false,
                'image' => null,
                'summary' => 'Applications for the Spring 2026 ATC training intake are now being accepted. Spots are limited — apply early.',
                'content' => "## Spring 2026 Training Intake\n\nThe Winnipeg FIR Training Department is pleased to announce that the **Spring 2026 training waitlist** is now open for new applicants.\n\n### Eligibility\n\n- Must hold a valid VATSIM account in good standing\n- Must be a member of the Winnipeg FIR division\n- No prior ATC experience required for S1 applicants\n\n### How to Apply\n\nNavigate to the **ATC Training** section of the portal and click *Apply for Training*. You will be contacted by a mentor once a spot becomes available.\n\nWe look forward to training the next generation of Winnipeg controllers!",
                'published' => '2026-05-10 09:00:00',
                'edited' => null,
                'visible' => true,
                'email_level' => 1,
                'certification' => 0,
                'slug' => 'spring-2026-training-waitlist-now-open',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'title' => 'Updated Winnipeg TMA Procedures Now in Effect',
                'user_id' => 1,
                'show_author' => false,
                'image' => null,
                'summary' => 'Revised terminal procedures for the Winnipeg TMA have been published and are now effective. All certified controllers should review the changes.',
                'content' => "## Updated TMA Procedures\n\nEffective **May 15, 2026**, revised terminal procedures for the Winnipeg TMA (CYWG/CYAV) are now in effect. All certified controllers are required to review the updated SOPs before their next controlling session.\n\n### Key Changes\n\n- Revised arrival flow for Runway 36 operations\n- Updated coordination procedures between APP and TWR during simultaneous operations\n- New Letter of Agreement with Winnipeg Centre (CWG)\n\n### Resources\n\nThe updated SOPs are available in the **Publications** section of the portal. If you have questions, please contact the Chief Instructor.\n\nThank you for your continued professionalism on the network.",
                'published' => '2026-05-15 14:30:00',
                'edited' => null,
                'visible' => true,
                'email_level' => 2,
                'certification' => 1,
                'slug' => 'updated-winnipeg-tma-procedures-now-in-effect',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        foreach ($articles as $article) {
            DB::table('news')->insert($article);
        }
    }
}
