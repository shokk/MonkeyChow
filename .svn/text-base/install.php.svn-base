<?php
/*
 * This file is part of Monkeychow - http://monkeychow.org
 *
 * install.php - creates tables and cache directory, if they don't exist
 *
 *
 * Copyright (C) 2006 Ernie Oporto
 * ernieoporto@yahoo.com - http://www.shokk.com/blog/
 *
 * Copyright (C) 2004 Stephen Minutillo
 * steve@minutillo.com - http://minutillo.com/steve/
 *
 * Distributed under the GPL - see LICENSE
 *
 */

$installing = true;
$fof_no_login = true;

include_once("config.php");
include_once("init.php");
include_once("fof-main.php");

header("Content-Type: text/html; charset=utf-8");

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">

<head><title>MonkeyChow - <?php echo _("installation") ?></title>
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
        <link rel="stylesheet" href="fof-common.css" media="all" />
		<script src="fof.js" type="text/javascript"></script>
		<meta name="ROBOTS" content="NOINDEX, NOFOLLOW" />
</head>

<body id="panel-page">

<?php
if($_GET['password'])
{
    $password = mysql_real_escape_string($_GET['password']);

    fof_do_query("insert into $FOF_USER_TABLE (user_id, user_name, user_password, isadmin) values (1, 'admin', '$password', 1)");

	    echo 'OK!  Setup complete! <a href=".">Login as admin</a>, and start subscribing!';
		echo 'Please rename install.php to install.php.not';
	}
else
{
?>

<?php echo _("Creating tables"); ?>...<br />
<?php

$tables[] = <<<EOQ
CREATE TABLE IF NOT EXISTS `$FOF_FEED_TABLE` (
  `id` int(11) NOT NULL auto_increment,
  `url` varchar(250) NOT NULL default '',
  `title` varchar(250) NOT NULL default '',
  `link` varchar(250) default NULL,
  `description` varchar(250) default NULL,
  `date_added` timestamp NOT NULL,
  `tags` varchar(250) default NULL,
  `aging` int(11) NOT NULL default '60',
  `expir` int(11) NOT NULL default '0',
  `private` bool default '0',
  `image` text NOT NULL,
  PRIMARY KEY  (`id`)

) ENGINE=MyISAM CHARACTER SET utf8;
EOQ;

$tables[] = <<<EOQ
CREATE TABLE IF NOT EXISTS `$FOF_ITEM_TABLE` (
  `id` int(11) NOT NULL auto_increment,
  `feed_id` int(11) NOT NULL default '0',
  `timestamp` timestamp NOT NULL,
  `link` text,
  `title` varchar(250) default NULL,
  `content` text,
  `dcdate` text,
  `dccreator` text,
  `dcsubject` text,
  `read` tinyint(4) default NULL,
  `publish` bool default '0',
  `star` bool default '0',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM CHARACTER SET utf8;
EOQ;

$tables[] = <<<EOQ
CREATE TABLE IF NOT EXISTS `$FOF_USER_TABLE` (
  `user_id` int(11) NOT NULL auto_increment,
  `user_name` varchar(100) NOT NULL default '', 
  `user_password` varchar(32) NOT NULL default '', 
  `user_level` enum('user','admin') NOT NULL default 'user',
  `user_prefs` text,
  `firstname` varchar(50) NOT NULL default '',
  `lastname` varchar(50) NOT NULL default '',
  `email` varchar(200) NOT NULL default '',
  `isactive` int(11) NOT NULL default '0',
  `isadmin` int(11) NOT NULL default '0',
  PRIMARY KEY  (`user_id`)
) ENGINE=MyISAM CHARACTER SET utf8;
EOQ;


$tables[] = <<<EOQ
CREATE TABLE IF NOT EXISTS `$FOF_SUBSCRIPTION_TABLE` (
  `feed_id` int(11) NOT NULL default '0',
  `user_id` int(11) NOT NULL default '0',
  `subscription_prefs` text,
  PRIMARY KEY  (`feed_id`,`user_id`)
) ENGINE=MyISAM CHARACTER SET utf8;
EOQ;

foreach($tables as $table)
{
    if(!fof_do_query($table, 1))
    {
        exit ("Can't create table.  MySQL says: <b>" . mysql_error() . "</b><br>" );
    }
}

echo _("Tables exist.");
?><br /><br />

<?php 
  echo _("Creating indexes");
?>...<br />

<?php
if(!fof_do_query("ALTER TABLE `items` ADD INDEX `feed_id_idx` ( `feed_id` )", 1) && mysql_errno() != 1061)
{
	exit (_("Can't create index.") . "  " . _("Mysql says") . ": <b>" . mysql_error() . "</b><br />");
}

if(!fof_do_query("ALTER TABLE `items` ADD INDEX `read_idx` ( `read` )", 1) && mysql_errno() != 1061)
{
	exit (_("Can't create index.") . "  " . _("Mysql says") . ": <b>" . mysql_error() . "</b><br />");
}

echo _("Indexes exist.");
?><br /><br />

<?php echo _("Checking cache directory"); ?>...<br />
<?php

if ( ! file_exists( "cache" ) )
{
	$status = @mkdir( "cache", 0755 );

	if ( ! $status )
	{
		echo _("Can't create directory") . " <code>" . getcwd() . "/cache/</code>.<br />" . _("You will need to create it yourself, and make it writable by your PHP process.") . " <br />" . _("Then, reload this page.");
		exit;
	}
}

if(!is_writable( "cache" ))
{
		echo _("The directory") . " <code>" . getcwd() . "/cache/</code> " . _("exists, but is not writable.") . "<br />" . _("You will need to make it writeable by your PHP process.") . "<br />" . _("Then, reload this page.");
		exit;
}

echo _("Cache directory exists and is writable.") ?><br /><br />

<?php

echo _("Encodings will be translated by:");

if ( substr(phpversion(),0,1) == 5)
{
	echo "<b>PHP5 XML parser</b>.  " . _("We're going to try to use the XML parser itself to handle encodings") . ".<br /><br />";
}
else 
{
	if(function_exists('iconv'))
	{
		echo '<b>iconv</b>.  ' . _("You have PHP4, and the ") . '<a href="http://us4.php.net/manual/en/ref.iconv.php">iconv module</a> '  . _("installed") . '.<br /><br />';
	}
	else if(function_exists('mb_convert_encoding'))
	{
		echo '<b>mbstring</b>.  ' . _("You have PHP4, and the ") . '<a href="http://us4.php.net/manual/en/ref.mbstring.php">mbstring module</a> '  . _("installed") . '.<br /><br />';
	}
	else
	{
		echo '<b>PHP4 XML parser</b>.  ' . _("You have PHP4, but neither iconv nor mbstring is intalled.  Only UTF-8, ISO-8859-1, and US-ASCII feeds are going to work.  Ask your host to install iconv and mbstring for best results.") . '<br /><br />';
	}
        if (function_exists("gettext"))
        {
            echo _("You have ") . "<b>gettext</b> " . _("installed") . ".<br /><br />";
        }
        else
        {
            echo _("You don't have ") . "<b>gettext</b> " . _("installed") . ".<br /><br />";
            echo _("It is recommended that you configure PHP adding --with-gettext.") . "<br /><br />";
        }
}

		    $result = fof_do_query("select * from $FOF_USER_TABLE where user_name = 'admin'");
    if(mysql_num_rows($result) == 0) {
			?>

You now need to chose a password for the 'admin' account.<br><br>

Password: <form><input type=string name=password><input type=submit></form>

<?php } else { ?>

'admin' account already exists.<br><br>
<?php
echo _("Setup complete!") ?><br><a href=".">Login as admin</a>, to <?php echo _("Go to the control pan    el and start subscribing.") ?>

<?php } } ?>

</body></html>
