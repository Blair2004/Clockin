<?php
namespace Modules\Clockin\Events;

/**
 * Register Events
**/
class ClockinEvent
{
    public function __construct( Menus $menus )
    {
        $this->menus    =   $menus;
    }
}