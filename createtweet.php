<?php
include_once("fof-main.php");

// Set username and password
$username = $fof_user_prefs['twitteruser'];
$password = $fof_user_prefs['twitterpass'];
// The message you want to send
$message = chunk_split(urlencode($_REQUEST['url'] . " " . $_REQUEST['title']), 122);
// The twitter API address
$url = 'http://twitter.com/statuses/update.xml';
// Set up and execute the curl process
$curl_handle = curl_init();
curl_setopt($curl_handle, CURLOPT_URL, "$url");
curl_setopt($curl_handle, CURLOPT_TIMEOUT, 5);
curl_setopt($curl_handle, CURLOPT_CONNECTTIMEOUT, 5);
curl_setopt($curl_handle, CURLOPT_RETURNTRANSFER, 1);
curl_setopt($curl_handle, CURLOPT_POST, 1);
curl_setopt($curl_handle, CURLOPT_POSTFIELDS, "status=" . $message . "&source=monkeychow");
curl_setopt($curl_handle, CURLOPT_USERPWD, "$username:$password");
$buffer = curl_exec($curl_handle);
curl_close($curl_handle);
?>
