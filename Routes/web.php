<?php

use Illuminate\Support\Facades\Route;

Route::middleware([ 'auth' ])->group( function() {
    Route::get( 'dashboard/clockin', 'ClockinController@index' );
    Route::get( '/api/modules/clockin/get', 'ClockinController@getClockin' );
    Route::post( '/api/modules/clockin/start', 'ClockinController@startClockin' );
    Route::post( '/api/modules/clockin/stop', 'ClockinController@stopClockin' );
    Route::get( '/api/modules/clockin/history', 'ClockinController@getHistory' );
});