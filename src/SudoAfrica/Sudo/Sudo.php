<?php

namespace SudoAfrica\Sudo;

use GuzzleHttp;
use SudoAfrica\Sudo\Exception;
use \Exception as phpException;

class Sudo {
	

	/**
	 * @var $api_key
	*/
	protected $api_key;
	

	/**
	 *
	 * @var $api_url
	 *
	*/
	protected $api_url = 'https://api.sudo.africa';
	

	/**
	 *
	 * @var $sandbox_api_url
	 *
	*/
	protected $sandbox_api_url = 'https://api.sandbox.sudo.africa';
	
	
	/**
	 * @var $client
	 *
	*/
	protected $client;

	

	//constructor
	public function __construct($api_key, $sandbox=false)
	{
		// Trim Key
		$api_key = trim($api_key);
		$this->api_key = $api_key;


		// Generate Authorization String
		$authorization_string = "Bearer {$this->api_key}";


		//Specify Api Url to use - Sandbox or Live
		$base_uri = '';
		if($sandbox === true){
			$base_uri = $this->sandbox_api_url;
		}else{
			$base_uri = $this->api_url;
		}


		//Set up Guzzle
		$this->client = new GuzzleHttp\Client( [
			'base_uri' => $base_uri,
			'protocols' => ['https'],
			'headers' => [
				'Authorization' => $authorization_string,
				'Content-Type' => 'application/json'
			]
		]);
	}

	/**
	 * [getStates Get States in a country (Nigeria)]
	 * @return [object] [list of banks and their respective bank_ids]
	*/
	public function getBanks(){
		return $this->sendRequest('get', '/accounts/banks');
	}
    
	
	/**
	* [addPayment]
	* @param [string] $method 		[Mandatory - request method <get | post | put | delete> ]
	* @param [string] $url           [Mandatory - url to send request to]
	* @param [array] $params         [data to post to request url]
	*/
	public function sendRequest($method, $url, $params=[])
	{
		try{
			if (strtolower($method) == 'get'){
				$result = $this->client->request('GET', $url);
			}elseif (strtolower($method) == 'post'){
				$result = $this->client->request('POST', $url, $params);
			}elseif (strtolower($method) == 'put'){
				$result = $this->client->request('PUT', $url, $params);
			}elseif (strtolower($method) == 'delete'){
				$result = $this->client->request('DELETE', $url);
			}

			return cleanResponse($result);
		}
        catch( Exception $e){
            throw $e;
        }
	}
}
