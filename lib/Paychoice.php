<?php
/**
 * Paychoice PHP API
 * 
 * Please note, this module REQUIRES the following:
 *   - PHP 5+
 *   - CURL (http://au.php.net/curl)
 * 
 * @see http://www.paychoice.com.au
 * @author Paychoice Pty Ltd (support@paychoice.com.au)
 * @copyright 2012 Paychoice Pty Ltd
 */
 
if (!function_exists('curl_init')) {
  throw new Exception('Paychoice needs the CURL PHP extension.');
}
if (!function_exists('json_decode')) {
  throw new Exception('Paychoice needs the JSON PHP extension.');
}

require(dirname(__FILE__) . '/PaychoiceException.php');
require(dirname(__FILE__) . '/PaychoiceHttpClient.php');

class Paychoice 
{

	private $chargeEndpoint = "api/v3/charge/";
    private $tokenEndpoint = "api/v3/token/";
    private $publicKeyEndpoint = "api/v3/publickey/";
    private $bankTokenEndpoint = "api/v3/bank_token/";
    private $refundEndpoint = "api/v3/refund/";
	
	private $httpClient;
			
    function __construct($userName, $password, $sandboxAccount) 
    {
		$credentials = "${userName}:${password}";
        $this->httpClient = new PaychoiceHttpClient($credentials, false, $sandboxAccount);
    }

    function setCredentials($userName, $password, $sandboxAccount)
    {
        $this->apiUserName = $userName;
        $this->apiPassword = $password;
        $this->useSandbox = $sandboxAccount;
    }

    function storeCard($cardName, $cardNumber, $cardExpiryMonth, $cardExpiryYear, $cardCvv, $storePermanently = false)
    {
        $data = array(
            "card[name]" => $cardName,
            "card[number]" => $cardNumber,
            "card[cvv]" => $cardCvv,
            "card[expiry_month]" => $cardExpiryMonth,
            "card[expiry_year]" => $cardExpiryYear,
            "durable" => ($storePermanently ? "true" : "false"),
        );
        return $this->httpClient->post($this->tokenEndpoint, $data);	
    }

    function storeBank($bankName, $bankBSB, $bankNumber)
    {
        $data = array(
            "bank[name]" => $bankName,
            "bank[bsb]" => $bankBSB,
            "bank[number]" => $bankNumber,
        );
        return $this->httpClient->post($this->bankTokenEndpoint, $data);    
    }

    function getPublicKey()
    {        
        return $this->httpClient->get($this->publicKeyEndpoint);	
    }

    function getToken($token)
    {        
        return $this->httpClient->get($this->tokenEndpoint . $token);	
    }

    function chargeToken($reference, $token, $currency, $amount)
    {
        $data = array(
            "currency" => $currency,
            "amount" => $amount,
            "reference" => $reference,
            "card_token" => $token,
        );
    	return $this->httpClient->post($this->chargeEndpoint, $data);	
    }

    function chargeCardToken($reference, $token, $currency, $amount)
    {
        $data = array(
            "currency" => $currency,
            "amount" => $amount,
            "reference" => $reference,
            "card_token" => $token,
        );
        return $this->httpClient->post($this->chargeEndpoint, $data);   
    }
    
    function chargeBankToken($reference, $token, $currency, $amount)
    {
        $data = array(
            "currency" => $currency,
            "amount" => $amount,
            "reference" => $reference,
            "bank_token" => $token,
        );
        return $this->httpClient->post($this->chargeEndpoint, $data);   
    }

    function refund($reference, $charge_id, $amount){
        $data = array(
            "amount" => $amount,
            "charge_id" => $charge_id,
            "reference" => $reference
        );
        return $this->httpClient->post($this->refundEndpoint, $data);
    }

    function chargeCard($reference, $cardName, $cardNumber, $cardExpiryMonth, $cardExpiryYear, $cardCvv, $currency, $amount)
    {
        $data = array(
            "currency" => $currency,
            "amount" => $amount,
            "reference" => $reference,
            "card[name]" => $cardName,
            "card[number]" => $cardNumber,
            "card[expiry_month]" => $cardExpiryMonth,
            "card[expiry_year]" => $cardExpiryYear,
            "card[cvv]" => $cardCvv,
        );
    	return $this->httpClient->post($this->chargeEndpoint, $data);	
    }

	function getCharge($chargeId)
	{        
	    return $this->httpClient->get($this->chargeEndpoint . $chargeId);	
    }
	
	function getCharges($pageNumber, $pageItems)
	{        
		return $this->httpClient->get($this->chargeEndpoint . "?page[number]=${pageNumber}&page[items]=${pageItems}");	
	}

}
?>
