<?php
/*
 * This file is part of Monkeychow - http://monkeychow.org
 *
 * aggregator.php - frames based viewer
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
header("Content-Type: text/html; charset=utf-8");

$which = 0;
$howmany = 50;
$order = 'desc';

$title = "My Aggregated Feeds by";

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">

	<head>
		<title><?php echo _("$title") ?> MonkeyChow</title>
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
		<link rel="stylesheet" href="fof-common.css" media="all" />
		<script src="fof.js" type="text/javascript"></script>
                <script src="behindthescenes.js"></script>
		<meta name="ROBOTS" content="NOINDEX, NOFOLLOW" />
	</head>
<body onload="parent.menu.location.reload();">

<p><?php echo _("$title") ?> <a href="http://www.monkeychow.org">MonkeyChow</a> </p>

<div id="items">


		<form name="items" action="view-action.php" method="post">
		<input type="hidden" name="action" id="action" />
		<input type="hidden" name="return" id="return" />

<?php

$sql="select 'items.read' as 'item_read', 'feeds.title' as 'feed_title', 'feeds.link' as 'feed_link', feeds.description as feed_description, items.id as item_id, items.link as item_link, items.title as item_title, UNIX_TIMESTAMP(items.timestamp) as timestamp, items.content as item_content, items.dcdate as dcdate, items.dccreator as dccreator, items.dcsubject as dcsubject, items.publish as item_publish, items.star as item_star from feeds, items where items.feed_id=feeds.id and items.publish=1 and feeds.private=0 order by timestamp desc limit 15";

$result = fof_do_query($sql);

#foreach($result as $row)
while($row = mysql_fetch_array($result))
{
	$items = true;

	$feed_link = htmlspecialchars($row['feed_link']);
	$feed_title = htmlspecialchars($row['feed_title']);
	$feed_description = htmlspecialchars($row['feed_description']);
	$item_id = $row['item_id'];
	$item_link = htmlspecialchars($row['item_link']);
	$item_title = htmlspecialchars($row['item_title']);
	$item_content = $row['item_content'];
	$item_read = $row['item_read'];
	$item_publish = $row['item_publish'];
	$item_star = $row['item_star'];
	$timestamp =  date("F j, Y, g:i a", $row['timestamp'] - (FOF_TIME_OFFSET * 60 * 60));
	$dccreator = $row['dccreator'];
	$dcdate = $row['dcdate'];
	$dcsubject = $row['dcsubject'];

	print "\n".'<div class="item">';
	print '<div class="headeragg">';

        print "<a href=\"$item_link\" rel=\"nofollow\">$item_title</a><br />";
        print "<a href=\"$feed_link\" title=\"$feed_description\" rel=\"nofollow\">$feed_title</a><br /><br />";
        print "<tr bgcolor=\"#dddddd\">";

	if($dccreator)
	{
		print "by $dccreator ";
	}

	if($dcsubject)
	{
		print "on $dcsubject ";
	}

	if($dcdate)
	{
				#$dcdate = date("F j, Y, g:i a", parse_w3cdtf($dcdate) + $asec - (FOF_TIME_OFFSET * 60 * 60));

		print "at $dcdate ";
	}
	print "(cached at $timestamp)";

	print "</div><div class=\"bodyagg\">$item_content</div><div class=\"clearer\"></div></div>";
}

if(!$items)
{
echo "<p>" . _("No items found") . ".</p>";
}

?>
		</form>
<?php
	if($links)
	{
?>
		<center><?php echo $links ?></center>

<?php
	}
?>





</div>
<script type="text/javascript" src="http://del.icio.us/js/playtagger"></script>
</body>

</html>
