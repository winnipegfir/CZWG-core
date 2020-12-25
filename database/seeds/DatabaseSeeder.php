<?php

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        DB::table('core_info')->insert([
            'id' => 1,
            'sys_name' => 'Winnipeg FIR Core',
            'release' => 'DEV',
            'sys_build' => 'DEV',
            'copyright_year' => 'NONE',
            'banner' => '',
            'bannerLink' => '',
            'bannerMode' => '',
            'emailfirchief' => 'info@info.ca',
            'emaildepfirchief' => 'info@info.ca',
            'emailcinstructor' => 'info@info.ca',
            'emaileventc' => 'info@info.ca',
            'emailfacilitye' => 'info@info.ca',
            'emailwebmaster' => 'info@info.ca',
        ]);

        DB::table('users')->insert([
            'id' => 1,
            'fname' => 'System',
            'lname' => 'User',
            'email' => 'no-reply@info.com',
            'permissions' => 4,
            'display_fname' => 'System',
        ]);

        DB::table('users')->insert([
            'id' => 2,
            'fname' => 'Roster',
            'lname' => 'Placeholder',
            'email' => 'no-reply@info.ca',
            'permissions' => 1,
            'display_fname' => 'Roster',
        ]);

        DB::table('staff_groups')->insert([
            'id' => 1,
            'name' => 'Executive Team',
            'slug' => 'executive',
            'description' => 'CZWG\'s executive team oversees FIR operations',
            'can_receive_tickets' => true,
        ]);

        DB::table('staff_member')->insert([
            'group' => 'exec',
            'position' => 'FIR Chief',
            'user_id' => 1,
            'group_id' => 1,
            'description' => 'Head of Winnipeg’s day-to-day operations, manages all staff in the FIR, and keeps VATCAN updated with Winnipeg. Also is currently the interim Events Coordinator.',
            'email' => 'chief@info.com',
            'shortform' => 'firchief',
        ]);

        DB::table('event_positions')->insert(
            ['position' => 'Delivery'],
            ['position' => 'Ground'],
            ['position' => 'Tower'],
            ['position' => 'Departure'],
            ['position' => 'Arrival'],
            ['position' => 'Centre'],
            ['position' => 'Relief']
        );

        DB::table('homepage_images')->insert([
            'url' => 'https://cdn.discordapp.com/attachments/598024548301930496/762594915552985108/unknown.png',
            'credit' => 'Winnipeg FIR',
        ]);
    }
}
