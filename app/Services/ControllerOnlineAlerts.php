<?php

namespace App\Services;

use App\Classes\VatsimHelper;
use App\Models\AtcTraining\RosterMember;
use Carbon\CarbonImmutable;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class ControllerOnlineAlerts
{
    public function run(): int
    {
        if (!config('controller-alerts.enabled')) {
            return 0;
        }
        $lock = Cache::lock('controller-online-alerts:poll', 600);
        if (!$lock->get()) {
            return 0;
        }
        try {
            $feed = Http::connectTimeout(5)->timeout(15)->get(VatsimHelper::getDatafeedUrl())->throw()->json();
            if (!is_array($feed) || !is_array($feed['controllers'] ?? null)
                || empty($feed['general']['update_timestamp'])) {
                throw new \RuntimeException('Incomplete feed.');
            }
            $observed = CarbonImmutable::parse($feed['general']['update_timestamp']);
            if ($observed->lt(now()->subSeconds(config('controller-alerts.max_feed_age_seconds')))
                || $observed->gt(now()->addMinute())) {
                throw new \RuntimeException('Stale feed.');
            }
            $policy = app(ControllerAuthorization::class);
            $controllers = collect($feed['controllers'])->filter(fn ($c) => is_array($c) && $policy->position($c));
            $members = RosterMember::whereIn('cid', $controllers->pluck('cid'))->get()->groupBy('cid');
            $sent = 0;
            $attempted = 0;
            foreach ($controllers as $controller) {
                $cid = filter_var($controller['cid'] ?? null, FILTER_VALIDATE_INT);
                if (!$cid || $cid < 1 || empty($controller['logon_time'])) {
                    continue;
                }
                try {
                    $logon = CarbonImmutable::parse($controller['logon_time']);
                } catch (\Throwable $e) {
                    continue;
                }
                if ($logon->gt($observed->addMinute())) {
                    continue;
                }
                $matches = $members->get($cid, collect());
                $assessment = $policy->assess($controller, $matches->count() === 1 ? $matches->first()->toArray() : null);
                if ($matches->count() > 1) {
                    $assessment['reason'] = 'Multiple roster records match this CID; staff should resolve the ambiguity.';
                }
                $status = $assessment['status'];
                $url = config('controller-alerts.'.($status === 'review' ? 'review_webhook' : 'online_webhook'));
                if (!is_string($url) || !preg_match('~^https://discord\.com/api/webhooks/[0-9]+/[A-Za-z0-9_-]+$~D', $url)) {
                    continue;
                }
                // Durable claim before POST: never repeat an uncertain delivery.
                // A genuine reconnect has a new logon time. Classification
                // changes can generate a new event during the same connection.
                $key = hash('sha256', $cid.'|'.$assessment['callsign'].'|'.$logon->timestamp.'|'.$status);
                if (!DB::table('controller_alert_events')->insertOrIgnore([
                    'event_key' => $key, 'cid' => $cid, 'callsign' => $assessment['callsign'],
                    'classification' => $status, 'delivery_status' => 'attempting', 'created_at' => now(),
                ])) {
                    continue;
                }
                if (++$attempted > 40) {
                    DB::table('controller_alert_events')->where('event_key', $key)->delete();
                    break;
                }
                $title = $status === 'review' ? 'Training / authorization check needed'
                    : ($status === 'solo' ? 'Student on solo '.$assessment['label'] : 'Controller online');
                try {
                    $response = Http::connectTimeout(5)->timeout(10)->withOptions(['allow_redirects' => false])
                        ->post($url.'?wait=true', [
                            'embeds' => [[
                                'title' => $title,
                                'description' => $assessment['callsign'].' is online.',
                                'color' => $status === 'review' ? 15105570 : ($status === 'solo' ? 3447003 : 3066993),
                                'fields' => [
                                    ['name' => 'Controller', 'value' => mb_substr(preg_replace('/[\\\\*_`~>|]/', '', (string) ($controller['name'] ?? 'Unknown')), 0, 100) ?: 'Unknown'],
                                    ['name' => 'CID', 'value' => (string) $cid, 'inline' => true],
                                    ['name' => 'Position', 'value' => $assessment['label'], 'inline' => true],
                                    ['name' => 'Assessment', 'value' => $assessment['reason']],
                                    ['name' => 'Roster', 'value' => route('roster.public')],
                                ],
                                'timestamp' => $observed->toIso8601String(),
                                'footer' => ['text' => $status === 'review' ? 'Staff review only. This is not an enforcement decision.' : 'Based on the live feed and current Winnipeg roster.'],
                            ]],
                            'allowed_mentions' => ['parse' => []],
                        ]);
                    $delivery = $response->successful() ? 'sent' : 'unconfirmed';
                } catch (\Throwable $e) {
                    $delivery = 'unconfirmed';
                }
                DB::table('controller_alert_events')->where('event_key', $key)->update(['delivery_status' => $delivery]);
                if ($delivery === 'sent') {
                    $sent++;
                } else {
                    Log::warning('Controller alert delivery unconfirmed; inspect Discord before reposting.', ['cid' => $cid, 'callsign' => $assessment['callsign']]);
                }
            }
            return $sent;
        } finally {
            $lock->release();
        }
    }
}
