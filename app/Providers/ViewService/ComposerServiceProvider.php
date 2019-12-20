<?php

namespace App\Providers;

use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class ComposerServiceProvider extends ServiceProvider
{
    /**
     * Register binding in container
     *
     * @return void
     */
    public function boot()
    {
        // // composer based on class
        // View::composer(
        //     'community', 'App\Http\ViewComposers\ProfileComposer'
        // );

    }

    /**
     * register service provider
     *
     * @return void
     */
    public function register()
    {
        //
    }
}