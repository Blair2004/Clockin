<?php

use Illuminate\Support\Facades\Route;

Route::middleware([ 'ns.installed' ])->group( function() {
    Route::get( 'dashboard/clockin', 'ClockinController@index' );
});