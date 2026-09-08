<?php

namespace App\Services;

use App\Classes\VatsimRating;
use App\Models\Academy\Course;
use App\Models\Academy\Enrollment;
use App\Models\Academy\VatcanMember;
use App\Models\Academy\VatcanSyncRun;
use App\Models\Users\User;
use Illuminate\Support\Facades\DB;

class AcademyVatcanSyncService
{
    private const RATING_COURSES = [
        1 => ['introduction-to-air-traffic-services', 'introduction-to-aviation', 'software-setup'],
        2 => ['clearance-delivery', 'ground'],
        3 => ['tower', 'advanced-tower'],
        4 => ['introduction-to-radar', 'departure', 'arrival', 'terminal'],
        5 => ['center'],
    ];

    public function entitlementSlugsForRating(?int $ratingId): array
    {
        if ($ratingId === null || $ratingId < 1) {
            return [];
        }

        $slugs = [];
        foreach (self::RATING_COURSES as $minimum => $courses) {
            if (min($ratingId, 5) >= $minimum) {
                $slugs = array_merge($slugs, $courses);
            }
        }

        return array_values(array_unique($slugs));
    }

    public function visitorEntitlementSlugsForRating(?int $ratingId, array $visitorCourseSlugs): array
    {
        // Winnipeg visiting controllers must hold S3 (VATSIM rating ID 4) or higher.
        if ($ratingId === null || $ratingId < 4) {
            return [];
        }

        return array_values(array_unique(array_merge(
            $this->entitlementSlugsForRating($ratingId),
            $visitorCourseSlugs
        )));
    }

    public function ratingLabel(?int $ratingId): string
    {
        if ($ratingId === null) {
            return 'Unknown';
        }

        try {
            return VatsimRating::from($ratingId)->getShortName();
        } catch (\ValueError $e) {
            return 'R'.$ratingId;
        }
    }

    public function sync(?int $initiatedBy = null): VatcanSyncRun
    {
        $vatcan = new VatcanService;
        $run = VatcanSyncRun::create(['initiated_by' => $initiatedBy, 'status' => 'running']);

        if (! $vatcan->canSyncRoster()) {
            $run->update(['status' => 'error', 'message' => 'No VATCAN roster source is configured on this server.']);
            return $run->fresh();
        }

        $result = $vatcan->getRoster();
        if (($result['status'] ?? 'error') !== 'ok') {
            $run->update(['status' => 'error', 'message' => $result['message'] ?? 'VATCAN roster request failed.']);
            return $run->fresh();
        }

        $data = $result['data'] ?? [];
        $controllers = collect($data['controllers'] ?? [])->values();
        $homeCids = $controllers->pluck('cid')->map(fn ($cid) => (int) $cid)->all();
        // Home membership wins if a stale VATCAN page happens to list a CID twice.
        $visitors = collect($data['visitors'] ?? [])->reject(function ($member) use ($homeCids) {
            return in_array((int) ($member['cid'] ?? 0), $homeCids, true);
        })->values();

        $minimum = max(1, (int) config('vatcan.public_roster_min_members', 5));
        $validControllers = $controllers->filter(function ($member) {
            $cid = (int) ($member['cid'] ?? 0);
            $rating = isset($member['rating']) ? (int) $member['rating'] : 0;
            return $cid > 0 && $rating >= 1 && $rating <= 12;
        });

        if ($controllers->count() < $minimum || $validControllers->count() !== $controllers->count()) {
            $run->update(['status' => 'error', 'message' => 'VATCAN roster safety check failed. No Academy memberships or enrollments were changed.']);
            return $run->fresh();
        }

        $previousCount = VatcanMember::where('active_home_member', true)->count();
        $maximumDrop = min(90, max(0, (int) config('vatcan.public_roster_max_drop_percent', 25)));
        $minimumComparedToSnapshot = (int) ceil($previousCount * ((100 - $maximumDrop) / 100));
        if ($previousCount >= $minimum && $controllers->count() < $minimumComparedToSnapshot) {
            $run->update([
                'status' => 'error',
                'message' => sprintf(
                    'VATCAN roster safety check failed: the roster fell from %d to %d home controllers. Current access was left unchanged for administrator review.',
                    $previousCount,
                    $controllers->count()
                ),
            ]);
            return $run->fresh();
        }

        $now = now();
        $stats = [
            'controllers_found' => $controllers->count(),
            // Kept in the legacy column so this release needs only one safe schema extension.
            'visitors_ignored' => $visitors->count(),
            'users_matched' => 0,
            'pending_cids' => 0,
            'enrollments_activated' => 0,
            'enrollments_deactivated' => 0,
        ];

        DB::transaction(function () use ($controllers, $visitors, $now, &$stats) {
            $visitorCourseSlugs = Course::where('visitor_only', true)->pluck('slug')->all();
            $allSlugs = collect(self::RATING_COURSES)->flatten()->merge($visitorCourseSlugs)->unique()->values();
            $courseBySlug = Course::whereIn('slug', $allSlugs)->get()->keyBy('slug');
            $seenHomeCids = [];
            $seenVisitorCids = [];

            $syncMember = function (array $member, bool $visitor) use ($now, &$stats, $courseBySlug, $visitorCourseSlugs, &$seenHomeCids, &$seenVisitorCids) {
                $cid = (int) ($member['cid'] ?? 0);
                if ($cid <= 0) {
                    return;
                }

                if ($visitor) {
                    $seenVisitorCids[] = $cid;
                } else {
                    $seenHomeCids[] = $cid;
                }

                $ratingId = isset($member['rating']) ? (int) $member['rating'] : null;
                $slugs = $visitor
                    ? $this->visitorEntitlementSlugsForRating($ratingId, $visitorCourseSlugs)
                    : $this->entitlementSlugsForRating($ratingId);

                if (! $visitor) {
                    $slugs = array_values(array_filter($slugs, function ($slug) use ($courseBySlug) {
                        $course = $courseBySlug->get($slug);
                        return $course && ! $course->visitor_only;
                    }));
                }

                $user = User::find($cid);
                $user ? $stats['users_matched']++ : $stats['pending_cids']++;

                $record = VatcanMember::firstOrNew(['cid' => $cid]);
                if (! $record->exists) {
                    $record->first_seen_at = $now;
                }
                $record->fill([
                    'user_id' => $user?->id,
                    'first_name' => $member['first_name'] ?? $member['fname'] ?? null,
                    'last_name' => $member['last_name'] ?? $member['lname'] ?? null,
                    'rating_id' => $ratingId,
                    'rating_label' => $this->ratingLabel($ratingId),
                    'entitled_course_slugs' => $slugs,
                    'active_home_member' => ! $visitor,
                    'active_visitor_member' => $visitor,
                    'last_seen_at' => $now,
                    'last_synced_at' => $now,
                ])->save();

                if (! $user) {
                    return;
                }

                $entitledIds = [];
                foreach ($slugs as $slug) {
                    $course = $courseBySlug->get($slug);
                    if (! $course) {
                        continue;
                    }
                    $entitledIds[] = $course->id;
                    $enrollment = Enrollment::where('user_id', $user->id)->where('course_id', $course->id)->first();
                    if (! $enrollment) {
                        Enrollment::create([
                            'user_id' => $user->id,
                            'course_id' => $course->id,
                            'source' => 'vatcan',
                            'active' => true,
                            'source_rating_id' => $ratingId,
                            'source_synced_at' => $now,
                            'assigned_by' => null,
                            'assigned_at' => $now,
                        ]);
                        $stats['enrollments_activated']++;
                    } elseif ($enrollment->source === 'vatcan') {
                        if (! $enrollment->active) {
                            $stats['enrollments_activated']++;
                        }
                        $enrollment->update(['active' => true, 'source_rating_id' => $ratingId, 'source_synced_at' => $now]);
                    }
                }

                $toDeactivate = Enrollment::where('user_id', $user->id)
                    ->where('source', 'vatcan')
                    ->where('active', true);
                if ($entitledIds) {
                    $toDeactivate->whereNotIn('course_id', $entitledIds);
                }
                foreach ($toDeactivate->get() as $enrollment) {
                    $enrollment->update(['active' => false, 'source_rating_id' => $ratingId, 'source_synced_at' => $now]);
                    $stats['enrollments_deactivated']++;
                }
            };

            foreach ($controllers as $member) {
                $syncMember($member, false);
            }
            foreach ($visitors as $member) {
                $syncMember($member, true);
            }

            $homeMissing = VatcanMember::where('active_home_member', true);
            if ($seenHomeCids) {
                $homeMissing->whereNotIn('cid', $seenHomeCids);
            }
            $homeMissing->update(['active_home_member' => false, 'last_synced_at' => $now]);

            $visitorMissing = VatcanMember::where('active_visitor_member', true);
            if ($seenVisitorCids) {
                $visitorMissing->whereNotIn('cid', $seenVisitorCids);
            }
            $visitorMissing->update(['active_visitor_member' => false, 'last_synced_at' => $now]);

            $inactiveUserIds = VatcanMember::where('active_home_member', false)
                ->where('active_visitor_member', false)
                ->whereNotNull('user_id')
                ->pluck('user_id');
            if ($inactiveUserIds->isNotEmpty()) {
                $deactivated = Enrollment::whereIn('user_id', $inactiveUserIds)
                    ->where('source', 'vatcan')
                    ->where('active', true)
                    ->update(['active' => false, 'source_synced_at' => $now]);
                $stats['enrollments_deactivated'] += $deactivated;
            }
        });

        $run->update($stats + [
            'status' => 'success',
            'message' => sprintf(
                'VATCAN home and visiting-controller rosters synchronized from %s. Visitor access requires S3 or higher.',
                ($result['source'] ?? 'api') === 'public_roster' ? 'the public CZWG roster' : 'the authenticated API'
            ),
        ]);

        return $run->fresh();
    }

    public function claimPendingForUser(User $user): void
    {
        $member = VatcanMember::where('cid', $user->id)
            ->where(function ($query) {
                $query->where('active_home_member', true)->orWhere('active_visitor_member', true);
            })->first();
        if (! $member) {
            return;
        }

        if ($member->user_id !== $user->id) {
            $member->update(['user_id' => $user->id]);
        }

        $now = now();
        $courses = Course::whereIn('slug', $member->entitled_course_slugs ?? [])->get();
        foreach ($courses as $course) {
            if ($course->visitor_only && (! $member->active_visitor_member || (int) $member->rating_id < 4)) {
                continue;
            }

            $existing = Enrollment::where('user_id', $user->id)->where('course_id', $course->id)->first();
            if (! $existing) {
                Enrollment::create([
                    'user_id' => $user->id,
                    'course_id' => $course->id,
                    'source' => 'vatcan',
                    'active' => true,
                    'source_rating_id' => $member->rating_id,
                    'source_synced_at' => $now,
                    'assigned_at' => $now,
                ]);
            } elseif ($existing->source === 'vatcan') {
                $existing->update([
                    'active' => true,
                    'source_rating_id' => $member->rating_id,
                    'source_synced_at' => $now,
                ]);
            }
        }
    }
}
