<?php
/**
 * Table Migration
 * @package  4.0
**/

namespace Modules\Clockin\Migrations;

use App\Models\Permission;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddSeeReportsPermission extends Migration
{
    /**
     * Run the migrations.
     *
     * @return  void
     */
    public function up()
    {
        if ( ! Permission::namespace( 'see.clockin.reports' ) instanceof Permission ) {
            $permission                 =   new Permission();
            $permission->namespace      =   'see.clockin.reports';
            $permission->name           =   __( 'See Clocking Reports' );
            $permission->description    =   __( 'Allow the user to see the clocking reports' );
            $permission->save();
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return  void
     */
    public function down()
    {
        Permission::namespace( 'see.clockin.reports' )->delete();
    }
}
