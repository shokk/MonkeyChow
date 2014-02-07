<?php
/*
 * This file is part of Monkeychow - http://shokk.wordpress.com/tag/monkeychow/
 *
 * view.php - frames based viewer
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

#fof_prune_expir_feeds();
#flush();

if($_REQUEST['how'] == 'paged' && !isset($_REQUEST['which']))
{
	$which = 0;
}
else
{
	$which = $_REQUEST['which'];
}
$mobiletrue = $_REQUEST['mobiletrue'];
if (preg_match("/(wap|midp|cldc|mmp|Symbian|Smartphone|iPhone|WebKit.*Mobile)/si",$_SERVER[HTTP_USER_AGENT])) {
		$mobiletrue = "yes";
}

$order = isset($_REQUEST['order']) ? $_REQUEST['order'] : "desc";

$feed = $_REQUEST['feed'];
$framed = $_REQUEST['framed'];
$how = $_REQUEST['how'];
$what = $_REQUEST['what'];
$when = $_REQUEST['when'];
$howmany = $_REQUEST['howmany'];
$search = htmlspecialchars($_REQUEST['search']);
$tags = $_REQUEST['tags'];

$title = fof_view_title($feed, $what, $when, $which, $howmany);
$noedit = $_GET['noedit'];
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">

<head>
	<title><?php echo strip_tags($title) ?></title>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<script src="fof.js" type="text/javascript"></script>
	<script src="behindthescenes.js"></script>
	<link rel="stylesheet" href="fof-common.css" media="all" />
<?php
	echo ($mobiletrue) ?  "<link rel=\"stylesheet\" href=\"mc-iphone.css\" media=\"all\" />" : "";
?>
	<meta name="ROBOTS" content="NOINDEX, NOFOLLOW" />

</head>
<body onload="<?php echo ($mobiletrue) ?  "" : "parent.menu.location.reload();"; ?>">
<?php
	if(!$_REQUEST['framed'])
	{
?>
<?php
		echo "<br />";
	}
?>

<p><?php echo $title?> -
<?php

if($order == "desc")
{
    echo "[" . _("time desc") . "] ";
	echo "<a href=\"framesview.php?feed=$feed&amp;what=$what&amp;when=$when&amp;how=$how&amp;howmany=$howmany&amp;order=asc";
	echo ($_REQUEST['framed'] == "yes") ? "&amp;framed=yes": "";
	if (($tags) && ($tags != _("All tags")) && ($tags != _("No tags")) )
	{
		echo	($_REQUEST['tags']) ? "&amp;tags=" . $tags: "";
	}
   	echo 	"\">[" . _("time asc") . "]</a>";
} else {
	echo "<a href=\"framesview.php?feed=$feed&amp;what=$what&amp;when=$when&amp;how=$how&amp;howmany=$howmany&amp;order=desc";
	echo	($_REQUEST['framed']) ? "&amp;framed=yes": "";
	if (($tags) && ($tags != _("All tags")) && ($tags != _("No tags")) )
	{
		echo	($_REQUEST['tags']) ? "&amp;tags=" . $tags: "";
	}
   	echo "\">[" . _("time desc") . "]</a>";
    echo " [" . _("time asc") . "]";
}
if($what == "all")
{
	echo " [" . _("all") . "] ";
	echo "<a href=\"framesview.php?feed=$feed&amp;what=&amp;when=$when&amp;how=$how&amp;howmany=$howmany&amp;order=asc";
    echo ($_REQUEST['framed'] == "yes") ? "&amp;framed=yes": "";
    if (($tags) && ($tags != _("All tags")) && ($tags != _("No tags")) )
    {
        echo    ($_REQUEST['tags']) ? "&amp;tags=" . $tags: "";
    }
    echo "\">[" . _("new") . "]</a>";
}
else
{
	echo " <a href=\"framesview.php?feed=$feed&amp;what=all&amp;when=$when&amp;how=$how&amp;howmany=$howmany&amp;order=asc";
	echo ($_REQUEST['framed'] == "yes") ? "&amp;framed=yes": "";
	if (($tags) && ($tags != _("All tags")) && ($tags != _("No tags")) )
	{
		echo	($_REQUEST['tags']) ? "&amp;tags=" . $tags: "";
	}
	echo "\">[" . _("all") . "]</a>";
    echo " [" . _("new") . "]";
}
echo "</p>";

?>
<div id="items">

<?php
	$links = fof_get_frame_nav_links($feed, $what, $when, $which, $howmany, $framed, $tags);

	if($links)
	{
?>
		<div class="nav mobilenav"><?php echo $links ?></div>

<?php
	}


$result = fof_get_items($feed, $what, $when, $which, $howmany, $order, $tags, $search);

$count = 0;
foreach($result as $row)
{
	$items = true;

	$timestamp =  date("F j, Y, g:i a", $row['timestamp'] - (FOF_TIME_OFFSET * 60 * 60));
	$feed_link = $row['feed_link'];
	$feed_image = $row['feed_image'];
	$feed_title = strip_tags($row['feed_title']);
	$feed_description = htmlspecialchars($row['feed_description']);
	$item_id = $row['item_id'];
	$item_link = $row['item_link'];
	$item_title = $row['item_title'];
    $item_title = strip_tags(htmlspecialchars_decode($item_title));
	$item_content = urldecode($row['item_content']);


	$result2 = fof_do_query("SELECT * FROM `user_items` WHERE `item_id`=" . $item_id . " AND `user_id`=" . current_user());
	foreach ($result2 as $row2)
	{
		$flag_val = $row2['flag_id'];
		switch ($flag_val) {
		case 1:
			$item_read = "1";
			break;
		case 2:
			$item_publish = "1";
			break;
		case 3:
			$item_star = "1";
			break;
		}
	}
	#$item_read = $row['item_read'];
	#$item_publish = $row['item_publish'];
	#$item_star = $row['item_star'];
    if ( $item_publish ) {
        $checked = "checked=\"checked\"";
    }
    else {
        $checked = "";
    }
    if ( $item_star ) {
        $starred = "star_on.gif";
    }
    else {
        $starred = "star_off.gif";
    }


	    $favicon_link = "<a href=\"$feed_link\" title=\"feed\"><img class=\"g120\" src='" . urldecode($row['feed_image']) . "' width='16' height='16' border='0' /></a>";


	$dccreator = $row['dccreator'];
	$dcdate = $row['dcdate'];
	$dcsubject = $row['dcsubject'];
    $expand_link = "<img class=\"g120\" border=\"0\" src=\"ipodarrowright.jpg\" name=\"exp$item_id\" id=\"exp$item_id\" alt=\"" . _("Expand Body") . "\" onclick=\"toggle_expand_item('body$item_id');toggle_expand_item('controls1-$item_id');toggle_expand_item('controls2-$item_id');toggle_arrowimage('exp$item_id');\" title=\"" . _("Expand Body") . "\" />";
		
    echo '<div id="shadow-container"><div class="shadow1"><div class="shadow2"><div class="shadow3">';
	echo "<div class=\"item itemout container\" onmouseover=\"this.className='item itemover container'\" onmouseout=\"this.className='item itemout container'\">";
	echo '<div class="header">';
    echo "<table style=\"table-layout:fixed;\" bgcolor=\"#ffffff\" width=\"100%\" border=\"0\">";
    echo "<tr bgcolor=\"#ffffff\" height=\"5\"><td class=\"headertitle\">";
    echo $expand_link;
	echo $favicon_link;
    echo "<a target=\"_blank\" class=\"item_title mobilestyle\" title=\"$item_title\" href=\"$item_link\">$item_title</a>";
    echo " <span class=\"mobiletext2 feed_title\">$feed_title</span>";
    echo "</td></tr><tr bgcolor=\"#ffffff\"><td>";
    echo "<div class=\"control\" id=\"controls1-$item_id\" style=\"display:none\">";
	echo "<span class=\"meta mobilecontent\">";
    if ( is_null($feed) || $feed == "" )
    {
      echo "from <a href=\"$feed_link\" title=\"$feed_title\">$feed_title</a> ";
    }
	if($dccreator)
	{
		echo _("by") . " $dccreator ";
	}
	if($dcsubject)
	{
		echo "<i>" . _("on") . " $dcsubject </i>";
	}
	echo "</span>";
    echo "</div>";
    echo "</div>";	#control div
    echo "</td></tr><tr><td >";
    echo "<div bgcolor=\"#dddddd\" class=\"control linksbar\" id=\"controls2-$item_id\" style=\"display:none\" >";
	echo "<span class=\"mobilecontent\">";
	if($dcdate)
	{
        $dcdate = date("F j, Y, g:i a", $dcdate);
		echo _("on") . " " . $dcdate . " ";
	}
	echo "(cached $timestamp)</span>";
	echo "</tr>";	#iconsbar
    echo "</div>";	#linksbar
    echo "</div>";	#header div

    echo "</td></tr></table>";

    echo "</div>"; #item div

    if( function_exists( 'tidy_parse_string' ) ) 
    {
			tidy_parse_string($item_content);
			tidy_setopt('output-xhtml', TRUE);
			tidy_setopt('indent', TRUE);
			tidy_setopt('indent-spaces', 2);
			tidy_setopt('wrap', 200);
			tidy_setopt('show-body-only', TRUE);
			tidy_clean_repair();
			$item_content = tidy_get_output();
	}
	echo "<div class=\"body\" id=\"body$item_id\" style=\"display:none\" ><span class=\"mobilecontent\">$item_content</span></div>";
        echo "</div>"; #items div
        echo "</div></div></div></div>"; #shadow, etc
        $count ++;
}

if(!$items)
{
	echo "<p>" . _("No items found") . ".</p>";
}

	if($links)
	{
?>
		<div class="nav mobilenav"><?php echo $links ?></div>

<?php
	}
?>

</div>
</body>
</html>
