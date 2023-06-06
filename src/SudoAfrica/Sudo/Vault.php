<?php

namespace SudoAfrica\Sudo;

use GuzzleHttp;
use SudoAfrica\Sudo\Exception;
use Exception as phpException;
class Vault extends Sudo{
	/**
	 * @var $card_token
	*/
	protected $card_token;
	
	/**
	 *
	 * @var $api_url
	 *
	*/
	protected $api_url = 'https://vault.sudo.africa';

	/**
	 *
	 * @var $sandbox_api_url
	 *
	*/
	protected $sandbox_api_url = 'https://vault.sandbox.sudo.cards';
	
	
	/**
	 * @var $client
	 *
	*/
	protected $client;

	//constructor
	public function __construct($card_token, $sandbox=false)
	{
		// Trim Key
		$card_token = trim($card_token);
		$this->card_token = $card_token;

		if(empty($card_token)){
			throw new Exception\InvalidCredentials('Invalid API Key');
		}

		// Generate Authorization String
		$authorization_string = "Bearer $this->card_token";

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
     * [displayCardNumber Reveals the card number of a card using token]
     * @param string $card_id
     * @return array
     */
    public function displayCardNumber($card_id)
    {
		if(!$card_id){
			throw new Exception\IsNullOrInvalid("Error Processing Request - Null/Invalid Card Id");
		}

		$url = "/cards/{$card_id}/secure-data/number";

		return $this->sendRequest('get', $url);
    }

    /**
     * [displayCardCvv Reveals the card CVV2 of a card using token]
     * @param string $card_id
     * @return array
     */
    public function displayCardCvv($card_id)
    {
		if(!$card_id){
			throw new Exception\IsNullOrInvalid("Error Processing Request - Null/Invalid Card Id");
		}

		$url = "/cards/{$card_id}/secure-data/cvv2";

		return $this->sendRequest('get', $url);
    }

    /**
     * [displayCardCvv Reveals the card CVV2 of a card using token]
     * @param string $card_id
     * @return array
     */
    public function displayCardPin($card_id)
    {
		if(!$card_id){
			throw new Exception\IsNullOrInvalid("Error Processing Request - Null/Invalid Card Id");
		}

		$url = "/cards/{$card_id}/secure-data/defaultPin";

		return $this->sendRequest('get', $url);
    }
}