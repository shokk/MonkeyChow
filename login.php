<?php
/*
 * This file is part of Monkeychow - http://shokk.wordpress.com/tag/monkeychow/
 * This has been adapted for use with MonkeyChow by Ernie Oporto
 *
 * login.php - username / password entry
 *
 *
 * Copyright (C) 2006 Ernie Oporto
 * ernieoporto@yahoo.com - http://shokk.wordpress.com
 *
 * Copyright (C) 2004-2007 Stephen Minutillo
 * steve@minutillo.com - http://minutillo.com/steve/
 *
 * Distributed under the GPL - see LICENSE
 *
 */

ob_start();

$fof_no_login = true;

include_once("fof-main.php");
header("Content-Type: text/html; charset=utf-8");

if(isset($_REQUEST["user_name"]) && isset($_REQUEST["user_password"]))
{
    if(fof_authenticate($_REQUEST["user_name"],md5($_REQUEST["user_password"])))
    {
		Header("Location: .");
    }
    else
    {
    	$failed = true;
		$mc_err_msg = "Incorrect username or password.";
    }
}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">

   <head>
      <title>MonkeyChow - Log on</title>
      
      <style>
      body
      {
		font-family: verdana, arial;
		font-size: 16px;
      }
      </style>
   </head>
      
  <body>
<div style="background: #eee; border: 1px solid black; width: 20em; margin: 5em auto; padding: 1.5em;">
	<form action="login.php" method="POST" style="display: inline">
		<center><a href="http://shokk.wordpress.com/tag/monkeychow/" style="font-size: 20px; font-family: georgia;">MonkeyChow Site</a></center><br>
		User name:<br><input type=string name=user_name style='font-size: 16px'><br><br>
		Password:<br><input type=password name=user_password style='font-size: 16px'><br><br>
		<input type=submit value="Log on!" style='font-size: 16px; float: right;'><br>
		<?php if($failed) echo "<br><center><font color=red><b>Incorrect user name or password</b></font></center>"; ?>
	</form>
</div>
  </body>
  
</html>
