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
        //
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
