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
error_reporting(E_ALL);

fof_prune_expir_feeds();
flush();

if($_REQUEST['how'] == 'paged' && !isset($_REQUEST['which']))
{
	$which = 0;
}
else
{
	$which = $_REQUEST['which'];
}
$mobiletrue = $_REQUEST['mobiletrue'];
if (preg_match("/(wap|midp|cldc|mmp|Symbian|Smartphone|iPhone|WebKit.*Mobile)/si",$_REQUEST[HTTP_USER_AGENT])) {
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
$newonly = $_REQUEST['newonly'];

$title = fof_view_title($feed, $what, $when, $which, $howmany);
$noedit = $_GET['noedit'];
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">

<head>
	<title><?php echo strip_tags($title) ?></title>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<meta name="apple-mobile-web-app-capable" content="yes" />
	<meta names="apple-mobile-web-app-status-bar-style" content="black-translucent" />

<?php
	if (isset($fof_user_prefs['itemsrefresh']) && $fof_user_prefs['itemsrefresh'] != 0)
	{
		echo "        <meta http-equiv=\"refresh\" content=\"" . $fof_user_prefs['itemsrefresh']*60 . ";url=" . $_REQUEST['REQUEST_URI'] . "\">\n";
	}
?>
	<script src="fof.js" type="text/javascript"></script>
	<script src="behindthescenes.js"></script>
    <script>
        Load();
    </script>
	<link rel="stylesheet" href="fof-common.css" media="all" />
<?php
	echo ($mobiletrue) ?  "<link rel=\"stylesheet\" href=\"mc-iphone.css\" media=\"all\" />" : "";
?>
	<meta name="ROBOTS" content="NOINDEX, NOFOLLOW" />
    <link href="//netdna.bootstrapcdn.com/font-awesome/3.2.1/css/font-awesome.css" rel="stylesheet">
</head>
<!--body onload='<?php echo ($mobiletrue) ?  "" : "parent.menu.location.reload();hideLoader();"; ?>' -->
<body onload="parent.menu.location.href='feeds.php?<?php if($newonly=="yes"){echo "newonly=yes&";} ?><?php if($framed=="yes"){echo "framed=yes&";} ?><?php if($tags!="All tags"){echo "tags=$tags";} ?>';" >
<?php
	if(!$_REQUEST['framed'])
	{
		#include("framesmenu.php");
		echo "<br />";
	}
?>
<span class="mobilecontent"><?php echo $title?> -
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
echo "</span>";

if ($mobiletruex) {
?>
<form id="TagsForm">
<select name="tags" class="mobilestyle" fontsize="8" onchange="window.location.href='framesview.php?how=paged&tags='+document.getElementById('TagsForm').tags.value;">
<?php
	$sql = "SELECT tags FROM `$FOF_FEED_TABLE` WHERE tags != '' group by tags";
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
	mysql_free_result($result);
?></select>
</form>
<?php
}
?>

<div id="items">


		<form name="items" action="view-action.php" method="post">
<?php

		if (! $_REQUEST['framed'] )
		{
?>
<div class="menu">
<ul>
<li class="mobilestyle"><a href="javascript:<?php echo ($_REQUEST['framed']) ? "parent.items." : ""; ?>mark_read()"><?php echo _("mark as read") ?></a></li>
<li class="mobilestyle"><a href="javascript:<?php echo ($_REQUEST['framed']) ? "parent.items." : ""; ?>toggle_flags_all();"><?php echo _("toggle flags") ?></a></li>
<li class="mobilestyle"><a href="javascript:<?php echo ($_REQUEST['framed']) ? "parent.items." : ""; ?>flag_all();<?php echo ($_REQUEST['framed']) ? "parent.items." : ""; ?>mark_read()"><?php echo _("mark all read") ?></a></li>
</ul>
<ul>
<li class="mobilestyle"><a target=_blank href="add.php"><?php echo _("add feed"); ?></a></li>
</li>
<li class="mobilestyle"><a target=_blank href="feeds.php?order=unread<?php if($newonly=="yes"){echo "&newonly=yes";} ?>&direction=desc"><?php echo _("feeds list"); ?></a></li>
<li>

</li>
</ul>
</div>
<?php
		}
?>
		<input type="hidden" name="action" id="action" />
		<input type="hidden" name="return" id="return" />

<?php
	$links = fof_get_frame_nav_links($feed, $what, $when, $which, $howmany, $framed, $tags);

	if($links)
	{
?>
		<div class="nav mobilenav"><?php echo $links ?></div>

<?php
	}

if (user_is_new())
{
    echo "<table bgcolor=\"#aaffaa\">";
    echo "<tr><td>";
    echo "<div id=\"newuserbox\">";
    echo "Hi, you seem to be a new user.  Click <a href=\"samplefeeds.php\" target=\"items\">here</a> for recommended starter feeds, or visit your favorite site for more details";
    echo "</div>";
    echo "</td></tr>";
    echo "</table>";
	exit(0);
}


$result = fof_get_items($feed, $what, $when, $which, $howmany, $order, $tags, $search);

$count = 0;
foreach($result as $row)
{
	$items = true;
    #$starred = "star_off.gif";
    $starred = "-empty";
    $checked = "";
	$item_read = "0";
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

    	$flag_sql = "SELECT `flag_id` FROM `" . $FOF_USERITEM_TABLE . "` WHERE `item_id`=" . $item_id . " AND `user_id`=" . current_user();
	# AND `flag_id`!=1

		#echo "$flag_sql <br />\n";
	$result2 = fof_do_query($flag_sql);
#	$result2=mysql_fetch_array(fof_do_query($flag_sql));
#print_r($result2);
#reset($result2);
    	#foreach ($result2 as $row2)
	while($row2 = mysql_fetch_array($result2))
    	{
        $flag_val = $row2['flag_id'];
		#echo "$item_id : $flag_val <br />\n";
        switch ($flag_val) {
        case 1:
            $item_read = "1";
            break;
        case 2:
			$starred = "";
            break;
        case 3:
			$checked = "checked=\"checked\"";
            break;
        }
    }
	unset($result2);

	if($row['feed_image'] && $fof_user_prefs['favicons'])
	{
	    $favicon_link = "<a target=\"_blank\"href=\"$feed_link\" title=\"feed\"><img class=\"g120\" src='" . urldecode($row['feed_image']) . "' width='" . $fof_user_prefs['faviconsize'] . "' height='" . $fof_user_prefs['faviconsize'] . "' border='0' /></a>";
    }
    else
    {
        $favicon_link="";
    }

	$dccreator = $row['dccreator'];
	$dcdate = $row['dcdate'];
	$dcsubject = $row['dcsubject'];

    $expand_link = "<i type=\"image\" class=\"g120 icon-large icon-double-angle-right\" border=\"0\" name=\"exp$item_id\" id=\"exp$item_id\" alt=\"" . _("Expand Body") . "\" onclick=\"toggle_arrowimage('exp$item_id');toggle_expand_item('body$item_id');toggle_expand_item('controls1-$item_id');toggle_expand_item('controls2-$item_id');\" title=\"" . _("Expand Body") . "\" ></i>";
    #$expand_link .= "<img class=\"g120 icon-large\" border=\"0\" src=\"ipodarrowright.jpg\" name=\"exp$item_id\" id=\"exp$item_id\" alt=\"" . _("Expand Body") . "\" onclick=\"toggle_arrowimage('exp$item_id');toggle_expand_item('body$item_id');toggle_expand_item('controls1-$item_id');toggle_expand_item('controls2-$item_id');\" title=\"" . _("Expand Body") . "\" >";
    $star_link   = "<i type=\"image\" class=\"g120 icon-large icon-star$starred\" border=\"0\" name=\"star$item_id\" id=\"star$item_id\" alt=\"" . _("Toggle Star") . "\" onclick=\"toggle_star('star$item_id')\" title=\"" . _("Toggle Star") . "\"></i>";
    #$star_link .= "<img class=\"g120\" border=\"0\" src=\"$starred\" name=\"star$item_id\" id=\"star$item_id\" alt=\"" . _("Toggle Star") . "\" onclick=\"toggle_star('star$item_id')\" title=\"" . _("Toggle Star") . "\" />";

    #echo "<div id=\"box\">";
    echo '<div id="shadow-container"><div class="shadow1"><div class="shadow2"><div class="shadow3">';
	echo "\n"."<div class=\"item itemout container\" onmouseover=\"this.className='item itemover container'\" onmouseout=\"this.className='item itemout container'\">";
	echo '<div class="header">';

    echo "<table style=\"table-layout:fixed;\" bgcolor=\"#ffffff\" width=\"100%\" border=\"0\">";
    echo "<tr bgcolor=\"#ffffff\" height=\"9\"><td class=\"headertitle\">";
    echo $expand_link . " ";
    echo $star_link . " ";
	echo $favicon_link;
    echo "<a target=\"_blank\" class=\"item_title mobilestyle\" title=\"$item_title\" href=\"$item_link\">$item_title</a>";
	#if($mobiletrue){echo "<br />"; }
    echo " <span class=\"mobiletext2 feed_title\">$feed_title</span>";

    echo "</td><td width=\"45\" align=\"right\">";
    echo "<div valign=\"center\" class=\"controls\">";
    echo "<i class=\"icon-large icon-arrow-down\"></i><img class=\"g120\" src=\"flagup.jpg\" title=\"" . _("flag up to here") . "\" border=\"0\" onclick=\"flag_upto('c" . $item_id . "." . $count . "')\" />";
    echo "<i class=\"icon-large icon-check-sign\"></i><input class=\"bigcheck\" onclick=\"clickage(event)\" type=\"checkbox\" name=\"c" . $item_id . "." . $count . "\" value=\"checked\" /><i class=\"icon-check icon-check-empty icon-large\"></i>";
    echo '</div>';

    echo "</tr></td>";
    echo "<tr bgcolor=\"#ffffff\">";
    echo "<td colspan=\"2\">";


    echo "<div class=\"control\" id=\"controls1-$item_id\"";
	echo ($fof_user_prefs['collapse'] == 1) ? " style=\"display:none\" " : "" ;
	echo ">";

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

        echo "</td></tr>";
        echo "<tr>";	#iconsbar
        echo "<td colspan=\"2\">";
        echo "<div bgcolor=\"#dddddd\" class=\"control linksbar\" id=\"controls2-$item_id\"";
	echo ($fof_user_prefs['collapse'] == 1) ? " style=\"display:none\" " : "" ;
		echo ">";

// itembar_plugins
// the following should be placed into individual plugins for the itembar
        $recycle_link= "<span class=\"mobiletext3\">" . _("Publish") . "</span> <input class=\"bigcheck\" type=\"checkbox\" name=\"pub$item_id\" onclick=\"togglePublish(this)\" value=\"$item_id\" $checked>";
//        $delicious_url = "http://del.icio.us/" . DELICIOUS_USERNAME . "?v=3&url=" . $item_link . "&title=" . $item_title . "&notes=&v=4&noui&jump=close&src=ffext1.0.2";
//       $technorati_url = "http://www.technorati.com/cosmos/search.html?sub=postcosm&url=" . $item_link;
//      $blogger_url = "http://www.blogger.com/blog_this.pyra?&u=" . $item_link . "&n=" . $item_title;
        $email_url="subject=" . rawurlencode($item_title) . "&body=" . _("Check this out at ") . rawurlencode($item_link) . " " . rawurlencode("
" . $item_content);
        $email_url = eregi_replace(",","%2C",$email_url);
        if (strlen($email_url) > 1800) 
        {
           // string exceeded length, truncate and add trailing dots
           $email_url=substr($email_url,0,1800);
        } 
        $email_tag = _("
---
This email brought to you by the Monkeychow web-based RSS reader.
http://shokk.wordpress.com/tag/monkeychow/");
        $email_url .= urlencode($email_tag);
//        $digg_url = "http://digg.com/submit?phase=2&url=" . rawurlencode($item_link);
//        $newsvine_url = "http://www.newsvine.com/_wine/save?u=" . rawurlencode($item_link) . "&h=" . rawurlencode($item_title);
//		$pingfm_url = "http://ping.fm/ref/?method=microblog&link=" . rawurlencode($item_link) . "&title=" . rawurlencode($item_title) . "&body=This link brought to you by the MonkeyChow RSS reader.";
//		$friendfeed_url="http://friendfeed.com/api/share?title=" . $item_title . "&link=" . $item_link;
//
//        $friendfeed_link = "<a href=\"$friendfeed_url\" target=\"_blank\" title=\"Submit to FriendFeed\"><img class=\"g120\" border=\"0\" src=\"friendfeed.png\"></a>";
//        $twitter_link = "<a style=\"cursor: pointer;\" onclick=\"twitterit('" . urlencode($item_title) . "','" . urlencode($item_link) . "'); alert('Posted to Twitter');\" target=\"_blank\" title=\"Twitter This!\"><img class=\"g120\" border=\"0\" src=\"twitter.png\"></a>";
//
//        $wordpresssite1_link = "<a style=\"cursor: pointer;\" onclick=\"javascript:pressit('" . $fof_user_prefs['wordpresssite1'] . "','" .urlencode($item_title) . "','" . urlencode($item_link) . "');\" target=\"_blank\" title=\"Press It! to " . $fof_user_prefs['wordpresssitename1']  . "\"><img class=\"g120\" border=\"0\" src=\"wordpress.gif\"></a>";
//        $wordpresssite2_link = "<a style=\"cursor: pointer;\" onclick=\"javascript:pressit('" . $fof_user_prefs['wordpresssite2'] . "','" .urlencode($item_title) . "','" . urlencode($item_link) . "');\" target=\"_blank\" title=\"Press It! to " . $fof_user_prefs['wordpresssitename2']  . "\"><img class=\"g120\" border=\"0\" src=\"wordpress.gif\"></a>";
//        $wordpresssite2_link = "<a style=\"cursor: pointer;\" onclick=\"javascript:pressit('" . $fof_user_prefs['wordpresssite2'] . "','" .urlencode($item_title) . "','" . urlencode($item_link) . "');\" target=\"_blank\" title=\"Press It! to " . $fof_user_prefs['wordpresssitename2']  . "\"><img class=\"g120\" border=\"0\" src=\"wordpress.gif\"></a>";
//        $wordpresssite2_link = "<a style=\"cursor: pointer;\" onclick=\"javascript:pressit('" . $fof_user_prefs['wordpresssite2'] . "','" .urlencode($item_title) . "','" . urlencode($item_link) . "');\" target=\"_blank\" title=\"Press It! to " . $fof_user_prefs['wordpresssitename2']  . "\"><img class=\"g120\" border=\"0\" src=\"wordpress.gif\"></a>";
//        $wordpresssite3_link = "<a style=\"cursor: pointer;\" onclick=\"javascript:pressit('" . $fof_user_prefs['wordpresssite3'] . "','" .urlencode($item_title) . "','" . urlencode($item_link) . "');\" target=\"_blank\" title=\"Press It! to " . $fof_user_prefs['wordpresssitename3']  . "\"><img class=\"g120\" border=\"0\" src=\"wordpress.gif\"></a>";
//
//        $pingfm_link = "<a href=\"$pingfm_url\" target=\"_blank\" title=\"Ping This!\"><img class=\"g120\" border=\"0\" src=\"pingfm.jpg\"></a>";
//        $digg_link = "<a href=\"$digg_url\" target=\"_blank\" title=\"" . "Digg This" . "\"><img class=\"g120\" border=\"0\" src=\"digg.png\"></a>";
//	$digg_link = "";
//        $newsvine_link = "<a href=\"$newsvine_url\" target=\"_blank\" title=\"Seed Newsvine\"><img class=\"g120\" border=\"0\" src=\"newsvine.png\"></a>";
//        $delicious_link = "<a href=\"$delicious_url\" target=\"_blank\" title=\"Add to My del.icio.us\"><img class=\"g120\" border=\"0\" src=\"delicious_favicon.ico\"></a>";
//        $technorati_link = "<a href=\"$technorati_url\" target=\"_blank\" title=\"" . _("Search") . " Technorati Cosmos\"><img class=\"g120\" border=\"0\" src=\"bubble.gif\"></a>";
//        $blogger_link = "<a href=\"$blogger_url\" target=\"_blank\" title=\"" . _("BlogThis") . "!\"><img class=\"g120\" border=\"0\" src=\"blogit.png\"></a>";
        $email_link = "<a target=\"_blank\" href=\"mailto:?" . $email_url . "\" title=\"" . _("Email To") . "...\"><img class=\"g120\" border=\"0\" src=\"mailto.gif\"></a>";

// each of the above should be assembled into the tem toolbar with plugins
// itembar_plugins($item_link,$item_content);


	echo $recycle_link;
	echo "&nbsp;&nbsp;&nbsp;";
//	echo $delicious_link . "&nbsp;&nbsp;&nbsp;" . $newsvine_link . "&nbsp;&nbsp;&nbsp;" . $digg_link . "&nbsp;&nbsp;&nbsp;" . $pingfm_link;
//	echo (isset($fof_user_prefs['twitteruser']) && $fof_user_prefs['twitteruser'] != "" ) ?  "&nbsp;&nbsp;&nbsp;" . $twitter_link : "" ;
//	echo (isset($fof_user_prefs['wordpresssite1']) && $fof_user_prefs['wordpresssite1'] != "" ) ?  "&nbsp;&nbsp;&nbsp;" . $wordpresssite1_link : "" ;
//	echo (isset($fof_user_prefs['wordpresssite2']) && $fof_user_prefs['wordpresssite2'] != "" ) ?  "&nbsp;&nbsp;&nbsp;" . $wordpresssite2_link : "" ;
//	echo (isset($fof_user_prefs['wordpresssite3']) && $fof_user_prefs['wordpresssite3'] != "" ) ?  "&nbsp;&nbsp;&nbsp;" . $wordpresssite3_link : "" ;
//	echo "&nbsp;&nbsp;&nbsp;" . $friendfeed_link . "&nbsp;&nbsp;&nbsp;" . $technorati_link . "&nbsp;&nbsp;&nbsp;" . $blogger_link . "&nbsp;&nbsp;&nbsp;";
    echo $email_link;

    //insert social plugins function here
    social_plugins($social_filters,$item_content);
    echo "<br />";
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
    echo "<div class=\"body\" id=\"body$item_id\"";
    echo ($fof_user_prefs['collapse'] == 1) ? " style=\"display:none\" " : "" ;
    echo "><span class=\"mobilecontent\">";

    // alter item_content here with plugins
    $item_content = content_plugins($item_filters,$item_content);
    echo $item_content;

    echo "</span></div>";
    echo "</div>"; #items div
    echo "</div></div></div></div>"; #shadow, etc
    $count ++;
}
unset($row);
mysql_free_result($result);
if(!$items)
{
	echo "<span class=\"mobilecontent\">" . _("No items found") . ".</span>";
}

?>
		</form>
<?php
	if($links)
	{
?>
		<div class="nav mobilenav"><?php echo $links ?></div>

<?php
	}
?>

</div>
<?php
		if (! $_REQUEST['framed'] )
		{
?>
<div class="menu">
<ul>
<li class="mobilestyle"><a href="javascript:<?php echo ($_REQUEST['framed']) ? "parent.items." : ""; ?>mark_read()"><?php echo _("mark as read") ?></a></li>
<li class="mobilestyle"><a href="javascript:<?php echo ($_REQUEST['framed']) ? "parent.items." : ""; ?>toggle_flags_all();"><?php echo _("toggle flags") ?></a></li>
<li class="mobilestyle"><a href="javascript:<?php echo ($_REQUEST['framed']) ? "parent.items." : ""; ?>flag_all();<?php echo ($_REQUEST['framed']) ? "parent.items." : ""; ?>mark_read()"><?php echo _("mark all read") ?></a></li>
</ul>
<ul>
<li class="mobilestyle"><a target=_blank href="add.php"><?php echo _("add feed"); ?></a></li>
<li class="mobilestyle"><a target=_blank href="feeds.php?order=unread&newonly=yes&direction=desc"><?php echo _("feeds list"); ?></a></li>
<li>
</li>
</ul>
</div>
<?php
		}
?>
</body>
</html>
