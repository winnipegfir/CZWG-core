<?php

namespace App\Models\Events;

use App\Models\AtcTraining\RosterMember;
use App\Models\Users\User;
use Auth;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\HtmlString;
use Parsedown;

class Event extends Model
{
    protected $fillable = [
        'id', 'name', 'start_timestamp', 'end_timestamp', 'user_id', 'description', 'image_url', 'controller_applications_open', 'departure_icao', 'arrival_icao', 'slug',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function getConfirmedAttribute()
    {
        $check = RosterMember::where('user_id', Auth::id())->first();

        return EventConfirm::where('event_id', $this->id)->where('roster_id', $check->id)->first();
    }

    public function updates()
    {
        return $this->hasMany(EventUpdate::class);
    }

    public function controllerApplications()
    {
        return $this->hasMany(ControllerApplication::class);
    }

    public function starts_in_pretty()
    {
        $t = Carbon::create($this->start_timestamp);

        return $t->diffForHumans();
    }

    public function start_timestamp_pretty()
    {
        $t = Carbon::create($this->start_timestamp);

        return $t->monthName.' '.$t->day.', '.$t->year.' '.$t->format('H:i').'z';
    }

    public function flatpickr_limits()
    {
        $start = Carbon::create($this->start_timestamp);
        $end = Carbon::create($this->end_timestamp);

        return [
            $start->format('H:i'),
            $end->format('H:i'),
        ];
    }

    public function end_timestamp_pretty()
    {
        $t = Carbon::create($this->end_timestamp);

        return $t->monthName.' '.$t->day.', '.$t->year.' '.$t->format('H:i').'z';
    }

    public function departure_icao_data()
    {
        if (! $this->departure_icao) {
            return null;
        }

        $output = Cache::remember('events.data.'.$this->departure_icao, 172800, function () {

            //Let's make sure our airport names look pretty
            $define = [
                'CYWG' => ['name' => 'Winnipeg International Airport', 'icao' => 'CYWG'],
                'CYAV' => ['name' => 'Winnipeg/ St. Andrews Airport', 'icao' => 'CYAV'],
                'CYPG' => ['name' => 'Portage la Prairie Airport', 'icao' => 'CYPG'],
                'CYMJ' => ['name' => 'Moose Jaw Airport', 'icao' => 'CYMJ'],
                'CYXE' => ['name' => 'Saskatoon International Airport', 'icao' => 'CYXE'],
                'CYQR' => ['name' => 'Regina International Airport', 'icao' => 'CYQR'],
                'CYQT' => ['name' => 'Thunder Bay International Airport', 'icao' => 'CYQT'],
                'CYYZ' => ['name' => 'Toronto Pearson International Airport', 'icao' => 'CYYZ'],
            ];
            json_encode($define);

            $icao = $this->departure_icao;

            if (isset($define[$icao])) {
                return $define[$icao];
            } else {
                $url = 'https://api.checkwx.com/station/'.$this->departure_icao;

                $ch = curl_init();
                curl_setopt($ch, CURLOPT_URL, $url);
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
                curl_setopt($ch, CURLOPT_HTTPHEADER, [
                    'X-API-Key: '.env('AIRPORT_API_KEY'), ]);
                $json = json_decode(curl_exec($ch), true);
                curl_close($ch);

                if (isset($json['data'][0])) {
                    return $json['data'][0];
                } else {
                    return ['name' => 'Error', 'icao' => $this->departure_icao];
                }
            }
        });

        return $output;
    }

    public function arrival_icao_data()
    {
        if (! $this->arrival_icao) {
            return null;
        }

        $output = Cache::remember('events.data.'.$this->arrival_icao, 172800, function () {

            //Let's make sure our airport names look pretty
            $define = [
                'CYWG' => ['name' => 'Winnipeg International Airport', 'icao' => 'CYWG'],
                'CYAV' => ['name' => 'Winnipeg/ St. Andrews Airport', 'icao' => 'CYAV'],
                'CYPG' => ['name' => 'Portage la Prairie Airport', 'icao' => 'CYPG'],
                'CYMJ' => ['name' => 'Moose Jaw Airport', 'icao' => 'CYMJ'],
                'CYXE' => ['name' => 'Saskatoon International Airport', 'icao' => 'CYXE'],
                'CYQR' => ['name' => 'Regina International Airport', 'icao' => 'CYQR'],
                'CYQT' => ['name' => 'Thunder Bay International Airport', 'icao' => 'CYQT'],
                'CYYZ' => ['name' => 'Toronto Pearson International Airport', 'icao' => 'CYYZ'],
            ];
            json_encode($define);

            $icao = $this->arrival_icao;

            if (isset($define[$icao])) {
                return $define[$icao];
            } else {
                $url = 'https://api.checkwx.com/station/'.$this->arrival_icao;

                $ch = curl_init();
                curl_setopt($ch, CURLOPT_URL, $url);
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
                curl_setopt($ch, CURLOPT_HTTPHEADER, [
                    'X-API-Key: '.env('AIRPORT_API_KEY'), ]);
                $json = json_decode(curl_exec($ch), true);
                curl_close($ch);

                if (isset($json['data'][0])) {
                    return $json['data'][0];
                } else {
                    return ['name' => 'Error', 'icao' => $this->arrival_icao];
                }
            }
        });

        return $output;
    }

    public function event_in_past()
    {
        $end = Carbon::create($this->end_timestamp);
        if (! $end->isPast()) {
            return false;
        }

        return true;
    }

    public function html()
    {
        return new HtmlString(app(Parsedown::class)->text($this->description));
    }

    public function userHasApplied()
    {
        if (ControllerApplication::where('event_id', $this->id)->where('user_id', Auth::id())->first()) {
            return true;
        }

        return false;
    }

    public function eventconfirm()
    {
        return $this->hasMany(EventConfirm::class);
    }
}
