<?php

class Facebook extends Oauth {
	
	protected $_prefix = 'facebook';
	protected $_authorize_url = 'https://www.facebook.com/dialog/oauth';
	protected $_access_token_url = 'https://graph.facebook.com/oauth/access_token';
	protected $_scope = array('publish_actions');
}
