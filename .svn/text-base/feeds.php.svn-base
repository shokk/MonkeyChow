<?php
/*
 * This file is part of Monkeychow - http://monkeychow.org
 *
 * feeds.php - feed list for frames mode
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

$specialtest=0;

//if ($_REQUEST['framed'] == "yes"){ // start of frames check
include_once("init.php");
include_once("fof-main.php");

header("Content-Type: text/html; charset=utf-8");

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">

<head>
    <title>MonkeyChow - <?php _("control panel") ?></title>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <link rel="stylesheet" href="fof-common.css" media="all" />
    <script src="fof.js" type="text/javascript"></script>
    <script src="behindthescenes.js"></script>
    <script language="javascript" type="text/javascript">
// this one doesn't quite work yet, supposed to be an onResize
// handler for body, works in Mozilla, but IE and Safari give me
// problems.  it remembers the frame sizes transparently in a cookie.
function saveLayout()
{
    expires = new Date()
    exptime = expires.getTime()
    exptime += (10 * 365 * 24 * 60 * 60 * 1000)
    expires.setTime(exptime)

    c = top.document.getElementById('hframeset').cols + '$' + top.document.getElementById('vframeset').rows;

    document.cookie = "fof_layout=" + c + "; expires=" + expires.toGMTString();
}
     </script>

        <meta name="ROBOTS" content="NOINDEX, NOFOLLOW" />
        <base target="items" />
    </head>

<?php //} // end of frames check ?>
<body id="panel-page" >

<?php if ($specialtest == "1") { ?>
<div class="menu">
<ul>
<li><a href="framesview.php?how=paged<?php echo ($_REQUEST['framed'] == "yes") ? "&framed=yes" : "" ; ?>"><?php echo _("unread") ?></a></li>
<li><a href="framesview.php?what=all&amp;how=paged<?php echo ($_REQUEST['framed'] == "yes") ? "&framed=yes" : "" ; ?>"><?php echo _("all") ?></a><a href="rss2.php"><img class="valign" border="0" src="rss.gif"></a></li>
<li><a href="framesview.php?what=all&amp;when=today&how=paged<?php echo ($_REQUEST['framed'] == "yes") ? "&framed=yes" : "" ; ?>"><?php echo _("today") ?></a></li>
<li><a href="framesview.php?what=starred&amp;how=paged<?php echo ($_REQUEST['framed'] == "yes") ? "&framed=yes" : "" ; ?>"><?php echo _("starred") ?></a></li>
<li><a href="framesview.php?what=published&amp;how=paged<?php echo ($_REQUEST['framed'] == "yes") ? "&framed=yes" : "" ; ?>"><?php echo _("published") ?></a><a href="rss.php"><img class="valign" border="0" src="rss.gif"></a></li>
<li><a href="dino.php"><?php echo _("dinosaurs") ?></a></li>
</ul>
<ul>
<li><a href="prefs.php<?php echo ($_REQUEST['framed'] == "yes") ? "?framed=yes" : "" ; ?>">prefs</a></li> 
<li><a href="javascript:<?php echo ($_REQUEST['framed'] == "yes") ? "parent.items." : ""; ?>toggle_expand_all()"><?php echo _("toggle collapse") ?></a></li>
<li><a href="javascript:<?php echo ($_REQUEST['framed'] == "yes") ? "parent.items." : ""; ?>toggle_flags_all()"><?php echo _("toggle flags") ?></a></li>
<li><a href="update.php"><?php echo _("update all") ?></a></li>
</ul>
<ul>
<center><table><tr><td><form method="get" action="framesview.php?what=search&amp;how=paged">
<?php
    echo ($_REQUEST['framed'] == "yes") ? "<input type='hidden' name='framed' value='yes'>" : "";
?>
<input type="hidden" name="what" value="search"><input style=".input" type="reset" value="<?php echo _("Clear") ?>"></input><input style=".input" type="text" height="8" size="8" maxlength="40" name ="search" value=""><input style=".input" type="submit" value="<?php echo _("Find") ?>"></form></td><td>
<?php
if ($_REQUEST['framed'] == "yes") {
?>  
        <form><select name="tags" fontsize="8" onchange="javascript:parent.menu.location.href='feeds.php?<?php echo ($_REQUEST['framed'] == "yes") ? "framed=yes&" : "" ;  ?>tags='+this.value;parent.items.location.href='framesview.php?<?php echo ($_REQUEST['framed'] == "yes") ? "framed=yes&how=paged&" : "" ;  ?>tags='+this.value"><?php
    $sql = "SELECT tags FROM `feeds` WHERE tags != '' group by tags";
$result = fof_do_query($sql);
print "<OPTION VALUE=\"" . _("All tags") . "\">" . _("All tags") . "\n";
print "<OPTION VALUE=\"" . _("No tags") . "\">" . _("No tags") . "\n";
while($row = mysql_fetch_array($result))
{   
    $tagarray = array_unique(array_merge($tagarray, parse_tag_string($row['tags'])));
}
sort($tagarray);
foreach ($tagarray as $piece)
{
   print "<OPTION VALUE=\"$piece\">$piece\n";
}

?></select></form>
<?php
}
?>
</td>
</tr></table></center>
</ul>
<ul>
<li><a href="javascript:<?php echo ($_REQUEST['framed'] == "yes") ? "parent.items." : ""; ?>mark_read()"><?php echo _("mark as read") ?></a></li>
<li><a href="javascript:<?php echo ($_REQUEST['framed'] == "yes") ? "parent.items." : ""; ?>mark_unread()"><?php echo _("mark as unread") ?></a></li>
<li><a href="javascript:<?php echo ($_REQUEST['framed'] == "yes") ? "parent.items." : ""; ?>flag_all();<?php echo ($_REQUEST['framed'] == "yes") ? "parent.items." : ""; ?>mark_read()"><?php echo _("mark all read") ?></a></li>
</ul>
<ul>
<li><a href="<?php echo ($_REQUEST['framed'] == "yes") ? "index" : "frames" ; ?>.php" target="_top"><?php echo ($_REQUEST['framed'] == "yes") ? _("panel") : _("frames") ?></a></li>
<li><a href="add.php"><?php echo _("add feeds") ?></a></li>
<li><a href="javascript:<?php echo ($_REQUEST['framed'] == "yes") ? "parent.items." : ""; ?>location.reload()<?php echo ($_REQUEST['framed'] == "yes") ? ";parent.menu.location.reload();" : ""; ?>"><?php echo _("refresh view") ?></a></li>
<li><a href="http://www.monkeychow.org"><?php echo _("about") ?></a></li>
<li><a href="logout.php" target="_top">log out</a></li>
</ul>
</div>
<?php } //specialtest ?>

<div id="feeds">
<?php
	include("panel.php"); 
	//include("framesmenu.php"); 

$order = $_REQUEST['order'];
$direction = $_REQUEST['direction'];
$tags = $_REQUEST['tags'];

if(!isset($order))
{
	$order = "title";
}

if(!isset($direction))
{
	$direction = "asc";
}

$feeds = fof_get_feeds($order, $direction, $tags);

foreach($feeds as $row)
{
    $n++;
    $unread += $row['unread'];
}

?>

<table cellspacing="0" cellpadding="1" border="0">
<tr><td colspan="3">

<?php echo $n . " " . _("feeds") . ", " . $unread . " " . _("new items") ?>;
</td><td align="right">
<a href="feeds.php?
<?php
	echo ($_REQUEST['newonly'] == "yes") ? "" : "&amp;newonly=yes" ;
	echo ($_REQUEST['framed'] == "yes") ? "&amp;framed=yes" : "" ; 
	echo ($_REQUEST['tags']) ? "&amp;tags=" . $tags : "" ; 
    #if ($_REQUEST['framed']) {
    #    echo "&amp;framed=yes";
    #}
?>
" target="menu"><div class="nowrap">
<?php 
    //echo ($_REQUEST['newonly'] == "yes") ? _("all articles") : _("new articles") ; </a><br />
?>
<a href="opml.php<?php 
    if (($tags) && ($tags != _("All tags")) && ($tags != _("No tags")) )
    {
        echo "?tags=".$tags;
    }
?>"><b><?php echo _("opml list") ?></b></div></a>

</td></tr>
<tr class="heading">

<?php

$title["age"] = _("sort by last update time");
$title["unread"] = _("sort by number of unread items");
$title["title"] = _("sort by feed title");

foreach (array("age", "unread", "title") as $col)
{
	echo "<td><nobr><a title=\"$title[$col]\"target=\"_self\" href=\"feeds.php?order=$col";
    echo ($_REQUEST['framed'] == "yes") ? "&amp;framed=yes" : "" ;
    echo ($_REQUEST['newonly'] == "yes") ? "&amp;newonly=yes" : "" ;
	if (($tags) && ($tags != _("All tags")) && ($tags != _("No tags")))
	{
        echo ($tags) ? "&amp;tags=".$tags : "" ;
	}
	if($col == $order && $direction == "asc")
	{
		echo "&amp;direction=desc\">";
	}
	else
	{
		echo "&amp;direction=asc\">";
	}


	if($col == "unread")
	{
		echo "<span class=\"unread\">#</span>";
	}
	else
	{
		echo $col;
	}

	if($col == $order)
	{
		echo ($direction == "asc") ? "&darr;" : "&uarr;";
	}

	echo "</nobr></a></td>";
}

?>

<td></td>
</tr>

<?php

foreach($feeds as $row)
{

	$id = $row['id'];
	$url = htmlspecialchars($row['url']);
	$title = htmlspecialchars($row['title']);
	$link = htmlspecialchars($row['link']);
	$description = $row['description'];
	#$age = fof_rss_age($row['url']);
	$unread = $row['unread'];
	$items = $row['items'];
	$agestr = $row['agestr'];
	$agestrabbr = $row['agestrabbr'];
	$image = $row['image'];

	if ( (($unread)&&($_REQUEST['newonly'])) || (!$_REQUEST['newonly']))
	{
	if(++$t % 2)
	{
		echo "<tr class=\"odd-row\">";
	}
	else
	{
		echo "<tr>";
	}

	echo "<td><span title=\"$agestr\">";
	echo ($_REQUEST['framed'] == "yes") ? "$agestrabbr" : $agestr ;
	echo "</span></td>";

	$u = "framesview.php?feed=$id&amp;how=paged";

	if ($_REQUEST['framed'] == "yes") {
		$u = $u . "&amp;framed=yes";
	}

	echo "<td class=\"nowrap\">";
	echo ($_REQUEST['framed'] == "yes") ? "" : "&nbsp;&nbsp;" ;

	if($unread)
	{
		echo ($_REQUEST['framed'] == "yes") ? "" : "(" ;
		echo "<a class=\"unread\" title=\"new items\" href=\"$u\">$unread";
		echo ($_REQUEST['framed'] == "yes") ? "" : " new" ;
		echo "</a>";
		echo ($_REQUEST['framed'] == "yes") ? "/" : " / " ;
	}
	else
	{
		if(!$_REQUEST['framed'])
		{
			echo "(0 " . _("new") . " / ";
		}
	}

	echo "<a href=\"" . $u . "&amp;what=all\" title=\"all items\">$items</a>";
	echo ($_REQUEST['framed'] == "yes") ? "" : "):" ;
	echo "</td><td><div class=\"headertitle\">";

	if($row['image'] && $fof_user_prefs['favicons'])
	{
		echo "<a href=\"$url\" title=\"feed\"><img src='" . urldecode($row['image']) . "' width='" . $fof_user_prefs['faviconsize'] . "' height='" . $fof_user_prefs['faviconsize'] . "' border='0' /></a>";
    }
    else
    {
       echo "<a href=\"$url\" title=\"feed\"><img src='feed-icon.png' width='" . $fof_user_prefs['faviconsize'] . "' height='" . $fof_user_prefs['faviconsize'] . "' border='0' /></a>";
    }

	echo fof_render_feed_link($row) . "</div></td>";

	echo "<td align=\"right\"><nobr>";
	echo ($_REQUEST['framed'] == "yes") ? "" : "&nbsp;&nbsp;";

	echo " <a href=\"mark-read.php?";
	echo ($_REQUEST['framed'] == "yes") ? "framed=yes&" : "";
	if (isset($order))
	{
			echo ($_REQUEST['order']) ? "order=" . $order . "&" : "";
	}
	if (isset($direction))
	{
			echo ($_REQUEST['direction']) ? "direction=" . $direction . "&" : "";
	}
	if (($tags) && ($tags != _("All tags")) && ($tags != _("No tags")))
	{
		echo ($_REQUEST['tags']) ? "tags=" . $tags . "&" : "";
	}
	echo ($_REQUEST['newonly'] == "yes") ? "newonly=yes&" : "";
	echo "feed=$id\" target=\"_self\" title=\"" . _("mark all read") . "\">";
	echo ($_REQUEST['framed'] == "yes") ? "m" : _("mark all read");
	echo "</a>";
	echo ($_REQUEST['framed'] == "yes") ? "" : "&nbsp;&nbsp;";

	echo " <a href=\"edit.php?";
	echo ($_REQUEST['framed'] == "yes") ? "framed=yes&" : "";
	if (($tags) && ($tags != _("All tags")) && ($tags != _("No tags")))
	{
		echo ($_REQUEST['tags']) ? "tags=" . $tags . "&" : "";
	}
	echo "feed=$id\" title=\"" . _("edit") . "\">";
	echo ($_REQUEST['framed'] == "yes") ? "e" : _("edit");
	echo "</a>";
	echo ($_REQUEST['framed'] == "yes") ? "" : "&nbsp;&nbsp;";

    echo " <a href=\"update.php?feed=$id\" title=\"" . _("update") . "\">";
	echo ($_REQUEST['framed'] == "yes") ? "u" : _("update");
	echo "</a>";
	echo ($_REQUEST['framed'] == "yes") ? "" : "&nbsp;&nbsp;";

	echo " <a href=\"delete.php?";
	echo ($_REQUEST['framed'] == "yes") ? "framed=yes&" : "";
	if (($tags) && ($tags != _("All tags")) && ($tags != _("No tags")))
	{
		echo ($_REQUEST['tags']) ? "tags=" . $tags . "&" : "";
	}
	echo "feed=$id\" title=\"" . _("delete") . "\" onclick=\"return confirm('" . _('Are you SURE?') . "')\">";
	echo ($_REQUEST['framed'] == "yes") ? "d" : _("delete");
	echo "</a></nobr></td>";

	echo "</tr>\n";
	}
}


?>

</table>

</div>
<?php 
    include("panel.php"); 
    //include("framesmenu.php"); 
    if($_REQUEST['framed'] == "yes")
    {
?>
        </body></html>
        <?php
    }
    else
    {
        echo "<br />
        <br />
        <br />
        <br />
        <br />
        <br />
        <br />";
    }

