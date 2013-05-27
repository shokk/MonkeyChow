<?php
/*
 * This file is part of Monkeychow - http://shokk.wordpress.com/tag/monkeychow/
 *
 * mark-read.php - marks a single item or all items in a feed as read
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

$feed = $_REQUEST['feed'];
$item = $_REQUEST['item'];
$tags = $_REQUEST['tags'];
$order = $_REQUEST['order'];
$direction = $_REQUEST['direction'];

# building this query
#$sql = "insert $FOF_ITEM_TABLE set flag_id=1,user_id=" . current_user(); //, timestamp=timestamp ";

if($feed)
{
	$sql = "insert into $FOF_USERITEM_TABLE (user_id,flag_id,item_id) VALUES "; //, timestamp=timestamp ";
	$id_list="";
	$sql1 = "select id as item_id from " . $FOF_ITEM_TABLE . " WHERE feed_id=" . $feed . " and NOT (id IN (SELECT item_id FROM $FOF_USERITEM_TABLE WHERE user_id=" . current_user() . " AND flag_id=1))";
echo "$sql1";
	$result1=fof_do_query($sql1);
    while($row = mysql_fetch_array($result1))
	{
		if ($id_list=="")
		{
				$id_list = $row['item_id'];
		}
		else
		{
				$id_list .= "," . $row['item_id'];
		}
	}

	$value_list="";
	$sql2 = "SELECT id item_id from $FOF_ITEM_TABLE WHERE feed_id=" . $feed . " and NOT (id IN (" . $id_list . "))";
	$result2=fof_do_query($sql1);
    while($row = mysql_fetch_array($result2))
	{
		if ($value_list=="")
		{
			$value_list = "(" . current_user() . ",1," . $row['item_id'] . ")";
		}
		else
		{
			$value_list .= ",(" . current_user() . ",1," . $row['item_id'] . ")";
		}
	}

	$sql .= $value_list;
	//$sql .= ", item_id=(SELECT id from items WHERE feed_id=" . $feed . " and NOT (id IN (" . $id_list . ")))";

	//$sql .= " WHERE item_id IN (SELECT id FROM " . $FOF_ITEM_TABLE . " WHERE feed_id=" . $feed . ") AND user_id=" . current_user() . " AND flag_id!=1";
	//$sql .= " AND item_id NOT IN (SELECT item_id FROM $FOF_ITEM_TABLE WHERE user_id=" . current_user() . " AND flag_id=1)";
}
else if($item)
{
	$sql = "insert $FOF_ITEM_TABLE set flag_id=1,user_id=" . current_user(); //, timestamp=timestamp ";
	$sql .= " where `item_id` = " . $item;
}

//echo $sql;
$result = fof_do_query($sql);

if($item)
{
	header("Status: 204 Yatta");
}
else
{
	    if (($tags) && ($tags != _("All tags")) && ($tags != _("No tags")))
	    {
	        $mods .= ($_REQUEST['tags']) ? "tags=" . $tags . "&" : "";
	    }
		$mods .= ($_REQUEST['framed'])? "framed=yes" : "" ;
		$mods .= ($_REQUEST['newonly'])? "&newonly=yes" : "" ;
		if (isset($order))
		{
			$mods .= ($_REQUEST['order']) ? "&order=" . $order : "";
	    }
	    if (isset($direction))
	    {
            $mods .= ($_REQUEST['direction']) ? "&direction=" . $direction : "";
	    }

		Header("Location: " . dirname($_SERVER['PHP_SELF']) . "/feeds.php?" . $mods);
}

?>
