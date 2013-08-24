<?php
/*
 * This file is part of Monkeychow - http://shokk.wordpress.com/tag/monkeychow/
 *
 * feeds.php - feed list for frames mode
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
$specialtest=0;
include_once("init.php");
include_once("fof-main.php");
header("Content-Type: text/html; charset=utf-8");
global $FOF_FEED_TABLE, $FOF_USER_TABLE, $FOF_FLAG_TABLE, $FOF_ITEM_TABLE, $FOF_SUBSCRIPTION_TABLE, $FOF_USERITEM_TABLE;


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
    $sql = "SELECT $FOF_SUBSCRIPTION_TABLE.tags FROM `" . $FOF_FEED_TABLE . "` WHERE $FOF_SUBSCRIPTION_TABLE.tags != '' group by tags";
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
<li><a href="http://shokk.wordpress.com/tag/monkeychow/"><?php echo _("about") ?></a></li>
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

if (user_is_new())
{
	echo "<table bgcolor=\"#aaffaa\">";
	echo "<tr><td>";
	echo "<div id=\"newuserbox\">";
	echo "Hi, you seem to be a new user.  Click below for recommended starter feeds, or visit your favorite site for more RSS feeds.<br />\n";
	include ("samplefeeds.php");
	echo "</div>";
	echo "</td></tr>";
	echo "</table>";
}
else
{

$feeds = fof_get_feeds($order, $direction, $tags);
/*

#find number of total unread for this user

# list their subscribed feeds
# list subset of existing on server minus marked
# use that number below

foreach($feeds as $row)
{
    $n++;
    $unread += $row['unread'];
}
*/

?>

<table cellspacing="0" cellpadding="1" border="0">
<tr><td colspan="3">

<?php echo $n . " " . _("feeds") . ", " . $unread . " " . _("new items") ?>;
</td><td align="right">
<a href="feeds.php?
<?php
	echo ($_REQUEST['newonly'] == "yes") ? "&amp;newonly=yes" : "&amp;newonly=" ;
	echo ($_REQUEST['framed'] == "yes") ? "&amp;framed=yes" : "&amp;framed=" ; 
	echo ($_REQUEST['tags']) ? "&amp;tags=" . $tags : "" ; 
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
    echo "<td><nobr><a title=\"$title[$col]\"target=\"_self\" href=\"feeds.php?order=$col " ;
    echo ($_REQUEST['framed'] == "yes") ? "&amp;framed=yes" : "" ;
    echo ($_REQUEST['newonly'] == "yes") ? "&amp;newonly=yes" : "" ;
	if (($tags) && ($tags != _("All tags")) && ($tags != _("No tags")))
	{
            echo ($tags) ? "&tags=".$tags : "" ;
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

	#beginning of row
	$rowstring="";
	if ( (($unread)&&($_REQUEST['newonly'])) || (!$_REQUEST['newonly']) || (1))
	{
	    	if(++$t % 2)
		{
			$rowstring.="<tr class=\"odd-row\">";
		}
		else
		{
			$rowstring.="<tr>";
		}

		$rowstring.= "<td><span title=\"$agestr\">";
		$rowstring.= ($_REQUEST['framed'] == "yes") ? "$agestrabbr" : $agestr ;
		$rowstring.= "</span></td>";
		$u = "framesview.php?feed=$id&amp;how=paged";

		if ($_REQUEST['framed'] == "yes") {
			$u = $u . "&amp;framed=yes";
		}

		$rowstring.= "<td class=\"nowrap\">";
		$rowstring.= ($_REQUEST['framed'] == "yes") ? "" : "&nbsp;&nbsp;" ;

		if($unread)
		{
			$rowstring.= ($_REQUEST['framed'] == "yes") ? "" : "(" ;
			$rowstring.= "<a class=\"unread\" title=\"new items\" href=\"$u\">$unread";
			$rowstring.= ($_REQUEST['framed'] == "yes") ? "" : " new" ;
			$rowstring.= "</a>";
			$rowstring.= ($_REQUEST['framed'] == "yes") ? "/" : " / " ;
		}
		else
		{
			if(!$_REQUEST['framed'])
			{
				$rowstring.= "(0 " . _("new") . " / ";
			}
		}


		# insert routines to determine unread counts
		# get number of articles in system
		$feedcountsql="SELECT DISTINCT `$FOF_ITEM_TABLE`.id FROM `$FOF_ITEM_TABLE` WHERE `$FOF_ITEM_TABLE`.feed_id=" . $id;
		#$rowstring.= $feedcountsql . "</br>";
		$myresult=fof_do_query($feedcountsql);
		$feedcounttotal="0";
		while($myrow = mysql_fetch_array($myresult))
		{
    			$feedcounttotal++;
		}
		#$rowstring.= $feedcounttotal . " total in feed</br>";
	
		# see if any of them are NOT set to 1 for read
		$feedusermarkeditemssql = "SELECT * FROM `$FOF_USERITEM_TABLE`,`$FOF_ITEM_TABLE` WHERE user_id=" . current_user() . " AND `$FOF_ITEM_TABLE`.id=`$FOF_USERITEM_TABLE`.item_id AND `$FOF_ITEM_TABLE`.feed_id=" . $id;
		# return remaining
		# if newonly and if count is 0, don't display the line!
		#$rowstring.= $feedusermarkeditemssql . "</br>";
		$myresult=fof_do_query($feedusermarkeditemssql);
		$feedcountmarked="0";
		while($myrow = mysql_fetch_array($myresult))
		{
    			$feedcountmarked++;
		}
		#$rowstring.= $feedcountmarked . " total marked by user in this feed.</br>";
		$totalnew = $feedcounttotal - $feedcountmarked;
		#$rowstring.= " (userid " . current_user() . " has " . $totalnew . " new items for feed id " . $id . ") ";
		$items = $totalnew;


		#if ($items > 0)
		#{
			$rowstring.= "<a href=\"" . $u . "&amp;what=" . (($_REQUEST['newonly']) ? "" : "all") . "\" title=\"all items\">$items</a>";
		#}
		#else
		#{
		#	$rowstring.= "0";
		#	#dont print the line!!
		#}

		$rowstring.= ($_REQUEST['framed'] == "yes") ? "" : "):" ;
	
		$rowstring.= "</td><td><div class=\"headertitle\">";

		if($row['image'] && $fof_user_prefs['favicons'])
		{
			$rowstring.= "<a href=\"$url\" title=\"feed\"><img src='" . urldecode($row['image']) . "' width='" . $fof_user_prefs['faviconsize'] . "' height='" . $fof_user_prefs['faviconsize'] . "' border='0' /></a>";
    		}
    		else
    		{
       			$rowstring.= "<a href=\"$url\" title=\"feed\"><img src='feed-icon.png' width='" . $fof_user_prefs['faviconsize'] . "' height='" . $fof_user_prefs['faviconsize'] . "' border='0' /></a>";
    		}

		$rowstring.= fof_render_feed_link($row) . "</div></td>";

		$rowstring.= "<td align=\"right\"><nobr>";
		$rowstring.= ($_REQUEST['framed'] == "yes") ? "" : "&nbsp;&nbsp;";

		$rowstring.= " <a href=\"mark-read.php?";
		$rowstring.= ($_REQUEST['framed'] == "yes") ? "framed=yes&" : "";
		if (isset($order))
		{
			$rowstring.= ($_REQUEST['order']) ? "order=" . $order . "&" : "";
		}
		if (isset($direction))
		{
			$rowstring.= ($_REQUEST['direction']) ? "direction=" . $direction . "&" : "";
		}
		if (($tags) && ($tags != _("All tags")) && ($tags != _("No tags")))
		{
			$rowstring.= ($_REQUEST['tags']) ? "tags=" . $tags . "&" : "";
		}
		$rowstring.= ($_REQUEST['newonly'] == "yes") ? "newonly=yes&" : "";
		$rowstring.= "feed=$id\" target=\"_self\" title=\"" . _("mark all read") . "\">";
		$rowstring.= ($_REQUEST['framed'] == "yes") ? "m" : _("mark all read");
		$rowstring.= "</a>";
		$rowstring.= ($_REQUEST['framed'] == "yes") ? "" : "&nbsp;&nbsp;";

		$rowstring.= " <a href=\"edit.php?";
		$rowstring.= ($_REQUEST['framed'] == "yes") ? "framed=yes&" : "";
		if (($tags) && ($tags != _("All tags")) && ($tags != _("No tags")))
		{
			$rowstring.= ($_REQUEST['tags']) ? "tags=" . $tags . "&" : "";
		}
		$rowstring.= "feed=$id\" title=\"" . _("edit") . "\">";
		$rowstring.= ($_REQUEST['framed'] == "yes") ? "e" : _("edit");
		$rowstring.= "</a>";
		$rowstring.= ($_REQUEST['framed'] == "yes") ? "" : "&nbsp;&nbsp;";

    		$rowstring.= " <a href=\"update.php?feed=$id\" title=\"" . _("update") . "\">";
		$rowstring.= ($_REQUEST['framed'] == "yes") ? "u" : _("update");
		$rowstring.= "</a>";
		$rowstring.= ($_REQUEST['framed'] == "yes") ? "" : "&nbsp;&nbsp;";

		$rowstring.= " <a href=\"delete.php?";
		$rowstring.= ($_REQUEST['framed'] == "yes") ? "framed=yes&" : "";
		if (($tags) && ($tags != _("All tags")) && ($tags != _("No tags")))
		{
			$rowstring.= ($_REQUEST['tags']) ? "tags=" . $tags . "&" : "";
		}
		$rowstring.= "feed=$id\" title=\"" . _("delete") . "\" onclick=\"return confirm('" . _('Are you SURE?') . "')\">";
		$rowstring.= ($_REQUEST['framed'] == "yes") ? "d" : _("delete");
		$rowstring.= "</a></nobr></td>";

		$rowstring.= "</tr>\n";
		if ( $items < 1 )
		{
			#echo "wooooooo why the fuck am i in this bracket set?!?!? i am zero </br>\n";
			#echo "newonly request is set to " . $_REQUEST['newonly'] . "</br>\n";
			if ( $_REQUEST['newonly'] )
			{
				#
			}
			else
			{
				#echo "echo wtf I'm asking for only new shit!!! </br>\n";
				echo $rowstring . "\n";
			}
		}
		else
		{
			echo $rowstring . "\n";
		}

		#end of row
	}
} // foreach feeds
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

