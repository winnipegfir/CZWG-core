<?php

namespace Tests\Unit;

use App\Models\Users\User;
use App\Services\AcademyLaunchNotice;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class AcademyLaunchNoticeTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        if (config('database.default') !== 'sqlite' || config('database.connections.sqlite.database') !== ':memory:') {
            throw new \RuntimeException('These tests require an isolated, in-memory SQLite database.');
        }
        Schema::create('users', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('permissions')->default(1);
        });
        Schema::create('core_info', function (Blueprint $table) {
            $table->increments('id');
            $table->string('academy_access_mode')->default('normal');
            $table->boolean('academy_nav_enabled')->default(true);
            $table->boolean('academy_maintenance_mode')->default(false);
        });
        DB::table('core_info')->insert(['id' => 1, 'academy_access_mode' => 'normal']);
        DB::table('users')->insert([['id' => 1, 'permissions' => 1], ['id' => 2, 'permissions' => 1]]);
        $migration = require database_path('migrations/2026_10_26_000000_create_academy_launch_notices_table.php');
        $migration->up();
    }

    public function test_five_displays_then_stops_across_new_requests(): void
    {
        for ($i = 0; $i < 5; $i++) {
            $this->assertTrue((new AcademyLaunchNotice)->record(User::findOrFail(1), 'show'));
        }
        $this->assertFalse((new AcademyLaunchNotice)->record(User::findOrFail(1), 'show'));
        $this->assertSame(5, (int) DB::table('academy_launch_notices')->where('user_id', 1)->value('display_count'));
    }

    public function test_dismissal_persists_without_affecting_another_account(): void
    {
        $service = new AcademyLaunchNotice;
        $this->assertTrue($service->record(User::findOrFail(1), 'show'));
        $service->record(User::findOrFail(1), 'dismiss');
        $this->assertFalse((new AcademyLaunchNotice)->record(User::findOrFail(1), 'show'));
        $this->assertTrue($service->record(User::findOrFail(2), 'show'));
    }

    public function test_preview_modes_do_not_consume_displays(): void
    {
        foreach (['admin', 'staff'] as $mode) {
            DB::table('core_info')->where('id', 1)->update(['academy_access_mode' => $mode]);
            $this->assertFalse((new AcademyLaunchNotice)->record(User::findOrFail(1), 'show'));
        }
        $this->assertSame(0, DB::table('academy_launch_notices')->count());
    }

    public function test_maintenance_and_hidden_navigation_do_not_consume_displays(): void
    {
        DB::table('core_info')->where('id', 1)->update(['academy_maintenance_mode' => true]);
        $this->assertFalse((new AcademyLaunchNotice)->record(User::findOrFail(1), 'show'));
        DB::table('core_info')->where('id', 1)->update(['academy_maintenance_mode' => false, 'academy_nav_enabled' => false]);
        $this->assertFalse((new AcademyLaunchNotice)->record(User::findOrFail(1), 'show'));
        $this->assertSame(0, DB::table('academy_launch_notices')->count());
    }

    public function test_guests_and_unaccepted_accounts_are_ineligible(): void
    {
        $this->assertFalse(AcademyLaunchNotice::eligible(null));
        DB::table('users')->where('id', 1)->update(['permissions' => 0]);
        $this->assertFalse((new AcademyLaunchNotice)->record(User::findOrFail(1), 'show'));
    }

    public function test_missing_migration_fails_closed(): void
    {
        Schema::drop('academy_launch_notices');
        $this->assertFalse((new AcademyLaunchNotice)->record(User::findOrFail(1), 'show'));
    }

    public function test_authenticated_endpoint_validates_action_and_limits_displays(): void
    {
        $this->actingAs(User::findOrFail(1));
        $this->postJson('/academy/launch-notice', ['action' => 'invalid'])->assertStatus(422);
        $this->postJson('/academy/launch-notice', ['action' => 'show'])->assertOk()->assertJson(['show' => true]);
        $this->postJson('/academy/launch-notice', ['action' => 'dismiss'])->assertOk()->assertJson(['show' => false]);
        $this->postJson('/academy/launch-notice', ['action' => 'show'])->assertOk()->assertJson(['show' => false]);
    }
}
