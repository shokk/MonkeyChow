<?php
/*
 * This file is part of Monkeychow - http://monkeychow.org
 *
 * mark-read.php - marks a single item or all items in a feed as read
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

include_once("init.php");
include_once("fof-main.php");

header("Content-Type: text/html; charset=utf-8");

$feed = $_REQUEST['feed'];
$item = $_REQUEST['item'];
$tags = $_REQUEST['tags'];
$order = $_REQUEST['order'];
$direction = $_REQUEST['direction'];

$sql = "update items set `read` = 1, timestamp=timestamp ";

if($feed)
{
	$sql .= 'where `feed_id` = ' . $feed;
}
else if($item)
{
	$sql .= 'where `id` = ' . $item;
}

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
