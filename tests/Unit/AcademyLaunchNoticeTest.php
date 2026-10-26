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
    private bool $transactionStarted = false;

    protected function setUp(): void
    {
        parent::setUp();
        $connection = config('database.default');
        $database = config("database.connections.{$connection}.database");
        $inMemory = $connection === 'sqlite' && $database === ':memory:';
        if (! app()->environment('testing') || (! $inMemory && ! str_contains(strtolower((string) $database), 'test'))) {
            throw new \RuntimeException('These tests require an in-memory database or a dedicated test database.');
        }

        // Local PHPUnit has an empty SQLite database. CI already migrates and
        // seeds MySQL; reuse that schema without running DDL inside a transaction.
        if ($inMemory && ! Schema::hasTable('users')) {
            Schema::create('users', function (Blueprint $table) {
                $table->increments('id');
                $table->integer('permissions')->default(1);
                $table->string('fname')->nullable();
                $table->string('lname')->nullable();
                $table->string('email')->nullable();
                $table->integer('rating_id')->nullable();
                $table->string('timezone')->nullable();
                $table->timestamps();
            });
            Schema::create('core_info', function (Blueprint $table) {
                $table->increments('id');
                $table->string('academy_access_mode')->default('normal');
                $table->boolean('academy_nav_enabled')->default(true);
                $table->boolean('academy_maintenance_mode')->default(false);
            });
            DB::table('core_info')->insert(['id' => 1, 'academy_access_mode' => 'normal']);
            $migration = require database_path('migrations/2026_10_26_000000_create_academy_launch_notices_table.php');
            $migration->up();
        }

        DB::beginTransaction();
        $this->transactionStarted = true;
        $this->assertNotNull(DB::table('core_info')->where('id', 1)->first(), 'The test database must be migrated and seeded.');
        DB::table('core_info')->where('id', 1)->update([
            'academy_access_mode' => 'normal',
            'academy_nav_enabled' => true,
            'academy_maintenance_mode' => false,
        ]);
        foreach ([920001, 920002] as $id) {
            $user = new User([
                'id' => $id, 'fname' => 'Notice', 'lname' => 'Test',
                'email' => "notice{$id}@example.invalid", 'permissions' => 1,
                'rating_id' => 2, 'timezone' => 'UTC',
            ]);
            $user->incrementing = false;
            $user->save();
        }
    }

    protected function tearDown(): void
    {
        try {
            if ($this->transactionStarted) {
                DB::rollBack();
            }
        } finally {
            parent::tearDown();
        }
    }

    public function test_five_displays_then_stops_across_new_requests(): void
    {
        for ($i = 0; $i < 5; $i++) {
            $this->assertTrue((new AcademyLaunchNotice)->record(User::findOrFail(920001), 'show'));
        }
        $this->assertFalse((new AcademyLaunchNotice)->record(User::findOrFail(920001), 'show'));
        $this->assertSame(5, (int) DB::table('academy_launch_notices')->where('user_id', 920001)->value('display_count'));
    }

    public function test_dismissal_persists_without_affecting_another_account(): void
    {
        $service = new AcademyLaunchNotice;
        $this->assertTrue($service->record(User::findOrFail(920001), 'show'));
        $service->record(User::findOrFail(920001), 'dismiss');
        $this->assertFalse((new AcademyLaunchNotice)->record(User::findOrFail(920001), 'show'));
        $this->assertTrue($service->record(User::findOrFail(920002), 'show'));
    }

    public function test_preview_modes_do_not_consume_displays(): void
    {
        foreach (['admin', 'staff'] as $mode) {
            DB::table('core_info')->where('id', 1)->update(['academy_access_mode' => $mode]);
            $this->assertFalse((new AcademyLaunchNotice)->record(User::findOrFail(920001), 'show'));
        }
        $this->assertSame(0, DB::table('academy_launch_notices')->whereIn('user_id', [920001, 920002])->count());
    }

    public function test_maintenance_and_hidden_navigation_do_not_consume_displays(): void
    {
        DB::table('core_info')->where('id', 1)->update(['academy_maintenance_mode' => true]);
        $this->assertFalse((new AcademyLaunchNotice)->record(User::findOrFail(920001), 'show'));
        DB::table('core_info')->where('id', 1)->update(['academy_maintenance_mode' => false, 'academy_nav_enabled' => false]);
        $this->assertFalse((new AcademyLaunchNotice)->record(User::findOrFail(920001), 'show'));
        $this->assertSame(0, DB::table('academy_launch_notices')->whereIn('user_id', [920001, 920002])->count());
    }

    public function test_guests_and_unaccepted_accounts_are_ineligible(): void
    {
        $this->assertFalse(AcademyLaunchNotice::eligible(null));
        DB::table('users')->where('id', 920001)->update(['permissions' => 0]);
        $this->assertFalse((new AcademyLaunchNotice)->record(User::findOrFail(920001), 'show'));
    }

    public function test_missing_migration_fails_closed(): void
    {
        // Simulate a missing migration without dropping a table in MySQL:
        // DDL would implicitly commit and escape the test transaction.
        Schema::shouldReceive('hasTable')->once()->with('academy_launch_notices')->andReturn(false);
        $this->assertFalse((new AcademyLaunchNotice)->record(User::findOrFail(920001), 'show'));
    }

    public function test_authenticated_endpoint_validates_action_and_limits_displays(): void
    {
        $this->actingAs(User::findOrFail(920001));
        $this->postJson('/academy/launch-notice', ['action' => 'invalid'])->assertStatus(422);
        $this->postJson('/academy/launch-notice', ['action' => 'show'])->assertOk()->assertJson(['show' => true]);
        $this->postJson('/academy/launch-notice', ['action' => 'dismiss'])->assertOk()->assertJson(['show' => false]);
        $this->postJson('/academy/launch-notice', ['action' => 'show'])->assertOk()->assertJson(['show' => false]);
    }
}
