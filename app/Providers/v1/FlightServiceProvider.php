<?php

namespace App\Providers\v1;

use Illuminate\Support\ServiceProvider;
use App\Services\v1;

use Illuminate\Support\Facades\Validator;

class FlightServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
        //custom validator for status
        Validator::extend('flightstatus',function($attribute, $value, $parameters) {
            return $value == 'ontime' || $value == 'delayed';
        });
    }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
        //binding service
        $this->app->bind(FlightsService::class, function ($app) {
            return new FlightsService();
        });
    }
}
