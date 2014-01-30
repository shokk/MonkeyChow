<?php

class Oauth {
	
	protected $_client_id;
	protected $_client_secret;
	protected $_callback;
	
	protected $_access_token;
	protected $_access_token_secret;
	protected $_expires;
	
	protected $_scope;
	
	protected $_prefix;
	protected $_authorize_url;
	protected $_access_token_url;
	protected $_request_token_url;
	
	public function __construct($client_id, $client_secret, $callback){
		$this->_client_id = $client_id;
		$this->_client_secret = $client_secret;
		$this->_callback = $callback;
	}
	
	public function setAccessToken($access_token, $access_token_secret = null, $expires = null){
		$this->_access_token = $access_token;
		$this->_access_token_secret = $access_token_secret;
		$this->_expires = $expires;
	}
	
	public function setScope(Array $scope){
		$this->_scope = $scope;
	}
	
	public function makeRequest($url, $method = 'GET', Array $parameters = array(), $returnType = 'json', $includeCallback = false, $includeVerifier = false){
		// set oauth headers for oauth 1.0
		if(isset($this->_request_token_url) && strlen($this->_request_token_url) > 0){
			$headers = $this->getOauthHeaders($includeCallback);
			if($includeVerifier && isset($_GET['oauth_verifier'])){
				$headers['oauth_verifier'] = $_GET['oauth_verifier'];
			}
			$base_info = $this->buildBaseString($url, $method, $headers);
			$composite_key = $this->getCompositeKey();
			$headers['oauth_signature'] = base64_encode(hash_hmac('sha1', $base_info, $composite_key, true));
			$header = array($this->buildAuthorizationHeader($headers), 'Expect:');
		}
		// add access token to parameter list for oauth 2.0 requests
		else {
			if(isset($_SESSION[$this->_prefix]['access_token'])){
				$parameters['access_token'] = $_SESSION[$this->_prefix]['access_token'];
			}
		}
		
		// create a querystring for GET requests
		if(count($parameters) > 0 && $method == 'GET' && strpos($url, '?') === false){
			$p = array();
			foreach($parameters as $k => $v){
				$p[] = $k . '=' . $v;
			}
			$querystring = implode('&', $p);
			$url = $url . '?' . $querystring;
		}
		
		// set default CURL options
		$options = array(
			CURLOPT_URL => $url,
			CURLOPT_RETURNTRANSFER => true,
			CURLOPT_SSL_VERIFYPEER => false
		);
		
		// set CURL headers for oauth 1.0 requests
		if(isset($this->_request_token_url) && strlen($this->_request_token_url) > 0){
			$options[CURLOPT_HTTPHEADER] = $header;
			$options[CURLOPT_HEADER] = false;
		}
		
		// set post fields for POST requests
		if($method == 'POST'){
			$options[CURLOPT_POST] = true;
			$options[CURLOPT_POSTFIELDS] = $parameters;
		}
		
		// make CURL request
		$curl = curl_init();
		curl_setopt_array($curl, $options);
		$response = curl_exec($curl);
		$info = curl_getinfo($curl);
		curl_close($curl);
		
		// show error when http_code is not 200
		if($info['http_code'] != 200){
			// mostly errors are thrown when a user has denied access
			unset($_SESSION[$this->_prefix]);
			throw new Exception($response);
		}
		
		// return json decoded array or plain response
		if($returnType == 'json'){
			return json_decode($response, true);
		} else {
			return $response;
		}
	}
	
	public function validateAccessToken(){
		// check if current token has expired
		if(isset($_SESSION[$this->_prefix]['expires']) && $_SESSION[$this->_prefix]['expires'] < time()){
			unset($_SESSION[$this->_prefix]);
			$this->authorize($this->_scope);
			return false;
		}
		// return true if access token is found
		if(isset($_SESSION[$this->_prefix]['access_token']) || (isset($this->_access_token) && strlen($this->_access_token) > 0)){
			$this->_access_token = $_SESSION[$this->_prefix]['access_token'];
			if(isset($_SESSION[$this->_prefix]['access_token_secret'])){
				$this->_access_token_secret = $_SESSION[$this->_prefix]['access_token_secret'];
			}
			if(isset($_SESSION[$this->_prefix]['expires'])){
				$this->_expires = $_SESSION[$this->_prefix]['expires'];
			}
			return true;
		}
		// authorize app if no token is found
		if(!isset($this->_access_token) || strlen($this->_access_token) == 0){
			// handle oauth 1.0 flow
			if(isset($this->_request_token_url) && strlen($this->_request_token_url) > 0){
				// request token and authorize app
				if(!isset($_GET['oauth_token']) && !isset($_GET['oauth_verifier'])){
					$this->requestToken();
					$this->authorize();
					return false;
				}
				// request access token
				else {
					if($_GET['oauth_token'] != $_SESSION[$this->_prefix]['token']){
						unset($_SESSION[$this->_prefix]['token'], $_SESSION[$this->_prefix]['token_secret']);
						return false;
					} else {
						$this->requestAccessToken();
						unset($_SESSION[$this->_prefix]['token'], $_SESSION[$this->_prefix]['token_secret']);
						return true;
					}
				}
			}
			// handle oauth 2.0 flow
			else {
				// authorize app
				if(!isset($_GET['state']) && !isset($_GET['code'])){
					$this->authorize($this->_scope);
					return false;
				}
				// request access token
				else {
					if($_GET['state'] != $_SESSION[$this->_prefix]['state']){
						unset($_SESSION[$this->_prefix]['state']);
						return false;
					} else {
						unset($_SESSION[$this->_prefix]['state']);
						$this->requestAccessToken();
						return true;
					}
				}
			}
		}
	}
	
	protected function requestToken($returnType = 'flat', Array $values = array('oauth_token', 'oauth_token_secret')){
		// make the request
		$response = $this->makeRequest($this->_request_token_url, 'POST', array(), $returnType, true);
		
		// get the correct parameters from the response
		$params = $this->getParameters($response, $returnType);
		
		// add the token and token secret to the session
		if(isset($params[$values[0]]) && isset($params[$values[1]])){
			$_SESSION[$this->_prefix]['token'] = $params[$values[0]];
			$_SESSION[$this->_prefix]['token_secret'] = $params[$values[1]];
		}
		// throw exception if incorrect parameters were returned
		else {
			$s = '';
			foreach($params as $k => $v){$s = $k . '=' . $v;}
			throw new Exception('incorrect access token parameters returned: ' . implode('&', $s));
		}
	}
	
	protected function requestAccessToken($method = 'GET', Array $params = array(), $returnType = 'flat', Array $values = array('access_token', 'expires')){
		// add oauth verifier to parameters for oauth 1.0 request
		if(isset($this->_request_token_url) && strlen($this->_request_token_url) > 0){
			$parameters = array('oauth_verifier' => $_GET['oauth_verifier']);
			$parameters = array_merge($parameters, $params);
		}
		// set parameters for oauth 2.0 request
		else {
			$parameters = array(
				'client_id' => $this->_client_id,
				'redirect_uri' => $this->_callback,
				'client_secret' => $this->_client_secret,
				'code' => $_GET['code']
			);
			$parameters = array_merge($parameters, $params);
		}
		
		// make the request
		$response = $this->makeRequest($this->_access_token_url, $method, $parameters, $returnType, false);
		
		// get the correct parameters from the response
		$params = $this->getParameters($response, $returnType);
		
		// add the token to the session
		if(isset($params[$values[0]]) && isset($params[$values[1]])){
			if(isset($this->_request_token_url) && strlen($this->_request_token_url) > 0){
				$_SESSION[$this->_prefix]['access_token'] = $params[$values[0]];
				$_SESSION[$this->_prefix]['access_token_secret'] = $params[$values[1]];
			} else {
				$_SESSION[$this->_prefix]['access_token'] = $params[$values[0]];
				$_SESSION[$this->_prefix]['expires'] = time() + $params[$values[1]];
			}
		}
		// throw exception if incorrect parameters were returned
		else {
			$s = '';
			foreach($params as $k => $v){$s = $k . '=' . $v;}
			throw new Exception('incorrect access token parameters returned: ' . implode('&', $s));
		}
	}
	
	protected function authorize(Array $scope = array(), $scope_seperator = ',', $attach = null){
		// build authorize url for oauth 1.0 requests
		if(isset($this->_request_token_url) && strlen($this->_request_token_url) > 0){
			$this->_authorize_url .= '?oauth_token=' . $_SESSION[$this->_prefix]['token'];
		}
		// build authorize url for oauth 2.0 requests
		else {
			$this->_authorize_url .= '?client_id=' . $this->_client_id . '&redirect_uri=' . $this->_callback;
			$state = md5(time() . mt_rand());
			$_SESSION[$this->_prefix]['state'] = $state;
			$this->_authorize_url .= '&state=' . $state . '&scope=' . implode($scope_seperator, $scope) . $attach;
		}
		// redirect
		header('Location: ' . $this->_authorize_url);exit;
	}
	
	private function getParameters($response, $returnType){
		if($returnType != 'json'){
			$r = explode('&', $response);
			$params = array();
			foreach($r as $v){
				$param = explode('=', $v);
				$params[$param[0]] = $param[1];
			}
		} else {
			$params = $response;
		}
		return $params;
	}
	
	private function getCompositeKey(){
		if(isset($this->_access_token_secret) && strlen($this->_access_token_secret) > 0){
			$composite_key = rawurlencode($this->_client_secret) . '&' . rawurlencode($this->_access_token_secret);
		} else if(isset($_SESSION[$this->_prefix]['token_secret'])){
			$composite_key = rawurlencode($this->_client_secret) . '&' . rawurlencode($_SESSION[$this->_prefix]['token_secret']);
		} else {
			$composite_key = rawurlencode($this->_client_secret) . '&';
		}
		return $composite_key;
	}
	
	private function getOauthHeaders($includeCallback = false){
		$oauth = array(
			'oauth_consumer_key' => $this->_client_id,
			'oauth_nonce' => time(),
			'oauth_signature_method' => 'HMAC-SHA1',
			'oauth_timestamp' => time(),
			'oauth_version' => '1.0'
		);
		if(isset($this->_access_token)){
			$oauth['oauth_token'] = $this->_access_token;
		} else if(isset($_SESSION[$this->_prefix]['token'])){
			$oauth['oauth_token'] = $_SESSION[$this->_prefix]['token'];
		}
		if($includeCallback){
			$oauth['oauth_callback'] = $this->_callback;
		}
		return $oauth;
	}
	
	private function buildBaseString($baseURI, $method, $params){
		$r = array();
		ksort($params);
		foreach($params as $key => $value){
			$r[] = $key . '=' . rawurlencode($value);
		}
		return $method . '&' . rawurlencode($baseURI) . '&' . rawurlencode(implode('&', $r));
	}
	
	private function buildAuthorizationHeader($oauth){
		$r = 'Authorization: OAuth ';
		$values = array();
		foreach($oauth as $key => $value){
			$values[] = $key . '="' . rawurlencode($value) . '"';
		}
		$r .= implode(', ', $values);
		return $r;
	}
	
}
