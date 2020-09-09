<?php
/**
 * Table Migration
 * @package  4.0
**/

namespace Modules\Clockin\Migrations;

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddImageUrl extends Migration
{
    /**
     * Run the migrations.
     *
     * @return  void
     */
    public function up()
    {
        Schema::table( 'clockin_timer', function( Blueprint $table ) {  
            $table->string( 'image_url_start' )->nullable();
            $table->string( 'image_url_end' )->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return  void
     */
    public function down()
    {
        Schema::dropColumn( 'clockin_timer', 'image_url_start' );
        Schema::dropColumn( 'clockin_timer', 'image_url_end' );
    }
}
