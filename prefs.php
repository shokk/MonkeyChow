<?php
/*
 * This file is part of MonkeyChow http://www.monkeychow.org
 *
 * prefs.php - display and change preferences
 *
 *
 * Distributed under the GPL - see LICENSE
 *
 */

include_once("fof-main.php");

function makeWPcleansite($text)
{
        $patterns = array(
                "/(([a-zA-Z]+:\/\/)([a-z][a-z0-9_\..-]*[a-z]{2,6})([a-zA-Z0-9\/*
-\.]*)([a-zA-Z0-9\/*-?&%]*))/i",
                "/(([a-zA-Z]+:\/\/)([a-z][a-z0-9_\..-]*[a-z]{2,6})([a-zA-Z0-9\/*
-?&%]*))/i",
                "/(([a-z][a-z0-9_\..-]*[a-z]{2,6})([a-zA-Z0-9\/*-?&%]*))/i",
                "/(([a-z][a-z0-9_\..-]*[a-z]{2,6})([a-zA-Z0-9\/*-\.]*)([a-zA-Z0-
9\/*-?&%]*))/i",
                "/(.*)\//",
        );
        $replacements = array(
                '$3$4',
                '$3$4',
                '$2$3',
                '$2$3',
                '$1',
        );
        return preg_replace($patterns, $replacements, $text);
}

function makeWPcleanname($text)
{
		return preg_replace('/(a-zA-Z0-9)/','$1',$text);
}

$message = '';
if(isset($_POST['prefs']))
{
	$fof_user_prefs['favicons']     = isset($_POST['favicons']);
	$fof_user_prefs['wordpresssite1']     = makeWPcleansite( (isset($_REQUEST['wordpresssite1'])) ? $_REQUEST['wordpresssite1'] : "");
	$fof_user_prefs['wordpresssitename1']     = strip_punctuation((isset($_REQUEST['wordpresssitename1'])) ? $_REQUEST['wordpresssitename1'] : "");
	$fof_user_prefs['wordpresssite2']     = (isset($_REQUEST['wordpresssite2'])) ? $_REQUEST['wordpresssite2'] : "" ;
	$fof_user_prefs['wordpresssitename2']     = (isset($_REQUEST['wordpresssitename2'])) ? $_REQUEST['wordpresssitename2'] : "" ;
	$fof_user_prefs['wordpresssite3']     = (isset($_REQUEST['wordpresssite3'])) ? $_REQUEST['wordpresssite3'] : "" ;
	$fof_user_prefs['wordpresssitename3']     = (isset($_REQUEST['wordpresssitename3'])) ? $_REQUEST['wordpresssitename3'] : "" ;
	$fof_user_prefs['twitteruser']     = (isset($_REQUEST['twitteruser'])) ? $_REQUEST['twitteruser'] : "" ;
	$fof_user_prefs['twitterpass']     = (isset($_REQUEST['twitterpass'])) ? $_REQUEST['twitterpass'] : "" ;
	$fof_user_prefs['faviconsize']     = (isset($_REQUEST['faviconsize'])) ? $_REQUEST['faviconsize'] : "" ;
	$fof_user_prefs['keyboard']     = isset($_POST['keyboard']);
	$fof_user_prefs['frames']       = isset($_POST['frames']);
	$fof_user_prefs['collapse']     = isset($_POST['collapse']);
	$fof_user_prefs['feedsrefresh'] = (isset($_REQUEST['feedsrefresh']) && ($_REQUEST['feedsrefresh']<61)) ? $_REQUEST['feedsrefresh'] : "" ;
	$fof_user_prefs['itemsrefresh'] = (isset($_REQUEST['itemsrefresh']) && ($_REQUEST['itemsrefresh']<61)) ? $_REQUEST['itemsrefresh'] : "" ;
	if ($_REQUEST['feedsrefresh']>60)
	{
			$message .= 'Feeds refresh illegal value<br />';
	}
	if ($_REQUEST['itemsrefresh']>60)
	{
			$message .= 'Items refresh illegal value<br />';
	}
	
	db_save_prefs(current_user(), $fof_user_prefs);
	
	$message .= 'Saved prefs.<br />';
}

if(isset($_POST['adduser']) && isset($_POST['username']) && isset($_POST['password']) )
{
	$username = $_POST['username'];
	$password = $_POST['password'];
	
	fof_do_query("insert into $FOF_USER_TABLE (user_name, user_password) values ('$username', '$password')");
	
	$message = "User '$username' added.";
}

if(isset($_POST['deleteuser']) && isset($_POST['username']))
{
	$username = $_POST['username'];
	
	fof_do_query("delete from $FOF_USER_TABLE where user_name = '$username'");
	
	$message = "User '$username' deleted.";
}

include("header.php");

?>
<body>

<?php if(isset($message) && $message != '') { ?>

<br><font color="red"><?php echo $message ?></font><br>
<a href="
<?php
	dirname($_SERVER["SCRIPT_NAME"]);
    echo ($_REQUEST['framed']) ? "framesview.php?how=paged&framed=yes" : "feeds.php" ;
?>
"><?php echo _("Return") ?></a><br />

<?php } ?>

<br><h1>Preferences</h1>
<form method="post" action="prefs.php" style="border: 1px solid black; margin: 10px; padding: 10px;">
Display custom feed favicons? <input type="checkbox" name="favicons" <?php echo ($fof_user_prefs['favicons']) ? "checked=true" : "" ;?> ><br />
Feed icon size?
<SELECT name="faviconsize" fontsize="8">
	<OPTION VALUE="16"<?php echo ($fof_user_prefs['faviconsize'] == "16") ? ' selected="selected"' : "" ; ?>>16x16</OPTION>
	<OPTION VALUE="10"<?php echo ($fof_user_prefs['faviconsize'] == "10") ? ' selected="selected"' : "" ; ?>>10x10</OPTION>
</SELECT>
<br /><br />
Use keyboard shortcuts? <input type="checkbox" name="keyboard" <?php echo ($fof_user_prefs['keyboard']) ? "checked=true" : "" ;?> ><br><br>
Time offset in hours: <input type="text" name=offset> (server time: <?php echo date("Y-n-d g:ia") ?>)
<br><br>
Collapse view? <input type="checkbox" name="collapse" <?php echo ($fof_user_prefs['collapse']) ? "checked=true" : "" ;?> >
<br><br>
Frames view? <input type="checkbox" name="frames" <?php echo ($fof_user_prefs['frames']) ? "checked=true" : "" ;?> >
<br><br>
Feeds refresh <input type="text" name="feedsrefresh" value="<?php echo (isset($fof_user_prefs['feedsrefresh'])) ? $fof_user_prefs['feedsrefresh'] : "" ;?>"> (1 to 60 indicates minutes, 0 = disabled)<br />
Items refresh <input type="text" name="itemsrefresh" value="<?php echo (isset($fof_user_prefs['itemsrefresh'])) ? $fof_user_prefs['itemsrefresh'] : "" ;?>"> (1 to 60 indicates minutes, 0 = disabled) <br />
<br><br>
<b>Twitter</b><br />
Twitter registered email address
<br /><input type="text" size="40" name="twitteruser" value="<?php echo (isset($fof_user_prefs['twitteruser'])) ? $fof_user_prefs['twitteruser'] : "" ;?>"><br />
Twitter password
<br /><input type="password" size="40" name="twitterpass" value="<?php echo (isset($fof_user_prefs['twitterpass'])) ? $fof_user_prefs['twitterpass'] : "" ;?>"><br />

<br><br>
<b>Wordpress</b><br />
Enter the URL of up to three Wordpress site here. Enter a nickname for each for easy reference in tooltips.<br />
Do not include the wp-admin directory, the http:// , or trailing slash, as that will all be added for you.
<br />1. url: <input type="text" size="40" name="wordpresssite1" value="<?php echo (isset($fof_user_prefs['wordpresssite1'])) ? $fof_user_prefs['wordpresssite1'] : "" ;?>">&nbsp;&nbsp;
name: <input type="text" size="40" name="wordpresssitename1" value="<?php echo (isset($fof_user_prefs['wordpresssitename1'])) ? $fof_user_prefs['wordpresssitename1'] : "" ;?>">
<br />2. url: <input type="text" size="40" name="wordpresssite2" value="<?php echo (isset($fof_user_prefs['wordpresssite2'])) ? $fof_user_prefs['wordpresssite2'] : "" ;?>">&nbsp;&nbsp;
name: <input type="text" size="40" name="wordpresssitename2" value="<?php echo (isset($fof_user_prefs['wordpresssitename2'])) ? $fof_user_prefs['wordpresssitename2'] : "" ;?>">
<br />3. url: <input type="text" size="40" name="wordpresssite3" value="<?php echo (isset($fof_user_prefs['wordpresssite3'])) ? $fof_user_prefs['wordpresssite3'] : "" ;?>">&nbsp;&nbsp;
name: <input type="text" size="40" name="wordpresssitename3" value="<?php echo (isset($fof_user_prefs['wordpresssitename3'])) ? $fof_user_prefs['wordpresssitename3'] : "" ;?>">
<br />

<br><br>
<input type="hidden" name="ref" value="<?php echo $_SERVER['HTTP_REFERER'] ?>">
<input type="hidden" name="framed" value="<?php echo $_REQUEST['framed'] ?>">
<input type=submit name=prefs value="Save Preferences">

</form>

<?php if(fof_is_admin()) { ?>

<br><h1>Add User</h1>
<form method="post" action="prefs.php" style="border: 1px solid black; margin: 10px; padding: 10px;">
Username: <input type=string name=username> Password: <input type=string name=password> <input type=submit name=adduser value="Add user">
</form>

<br><h1>Delete user</h1>
<form method="post" action="prefs.php" style="border: 1px solid black; margin: 10px; padding: 10px;" onsubmit="return confirm('Are you sure?')">
<select name=username>
<?php
	$result = fof_do_query("select user_name from $FOF_USER_TABLE where user_id > 1");
	
	while($row = mysql_fetch_array($result))
	{
		$username = $row['user_name'];
		echo "<option value=$username>$username</option>";
	}
?>

</select> <input type=submit name=deleteuser value="Delete user"><br>
</form>

<br><h1>Admin Options</h1>
<form method="post" action="prefs.php" style="border: 1px solid black; margin: 10px; padding: 10px;">
No Admin options yet!<br><br>
<input type=submit name=options value="Save Options">
</form>


<?php } ?>

<?php include("footer.php") ?>
