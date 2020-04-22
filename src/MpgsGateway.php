<?php 

namespace Phyowailinn\Payment;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\ServerException;

trait MpgsGateway {

	public $config;
	
	public function setConfig($config)
	{
		$this->config = $config;
	}

	public function verify($info)
	{
		$url = "{$this->config['url']}{$this->config['merchant_id']}/order/{$info['order_id']}/transaction/{$info['transaction_id']}";
        $method = 'PUT';
        
        $data = [
	        'apiOperation' => 'VERIFY',
	        'order' => [
		        'currency' => 'MMK',
	        ],
	        'session' => [
	        	'id' =>$info['session_id'],
        	],
        ];

        $response = $this->request_api($url, $method, $data);
        
        return $response;
	}

	public function send($data)
	{
		$verify = $this->verify($data);
	
		if ($verify->result !== 'SUCCESS') {
			return ['success' => false, 'message' => 'Your card issuer bank has declined. Please contact your bank for support.'];
		}

		$result = $this->getToken($data['session_id']);

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

	public function deleteToken($token)
	{
		$url = "{$this->config['url']}{$this->config['merchant_id']}/token/{$token}";
        $method = 'DELETE';
        $response = $this->request_api($url, $method);
        
        if ($response->result === 'SUCCESS') return $response->result;
	}

	private function request_api($url,$method,$data=[])
	{
		$data = json_encode($data);
		$client = new Client;   
		$header = [
			'Authorization' => 'Basic ' . $this->config['basic_auth'],
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