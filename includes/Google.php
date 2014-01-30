<?php

class Google extends Oauth {
	
	protected $_prefix = 'google';

	protected $_authorize_url = 'https://accounts.google.com/o/oauth2/auth';
	protected $_access_token_url = 'https://accounts.google.com/o/oauth2/token';
	
	protected function authorize(Array $scope = array(), $scope_seperator = '+'){
		parent::authorize($scope, $scope_seperator, '&response_type=code');
	}
	
	protected function requestAccessToken($method = 'POST', Array $params = array('grant_type' => 'authorization_code'), $returnType = 'json', Array $values = array('access_token', 'expires_in')){
		parent::requestAccessToken($method, $params, $returnType, $values);
	}
	
}