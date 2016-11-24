<?php


Route::group([
    'prefix' => config('laravel-uptime-monitor.restAPI.routePrefix'),
    'middleware' => config('laravel-uptime-monitor.restAPI.middleware')
    ], function () {
    Route::resource('monitor',config('laravel-uptime-monitor.restAPI.controller'));
});