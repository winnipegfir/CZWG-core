<?php

namespace Tests\Feature;

use App\Models\Users\User;
use App\Services\SectorFiles\GitHubReleases;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class SectorFilesTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        config(['app.key' => 'base64:'.base64_encode(str_repeat('x', 32)),
            'sector-files.owner' => 'example', 'sector-files.repository' => 'sectors',
            'sector-files.token' => 'test-only-token', 'sector-files.tag_prefix' => 'sector-',
            'sector-files.discord_webhook' => null,
            'sector-files.discord_thread_id' => null,
            'sector-files.scheduling_enabled' => false,
        ]);
        Cache::flush();
        Http::preventStrayRequests();
        // Existing global layouts require the site's unrelated database settings.
        // Render feature views with a minimal layout; do not migrate a real database.
        app('view')->getFinder()->prependLocation(__DIR__.'/../Fixtures/sector-files');
    }

    private function release(int $id = 1, array $changes = []): array
    {
        return array_replace([
            'id' => $id, 'tag_name' => 'sector-2026.'.$id, 'name' => 'Release '.$id,
            'body' => 'Notes '.$id, 'draft' => false, 'prerelease' => false,
            'published_at' => '2026-10-'.str_pad($id, 2, '0', STR_PAD_LEFT).'T12:00:00Z',
            'created_at' => '2026-10-01T12:00:00Z',
            'assets' => [['id' => $id, 'name' => 'winnipeg-full-install-2026.'.$id.'.zip', 'state' => 'uploaded', 'size' => 100,
                'browser_download_url' => 'https://github.com/example/sectors/releases/download/sector-'.$id.'/full.zip'],
                ['id' => $id + 100, 'name' => 'winnipeg-nav-data-2026.'.$id.'.zip', 'state' => 'uploaded', 'size' => 80,
                'browser_download_url' => 'https://github.com/example/sectors/releases/download/sector-'.$id.'/nav.zip']],
        ], $changes);
    }

    private function staff(int $permission = 4): self
    {
        $user = new User();
        $user->id = 99;
        $user->permissions = $permission;
        $this->actingAs($user);
        return $this;
    }

    public function test_public_page_orders_releases_and_hides_drafts_prereleases_and_unrelated_tags(): void
    {
        Http::fake(['https://api.github.com/repos/example/sectors' => Http::response(['private' => false]), 'api.github.com/*' => Http::response([
            $this->release(1), $this->release(4, ['draft' => true]),
            $this->release(3, ['tag_name' => 'website-v3']), $this->release(2),
            $this->release(5, ['prerelease' => true]), $this->release(6, ['assets' => []]),
        ])]);
        $this->get(route('sector-files.index'))->assertOk()->assertSeeInOrder(['Release 2', 'Archived releases', 'Release 1'])
            ->assertDontSee('Release 4')->assertDontSee('Release 3')->assertDontSee('Release 5')->assertDontSee('Release 6');
    }

    public function test_all_staff_routes_require_staff_permission(): void
    {
        $routes = [
            ['GET', 'sector-files.manage', []], ['POST', 'sector-files.store', []],
            ['POST', 'sector-files.publish', [1]], ['PATCH', 'sector-files.schedule', [1]], ['PATCH', 'sector-files.update', [1]],
            ['PATCH', 'sector-files.archive', [1]], ['DELETE', 'sector-files.destroy', [1]],
        ];
        foreach ($routes as [$method, $route, $parameters]) {
            $this->call($method, route($route, $parameters))->assertRedirect(route('auth.connect.login'));
        }
        foreach ([0, 1, 2, 3] as $permission) {
            $this->staff($permission);
            foreach ($routes as [$method, $route, $parameters]) {
                $this->call($method, route($route, $parameters))->assertForbidden();
            }
        }
        Http::assertNothingSent();
    }

    public function test_staff_and_admin_can_manage_releases(): void
    {
        Http::fake(['api.github.com/*' => Http::response([])]);
        foreach ([4, 5] as $permission) {
            $this->staff($permission)->get(route('sector-files.manage'))->assertOk()->assertSee('Upload and publish');
        }
    }

    public function test_publish_uploads_draft_before_making_it_public(): void
    {
        Http::fake([
            'https://api.github.com/repos/example/sectors' => Http::response(['private' => false]),
            'https://api.github.com/repos/example/sectors/releases' => Http::response($this->release(1, ['draft' => true]), 201),
            'https://uploads.github.com/*' => Http::response(['state' => 'uploaded'], 201),
            'https://api.github.com/repos/example/sectors/releases/1' => Http::sequence()
                ->push($this->release(1, ['draft' => true]))->push($this->release()),
        ]);
        $this->staff()->post(route('sector-files.store'), [
            'title' => 'New release', 'version' => '2026.1', 'notes' => 'New notes',
            'full_zip' => UploadedFile::fake()->create('full.zip', 10, 'application/zip'),
            'nav_zip' => UploadedFile::fake()->create('nav.zip', 10, 'application/zip'),
        ])->assertRedirect(route('sector-files.manage'))->assertSessionHasNoErrors()->assertSessionHas('sector_status', 'Release published successfully.');
        $requests = Http::recorded()->pluck(0)->all();
        $this->assertTrue($requests[1]['draft']);
        $this->assertStringStartsWith('https://uploads.github.com/', $requests[2]->url());
        $this->assertSame('application/zip', $requests[2]->header('Content-Type')[0]);
        $this->assertStringContainsString('winnipeg-full-install-2026.1.zip', $requests[2]->url());
        $this->assertStringContainsString('winnipeg-nav-data-2026.1.zip', $requests[3]->url());
        $this->assertFalse($requests[5]['draft']);
    }

    public function test_upload_failure_never_publishes(): void
    {
        Http::fake([
            'https://api.github.com/repos/example/sectors' => Http::response(['private' => false]),
            'https://api.github.com/repos/example/sectors/releases' => Http::response($this->release(1, ['draft' => true]), 201),
            'https://uploads.github.com/*' => Http::response([], 502),
        ]);
        $this->staff()->post(route('sector-files.store'), [
            'title' => 'New', 'version' => '2026.1', 'full_zip' => UploadedFile::fake()->create('full.zip', 10, 'application/zip'),
            'nav_zip' => UploadedFile::fake()->create('nav.zip', 10, 'application/zip'),
        ])->assertSessionHasErrors('release');
        Http::assertNotSent(fn ($r) => $r->method() === 'PATCH');
    }

    public function test_invalid_zip_and_version_are_rejected_before_network_calls(): void
    {
        $this->staff()->post(route('sector-files.store'), [
            'title' => 'New', 'version' => '../invalid', 'full_zip' => UploadedFile::fake()->create('sector.txt', 10, 'text/plain'),
        ])->assertSessionHasErrors(['version', 'full_zip', 'nav_zip']);
        Http::assertNothingSent();
    }

    public function test_archive_preserves_notes_and_invalidates_public_cache(): void
    {
        $github = app(GitHubReleases::class);
        Cache::put($github->cacheKey(), ['old'], 60);
        Http::fake(['api.github.com/*' => Http::response($this->release())]);
        $this->staff()->patch(route('sector-files.archive', 1), ['archived' => 1])->assertSessionHasNoErrors();
        Http::assertSent(fn ($r) => $r->method() === 'PATCH' && $r['body'] === "Notes 1\n\n".GitHubReleases::ARCHIVED);
        $this->assertNull(Cache::get($github->cacheKey()));
    }

    public function test_unrelated_release_cannot_be_deleted(): void
    {
        Http::fake(['api.github.com/*' => Http::response($this->release(1, ['tag_name' => 'website-v1']))]);
        $this->staff()->delete(route('sector-files.destroy', 1), ['confirm_delete' => 1])->assertNotFound();
        Http::assertNotSent(fn ($r) => $r->method() === 'DELETE');
    }

    public function test_delete_requires_confirmation_and_retains_git_tag(): void
    {
        Http::fake(['api.github.com/*' => Http::response($this->release())]);
        $this->staff()->delete(route('sector-files.destroy', 1))->assertSessionHasErrors('confirm_delete');
        Http::assertNothingSent();
        $this->delete(route('sector-files.destroy', 1), ['confirm_delete' => 1])->assertSessionHasNoErrors();
        Http::assertSent(fn ($r) => $r->method() === 'DELETE' && str_ends_with($r->url(), '/releases/1'));
        Http::assertNotSent(fn ($r) => str_contains($r->url(), '/git/refs'));
    }

    public function test_public_notes_are_escaped_and_archived_release_is_not_latest(): void
    {
        Http::fake(['https://api.github.com/repos/example/sectors' => Http::response(['private' => false]), 'api.github.com/*' => Http::response([
            $this->release(1), $this->release(2, ['body' => '<script>alert(1)</script>'.GitHubReleases::ARCHIVED]),
        ])]);
        $this->get(route('sector-files.index'))->assertOk()->assertSeeInOrder(['Release 1', 'Archived releases', 'Release 2'])
            ->assertDontSee('<script>alert(1)</script>', false)->assertDontSee(GitHubReleases::ARCHIVED);
    }

    public function test_github_failure_returns_safe_public_unavailable_page(): void
    {
        Http::fake(['api.github.com/*' => Http::response(['message' => 'sensitive test-only-token'], 403)]);
        $this->get(route('sector-files.index'))->assertStatus(503)->assertSee('temporarily unavailable')->assertDontSee('test-only-token');
    }

    public function test_discord_failure_does_not_undo_publish(): void
    {
        config(['sector-files.discord_webhook' => 'https://discord.com/api/webhooks/123/test-token']);
        Http::fake([
            'api.github.com/*' => Http::sequence()->push($this->release(1, ['draft' => true]))->push($this->release()),
            'discord.com/*' => Http::response([], 500),
        ]);
        $this->staff()->post(route('sector-files.publish', 1), ['announce' => 1])
            ->assertSessionHasNoErrors()->assertSessionHas('sector_status', fn ($message) => str_contains($message, 'Release published.'));
        Http::assertSent(fn ($r) => str_contains($r->url(), 'discord.com') && $r['allowed_mentions']['parse'] === []);
    }

    public function test_private_repository_is_rejected_before_upload(): void
    {
        Http::fake(['api.github.com/*' => Http::response(['private' => true])]);
        $this->staff()->post(route('sector-files.store'), [
            'title' => 'New', 'version' => '2026.1', 'full_zip' => UploadedFile::fake()->create('full.zip', 10, 'application/zip'),
            'nav_zip' => UploadedFile::fake()->create('nav.zip', 10, 'application/zip'),
        ])->assertSessionHasErrors('release');
        Http::assertNotSent(fn ($r) => $r->method() === 'POST');
    }

    public function test_private_repository_metadata_is_not_shown_publicly(): void
    {
        Http::fake(['api.github.com/*' => Http::response(['private' => true])]);
        $this->get(route('sector-files.index'))->assertStatus(503);
        Http::assertSentCount(1);
    }

    public function test_history_reads_additional_github_pages(): void
    {
        Http::fake(['api.github.com/*' => Http::sequence()
            ->push(array_fill(0, 100, $this->release(1, ['tag_name' => 'website-v1'])))
            ->push([$this->release(2)])]);
        $releases = app(GitHubReleases::class)->all();
        $this->assertCount(1, $releases);
        $this->assertSame(2, $releases[0]['id']);
        Http::assertSent(fn ($r) => str_contains($r->url(), 'page=2'));
    }

    public function test_draft_without_zip_cannot_be_published(): void
    {
        Http::fake(['api.github.com/*' => Http::response($this->release(1, ['draft' => true, 'assets' => []]))]);
        $this->staff()->post(route('sector-files.publish', 1))->assertSessionHasErrors('release');
        Http::assertNotSent(fn ($r) => $r->method() === 'PATCH');
    }

    public function test_edit_preserves_archive_status_and_restore_removes_it(): void
    {
        Http::fake(['api.github.com/*' => Http::response($this->release(1, ['body' => 'Old notes'.GitHubReleases::ARCHIVED]))]);
        $this->staff()->patch(route('sector-files.update', 1), ['title' => 'Corrected', 'notes' => 'Corrected notes'])
            ->assertSessionHasNoErrors();
        Http::assertSent(fn ($r) => $r->method() === 'PATCH' && $r['name'] === 'Corrected'
            && $r['body'] === "Corrected notes\n\n".GitHubReleases::ARCHIVED);
        $this->patch(route('sector-files.archive', 1), ['archived' => 0])->assertSessionHasNoErrors();
        Http::assertSent(fn ($r) => $r->method() === 'PATCH' && $r['body'] === 'Old notes');
    }

    public function test_concurrent_writes_do_not_reach_github(): void
    {
        $lock = Cache::lock(app(GitHubReleases::class)->cacheKey().':write', 600);
        $lock->get();
        try {
            $this->staff()->delete(route('sector-files.destroy', 1), ['confirm_delete' => 1])->assertSessionHasErrors('release');
            Http::assertNothingSent();
        } finally {
            $lock->release();
        }
    }

    public function test_second_upload_failure_keeps_release_unpublished_and_sends_no_announcement(): void
    {
        config(['sector-files.discord_webhook' => 'https://discord.com/api/webhooks/123/test-token']);
        Http::fake([
            'https://api.github.com/repos/example/sectors' => Http::response(['private' => false]),
            'https://api.github.com/repos/example/sectors/releases' => Http::response($this->release(1, ['draft' => true]), 201),
            'https://uploads.github.com/*' => Http::sequence()->push(['state' => 'uploaded'], 201)->push([], 502),
        ]);
        $this->staff()->post(route('sector-files.store'), [
            'title' => 'New', 'version' => '2026.1', 'announce' => 1,
            'full_zip' => UploadedFile::fake()->create('full.zip', 10, 'application/zip'),
            'nav_zip' => UploadedFile::fake()->create('nav.zip', 10, 'application/zip'),
        ])->assertSessionHasErrors('release');
        Http::assertSentCount(4);
        Http::assertNotSent(fn ($r) => $r->method() === 'PATCH' || str_contains($r->url(), 'discord.com'));
    }

    public function test_incomplete_pair_is_neither_publishable_nor_public(): void
    {
        $partial = $this->release(2);
        $partial['assets'] = [$partial['assets'][0]];
        Http::fake([
            'https://api.github.com/repos/example/sectors' => Http::response(['private' => false]),
            'https://api.github.com/repos/example/sectors/releases?*' => Http::response([$partial, $this->release(1)]),
            'https://api.github.com/repos/example/sectors/releases/2' => Http::response(array_replace($partial, ['draft' => true])),
        ]);
        $this->get(route('sector-files.index'))->assertOk()->assertSee('Release 1')->assertDontSee('Release 2');
        $this->staff()->post(route('sector-files.publish', 2))->assertSessionHasErrors('release');
        Http::assertNotSent(fn ($r) => $r->method() === 'PATCH');
    }

    public function test_newest_pair_has_two_downloads_and_legacy_download_remains_in_history(): void
    {
        $legacy = $this->release(1);
        $legacy['assets'] = [array_replace($legacy['assets'][0], ['name' => 'old-sector.zip'])];
        Http::fake([
            'https://api.github.com/repos/example/sectors' => Http::response(['private' => false]),
            'api.github.com/*' => Http::response([$legacy, $this->release(2)]),
        ]);
        $this->get(route('sector-files.index'))->assertOk()
            ->assertSeeInOrder(['CURRENT RELEASE', 'Release 2', 'Download Full Install', 'Download Nav Data', 'Archived releases', 'Release 1', 'Download Legacy ZIP'])
            ->assertSee('https://github.com/example/sectors/releases/download/sector-2/full.zip')
            ->assertSee('https://github.com/example/sectors/releases/download/sector-2/nav.zip');
    }

    public function test_discord_includes_bounded_notes_and_public_download_url_without_mentions(): void
    {
        config(['sector-files.discord_webhook' => 'https://discord.com/api/webhooks/123/test-token',
            'sector-files.public_url' => 'https://winnipegfir.ca/atc/sector-files']);
        Http::fake(['discord.com/*' => Http::response(['id' => '123'], 200)]);
        $release = $this->release();
        $release['version'] = '2026.1';
        $release['notes'] = '@everyone '.str_repeat('Navigation update. ', 400);
        $this->assertTrue(app(\App\Services\SectorFiles\DiscordAnnouncement::class)->send($release));
        Http::assertSent(function ($r) {
            $embed = $r['embeds'][0];
            return str_ends_with($r->url(), '?wait=true') && $r['allowed_mentions']['parse'] === []
                && $embed['url'] === 'https://winnipegfir.ca/atc/sector-files'
                && str_starts_with($embed['description'], '@everyone Navigation update.')
                && mb_strlen($embed['description']) <= 4096
                && str_contains($embed['description'], 'complete release notes');
        });
    }

    public function test_editing_release_does_not_send_discord_message(): void
    {
        config(['sector-files.discord_webhook' => 'https://discord.com/api/webhooks/123/test-token']);
        Http::fake(['api.github.com/*' => Http::response($this->release())]);
        $this->staff()->patch(route('sector-files.update', 1), ['title' => 'Updated', 'notes' => 'Revised notes'])
            ->assertSessionHasNoErrors();
        Http::assertNotSent(fn ($r) => str_contains($r->url(), 'discord.com'));
    }
    public function test_discord_targets_existing_update_hub_thread(): void
    {
        config(['sector-files.discord_webhook' => 'https://discord.com/api/webhooks/123/test-token',
            'sector-files.discord_thread_id' => '1468760234276163664',
            'sector-files.public_url' => 'https://winnipegfir.ca/atc/sector-files']);
        Http::fake(['discord.com/*' => Http::response(['id' => '123'], 200)]);
        $this->assertTrue(app(\App\Services\SectorFiles\DiscordAnnouncement::class)->send([
            'name' => 'Test', 'version' => 'test-1', 'notes' => 'Notes',
        ]));
        Http::assertSent(fn ($r) => $r->url() === 'https://discord.com/api/webhooks/123/test-token?wait=true&thread_id=1468760234276163664');
    }

    public function test_invalid_discord_thread_is_not_sent(): void
    {
        config(['sector-files.discord_webhook' => 'https://discord.com/api/webhooks/123/test-token',
            'sector-files.discord_thread_id' => 'invalid&thread_id=123']);
        Http::fake();
        $this->assertFalse(app(\App\Services\SectorFiles\DiscordAnnouncement::class)->send([]));
        Http::assertNothingSent();
    }

    private function scheduledBody(int $at, bool $announce = false): string
    {
        return 'Notes 1'."\n\n".GitHubReleases::SCHEDULE.base64_encode(json_encode(['at' => $at, 'announce' => $announce])).' -->';
    }

    public function test_scheduled_upload_remains_a_complete_hidden_draft_without_announcement(): void
    {
        config(['sector-files.scheduling_enabled' => true]);
        $at = now('UTC')->addDay()->startOfMinute();
        Http::fake([
            'https://api.github.com/repos/example/sectors' => Http::response(['private' => false]),
            'https://api.github.com/repos/example/sectors/releases' => Http::response($this->release(1, ['draft' => true]), 201),
            'https://uploads.github.com/*' => Http::response(['state' => 'uploaded'], 201),
            'https://api.github.com/repos/example/sectors/releases/1' => Http::sequence()
                ->push($this->release(1, ['draft' => true]))
                ->push($this->release(1, ['draft' => true, 'body' => $this->scheduledBody($at->timestamp, true)])),
        ]);
        $this->staff()->post(route('sector-files.store'), [
            'title' => 'New', 'version' => '2026.1', 'publish_at' => $at->format('Y-m-d\TH:i'), 'announce' => 1,
            'full_zip' => UploadedFile::fake()->create('full.zip', 10, 'application/zip'),
            'nav_zip' => UploadedFile::fake()->create('nav.zip', 10, 'application/zip'),
        ])->assertSessionHasNoErrors();
        Http::assertSent(fn ($r) => $r->method() === 'PATCH' && $r['body'] === $this->scheduledBody($at->timestamp, true));
        Http::assertNotSent(fn ($r) => isset($r['draft']) && $r['draft'] === false || str_contains($r->url(), 'discord.com'));
    }

    public function test_schedule_rejects_past_invalid_and_disabled_dates_before_network(): void
    {
        $this->staff();
        foreach ([false, true] as $enabled) {
            config(['sector-files.scheduling_enabled' => $enabled]);
            foreach (['2020-01-01T09:00', 'not-a-date', '2099-02-30T09:00'] as $at) {
                $this->patch(route('sector-files.schedule', 1), ['publish_at' => $at])->assertSessionHasErrors('publish_at');
            }
        }
        config(['sector-files.scheduling_enabled' => false]);
        $this->patch(route('sector-files.schedule', 1), ['publish_at' => '2099-01-01T09:00'])->assertSessionHasErrors('publish_at');
        Http::assertNothingSent();
    }

    public function test_schedule_can_be_cancelled_even_when_scheduling_is_disabled(): void
    {
        Http::fake(['api.github.com/*' => Http::response($this->release(1, ['draft' => true, 'body' => $this->scheduledBody(time() + 3600)]))]);
        $this->staff()->patch(route('sector-files.schedule', 1), [])->assertSessionHasNoErrors();
        Http::assertSent(fn ($r) => $r->method() === 'PATCH' && $r['body'] === 'Notes 1');
    }

    public function test_edit_preserves_schedule_and_hides_marker_from_notes(): void
    {
        $body = $this->scheduledBody(time() + 3600, true);
        Http::fake(['api.github.com/*' => Http::response($this->release(1, ['draft' => true, 'body' => $body]))]);
        $release = app(GitHubReleases::class)->get(1);
        $this->assertSame('Notes 1', $release['notes']);
        $this->staff()->patch(route('sector-files.update', 1), ['title' => 'Edited', 'notes' => 'New notes'])->assertSessionHasNoErrors();
        Http::assertSent(fn ($r) => $r->method() === 'PATCH' && $r['body'] === str_replace('Notes 1', 'New notes', $body));
    }

    public function test_scheduler_publishes_due_only_and_does_not_repeat_discord(): void
    {
        config(['sector-files.scheduling_enabled' => true,
            'sector-files.discord_webhook' => 'https://discord.com/api/webhooks/123/test-token',
            'sector-files.public_url' => 'https://winnipegfir.ca/atc/sector-files']);
        $due = $this->release(1, ['draft' => true, 'body' => $this->scheduledBody(time() - 60, true)]);
        $future = $this->release(2, ['draft' => true, 'body' => $this->scheduledBody(time() + 3600)]);
        $incomplete = $this->release(3, ['draft' => true, 'body' => $this->scheduledBody(time() - 60), 'assets' => []]);
        Http::fake([
            'https://api.github.com/repos/example/sectors/releases?*' => Http::sequence()->push([$due, $future, $incomplete])->push([$this->release(), $future, $incomplete]),
            'https://api.github.com/repos/example/sectors/releases/1' => Http::sequence()->push($due)->push($this->release()),
            'discord.com/*' => Http::response([], 200),
        ]);
        $this->artisan('sector-files:publish-due')->assertExitCode(0);
        $this->artisan('sector-files:publish-due')->assertExitCode(0);
        $this->assertCount(1, Http::recorded(fn ($r) => str_contains($r->url(), 'discord.com')));
        Http::assertSent(fn ($r) => $r->method() === 'PATCH' && $r['draft'] === false && $r['body'] === 'Notes 1');
        Http::assertNotSent(fn ($r) => str_ends_with($r->url(), '/releases/2') || str_ends_with($r->url(), '/releases/3'));
    }

    public function test_scheduler_respects_disabled_setting_and_staff_lock(): void
    {
        Http::fake();
        $this->artisan('sector-files:publish-due')->assertExitCode(0);
        config(['sector-files.scheduling_enabled' => true]);
        $lock = Cache::lock(app(GitHubReleases::class)->cacheKey().':write', 600);
        $lock->get();
        try {
            $this->artisan('sector-files:publish-due')->assertExitCode(0);
            Http::assertNothingSent();
        } finally {
            $lock->release();
        }
    }

    public function test_schedule_uses_utc_even_if_application_timezone_differs(): void
    {
        config(['sector-files.scheduling_enabled' => true, 'app.timezone' => 'America/Winnipeg']);
        Http::fake(['api.github.com/*' => Http::response($this->release(1, ['draft' => true]))]);
        $this->staff()->patch(route('sector-files.schedule', 1), ['publish_at' => '2099-01-01T09:00'])->assertSessionHasNoErrors();
        $at = \Carbon\CarbonImmutable::parse('2099-01-01T09:00:00Z')->timestamp;
        Http::assertSent(fn ($r) => $r->method() === 'PATCH' && $r['body'] === $this->scheduledBody($at));
    }

    public function test_scheduled_draft_is_visible_to_staff_but_not_public(): void
    {
        config(['sector-files.scheduling_enabled' => true]);
        $draft = $this->release(1, ['draft' => true, 'body' => $this->scheduledBody(time() + 3600, true)]);
        Http::fake([
            'https://api.github.com/repos/example/sectors' => Http::response(['private' => false]),
            'https://api.github.com/repos/example/sectors/releases?*' => Http::response([$draft]),
        ]);
        $this->get(route('sector-files.index'))->assertOk()->assertDontSee('Release 1');
        $this->staff()->get(route('sector-files.manage'))->assertOk()->assertSee('Scheduled:')->assertSee('Cancel schedule and keep draft')->assertDontSee(GitHubReleases::SCHEDULE);
    }

    public function test_scheduled_publish_failure_does_not_announce_and_releases_lock(): void
    {
        config(['sector-files.scheduling_enabled' => true]);
        $due = $this->release(1, ['draft' => true, 'body' => $this->scheduledBody(time() - 60, true)]);
        Http::fake([
            'https://api.github.com/repos/example/sectors/releases?*' => Http::response([$due]),
            'https://api.github.com/repos/example/sectors/releases/1' => Http::sequence()->push($due)->push([], 500),
        ]);
        $this->artisan('sector-files:publish-due')->assertExitCode(1);
        Http::assertNotSent(fn ($r) => str_contains($r->url(), 'discord.com'));
        $lock = Cache::lock(app(GitHubReleases::class)->cacheKey().':write', 600);
        $this->assertTrue($lock->get());
        $lock->release();
    }

    public function test_scheduled_discord_failure_is_not_retried_after_publication(): void
    {
        config(['sector-files.scheduling_enabled' => true,
            'sector-files.discord_webhook' => 'https://discord.com/api/webhooks/123/test-token']);
        $due = $this->release(1, ['draft' => true, 'body' => $this->scheduledBody(time() - 60, true)]);
        Http::fake([
            'https://api.github.com/repos/example/sectors/releases?*' => Http::sequence()->push([$due])->push([$this->release()]),
            'https://api.github.com/repos/example/sectors/releases/1' => Http::sequence()->push($due)->push($this->release()),
            'discord.com/*' => Http::response([], 500),
        ]);
        $this->artisan('sector-files:publish-due')->assertExitCode(1);
        $this->artisan('sector-files:publish-due')->assertExitCode(0);
        $this->assertCount(1, Http::recorded(fn ($r) => str_contains($r->url(), 'discord.com')));
    }
}
