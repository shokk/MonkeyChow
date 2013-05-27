<?php
/*
 * This file is part of Monkeychow - http://shokk.wordpress.com/tag/monkeychow/
 *
 * delete.php - deletes a feed and all items
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

include_once("init.php");
include_once("fof-main.php");

header("Content-Type: text/html; charset=utf-8");

?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">

<head><title>MonkeyChow - delete</title>
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
		<link rel="stylesheet" href="fof-common.css" media="all" />
		<script src="fof.js" type="text/javascript"></script>
		<meta name="ROBOTS" content="NOINDEX, NOFOLLOW" />
</head>
<body>

<?php

$feed = $_REQUEST['feed'];
$framed = $_REQUEST['framed'];
$FOF_FEED_TABLE = FOF_FEED_TABLE;
$FOF_ITEM_TABLE = FOF_ITEM_TABLE;
$FOF_USERITEM_TABLE = FOF_USERITEM_TABLE;

if(fof_is_admin())
{
	// should remove all items from a feed from user_items list!!	
	// but only if there are no starred items
	// if there are starred items, dump out

	// remove feed from system and everyone's subscriptions list
	fof_do_query("delete from " . $FOF_FEED_TABLE . " where id = $feed");
	fof_do_query("delete from " . $FOF_ITEM_TABLE . " where feed_id = $feed");
	$sql = "delete from " . $FOF_SUBSCRIPTION_TABLE . " where feed_id = $feed; ";

	$result_text= _("Deleted.");
}
else
{
	// remove from my subscription list
	$sql = "delete from " . $FOF_SUBSCRIPTION_TABLE . " where feed_id = " . $feed . " AND user_id=" . current_user();
	$result_text= _("Unsubscribed.");
}

#echo $sql;

$result = fof_do_query($sql);


if (eregi("feeds.php",$_SERVER['HTTP_REFERER']))
{
    echo $result_text . "  <a href=\"";
    echo ($framed) ? "framesview.php" : "index.php";
    echo ($framed) ? "?framed=yes" : "";
    echo "\">" . _("Return to new items") . "</a>";
}
else
{
   echo $_REQUEST['ref'] . ":";
   echo $result_text . "  <a href=\"index.php\">Return to new items.</a>";
}
?>

</body></html>
