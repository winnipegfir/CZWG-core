<?php

namespace App\Services;

class ControllerAuthorization
{
    public const POSITIONS = [
        'DEL' => ['del', 'Delivery', 2, 2],
        'GND' => ['gnd', 'Ground', 2, 3],
        'TWR' => ['twr', 'Tower', 3, 4],
        'DEP' => ['dep', 'Departure', 4, 5],
        'APP' => ['app', 'Approach / Arrival', 4, 5],
        'ARR' => ['app', 'Arrival', 4, 5],
        'CTR' => ['ctr', 'Centre', 5, 6],
    ];

    public function position(array $controller): ?array
    {
        $callsign = strtoupper(trim((string) ($controller['callsign'] ?? '')));
        $parts = explode('_', $callsign);
        $suffix = end($parts);
        // Observers, ATIS and unsupported facilities are not controlling alerts.
        if (!preg_match('/^[A-Z0-9]+(?:_[A-Z0-9]+)+$/D', $callsign) || count($parts) < 2 || !in_array($parts[0], config('controller-alerts.prefixes', []), true)
            || !isset(self::POSITIONS[$suffix]) || (int) ($controller['facility'] ?? 0) === 0) {
            return null;
        }
        [$field, $label, $rating, $facility] = self::POSITIONS[$suffix];
        return compact('callsign', 'field', 'label', 'rating', 'facility');
    }

    public function assess(array $controller, ?array $member): array
    {
        $position = $this->position($controller);
        if (!$position) {
            return ['status' => 'ignored', 'reason' => 'Not a monitored controlling position.'];
        }
        $rating = (int) ($controller['rating'] ?? -1);
        $review = function (string $reason) use ($position) {
            return $position + ['status' => 'review', 'reason' => $reason];
        };
        if (!$member) {
            return $review('CID not found on the local home or visiting roster. Verify membership and any special authorization.');
        }
        if ((int) ($member['active'] ?? 0) !== 1) {
            return $review('Local roster record is not marked active.');
        }
        if ((int) ($controller['facility'] ?? 0) !== $position['facility']) {
            return $review('Live facility type does not match the callsign position.');
        }
        if ($rating <= 1) {
            return $review('Live rating is Observer, inactive, suspended, or unknown; it does not show a controlling rating. Verify the connection with staff.');
        }
        // I1/I3 carry controller qualifications. SUP/ADM are not proof of a
        // particular controlling qualification and are sent for staff review.
        $level = in_array($rating, [7, 8, 10], true) ? 5 : $rating;
        if (!in_array($rating, [2, 3, 4, 5, 7, 8, 10], true)) {
            return $review('Special or unrecognized network rating; verify controlling qualifications.');
        }
        $endorsement = (int) ($member[$position['field']] ?? 1);
        if ($endorsement === 3 && $level >= max(2, $position['rating'] - 1)) {
            return $position + ['status' => 'solo', 'reason' => 'Current local roster marks this position as Solo.'];
        }
        if ($endorsement === 4 && $level >= $position['rating']) {
            return $position + ['status' => 'online', 'reason' => 'Active roster member with matching position certification and network rating.'];
        }
        if ($endorsement === 2) {
            return $review('Roster marks this position as Training. Supervision cannot be confirmed from the live feed.');
        }
        if ($level < $position['rating']) {
            return $review('Position is above the live network rating without a qualifying roster solo badge. Verify training or special authorization.');
        }
        return $review('Local roster does not show certification or solo authorization for this position.');
    }
}
