<?php

class LinkedIn extends Oauth {
	
	protected $_prefix = 'linkedin';
	protected $_authorize_url = 'https://www.linkedin.com/uas/oauth/authorize';
	protected $_access_token_url = 'https://api.linkedin.com/uas/oauth/accessToken';
	protected $_request_token_url = 'https://api.linkedin.com/uas/oauth/requestToken';
	
	public function requestAccessToken($method = 'GET', Array $params = array(), $returnType = 'flat', Array $values = array('oauth_token', 'oauth_token_secret')){
		$response = $this->makeRequest($this->_access_token_url, 'POST', array(), $returnType, false, true);
		
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
		
		if(isset($params[$values[0]]) && isset($params[$values[1]])){
			$_SESSION[$this->_prefix]['access_token'] = $params[$values[0]];
			$_SESSION[$this->_prefix]['access_token_secret'] = $params[$values[1]];
		} else {
			$s = '';
			foreach($params as $k => $v){
				$s = $k . '=' . $v;
			}
			throw new Exception('incorrect access token parameters returned: ' . implode('&', $s));
		}
	}
	
}