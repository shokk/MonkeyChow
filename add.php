<?php
/*
 * This file is part of Monkeychow - http://shokk.wordpress.com/tag/monkeychow/
 *
 * add.php - displays form to add a feed
 *
 * Copyright (C) 2006 Ernie Oporto
 * ernieoporto@yahoo.com - http://shokk.wordpress.com
 * 
 * Copyright (C) 2004 Stephen Minutillo
 * steve@minutillo.com - http://minutillo.com/steve/
 *
 * Distributed under the GPL - see LICENSE
 *
 */

include_once("init.php");
include_once("fof-main.php");

$ipod_font=8;
$nonipod_font=2;

header("Content-Type: text/html; charset=utf-8");
 
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html>
<head><title>MonkeyChow - <?php echo _("Add a feed") ?></title>
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
                <link rel="stylesheet" href="fof-common.css" media="all" />
		<script src="fof.js" type="text/javascript"></script>
		<meta name="ROBOTS" content="NOINDEX, NOFOLLOW" />
</head>

<body id="panel-page">
<?php
	if (!preg_match("/(wap|midp|cldc|mmp|Symbian|Smartphone|iPhone|WebKit.*Mobile)/si",$_SERVER[HTTP_USER_AGENT])) 
{
?>
<script type="text/javascript" />
if (top==self) document.writeln('<?php 
$handle = fopen("panel-menu.html", "r");
   $xml = "";
   if ($handle)
   {
       while (!feof($handle))
       {
           $xml .= fread($handle, 128);
       }
       fclose($handle);
   }
   $xml = substr("$xml", 0, -1); // Perl style chop
   print $xml;
?>');
</script>
<?php 
}

$url = $_REQUEST['rss_url'];
$opml = $_REQUEST['opml_url'];
$file = $_REQUEST['opml_file'];
	if (!preg_match("/(wap|midp|cldc|mmp|Symbian|Smartphone|iPhone|WebKit.*Mobile)/si",$_SERVER[HTTP_USER_AGENT])) 
{
?>

<br /><br />
<table border=1 cellpadding=3 cellspacing=0 bgcolor="#EEEEEE"><tr><td>
<a href="javascript:void(location.href='http://<?php echo $_SERVER["HTTP_HOST"] . $_SERVER["SCRIPT_NAME"] ?>?rss_url='+escape(location))">Monkeychow <?php echo _("subscribe") ?></a> - <?php echo _("This bookmarklet will attempt to subscribe to whatever page you are on.") ?><br /><?php echo _("Drag it to your toolbar and then click on it when you are at a weblog you like.") ?>
</tr></td>
<tr><td>
<?php 
if (ereg('Firefox/2.0', $_SERVER['HTTP_USER_AGENT'])) {
    echo _("Firefox 2.0 detected!  Click the following to add MonkeyChow to your Firefox 2.0 Feed Reader list!");
}
if (ereg('Firefox/3.0', $_SERVER['HTTP_USER_AGENT'])) {
    echo _("Firefox 3.0 detected!  Click the following to add MonkeyChow to your Firefox 3.0 Feed Reader list!");
}
?>
<br /><a href="" onclick="navigator.registerContentHandler('application/vnd.mozilla.maybe.feed','http://<?php echo $_SERVER["HTTP_HOST"] . dirname($_SERVER["SCRIPT_NAME"]) ?>/add.php?rss_url=%s','MonkeyChow');">Add MonkeyChow to Feed Reader List</a>
</tr></td></table>

<?php
}
?>
<br /><br />
<form method="post" action="add.php" enctype="multipart/form-data">
<input type="hidden" name="MAX_FILE_SIZE" value="100000">
<li><font size="<?php 
		echo (preg_match("/(wap|midp|cldc|mmp|Symbian|Smartphone|iPhone|WebKit.*Mobile)/si",$_SERVER[HTTP_USER_AGENT])) ?  "$ipod_font":  "$nonipod_font";
?>"><?php echo _("RSS or weblog URL"); ?>: <input type="text" name="rss_url" size="40" value="<?php echo $url ?>"<?php
		if (preg_match("/(wap|midp|cldc|mmp|Symbian|Smartphone|iPhone|WebKit.*Mobile)/si",$_SERVER[HTTP_USER_AGENT])) 
		{
				echo " style=\"font-size: 36px;\"";
		}
?>
>
<input type="Submit" value="<?php echo _("Add a feed") ?>"<?php
		if (preg_match("/(wap|midp|cldc|mmp|Symbian|Smartphone|iPhone|WebKit.*Mobile)/si",$_SERVER[HTTP_USER_AGENT])) 
		{
				echo " style=\"font-size: 36px;\"";
		}
?>
><br /><br />
</font></li>
</form>

<?php
	if (!preg_match("/(wap|midp|cldc|mmp|Symbian|Smartphone|iPhone|WebKit.*Mobile)/si",$_SERVER[HTTP_USER_AGENT])) 
{
?>
<form method="post" action="add.php" enctype="multipart/form-data">
<?php echo _("OPML URL") ?>: <input type="hidden" name="MAX_FILE_SIZE" value="100000">

<input type="text" name="opml_url" size="40" value="<?php echo $opml ?>"><input type="Submit" value="<?php echo _("Add feeds from OPML file on the Internet") ?>"><br /><br />
</form>

<form method="post" action="add.php" enctype="multipart/form-data">
<input type="hidden" name="MAX_FILE_SIZE" value="100000">
<?php echo _("OPML filename"); ?>: <input type="file" name="opml_file" size="40" value="<?php echo $file ?>"><input type="Submit" value="<?php echo _("Upload an OPML file") ?>">

</form>
Note that anyone with a Google Reader account can use Google Takeout to download a zip file of their settings, which includes a Subscriptions.XML file that can be used as an OPML file above.
<?php
}

if($url) fof_add_feed($url);

if($opml)
{
	if(!$content_array = file($opml))
	{
		echo _("Cannot open") . ": $opml <br />";
		return false;
	}

	$content = implode("", $content_array);

	$feeds = fof_opml_to_array($content);
}

if($_FILES['opml_file']['tmp_name'])
{
	if(!$content_array = file($_FILES['opml_file']['tmp_name']))
	{
		echo _("Cannot open uploaded file") . "<br />";
		return false;
	}

	$content = implode("", $content_array);

	$feeds = fof_opml_to_array($content);
}

foreach ($feeds as $feed)
{
	fof_add_feed($feed);
	echo "<hr size=1>";
	flush();
}

?>
<BR>
<?php
	if (!preg_match("/(wap|midp|cldc|mmp|Symbian|Smartphone|iPhone|WebKit.*Mobile)/si",$_SERVER[HTTP_USER_AGENT])) 
{
?>
<script type="text/javascript" />
if (top==self) document.writeln('<?php 
$handle = fopen("panel-menu.html", "r");
   $xml = "";
   if ($handle)
   {
       while (!feof($handle))
       {
           $xml .= fread($handle, 128);
       }
       fclose($handle);
   }
   $xml = substr("$xml", 0, -1); // Perl style chop
   print $xml;
?>');
<?php 
}
else
{
?><li><font size="<?php 
		echo (preg_match("/(wap|midp|cldc|mmp|Symbian|Smartphone|iPhone|WebKit.*Mobile)/si",$_SERVER[HTTP_USER_AGENT])) ?  "$ipod_font":  "$nonipod_font";
		echo "\">";
		echo "<a href=\"" . $_SERVER['HTTP_REFERER'] . "\">Return</a>";
		echo "</font>";
}
?>
</script>

</body>
</html>
