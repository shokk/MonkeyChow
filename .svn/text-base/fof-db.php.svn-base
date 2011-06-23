<?php
/*
 * This file is part of FEED ON FEEDS - http://feedonfeeds.com/
 *
 * db.php - (nearly) all of the DB specific code
 *
 *
 * Copyright (C) 2004-2007 Stephen Minutillo
 * steve@minutillo.com - http://minutillo.com/steve/
 *
 * Distributed under the GPL - see LICENSE
 *
 */

require_once("init.php");

$FOF_FEED_TABLE = FOF_FEED_TABLE;
$FOF_ITEM_TABLE = FOF_ITEM_TABLE;
$FOF_ITEM_TAG_TABLE = FOF_ITEM_TAG_TABLE;
$FOF_SUBSCRIPTION_TABLE = FOF_SUBSCRIPTION_TABLE;
$FOF_TAG_TABLE = FOF_TAG_TABLE;
$FOF_USER_TABLE = FOF_USER_TABLE;

function fof_db_get_row($result)
{
    return mysql_fetch_array($result);
}

function db_save_prefs($user_id, $prefs)
{
   global $FOF_USER_TABLE, $fof_connection, $fof_user_id, $fof_user_name, $fof_user_level, $fof_user_prefs;
   
   $prefs = mysql_escape_string(serialize($prefs));
   
   $sql = "update $FOF_USER_TABLE set user_prefs = '$prefs' where user_id = $user_id";
   
   fof_do_query($sql);
}

function fof_db_authenticate($user_name, $user_password_hash)
{
   global $FOF_USER_TABLE, $FOF_ITEM_TABLE, $FOF_ITEM_TAG_TABLE, $fof_connection, $fof_user_id, $fof_user_name, $fof_user_level, $fof_user_prefs;

   $sql = "select * from $FOF_USER_TABLE where user_name = '$user_name' and md5(user_password) = '" . mysql_escape_string($user_password_hash) . "'";

   $result = fof_do_query($sql);

    if(mysql_num_rows($result) == 0)
    {
        return false;
    }

    $row = mysql_fetch_array($result);

    $fof_user_name = $row['user_name'];
    $fof_user_id = $row['user_id'];
    $fof_user_level = $row['user_level'];
    $fof_user_prefs = unserialize($row['user_prefs']);

    if(!is_array($fof_user_prefs)) $fof_user_prefs = array();
    if(!isset($fof_user_prefs['favicons'])) $fof_user_prefs['favicons'] = false;
    if(!isset($fof_user_prefs['keyboard'])) $fof_user_prefs['keyboard'] = false;
    if(!isset($fof_user_prefs['frames'])) $fof_user_prefs['frames'] = false;

   return true;
}

function fof_db_get_subscriptions($user_id)
{
   global $FOF_FEED_TABLE, $FOF_ITEM_TABLE, $FOF_SUBSCRIPTION_TABLE, $FOF_ITEM_TAG_TABLE;

   return(fof_do_query("select * from $FOF_FEED_TABLE, $FOF_SUBSCRIPTION_TABLE where $FOF_SUBSCRIPTION_TABLE.user_id = $user_id and $FOF_FEED_TABLE.feed_id = $FOF_SUBSCRIPTION_TABLE.feed_id order by feed_title"));
}

function fof_db_add_subscription($user_id, $feed_id)
{
   global $FOF_FEED_TABLE, $FOF_ITEM_TABLE, $FOF_SUBSCRIPTION_TABLE, $fof_connection;

   $sql = "insert into $FOF_SUBSCRIPTION_TABLE (feed_id, user_id) values ($feed_id, $user_id)";

   fof_do_query($sql);
}

function fof_db_delete_subscription($user_id, $feed_id)
{
   global $FOF_FEED_TABLE, $FOF_ITEM_TABLE, $FOF_SUBSCRIPTION_TABLE, $fof_connection;

   $sql = "delete from $FOF_SUBSCRIPTION_TABLE where feed_id = $feed_id and user_id = $user_id";

   fof_do_query($sql);
}

function fof_db_get_subscribed_users($feed_id)
{
    global $FOF_SUBSCRIPTION_TABLE;

    return(fof_do_query("select user_id from $FOF_SUBSCRIPTION_TABLE where $FOF_SUBSCRIPTION_TABLE.feed_id = $feed_id"));
}

function fof_db_is_subscribed($user_id, $feed_url)
{
    global $FOF_FEED_TABLE, $FOF_ITEM_TABLE, $FOF_SUBSCRIPTION_TABLE, $FOF_ITEM_TAG_TABLE;

    $safeurl = mysql_escape_string( $feed_url );
    $result = fof_do_query("select $FOF_SUBSCRIPTION_TABLE.feed_id from $FOF_FEED_TABLE, $FOF_SUBSCRIPTION_TABLE where feed_url='$safeurl' and $FOF_SUBSCRIPTION_TABLE.feed_id = $FOF_FEED_TABLE.feed_id and $FOF_SUBSCRIPTION_TABLE.user_id = $user_id");

    if(mysql_num_rows($result) == 0)
    {
        return false;
    }

    return true;
}

?>
