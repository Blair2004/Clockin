<?php

/**
 * Clock&#039;in Controller
 * @since  1.2
 * @package  modules/Clockin
**/

namespace Modules\Clockin\Http\Controllers;

use Exception;
use Illuminate\Support\Facades\View;
use Illuminate\Http\Request;
use App\Http\Controllers\DashboardController;
use Modules\Clockin\Models\Clockin;
use App\Services\DateService;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

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
        return $this->view( 'Clockin::index', [
            'title'     =>  __( 'Start Timer' )
        ]);
    }

    public function getClockin()
    {
        $date       =   app()->make( DateService::class );
        $clockin    =   Clockin::where( 'user_id', Auth::id() )
            ->where( 'from_moment', '>=', $date->copy()->startOfDay()->toDateTimeString() )
            ->where( 'to_moment', null )
            ->first();

        if ( $clockin instanceof Clockin ) {
            return $clockin;
        }

        return [
            'status'    =>  'failed',
            'message'   =>  __( 'No clockin has been started today for the current user.' )
        ];
    }

    public function startClockin( Request $request )
    {
        $date       =   app()->make( DateService::class );
        $clockin    =   Clockin::where( 'user_id', Auth::id() )
            ->where( 'from_moment', '>=', $date->copy()->startOfDay()->toDateTimeString() )
            ->where( 'from_moment', '<=', $date->copy()->endOfDay()->toDateTimeString() )
            ->where( 'action', $request->input( 'action' ) )
            ->where( 'to_moment', null )
            ->first();

        if ( ! $clockin instanceof Clockin ) {
            $clockin    =   new Clockin;
            $clockin->user_id       =   Auth::id();
            $clockin->from_moment   =   $date->toDateTimeString();
            $clockin->action        =   $request->input( 'action' );
            $clockin->save();

            return [
                'status'    =>  'success',
                'message'   =>  __( 'The time has started' )
            ];
        }

        return [
            'status'    =>  'failed',
            'message'   =>  __( 'The clockin has already started for the current day.' )
        ];
    }

    public function stopClockin( Request $request )
    {
        $date       =   app()->make( DateService::class );
        $clockin    =   Clockin::where( 'user_id', Auth::id() )
            ->where( 'from_moment', '>=', $date->copy()->startOfDay()->toDateTimeString() )
            ->where( 'from_moment', '<=', $date->copy()->endOfDay()->toDateTimeString() )
            ->where( 'action', $request->input( 'action' ) )
            ->where( 'to_moment', null )
            ->first();

        if ( $clockin instanceof Clockin ) {
            $clockin->user_id       =   Auth::id();
            $clockin->to_moment     =   $date->toDateTimeString();
            $clockin->total_seconds =   Carbon::parse( $clockin->from_moment )->diffInSeconds( $clockin->to_moment );
            $clockin->action        =   $request->input( 'action' );
            $clockin->save();

            return [
                'status'    =>  'success',
                'message'   =>  __( 'The time has stopped.' )
            ];
        }

        throw new Exception( sprintf( __( 'The timer hasn\'t started today to be stoped for the action "%s".' ), $request->input( 'action' ) ) );
    }

    public function getHistory()
    {
        return Clockin::where( 'user_id', Auth::id() )
            ->where( 'from_moment', '<>', null )
            ->where( 'to_moment', '<>', null )
            ->orderBy( 'id', 'desc' )
            ->paginate(10);
    }
}
