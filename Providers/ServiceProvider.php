<?php
/**
 * Service Provider
 * @package  : Clockin
**/
namespace Modules\Clockin\Providers;
use Illuminate\Support\ServiceProvider as Provider;
use TorMorten\Eventy\Facades\Eventy as Hook;

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
                        'href'      =>  url( '/dashboard/clockin' )
                    ], [
                        'label'     =>  __( 'Report' ),
                        'href'      =>  url( '/dashboard/clockin/reports' ),
                    ]
                ]
            ];

            return $menus;
        });
    }
}