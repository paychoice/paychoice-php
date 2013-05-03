<?php

class PaychoiceHttpClient
{
  
	private $useSandbox = false; //true for sandbox test site; otherwise false for production
	private $useOAuth2 = false;
	private $credentials = ""; 
  
    function __construct($credentials, $useOAuth2, $useSandbox) 
    {
        $this->credentials = $credentials;
		$this->useOAuth2 = $useOAuth2; 
		$this->useSandbox = $useSandbox;
    }
	
	public function post($serviceAddress, $requestData)
	{
		return $this->sendRequest("post", $serviceAddress, $requestData);
	}
			
	public function get($serviceAddress)
	{
		return $this->sendRequest("get", $serviceAddress, "");
	}
  
  	public function sendRequest($method, $serviceAddress, $requestData)
	{	
        $headers = array();

		if (strlen($serviceAddress) < 1)
        {
            throw new PayChoiceException("Service endpoint not set");
        }

		$environment = $this->useSandbox == true ? "sandbox" : "secure";
		$endPoint = "https://{$environment}.paychoice.com.au/{$serviceAddress}";
		$assignedMethod = "!!!!unassigned HTTP method!!!!";
				
		// Initialise CURL and set base options
		$curl = curl_init();
		curl_setopt($curl, CURLOPT_TIMEOUT, 60);
		curl_setopt($curl, CURLOPT_CONNECTTIMEOUT, 30);
		curl_setopt($curl, CURLOPT_FRESH_CONNECT, true);
		curl_setopt($curl, CURLOPT_FORBID_REUSE, true);
		curl_setopt($curl, CURLOPT_HEADER, false);
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($curl, CURLOPT_HTTPHEADER, array('Content-Type: application/x-www-form-urlencoded; charset=utf-8'));

        // Setup CURL request method
		if ($method == "post")
		{
			$assignedMethod = "post";
			curl_setopt($curl, CURLOPT_POST, true);
			curl_setopt($curl, CURLOPT_POSTFIELDS, $this->encodeData($requestData));
		}
		else if ($method == "put")
		{
			$assignedMethod = "put";
			curl_setopt($curl, CURLOPT_CUSTOMREQUEST, 'PUT');
			curl_setopt($curl, CURLOPT_POSTFIELDS, $this->encodeData($requestData));
		}
		else if ($method == "delete")
		{
			$assignedMethod = "delete";
			curl_setopt($curl, CURLOPT_CUSTOMREQUEST, 'DELETE');
		}
        else if ($method == "get")
        {
            $assignedMethod = "get";
            //curl_setopt($curl, CURLOPT_GET, true);
        }
		else
		{
			throw new PaychoiceException("Unassigned communication method");
		}
		
		// Setup CURL params for this request
		curl_setopt($curl, CURLOPT_URL, $endPoint);
		if (strlen($this->credentials) > 0)
		{
			if ($this->useOAuth2 == false)
			{
				curl_setopt($curl, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
				curl_setopt($curl, CURLOPT_USERPWD, $this->credentials);	
			}			
		}

		// Run CURL
		$response = curl_exec($curl);

        //check to see if CURL had a certificate issue
        $errno = curl_errno($curl);
        if ($errno == CURLE_SSL_CACERT || $errno == CURLE_SSL_PEER_CERTIFICATE || $errno == 77) // CURLE_SSL_CACERT_BADFILE (constant not defined in PHP though)
        {
            array_push($headers, 'X-Paychoice-Client-Info: {"ca":"using Paychoice supplied CA bundle"}');
            curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
            curl_setopt($curl, CURLOPT_CAINFO, dirname(__FILE__) . '/ca.crt');

            $response = curl_exec($curl); // Re-run CURL
        }

   		$error = curl_error($curl);
		$responseCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);	
		
        $responseObject = json_decode($response);

        if (is_object($responseObject) && $responseObject->object_type == "error")
        {
            $errorParam = strlen($responseObject->error->param) > 0 ? ". Parameter: " . $responseObject->error->param : "";
            throw new PaychoiceException("Paychoice returned an error. Error: " . $responseObject->error->message . $errorParam);
        }

		// Check for CURL errors
		if (isset($error) && strlen($error))
		{
			throw new PaychoiceException("Could not successfully communicate with payment processor. Error: {$error}.");
		}
		else if (isset($responseCode) && strlen($responseCode) && $responseCode == '500')
		{
			throw new PaychoiceException("Could not successfully communicate with payment processor. HTTP response code {$responseCode}.");
		}
	

        return $responseObject;
	}

    private function encodeData($requestData)
    {
        if (!is_array($requestData))
        {
            throw new PaychoiceException("Request data is not in an array");
        }

        $formValues = "";
        foreach($requestData as $key=>$value) 
        { 
            $formValues .= $key.'='.urlencode($value).'&'; 
        }
        rtrim($formValues, '&');

        return $formValues;        
    }
}

?>