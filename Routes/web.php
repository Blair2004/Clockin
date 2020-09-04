<?php

use Illuminate\Support\Facades\Route;

Route::middleware([ 'ns.installed', 'auth' ])->group( function() {
    Route::get( 'dashboard/clockin', 'ClockinController@index' );
});