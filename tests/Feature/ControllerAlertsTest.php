<?php

namespace Tests\Feature;

use App\Services\ControllerAuthorization;
use App\Services\ControllerOnlineAlerts;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class ControllerAlertsTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        config(['controller-alerts.enabled' => true,
            'controller-alerts.online_webhook' => 'https://discord.com/api/webhooks/111/public-test',
            'controller-alerts.review_webhook' => 'https://discord.com/api/webhooks/222/private-test']);
        Cache::flush();
        Cache::put('vatsim-datafeed-url', 'https://data.vatsim.net/v3/vatsim-data.json', 3600);
        Http::preventStrayRequests();
        Schema::create('roster', function (Blueprint $t) {
            $t->id(); $t->integer('cid'); $t->integer('active')->default(1);
            foreach (['del','gnd','twr','dep','app','ctr'] as $f) $t->integer($f)->default(1);
        });
        (require database_path('migrations/2026_10_30_000002_create_controller_alert_events.php'))->up();
    }

    private function controller(array $extra = []): array
    {
        return array_replace(['cid' => 1234567, 'name' => 'Test @everyone', 'callsign' => 'CYWG_TWR',
            'facility' => 4, 'rating' => 3, 'logon_time' => now()->subMinutes(5)->toIso8601String()], $extra);
    }

    private function feed(array $controllers, ?string $timestamp = null): array
    {
        return ['general' => ['update_timestamp' => $timestamp ?? now()->toIso8601String()], 'controllers' => $controllers];
    }

    public function test_position_and_rating_matrix_with_solo_and_private_review(): void
    {
        $policy = app(ControllerAuthorization::class);
        foreach (ControllerAuthorization::POSITIONS as $suffix => [$field, $label, $rating, $facility]) {
            $controller = $this->controller(['callsign' => 'CYWG_E_'.$suffix, 'rating' => $rating, 'facility' => $facility]);
            $this->assertSame('online', $policy->assess($controller, ['active' => 1, $field => 4])['status']);
            $this->assertSame('review', $policy->assess($controller, null)['status']);
            $this->assertSame('review', $policy->assess($controller, ['active' => 1, $field => 2])['status']);
            $this->assertSame('review', $policy->assess($controller, ['active' => 0, $field => 4])['status']);
            $controller['rating'] = max(2, $rating - 1);
            $this->assertSame('solo', $policy->assess($controller, ['active' => 1, $field => 3])['status']);
            $controller['rating'] = 1;
            $this->assertSame('review', $policy->assess($controller, ['active' => 1, $field => 3])['status']);
        }
        $this->assertSame('review', $policy->assess($this->controller(['callsign' => 'CZWG_CTR','facility' => 6,'rating' => 2]), ['active' => 1,'ctr' => 3])['status']);
        $this->assertSame('review', $policy->assess($this->controller(['rating' => 11]), ['active' => 1,'twr' => 4])['status']);
        foreach (['CYWG_OBS','CYWG_ATIS','CYYZ_TWR','NOTCYWG_TWR'] as $callsign) {
            $this->assertNull($policy->position($this->controller(['callsign' => $callsign])));
        }
        $this->assertNull($policy->position($this->controller(['facility' => 0])));
    }

    public function test_repeat_polls_send_once_and_reviews_never_use_public_webhook(): void
    {
        DB::table('roster')->insert(['cid' => 1234567, 'twr' => 4]);
        $controllers = [$this->controller(), $this->controller(['cid' => 7654321, 'callsign' => 'CYWG_GND', 'facility' => 3, 'rating' => 1])];
        Http::fake(['data.vatsim.net/*' => Http::response($this->feed($controllers)), 'discord.com/*' => Http::response(['id' => '1'])]);
        $this->assertSame(2, app(ControllerOnlineAlerts::class)->run());
        $this->assertSame(0, app(ControllerOnlineAlerts::class)->run());
        $this->assertSame(2, DB::table('controller_alert_events')->count());
        Http::assertSent(fn ($r) => str_contains($r->url(), '/111/') && $r['embeds'][0]['title'] === 'Controller online' && $r['allowed_mentions']['parse'] === []);
        Http::assertSent(fn ($r) => str_contains($r->url(), '/222/') && $r['embeds'][0]['title'] === 'Training / authorization check needed');
        $this->assertCount(2, Http::recorded(fn ($r) => str_contains($r->url(), 'discord.com')));
    }

    public function test_reconnect_and_solo_classification_change_are_announced(): void
    {
        DB::table('roster')->insert(['cid' => 1234567, 'twr' => 2]);
        $controller = $this->controller(['rating' => 2]);
        Http::fake(['data.vatsim.net/*' => Http::sequence()->push($this->feed([$controller]))->push($this->feed([$controller]))
            ->push($this->feed([array_replace($controller, ['logon_time' => now()->subMinute()->toIso8601String()])])),
            'discord.com/*' => Http::response(['id' => '1'])]);
        $this->assertSame(1, app(ControllerOnlineAlerts::class)->run());
        DB::table('roster')->update(['twr' => 3]);
        $this->assertSame(1, app(ControllerOnlineAlerts::class)->run());
        $this->assertSame(1, app(ControllerOnlineAlerts::class)->run());
        Http::assertSent(fn ($r) => str_contains($r->url(), '/111/') && $r['embeds'][0]['title'] === 'Student on solo Tower');
    }

    public function test_stale_or_failed_feed_never_causes_discord_alerts(): void
    {
        Http::fake(['data.vatsim.net/*' => Http::sequence()->push($this->feed([$this->controller()], now()->subHour()->toIso8601String()))->push([], 503)]);
        $this->artisan('controllers:announce-online')->assertExitCode(1);
        $this->artisan('controllers:announce-online')->assertExitCode(1);
        Http::assertNotSent(fn ($r) => str_contains($r->url(), 'discord.com'));
        $this->assertSame(0, DB::table('controller_alert_events')->count());
    }

    public function test_uncertain_discord_delivery_is_not_repeated(): void
    {
        Http::fake(['data.vatsim.net/*' => Http::response($this->feed([$this->controller()])), 'discord.com/*' => Http::response([], 500)]);
        app(ControllerOnlineAlerts::class)->run(); app(ControllerOnlineAlerts::class)->run();
        $this->assertCount(1, Http::recorded(fn ($r) => str_contains($r->url(), 'discord.com')));
        $this->assertDatabaseHas('controller_alert_events', ['delivery_status' => 'unconfirmed']);
    }

    public function test_disabled_or_missing_private_webhook_never_falls_back_publicly(): void
    {
        config(['controller-alerts.enabled' => false]);
        $this->assertSame(0, app(ControllerOnlineAlerts::class)->run());
        Http::assertNothingSent();
        config(['controller-alerts.enabled' => true, 'controller-alerts.review_webhook' => null]);
        Http::fake(['data.vatsim.net/*' => Http::response($this->feed([$this->controller()]))]);
        $this->assertSame(0, app(ControllerOnlineAlerts::class)->run());
        Http::assertNotSent(fn ($r) => str_contains($r->url(), 'discord.com'));
    }
}
