<?php

namespace SudoAfrica\Sudo;

use GuzzleHttp;
use SudoAfrica\Sudo\Exception;
use Exception as phpException;

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
	protected $sandbox_api_url = 'https://api.sandbox.sudo.cards';
	
	
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

		if(empty($api_key)){
			throw new Exception\InvalidCredentials('Invalid API Key');
		}

		// Generate Authorization String
		$authorization_string = "Bearer $this->api_key";

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
     * [addCustomer description]
     * @param array $client_data [description]
     * Required fields - 'type', 'status', 'name', phoneNumber, emailAddress, individual => ['firstName', 'lastName', 'dob', 'identity' => ['type', 'number']]
	 * Optional fields - 'identity' =>  'documents' => ['idFrontUrl', 'idBackUrl', 'addressVerificationUrl']
	 * @return [object] [Added Customer Object]
    */
    public function addCustomer( array $client_data){
		// Mandatory fields
		$required_values = ['type', 'status', 'emailAddress', 'phoneNumber', 'name', 'individual', "billingAddress"];

		if(!array_keys_exist($client_data, $required_values)){
			throw new Exception\RequiredValuesMissing("Missing required values :(");
		}

		$url = '/customers';

		return $this->sendRequest('post', $url, ['body' => json_encode($client_data)]);
    }

    /**
     * [getClient Get client Details]
     * @param  [string] $customer_id
     * @return [object] [Client Object]
    */
	public function getCustomer($customer_id = null){
		if(!$customer_id){
			throw new Exception\IsNullOrInvalid("Error Processing Request - Null/Invalid Client Id");
		}

		$url = "/customers/{$customer_id}";

		return $this->sendRequest('get', $url);
	}

	/**
	* [editCustomer - Edit Existing Customer]
	* @param [string] $customer_id
	* @param [array] $client_data
    * Required fields - 'type', 'status', 'name', phoneNumber, emailAddress, individual => ['firstName', 'lastName', 'dob', 'identity' => ['type', 'number']]
	* Optional fields - 'identity' =>  'documents' => ['idFrontUrl', 'idBackUrl', 'addressVerificationUrl']
	*/
    public function editCustomer( $customer_id, array $client_data){
		if(!$customer_id){
		   throw new Exception\IsNullOrInvalid("Error Processing Request - Null/Invalid Client Id");
		}

		$url = "/customers/{$customer_id}";

		// Mandatory fields
		$required_values = ['type', 'status', 'emailAddress', 'phoneNumber', 'name', 'individual'];

		if(!array_keys_exist($client_data, $required_values)){
		     throw new Exception\RequiredValuesMissing("Missing required values :(");
		}

		return $this->sendRequest('put', $url, ['form_params' => $client_data]);
    }

    
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
        catch(\Exception $e){
            throw $e;
        }
	}
}
