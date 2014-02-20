<?php
/*
 * This file is part of Monkeychow - http://shokk.wordpress.com/tag/monkeychow/
 *
 * config.php - modify this file with your database settings
 *
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


// Difference, in hours, between your server and your local time zone.

define('FOF_TIME_OFFSET', 0);
define('COOKIE_DIR', "/");
define('MC_PATH', "/monkeychow");

######################################################################
# Database connection information.  Host, username, password, database name.
######################################################################
define('FOF_DB_HOST', "localhost");
define('FOF_DB_USER', "db_username");
define('FOF_DB_PASS', "db_password");
define('FOF_DB_DBNAME', "feeds");

define('FOF_DB_PREFIX', "");
define('FOF_FEED_TABLE', FOF_DB_PREFIX . "feeds");
define('FOF_ITEM_TABLE', FOF_DB_PREFIX . "items");
define('FOF_SUBSCRIPTION_TABLE', FOF_DB_PREFIX . "subscriptions");
define('FOF_FLAG_TABLE', FOF_DB_PREFIX . "flags");
define('FOF_USER_TABLE', FOF_DB_PREFIX . "users");
define('FOF_USERITEM_TABLE', FOF_DB_PREFIX . "user_items");

######################################################################
# PREFS
######################################################################
# these items shoud be moved into user prefs in the database
define('RE_CHANNEL_URL', "http://www.site.com/news/rss.php");
define('RE_CHANNEL_TITLE', "Recycled RSS items");
define('RE_CHANNEL_DESC', "Blah");
define('REPUBLISH_CHANNEL_URL', "http://www.site.com/news/rss.php");
define('REPUBLISH_CHANNEL_TITLE', "Blogroll");
define('REPUBLISH_CHANNEL_DESC', "Something for people to read.");
define('DELICIOUS_USERNAME',"yummy");
define('FULLNAME',"My Name");
define('EMAIL',"email@x.com");

// How many posts to show by default in paged mode
define('FOF_HOWMANY', 50);
define('FB_APP_ID', "");
define('FB_APP_SECRET', "");
define('FB_CALLBACK', "http://" . $_REQUEST['baserequest'] . "/auth/facebook.php");
define('BUFFER_APP_ID','');
define('BUFFER_APP_SECRET','');
define('BUFFER_CALLBACK', "http://" . $_REQUEST['baserequest'] . "/auth/buffer.php");
define('TWITTER_APP_ID','');
define('TWITTER_APP_SECRET','');
define('TWITTER_CALLBACK', "http://" . $_REQUEST['baserequest'] . "/auth/twitter.php");
define('LINKEDIN_APP_ID',"");
define('LINKEDIN_APP_SECRET',"");
define('LINKEDIN_CALLBACK', "http://" . $_REQUEST['baserequest'] . "/auth/linkedin.php");
define('GOOGLE_APP_ID',"");
define('GOOGLE_APP_SECRET',"");
define('GOOGLE_CALLBACK', "http://" . $_REQUEST['baserequest'] . "/auth/google.php");
######################################################################
# END PREFS
######################################################################


######################################################################
# DO NOT CHANGE ANYTHING BELOW THIS LINE
######################################################################
define('FOF_QUERY_LOG', false);

// Find ourselves and the cache dir
// This relies on PHP to determine this from the OS
if (!defined('DIR_SEP')) {
	define('DIR_SEP', DIRECTORY_SEPARATOR);
}

if (!defined('FOF_DIR')) {
    define('FOF_DIR', dirname(__FILE__) . DIR_SEP);
}

if (!defined('FOF_CACHE_DIR'))
{
    define('FOF_CACHE_DIR', FOF_DIR . DIR_SEP . "cache");
}
######################################################################
# END OF DO NOT CHANGE
######################################################################
?>
