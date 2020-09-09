<?php
namespace Modules\Clockin\Forms;

use App\Services\SettingsPage;

class ReportForm extends SettingsPage
{
    public function __construct()
    {
        $this->form     =   [
            'tabs'      =>  [
                'general'   =>  include( dirname( __FILE__ ) . '/fields/general.php' )
            ]
        ];
    }
}