<?php
namespace Phyowailinn\Payment\Facades;

use Illuminate\Support\Facades\Facade;

class Gateway extends Facade 
{

    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor() { return 'payment.gateway'; }

}
