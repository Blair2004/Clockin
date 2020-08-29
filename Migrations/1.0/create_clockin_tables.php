<?php
/**
 * Table Migration
 * @package  4.0
**/

namespace Modules\Clockin\Migrations;

use App\Models\Permission;
use App\Models\Role;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateClockinTables extends Migration
{
    /**
     * Run the migrations.
     *
     * @return  void
     */
    public function up()
    {
        if ( ! Schema::hasTable( 'clockin_timer' ) ) {
            Schema::create( 'clockin_timer', function( Blueprint $table ) {
                $table->increments( 'id' );
                $table->integer( 'user_id' );
                $table->datetime( 'from' );
                $table->string( 'action' );
                $table->datetime( 'to' );
                $table->integer( 'total_minutes' )->default(0);
            });
        }

        if ( ! Permission::namespace( 'see.clockin' ) instanceof Permission ) {
            $permission                 =   new Permission();
            $permission->namespace      =   'see.clockin';
            $permission->name           =   __( 'See Clocking UI' );
            $permission->description    =   __( 'Allow the user to see the clokin' );
            $permission->save();
        }

        if ( ! ( $employee = Role::namespace( 'employee' ) ) instanceof Role ) {
            $employee               =   new Role();
            $employee->name         =   __( 'Employee' );
            $employee->namespace    =   'employee';
            $employee->description  =   __( 'HRCom Employee' );
            $employee->save();
        }
        
        $employee->addPermissions([ 'manage.profile', 'see.clockin' ]);
    }

    /**
     * Reverse the migrations.
     *
     * @return  void
     */
    public function down()
    {
        $role   =   Role::namespace( 'employee' );
        
        if ( $role instanceof Role ) {
            $role->removePermissions([ 'manage.profile', 'see.clokin' ]);
            $role->delete();
        }

        $permission     =   Permission::namespace( 'see.clockin' );
        
        if ( $permission instanceof Permission ) {
            $permission->delete();
        }

        Schema::dropIfExists( 'clockin_timer' );
    }
}
