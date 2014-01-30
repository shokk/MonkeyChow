<?php

require_once('../init.php');
require_once('../includes/Oauth.php');
require_once('../ncludes/Facebook.php');

$facebook = new Facebook($fb_app_id, $fb_app_secret, $fb_callback);
if($facebook->validateAccessToken()){
        // do something with this token
        header('Location: ../index.php');
}

exit;
?>
