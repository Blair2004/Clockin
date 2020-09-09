<?php

/**
 * Clock&#039;in Controller
 * @since  1.2
 * @package  modules/Clockin
**/

namespace Modules\Clockin\Http\Controllers;

use Exception;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\DB;
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
        ns()->restrict([ 'see.clockin' ]);
        
        return $this->view( 'Clockin::index', [
            'title'     =>  __( 'Start Timer' )
        ]);
    }

    public function getClockin()
    {
        ns()->restrict([ 'see.clockin' ]);

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
        ns()->restrict([ 'see.clockin' ]);

        $date       =   app()->make( DateService::class );
        $clockin    =   Clockin::where( 'user_id', Auth::id() )
            ->where( 'from_moment', '>=', $date->copy()->startOfDay()->toDateTimeString() )
            ->where( 'from_moment', '<=', $date->copy()->endOfDay()->toDateTimeString() )
            ->where( 'action', $request->input( 'action' ) )
            ->where( 'to_moment', null )
            ->first();

        if ( ! $clockin instanceof Clockin ) {
            $file                           =   $request->file( 'file' )->storePublicly( 'clockin-shots', 'public' );
            $clockin                        =   new Clockin;
            $clockin->user_id               =   Auth::id();
            $clockin->from_moment           =   $date->toDateTimeString();
            $clockin->action                =   $request->input( 'action' );
            $clockin->image_url_start       =   url( 'storage/' . $file);
            $clockin->save();

            return [
                'status'    =>  'success',
                'message'   =>  __( 'The time has started' ),
                'data'      =>  compact( 'clockin' )
            ];
        }

        return [
            'status'    =>  'failed',
            'message'   =>  __( 'The clockin has already started for the current day.' )
        ];
    }

    public function stopClockin( Request $request )
    {
        ns()->restrict([ 'see.clockin' ]);

        $date       =   app()->make( DateService::class );
        $clockin    =   Clockin::where( 'user_id', Auth::id() )
            ->where( 'from_moment', '>=', $date->copy()->startOfDay()->toDateTimeString() )
            ->where( 'from_moment', '<=', $date->copy()->endOfDay()->toDateTimeString() )
            ->where( 'action', $request->input( 'action' ) )
            ->where( 'to_moment', null )
            ->first();

        if ( $clockin instanceof Clockin ) {
            $file                           =   $request->file( 'file' )->storePublicly( 'clockin-shots', 'public' );
            $clockin->user_id               =   Auth::id();
            $clockin->to_moment             =   $date->toDateTimeString();
            $clockin->total_seconds         =   Carbon::parse( $clockin->from_moment )->diffInSeconds( $clockin->to_moment );
            $clockin->action                =   $request->input( 'action' );
            $clockin->image_url_end         =   url( 'storage/' . $file);
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
        ns()->restrict([ 'see.clockin' ]);

        return Clockin::where( 'user_id', Auth::id() )
            ->where( 'from_moment', '<>', null )
            ->where( 'to_moment', '<>', null )
            ->orderBy( 'id', 'desc' )
            ->paginate(10);
    }

    public function showReport()
    {
        ns()->restrict([ 'see.clockin.reports' ]);

        return $this->view( 'Clockin::report', [
            'title'     =>  __( 'Clockin Report' )
        ]);
    }

    public function getEmployeeReport( Request $request )
    {
        ns()->restrict([ 'see.clockin.reports' ]);

        $result     =   Clockin::where( 'user_id', $request->input( 'user_id' ) )
            ->where( 'from_moment', '>=', Carbon::parse( $request->input( 'from_moment' ) )->startOfDay()->toDateTimeString() )
            ->where( 'to_moment', '<=', Carbon::parse( $request->input( 'to_moment' ) )->endOfDay()->toDateTimeString() )
            ->paginate(20);

        $bestWorkingDay     =   Clockin::where( 'user_id', $request->input( 'user_id' ) )
            ->where( 'from_moment', '>=', Carbon::parse( $request->input( 'from_moment' ) )->startOfDay()->toDateTimeString() )
            ->where( 'to_moment', '<=', Carbon::parse( $request->input( 'to_moment' ) )->endOfDay()->toDateTimeString() )
            ->orderBy( 'total_seconds' )
            ->first();

        $working    =   DB::table( 'clockin_timer' )->where( 'user_id', $request->input( 'user_id' ) )
            ->where( 'from_moment', '>=', Carbon::parse( $request->input( 'from_moment' ) )->startOfDay()->toDateTimeString() )
            ->where( 'to_moment', '<=', Carbon::parse( $request->input( 'to_moment' ) )->endOfDay()->toDateTimeString() )
            ->where( 'action', 'working' )
            ->sum( 'total_seconds' );

        $break    =   DB::table( 'clockin_timer' )->where( 'user_id', $request->input( 'user_id' ) )
            ->where( 'from_moment', '>=', Carbon::parse( $request->input( 'from_moment' ) )->startOfDay()->toDateTimeString() )
            ->where( 'to_moment', '<=', Carbon::parse( $request->input( 'to_moment' ) )->endOfDay()->toDateTimeString() )
            ->where( 'action', 'break' )
            ->sum( 'total_seconds' );

        return [
            'history'               =>  $result,
            'total_working_time'    =>  $working,
            'total_break_time'      =>  $break,
            'best_session'          =>  $bestWorkingDay
        ];
    }
}
