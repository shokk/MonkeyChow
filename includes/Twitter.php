<?php

class Twitter extends Oauth {
	
	protected $_prefix = 'twitter';
	protected $_authorize_url = 'https://api.twitter.com/oauth/authorize';
	protected $_access_token_url = 'https://api.twitter.com/oauth/access_token';
	protected $_request_token_url = 'https://api.twitter.com/oauth/request_token';
	
	public function requestAccessToken($method = 'POST', Array $params = array(), $returnType = 'flat', Array $values = array('oauth_token', 'oauth_token_secret')){
		parent::requestAccessToken($method, $params, $returnType, $values);
	}
	
}