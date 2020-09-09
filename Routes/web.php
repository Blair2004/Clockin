<?php

use Illuminate\Support\Facades\Route;

Route::middleware([ 'ns.installed', 'auth' ])->group( function() {
    Route::get( 'dashboard/clockin', 'ClockinController@index' );
    Route::get( 'dashboard/clockin/reports', 'ClockinController@showReport' );
    Route::get( '/api/modules/clockin/get', 'ClockinController@getClockin' );
    Route::post( '/api/modules/clockin/start', 'ClockinController@startClockin' );
    Route::post( '/api/modules/clockin/stop', 'ClockinController@stopClockin' );
    Route::get( '/api/modules/clockin/history', 'ClockinController@getHistory' );
    Route::post( '/api/modules/clockin/report', 'ClockinController@getEmployeeReport' );
});