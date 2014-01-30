<?php

require_once('../init.php');
require_once('../includes/Oauth.php');
require_once('../ncludes/Twitter.php');

$facebook = new Twitter($twitter_app_id, $twitter_app_secret, $twitter_callback);
if($twitter->validateAccessToken()){
        // do something with this token
        header('Location: ../index.php');
}

exit;
?>
