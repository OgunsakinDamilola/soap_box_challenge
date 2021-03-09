<?php

namespace App\Providers;

use App\Models\WorkspaceUser;
use App\Observers\WorkspaceUserObserver;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        WorkspaceUser::observe(WorkspaceUserObserver::class);
    }
}
