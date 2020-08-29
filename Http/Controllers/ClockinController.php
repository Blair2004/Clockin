<?php

/**
 * Clock&#039;in Controller
 * @since  1.2
 * @package  modules/Clockin
**/

namespace Modules\Clockin\Http\Controllers;
use Illuminate\Support\Facades\View;
use App\Http\Controllers\DashboardController;

class ClockinController extends DashboardController
{
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Index Controller Page
     * @return  view
     * @since  1.2
    **/
    public function index()
    {
        return $this->view( 'Clockin::index' );
    }
}
