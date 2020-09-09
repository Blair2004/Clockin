<?php
/**
 * Service Provider
 * @package  : Clockin
**/
namespace Modules\Clockin\Providers;
use Illuminate\Support\ServiceProvider as Provider;
use TorMorten\Eventy\Facades\Eventy as Hook;
use Modules\Clockin\Forms\ReportForm;

class ServiceProvider extends Provider
{
    /**
     * register method
     */
    public function register()
    {
    }
    
    /**
     * Boot method
     * @return  void
    **/
    public function boot()
    {
        Hook::addFilter( 'ns-dashboard-menus', function( $menus ) {
            $menus[ 'clockin-menu' ]    =   [
                'label'     =>  __( 'Clock\'in' ),
                'icon'      =>  'la-business-time',
                'childrens' =>  [
                    [
                        'label'     =>  __( 'Timer' ),
                        'permissions'   =>      [ 'see.clockin' ],
                        'href'      =>  url( '/dashboard/clockin' )
                    ], [
                        'label'     =>  __( 'Report' ),
                        'permissions'   =>      [ 'see.clockin.reports' ],
                        'href'      =>  url( '/dashboard/clockin/reports' ),
                    ]
                ]
            ];

            return $menus;
        });

        Hook::addFilter( 'ns.forms', function( $forms, $identifier ) {
            if ( $identifier === 'ck.report' ) {
                return new ReportForm;
            }
            return $forms;
        }, 20, 2 );
    }
}