<?php
/*
 * This file is part of Monkeychow - http://monkeychow.org
 *
 * view.php - views items based on query parameters
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

include_once("fof-main.php");
include_once("init.php");

header("Content-Type: text/html; charset=utf-8");

if($_REQUEST['how'] == 'paged' && !isset($_REQUEST['which']))
{
	$which = 0;
} else {
	$which = $_REQUEST['which'];
}

$title = fof_view_title($_REQUEST['feed'], $_REQUEST['what'], $_REQUEST['when'], $which, $_REQUEST['howmany']);
$noedit = $_REQUEST['noedit'];
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">

<head>
	<title><?php echo strip_tags($title) ?></title>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<link rel="stylesheet" href="fof-common.css" media="all" />
	<script src="fof.js" type="text/javascript"></script>
	<script src="behindthescenes.js"></script>
	<meta name="ROBOTS" content="NOINDEX, NOFOLLOW" />
</head>

<body id="view-page">

<?php

if(!$noedit)
{
?>
		<form name="items" action="view-action.php" method="post">
		<input type="hidden" name="action" id="action" />
		<input type="hidden" name="return" id="return" />

		<?php readfile("view-menu.html"); ?>

<?php
	$links = fof_get_nav_links($_REQUEST['feed'], $_REQUEST['what'], $_REQUEST['when'], $which, $_REQUEST['howmany']);

	if($links)
	{
?>
		<div class="nav"><?php echo $links ?></div>


<?php
	}
}


$result = fof_get_items($_REQUEST['feed'], $_REQUEST['what'], $_REQUEST['when'], $which, $_REQUEST['howmany']);

foreach($result as $row)
{
	$items = true;

	$feed_link = htmlspecialchars($row['feed_link']);
	$feed_title = htmlspecialchars($row['feed_title']);
	$feed_description = htmlspecialchars($row['feed_description']);
	$item_id = $row['item_id'];
	$item_link = htmlspecialchars($row['item_link']);
	#$item_title = htmlspecialchars($row['item_title']);
	$item_title = $row['item_title'];
	$item_content = fof_balanceTags($row['item_content']);
	$item_read = $row['item_read'];
        $item_publish = $row['item_publish'];
	$timestamp =  date("F j, Y, g:i a", $row['timestamp'] - (FOF_TIME_OFFSET * 60 * 60));
	$dccreator = $row['dccreator'];
	$dcdate = $row['dcdate'];
	$dcsubject = $row['dcsubject'];


	print '<div class="item">';
	print '<div class="header">';

	print "<a class=\"headline\" href=\"$item_link\">$item_title</a> ";
	print "<a class=\"feed\" href=\"$feed_link\" title=\"$feed_description\">$feed_title</a>";

	print '<span class="meta">';

	if($dccreator)
	{
		print _("by") . " $dccreator <br />";
	}

	if($dcsubject)
	{
		print "<i>" . _("on") . " $dcsubject </i><br />";
	}

	if($dcdate)
	{
	#			$dcdate = date("F j, Y, g:i a", parse_w3cdtf($dcdate) + $asec - (FOF_TIME_OFFSET * 60 * 60));
		print _("at") . " $dcdate ";
	}
	print "(" . _("cached at") . " $timestamp)</span>";


	if(!$noedit)
	{
		echo ' <span class="controls">';
		print "<font size=1><a href=\"javascript:flag_upto('c$item_id')\">" . _("flag all up to this item") . "</a></font> ";
		print "<input type=\"checkbox\" name=\"c$item_id\" value=\"checked\" />";
		echo '</span>';
	}
        print "<tr bgcolor=\"#dddddd\">";
        print "<td colspan=\"2\">";
        if ( $item_publish ) {
            $checked = "checked=\"checked\"";
        }
         else {
            $checked = "";
        }
        print _("Recycle") . ": <input type=\"checkbox\" name=\"pub$item_id\" onclick=\"togglePublish(this)\" value=\"$item_id\" $checked>";
        print "</td></tr>";


	print "<div class=\"clearer\"></div><br/></div><div class=\"body\">$item_content</div></div>";
}

if(!$items)
{
	echo "<p>" . _("No items found") . ".</p>";
}

if(!$noedit)
{
?>
		</form>
<?php
	if($links)
	{
?>
		<div class="nav"><?php echo $links ?></div>

<?php
	}

	readfile("view-menu.html");
}
?>
<script type="text/javascript" src="http://del.icio.us/js/playtagger"></script>
</body>
</html>
