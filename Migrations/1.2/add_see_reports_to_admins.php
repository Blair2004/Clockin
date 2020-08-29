<?php
/**
 * Table Migration
 * @package  4.0
**/

namespace Modules\Clockin\Migrations;

use App\Models\Role;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddSeeReportsToAdmins extends Migration
{
    /**
     * Run the migrations.
     *
     * @return  void
     */
    public function up()
    {
        Role::namespace( 'admin' )->addPermissions([ 'see.clockin.reports' ]);
        Role::namespace( 'supervisor' )->addPermissions([ 'see.clockin.reports' ]);
    }

    /**
     * Reverse the migrations.
     *
     * @return  void
     */
    public function down()
    {
        Role::namespace( 'admin' )->removePermissions([ 'see.clockin.reports' ]);
        Role::namespace( 'supervisor' )->removePermissions([ 'see.clockin.reports' ]);
    }
}
