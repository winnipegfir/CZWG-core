<?php

namespace App\Services;

use App\Models\Settings\CoreSettings;
use App\Models\Users\User;

class AcademyVisibility
{
    private static function settings(): ?CoreSettings
    {
        return CoreSettings::find(1);
    }

    public static function accessMode(): string
    {
        $settings = self::settings();
        $mode = $settings->academy_access_mode ?? null;

        if (in_array($mode, ['admin', 'staff', 'normal'], true)) {
            return $mode;
        }

        // Backwards-compatible interpretation for databases that have not run v25 yet.
        if ($settings && ! ($settings->academy_preview_mode ?? true)) {
            return 'normal';
        }
        if ($settings && ($settings->academy_staff_access_enabled ?? false)) {
            return 'staff';
        }

        return 'admin';
    }

    public static function previewMode(): bool
    {
        return self::accessMode() !== 'normal';
    }

    public static function maintenanceMode(): bool
    {
        $settings = self::settings();
        return $settings ? (bool) ($settings->academy_maintenance_mode ?? false) : false;
    }

    public static function navEnabled(): bool
    {
        $settings = self::settings();
        return $settings ? (bool) ($settings->academy_nav_enabled ?? true) : true;
    }

    public static function staffToolsEnabled(): bool
    {
        return in_array(self::accessMode(), ['staff', 'normal'], true);
    }

    private static function isAdmin(?User $user): bool
    {
        return $user && (int) $user->permissions >= 5;
    }

    private static function isAcademyStaff(?User $user): bool
    {
        return $user && $user->canOverseeAcademy();
    }

    public static function canBypassMaintenance(?User $user): bool
    {
        return self::isAdmin($user) || (self::staffToolsEnabled() && self::isAcademyStaff($user));
    }

    public static function canUseStudentAcademy(?User $user): bool
    {
        if (! $user) {
            return false;
        }

        return match (self::accessMode()) {
            'admin' => self::isAdmin($user),
            'staff' => self::isAdmin($user) || self::isAcademyStaff($user),
            'normal' => (int) $user->permissions >= 1,
            default => self::isAdmin($user),
        };
    }

    public static function shouldShowMaintenance(?User $user): bool
    {
        return self::maintenanceMode()
            && self::canUseStudentAcademy($user)
            && ! self::canBypassMaintenance($user);
    }

    public static function shouldShowNav(?User $user): bool
    {
        return self::navEnabled() && self::canUseStudentAcademy($user);
    }

    public static function canUseStaffTools(?User $user): bool
    {
        if (! $user) {
            return false;
        }

        // Administrators can always manage/test the Academy in every deployment state.
        if (self::isAdmin($user)) {
            return true;
        }

        return self::staffToolsEnabled() && self::isAcademyStaff($user);
    }
}
