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
      <link rel="stylesheet" href="<?php echo $MC_PATH?>/Font-Awesome/css/font-awesome.min.css" media="all">
   </head>
      
  <body>
<?php echo MC_PATH ?>
<div style="background: #eee; border: 1px solid black; width: 15em; margin: 5em auto; padding: 1.5em;">
	<form action="login.php" method="POST" style="display: inline">

		Please log in

		<div class="input-prepend">
    			<span class="add-on"><i class="icon-envelope"></i></span>
    			<input class="span2" type="text" name="user_name" placeholder="Username or Email">
  		</div>
  		<div class="input-prepend">
    			<span class="add-on"><i class="icon-key"></i></span>
    			<input class="span2" type="password" name="user_password" placeholder="Password">
		</div>
		<input type=submit value="Log on!" style='font-size: 16px; float: left;'><br>

		</br></br>
		<p align="left"><a href="http://shokk.wordpress.com/tag/monkeychow/" style="font-size: 12px; font-family: georgia;">MonkeyChow Author's Site</a><br></p>
		<?php if($failed) echo "<br><center><font color=red><b>Incorrect user name or password</b></font></center>"; ?>
	</form>
   </div>
  </body>
</html>
