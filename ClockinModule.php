<?php
namespace Modules\Clockin;

use Illuminate\Support\Facades\Event;
use App\Services\Module;

class ClockinModule extends Module
{
    public function __construct()
    {
        parent::__construct( __FILE__ );
    }
}