<?php 

namespace Phyowailinn\Payment;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;

trait MpgsGateway {

	public $config;
	
	public function setConfig($config)
	{
		$this->config = $config;
	}

	public function verify($attributes)
	{	
		$url = "{$this->config['url']}{$this->config['merchant_id']}/order/{$attributes['order_id']}/transaction/{$attributes['transaction_id']}";
        $method = 'PUT';
        
        $data = [
	        'apiOperation' => 'VERIFY',
	        'order' => [
		        'currency' => 'MMK',
	        ],
	        'session' => [
	        	'id' =>$attributes['session_id'],
        	],
        ];

        $verify = $this->request_api($url, $method, $data);
        
        if ($verify->result !== 'SUCCESS') {
			return [
				'success' => false, 
				'message' => 'Your card issuer bank has declined. Please contact your bank for support.',
				'error_message' => [$verify->error->cause => [$verify->error->explanation]]
			];
		}

		$result = $this->getToken($attributes['session_id']);

		if ($result) {
			return ['success' => true, 'data' => $result];
		}

		return $result;
	}

	public function getToken($sessionId)
	{
		$url = "{$this->config['url']}{$this->config['merchant_id']}/token";
        $method = 'POST';
        $data = [
            'session' => [
                'id' =>$sessionId,
            ],
            'sourceOfFunds' => [
                'type' => 'CARD',
            ],
        ];

        $response = $this->request_api($url, $method, $data);
       
        if ($response->result === 'SUCCESS' && $response->status === 'VALID') return $response;
	}

	public function delete($token)
	{
		$url = "{$this->config['url']}{$this->config['merchant_id']}/token/{$token}";
        $method = 'DELETE';
        $response = $this->request_api($url, $method);

        $result['success'] = true;
        if ($response->result !== 'SUCCESS'){
        	$result['success'] = false;
        	$result['message'] = 'Your card can`t delete!';
        	$result['error_message'] = [$verify->error->cause => [$verify->error->explanation]];
        } 

        return $result;
	}

	private function request_api($url,$method,$data=[])
	{
		$data = json_encode($data);
		$client = new Client;   
		$header = [
			'Authorization' => 'Basic ' . base64_encode($this->config['basic_auth']),
			'Content-Type' => 'Application/json;charset=UTF-8',
        	'Content-Length' => strlen($data),
		];

        try {
			if ($method == "GET") {
				$response = $client->get($url);
			}else{
				$response = $client->request($method,$url,['body' => $data,'headers'=>$header]);  
			}
			return json_decode( $response->getBody()->getContents());
		} catch (ClientException $e) {
			return json_decode( $e->getResponse()->getBody()->getContents());
		}  
	}
}