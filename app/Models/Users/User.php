<?php

namespace App\Models\Users;

use App\Classes\VatsimRating;
use App\Models\AtcTraining;
use App\Models\ControllerBookings;
use App\Models\Events;
use App\Models\News;
use App\Models\Tickets;
use Exception;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use LasseRafn\InitialAvatarGenerator\InitialAvatar;
use App\Classes\DiscordClient;

class User extends Authenticatable
{
    use Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'id', 'fname', 'lname', 'email', 'rating_id',
        'reg_date', 'permissions', 'init', 'gdpr_subscribed_emails', 'avatar', 'bio', 'display_cid_only', 'display_fname', 'display_last_name',
        'discord_user_id', 'discord_dm_channel_id', 'avatar_mode', 'timezone'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    /**
     * Return articles that the user has written.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function news()
    {
        return $this->hasMany(News\News::class);
    }

    public function applications()
    {
        return $this->hasMany(AtcTraining\Application::class);
    }

    public function eventApplications()
    {
        return $this->hasMany(Events\ControllerApplication::class);
    }

    public function eventConfirms()
    {
        return $this->hasMany(Events\EventConfirm::class);
    }

    public function instructorProfile()
    {
        return $this->hasOne(AtcTraining\Instructor::class);
    }

    public function studentProfile()
    {
        return $this->hasOne(AtcTraining\Student::class);
    }

    public function tickets()
    {
        return $this->hasMany(Tickets\Ticket::class);
    }

    public function ticketReplies()
    {
        return $this->hasMany(Tickets\TicketReply::class);
    }

    public function staffProfile()
    {
        return $this->hasOne(StaffMember::class);
    }

    public function rosterProfile()
    {
        return $this->hasOne(AtcTraining\RosterMember::class);
    }

    public function notes()
    {
        return $this->hasMany(UserNote::class);
    }

    public function userSinceInDays()
    {
        $created = $this->created_at;
        $now = Carbon::now();
        $difference = $created->diff($now)->days;

        return $difference;
    }

    public function permissions()
    {
        return match ($this->permissions) {
            0 => 'Guest',
            1 => 'Controller/Trainee',
            2 => 'Mentor',
            3 => 'Instructor',
            4 => 'Staff Member',
            5 => 'Administrator',
            default => 'Unknown',
        };
    }

    public function fullName($format)
    {
        //display name check
        if ($this->display_cid_only == true) {
            return strval($this->id);
        }

        if ($format == 'FLC') {
            if ($this->display_last_name == true) {
                return $this->display_fname.' '.$this->lname.' '.$this->id;
            } else {
                return $this->display_fname.' '.$this->id;
            }
        } elseif ($format === 'FL') {
            if ($this->display_last_name == true) {
                return $this->display_fname.' '.$this->lname;
            } else {
                return $this->display_fname;
            }
        } elseif ($format === 'F') {
            return $this->display_fname;
        }

        return null;
    }

    public function isAvatarDefault()
    {
        if ($this->avatar_mode == 0) {
            return true;
        }

        return false;
    }

    public function certified()
    {
        if (! $this->rosterProfile()) {
            return false;
        }

        return true;
    }

    public function bookingBanned()
    {
        if (! ControllerBookings\ControllerBookingsBan::where('user_id', $this->id)->first()) {
            return false;
        }

        return true;
    }

    public function routeNotificationForDiscord()
    {
        return $this->discord_dm_channel_id;
    }

    public function hasDiscord()
    {
        if (! $this->discord_user_id) {
            return false;
        }

        return true;
    }

    public function getDiscordUser()
    {
        return Cache::remember('users.discorduserdata.'.$this->id, 84600, function () {
            if (!$this->discord_user_id)
                return null;

            $discord = new DiscordClient(config('services.discord.token'));
            return $discord->GetDiscordUser($this->discord_user_id);
        });
    }

    public function getDiscordAvatar()
    {
        return Cache::remember('users.discorduserdata.'.$this->id.'.avatar', 21600, function () {
            $user = $this->getDiscordUser();

            $url = 'https://cdn.discordapp.com/avatars/'.$user->id.'/'.$user->avatar.'.png';

            return $url;
        });
    }

    public function memberOfCZWGGuild()
    {
        $discord = new DiscordClient(config('services.discord.token'));
        try {
            if ($discord->GetGuildMember($this->discord_user_id)) {
                return true;
            }
        } catch (Exception $ex) {
            return false;
        }

        return false;
    }

    public function currentDiscordBan()
    {
        $ban = DiscordBan::whereDate('ban_end_timestamp', '>', Carbon::now())->where('user_id', $this->id)->first();
        if ($ban) {
            return $ban;
        } else {
            return null;
        }
    }

    public function discordBans()
    {
        return $this->hasMany(DiscordBan::class);
    }

    public function avatar()
    {
        if ($this->avatar_mode == 0) {
            return Cache::remember('users.'.$this->id.'.initialsavatar', 172800, function () {
                $avatar = new InitialAvatar();
                $image = $avatar
                    ->name($this->fullName('FL'))
                    ->size(125)
                    ->background('#122b44')
                    ->color('#2196f3')
                    ->generate();
                Storage::put('public/files/avatars/'.$this->id.'/initials.png', (string) $image->encode('png'));

                return Storage::url('public/files/avatars/'.$this->id.'/initials.png');
                imagedestroy($image);
            });
        } elseif ($this->avatar_mode == 1) {
            return $this->avatar;
        } else {
            return $this->getDiscordAvatar();
        }
    }

    public function preferences()
    {
        return $this->hasOne(UserPreferences::class);
    }

    public function displayTimezone(): string
    {
        return $this->timezone ?: 'UTC';
    }

    /**
     * Common IANA timezone ids -> friendly generic name, e.g. "Mountain Time".
     * Hand-maintained rather than sourced from the PHP intl extension so this
     * works the same everywhere regardless of server config (intl isn't
     * reliably installable on every host — see production incident notes).
     * Covers Canada/US comprehensively plus the major world regions; anything
     * not listed just falls back to showing the plain IANA id.
     */
    protected static array $timezoneNames = [
        // Canada / US
        'America/St_Johns' => 'Newfoundland Time',
        'America/Halifax' => 'Atlantic Time',
        'America/Glace_Bay' => 'Atlantic Time',
        'America/Moncton' => 'Atlantic Time',
        'America/Goose_Bay' => 'Atlantic Time',
        'America/Toronto' => 'Eastern Time',
        'America/New_York' => 'Eastern Time',
        'America/Detroit' => 'Eastern Time',
        'America/Nassau' => 'Eastern Time',
        'America/Iqaluit' => 'Eastern Time',
        'America/Nipigon' => 'Eastern Time',
        'America/Thunder_Bay' => 'Eastern Time',
        'America/Winnipeg' => 'Central Time',
        'America/Chicago' => 'Central Time',
        'America/Mexico_City' => 'Central Time',
        'America/Regina' => 'Central Time',
        'America/Swift_Current' => 'Central Time',
        'America/Rainy_River' => 'Central Time',
        'America/Resolute' => 'Central Time',
        'America/Edmonton' => 'Mountain Time',
        'America/Denver' => 'Mountain Time',
        'America/Phoenix' => 'Mountain Time',
        'America/Yellowknife' => 'Mountain Time',
        'America/Cambridge_Bay' => 'Mountain Time',
        'America/Boise' => 'Mountain Time',
        'America/Vancouver' => 'Pacific Time',
        'America/Los_Angeles' => 'Pacific Time',
        'America/Tijuana' => 'Pacific Time',
        'America/Whitehorse' => 'Yukon Time',
        'America/Dawson' => 'Yukon Time',
        'America/Anchorage' => 'Alaska Time',
        'America/Juneau' => 'Alaska Time',
        'America/Sitka' => 'Alaska Time',
        'Pacific/Honolulu' => 'Hawaii Time',
        // Central / South America
        'America/Sao_Paulo' => 'Brasília Time',
        'America/Argentina/Buenos_Aires' => 'Argentina Time',
        'America/Bogota' => 'Colombia Time',
        'America/Lima' => 'Peru Time',
        'America/Santiago' => 'Chile Time',
        'America/Panama' => 'Panama Time',
        // Europe
        'Europe/London' => 'United Kingdom Time',
        'Europe/Dublin' => 'Ireland Time',
        'Europe/Lisbon' => 'Western European Time',
        'Europe/Paris' => 'Central European Time',
        'Europe/Berlin' => 'Central European Time',
        'Europe/Madrid' => 'Central European Time',
        'Europe/Rome' => 'Central European Time',
        'Europe/Amsterdam' => 'Central European Time',
        'Europe/Brussels' => 'Central European Time',
        'Europe/Zurich' => 'Central European Time',
        'Europe/Vienna' => 'Central European Time',
        'Europe/Warsaw' => 'Central European Time',
        'Europe/Prague' => 'Central European Time',
        'Europe/Stockholm' => 'Central European Time',
        'Europe/Oslo' => 'Central European Time',
        'Europe/Copenhagen' => 'Central European Time',
        'Europe/Athens' => 'Eastern European Time',
        'Europe/Helsinki' => 'Eastern European Time',
        'Europe/Bucharest' => 'Eastern European Time',
        'Europe/Kyiv' => 'Eastern European Time',
        'Europe/Moscow' => 'Moscow Time',
        // Asia / Middle East
        'Asia/Dubai' => 'Gulf Time',
        'Asia/Kolkata' => 'India Time',
        'Asia/Karachi' => 'Pakistan Time',
        'Asia/Dhaka' => 'Bangladesh Time',
        'Asia/Bangkok' => 'Indochina Time',
        'Asia/Jakarta' => 'Western Indonesia Time',
        'Asia/Singapore' => 'Singapore Time',
        'Asia/Hong_Kong' => 'Hong Kong Time',
        'Asia/Shanghai' => 'China Time',
        'Asia/Taipei' => 'Taipei Time',
        'Asia/Tokyo' => 'Japan Time',
        'Asia/Seoul' => 'Korea Time',
        'Asia/Manila' => 'Philippine Time',
        // Australia / Pacific
        'Australia/Perth' => 'Australian Western Time',
        'Australia/Adelaide' => 'Australian Central Time',
        'Australia/Darwin' => 'Australian Central Time',
        'Australia/Sydney' => 'Australian Eastern Time',
        'Australia/Melbourne' => 'Australian Eastern Time',
        'Australia/Brisbane' => 'Australian Eastern Time',
        'Pacific/Auckland' => 'New Zealand Time',
        // Africa
        'Africa/Cairo' => 'Eastern European Time',
        'Africa/Johannesburg' => 'South Africa Time',
        'Africa/Lagos' => 'West Africa Time',
        'Africa/Nairobi' => 'East Africa Time',
    ];

    /**
     * A friendly label for an IANA timezone id, e.g. "America/Edmonton — Mountain Time".
     * Looks up a hand-maintained table first (works everywhere, no server
     * dependency); falls back to the raw id for anything not in that list.
     */
    public static function timezoneLabel(string $tz): string
    {
        if ($tz === 'UTC') {
            return 'Zulu (UTC)';
        }

        if (isset(self::$timezoneNames[$tz])) {
            return $tz . ' — ' . self::$timezoneNames[$tz];
        }

        return $tz;
    }

    /**
     * Just the friendly name ("Mountain Time"), no IANA id — for compact
     * inline display where the id would just be visual noise (e.g. a
     * dashboard list showing the same zone repeated many times).
     */
    public static function timezoneShortLabel(string $tz): string
    {
        if ($tz === 'UTC') {
            return 'Zulu';
        }

        return self::$timezoneNames[$tz] ?? $tz;
    }

    protected function rating(): Attribute
    {
        return Attribute::make(
            get: fn () => VatsimRating::from($this->rating_id)
        );
    }
}
