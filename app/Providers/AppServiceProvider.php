<?php

namespace App\Providers;

use App\Models\AtcTraining\Instructor;
use App\Models\AtcTraining\RosterMember;
use App\Models\Users\StaffMember;
use App\Observers\InstructorObserver;
use App\Observers\RosterMemberObserver;
use App\Observers\StaffMemberObserver;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        // Local testing must use host-only, non-secure session cookies. This
        // prevents a copied production .env (for example SESSION_DOMAIN set to
        // winnipegfir.ca) from immediately losing the local admin login session.
        if ($this->app->environment('local')) {
            config([
                'session.domain' => null,
                'session.secure' => false,
            ]);
        }

        Schema::defaultStringLength(191);

        RosterMember::observe(RosterMemberObserver::class);
        Instructor::observe(InstructorObserver::class);
        StaffMember::observe(StaffMemberObserver::class);
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton(\Parsedown::class);
    }
}
