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
	 * [getBanks Get Banks in a country (Nigeria)]
	 * @return [object] [list of banks and their respective bank_ids]
	*/
	public function getBanks(){
		return $this->sendRequest('get', '/accounts/banks');
	}

	/**
	 * [nameEnquiry Verify Account Number]
	 * Required fields - 'bankCode', 'accountNumber'
	 * @return [object] [Account Object]
	*/
	public function	nameEnquiry(array $params){
		$required_values = ['bankCode', 'accountNumber'];

		if(!array_keys_exist($params, $required_values)){
		    throw new Exception\RequiredValuesMissing("Missing required values :(");
		}

		$url = "/accounts/transfer/name-enquiry";

		return $this->sendRequest('post', $url, ['body' => json_encode($params)]);
	}

	/**
	 * [transfer Transfer Funds]
	 * Required fields - 'amount', 'beneficiaryAccountNumber', 'beneficiaryBankCode', 'paymentReference'
	 * Optional fields - 'narration'
	 * @return [object] [Transfer Object]
	*/
	public function transfer(array $params){
		$required_values = ['amount', 'beneficiaryAccountNumber', 'beneficiaryBankCode', 'paymentReference'];

		if(!array_keys_exist($params, $required_values)){
		    throw new Exception\RequiredValuesMissing("Missing required values :(");
		}

		$url = "/accounts/transfer";

		return $this->sendRequest('post', $url, ['body' => json_encode($params)]);
	}

	/**
	 * [getAccounts Get Settlement Accounts]
	 * @return [object] [list of respective business Settlement Accounts]
	*/
	public function getAccounts(){
		return $this->sendRequest('get', '/accounts');
	}

	/**
	 * [getAccount Get Settlement Account]
	 * @param  [string] $account_id
	 * Required fields - 'id'
	 * @return [object] [Settlement Account Object]
	*/
	public function getAccount($account_id){
		if(!$account_id){
			throw new Exception\IsNullOrInvalid("Error Processing Request - Null/Invalid Account Id");
		}

		$url = "/accounts/{$account_id}";

		return $this->sendRequest('get', $url);
	}

	/**
	 * [createAccount Create Settlement Account]
	 * @param  [array] $params
	 * Required fields - 'type', 'accountType', 'currency'
	 * Optional fields - 'customerId' [The id of the customer. Required when 'type' is wallet]
	*/
	public function createAccount(array $params){
		$required_values = ['type', 'accountType', 'currency'];
		
		if(!array_keys_exist($params, $required_values)){
		    throw new Exception\RequiredValuesMissing("Missing required values :(");
		}

		$url = "/accounts";

		return $this->sendRequest('post', $url, ['body' => json_encode($params)]);
	}

	/**
	 * [getAccount Get Account Balance]
	 * @param  [string] $account_id
	 * Required fields - 'id'
	 * @return [object] [Account Balance Object]
	*/
	public function getAccountBalance($account_id){
		if(!$account_id){
			throw new Exception\IsNullOrInvalid("Error Processing Request - Null/Invalid Account Id");
		}

		$url = "/accounts/{$account_id}/balance";

		return $this->sendRequest('get', $url);
	}

	/**
	 * [getAccount Get Account Transactions]
	 * @param  [string] $account_id
	 * Required fields - 'id'
	 * @return [object] [Account Transactions Object]
	*/
	public function getAccountTransactions($account_id){
		if(!$account_id){
			throw new Exception\IsNullOrInvalid("Error Processing Request - Null/Invalid Account Id");
		}

		$url = "/accounts/{$account_id}/transactions";

		return $this->sendRequest('get', $url);
	}

	/**
	 * [updateAccount Edit Settlement Account]
	 * @param  [string] $account_id
	 * @param  [array] $params
	 * Required fields - 'id'
	 * Optional fields - 'type', 'accountType', 'currency', 'customerId' [The id of the customer. Required when 'type' is wallet]
	 * @return [object] [Settlement Account Object]
	*/
	public function updateAccount($account_id, array $params){
		if(!$account_id){
			throw new Exception\IsNullOrInvalid("Error Processing Request - Null/Invalid Account Id");
		}

		$url = "/accounts/{$account_id}";

		return $this->sendRequest('put', $url, ['body' => json_encode($params)]);
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
     * [getCustomers get Customers]
     * @param null [list of customers under your account]
     * Required fields - 'type', 'status', 'name', phoneNumber, emailAddress, individual => ['firstName', 'lastName', 'dob', 'identity' => ['type', 'number']]
	 * Optional fields - 'identity' =>  'documents' => ['idFrontUrl', 'idBackUrl', 'addressVerificationUrl']
	 * @return [object] [Added Customer Object]
    */
    public function getCustomers(){
		$url = '/customers';

		return $this->sendRequest('get', $url);
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
	* [updateCustomer - Edit Existing Customer]
	* @param [string] $customer_id
	* @param [array] $client_data
    * Required fields - 'type', 'status', 'name', phoneNumber, emailAddress, individual => ['firstName', 'lastName', 'dob', 'identity' => ['type', 'number']]
	* Optional fields - 'identity' =>  'documents' => ['idFrontUrl', 'idBackUrl', 'addressVerificationUrl']
	*/
    public function updateCustomer( $customer_id, array $client_data){
		if(!$customer_id){
		   throw new Exception\IsNullOrInvalid("Error Processing Request - Null/Invalid Client Id");
		}

		$url = "/customers/{$customer_id}";

		// Mandatory fields
		$required_values = ['type', 'status', 'emailAddress', 'phoneNumber', 'name', 'individual'];

		if(!array_keys_exist($client_data, $required_values)){
		    throw new Exception\RequiredValuesMissing("Missing required values :(");
		}

		return $this->sendRequest('put', $url, ['body' => $client_data]);
    }

	/**
	* [createFundingSource - Create Funding Sources]
	* @param [array] $params []
	* @return [object] [Funding Sources Object]
	* Required fields - 'type', 'status'
	* Optional fields - 'jitGateway' [Just in-time gateway details. Required if type is gateway.]
	*/
	public function createFundingSource(array $params){
		$required_values = ['type', 'status'];

		if(!array_keys_exist($params, $required_values)){
		    throw new Exception\RequiredValuesMissing("Missing required values :(");
		}

		$url = "/fundingsources";

		return $this->sendRequest('post', $url, ['body' => json_encode($params)]);
	}

	/**
	 * [getFundingSources - Get Funding Sources]
	 * @return [object] [Funding Sources Object]
	*/
	public function getFundingSources(){
		$url = "/fundingsources";

		return $this->sendRequest('get', $url);
	}


	/**
	 * [getFundingSource - Get Funding Sources]
	 * @param  [string] $funding_source_id
	 * @return [object] [Funding Source Object]
	 * Required fields - 'id'
	*/
	public function getFundingSource($funding_source_id){
		if(!$funding_source_id){
			throw new Exception\IsNullOrInvalid("Error Processing Request - Null/Invalid Funding Source Id");
		}

		$url = "/fundingsources/{$funding_source_id}";

		return $this->sendRequest('get', $url);
	}

	/**
	 * [updateFundingSource - Edit Funding Sources]
	 * @param  [string] $funding_source_id
	 * @param  [array] $params
	 * @return [object] [Funding Source Object]
	 * Required fields - 'id'
	 */

	public function updateFundingSource($funding_source_id, array $params){
		if(!$funding_source_id){
			throw new Exception\IsNullOrInvalid("Error Processing Request - Null/Invalid Funding Source Id");
		}

		$url = "/fundingsources/{$funding_source_id}";

		return $this->sendRequest('put', $url, ['body' => json_encode($params)]);
	}

	/**
	 * [createCard - Create Card]
	 * @param  [array] $params
	 * @return [object] [Card Object]
	 * Required fields - 'customerId', 'fundingSourceId', 'debitAccountId', 'type', 'brand', 'currency', 'issuerCountry', 'status'
	 * Optional fields - 'number', 'amount', 'expirationDate'
	*/
    
	public function createCard( array $param){
		$required_values = ['customerId', 'fundingSourceId', 'debitAccountId', 'type', 'brand', 'currency', 'issuerCountry', 'status', 'spendingControls'];

		if(!array_keys_exist($param, $required_values)){
		    throw new Exception\RequiredValuesMissing("Missing required values :(");
		}

		$url = "/cards";

		return $this->sendRequest('post', $url, ['body' => json_encode($param)]);
	}

	/**
	 * [getCards - Get Cards]
	 * @return [object] [Cards Object]
	*/
	public function getCards(){
		$url = "/cards";

		return $this->sendRequest('get', $url);
	}

	/**
	 * [getCustomerCards - Get Customer Cards]
	 * @param  [string] $customer_id
	 * @return [object] [Customer Cards Object]
	*/
	public function getCustomerCards($customer_id){
		$url = "/cards/customer/{$customer_id}";

		return $this->sendRequest('get', $url);
	}

	/**
	 * [getCard - Get Card]
	 * @param  [string] $card_id
	 * @return [object] [Card Object]
	 * Required fields - 'id'
	*/
	public function getCard($card_id){
		if(!$card_id){
			throw new Exception\IsNullOrInvalid("Error Processing Request - Null/Invalid Card Id");
		}

		$url = "/cards/{$card_id}";

		return $this->sendRequest('get', $url);
	}

	/**
	 * [updateCard - Edit Card]
	 * @param  [string] $card_id
	 * @param  [array] $params
	 * @return [object] [Card Object]
	 * Required fields - 'id'
	*/
	public function updateCard($card_id, array $params){
		if(!$card_id){
			throw new Exception\IsNullOrInvalid("Error Processing Request - Null/Invalid Card Id");
		}

		$url = "/cards/{$card_id}";

		return $this->sendRequest('put', $url, ['body' => json_encode($params)]);
	}

	/**
	 * [getCardTransactions - Get Card Transactions]
	 * @param  [string] $card_id
	 * @return [object] [Card Transactions Object]
	 * Required fields - 'id'
	*/
	public function getCardTransactions($card_id){
		if(!$card_id){
			throw new Exception\IsNullOrInvalid("Error Processing Request - Null/Invalid Card Id");
		}

		$card = $this->getCard($card_id);
		if($card->statusCode == 200){
			$card = $card->data;
		}else{
			throw new Exception\IsNullOrInvalid("Error Processing Request - Null/Invalid Card Id");
		}

		$url = "/accounts/{$card->account->_id}/transactions";

		return $this->sendRequest('get', $url);
	}

	/**
	 * [getCardBalance - Get Card Balance]
	 * @param  [string] $card_id
	 * @return [object] [Card Balance Object]
	 * Required fields - 'id'
	*/
	public function getCardBalance($card_id){
		if(!$card_id){
			throw new Exception\IsNullOrInvalid("Error Processing Request - Null/Invalid Card Id");
		}

		$card = $this->getCard($card_id);
		if($card->statusCode == 200){
			$card = $card->data;
		}else{
			throw new Exception\IsNullOrInvalid("Error Processing Request - Null/Invalid Card Id");
		}

		$url = "/accounts/{$card->account->_id}/balance";

		return $this->sendRequest('get', $url);
	}

	/**
	 * [generateCardToken - Generate Card Token]
	 * @param  [string] $card_id
	 * @return [object] [Card Token Object]
	 * Required fields - 'id'
	*/
	public function generateCardToken($card_id){
		if(!$card_id){
			throw new Exception\IsNullOrInvalid("Error Processing Request - Null/Invalid Card Id");
		}

		$url = "/cards/{$card_id}/token";

		return $this->sendRequest('get', $url);
	}

	/**
	 * [fundCard - Fund Card]
	 * @param [array] $data
	 * Required fields - 'debitAccountId', 'creditAccountId', 'amount', 'paymentReference'
	 * @return [object] [Transaction Object]
	*/
	public function fundCard(array $data){
		$required_values = ['debitAccountId', 'creditAccountId', 'amount', 'paymentReference'];
		if(!in_array($data, $required_values)){
		    throw new Exception\RequiredValuesMissing("Missing required values :(");
		}

		$url = "/accounts/transfer";

		return $this->sendRequest('post', $url, ['body' => json_encode($data)]);
	}

	/**
	 * [debitCard - Withdraw from Card]
	 * @param [array] $data
	 * Required fields - 'debitAccountId', 'creditAccountId', 'amount', 'paymentReference'
	 * @return [object] [Transaction Object]
	*/
	public function debitCard(array $data){
		$required_values = ['debitAccountId', 'creditAccountId', 'amount', 'paymentReference'];
		if(!in_array($data, $required_values)){
		    throw new Exception\RequiredValuesMissing("Missing required values :(");
		}

		$url = "/accounts/transfer";

		return $this->sendRequest('post', $url, ['body' => json_encode($data)]);
	}

	/**
	 * [transferStatus - Get Status of a transfer]
	 * @param  [string] $transfer_id
	 * @return [object] [Transfer Object]
	 */
	public function transferStatus($transfer_id){
		if(!$transfer_id){
			throw new Exception\IsNullOrInvalid("Error Processing Request - Null/Invalid Transfer Id");
		}

		$url = "/accounts/transfers/{$transfer_id}";

		return $this->sendRequest('get', $url);
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
