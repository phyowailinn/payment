<?php

namespace Phyowailinn\Payment;

use Phyowailinn\Payment\MpgsGateway;
use Illuminate\Contracts\Config\Repository as ConfigRepository;

class Gateway
{
	use MpgsGateway;

    public function request($data)
    {
    	$default = config('payment.default');
    	$this->setConfig(config('payment.'.$default));
    	$this->send($data);
    }

    public function delete($token)
    {
    	$this->deleteToken($token);
    }

    protected function mpgs()
    {
    	return method_exists($this, 'verify') ? true : false;
    }
}