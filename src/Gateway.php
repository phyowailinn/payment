<?php

namespace Phyowailinn\Payment;

/**
 * 
 */

use Phyowailinn\Payment\MpgsGateway;
use Illuminate\Contracts\Config\Repository as ConfigRepository;

class Gateway
{
	use MpgsGateway;

	public $config;

	public function __construct(){
        $this->config = config('payment');
    }

	public function call()
	{	
		return $this->config[$this->config['default']];
	}
}