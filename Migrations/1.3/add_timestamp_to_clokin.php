<?php
/**
 * Table Migration
 * @package  4.0
**/

namespace Modules\Clockin\Migrations;

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddTimestampToClokin extends Migration
{
    /**
     * Run the migrations.
     *
     * @return  void
     */
    public function up()
    {
        Schema::table( 'clockin_timer', function( Blueprint $table ) {  
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return  void
     */
    public function down()
    {
        Schema::dropColumn( 'clockin_timer', 'created_at' );
        Schema::dropColumn( 'clockin_timer', 'updated_at' );
    }
}
