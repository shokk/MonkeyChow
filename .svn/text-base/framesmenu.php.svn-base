<?php
/*
 * This file is part of Monkeychow - http://monkeychow.org
 *
 * framesmenu.php - upper right menu for frames mode
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
if ($_REQUEST['framed']){ // start of frames check
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
// doesn't quite work yet, supposed to be an onResize
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

	<body id="panel-page" bgcolor="#eee">
<?php 
		} // end of frames check 
?>

<div class="menu">
<ul>
<li><a href="framesview.php?how=paged<?php echo ($_REQUEST['framed']) ? "&framed=yes" : "" ; ?>"><?php echo _("unread") ?></a></li>
<li><a href="framesview.php?what=all&amp;how=paged<?php echo ($_REQUEST['framed']) ? "&framed=yes" : "" ; ?>"><?php echo _("all") ?></a><a href="rss2.php"><img class="valign" border="0" src="rss.gif"></a></li>
<li><a href="framesview.php?what=all&amp;when=today&how=paged<?php echo ($_REQUEST['framed']) ? "&framed=yes" : "" ; ?>"><?php echo _("today") ?></a></li>
<li><a href="framesview.php?what=starred&amp;how=paged<?php echo ($_REQUEST['framed']) ? "&framed=yes" : "" ; ?>"><?php echo _("starred") ?></a></li>
<li><a href="framesview.php?what=published&amp;how=paged<?php echo ($_REQUEST['framed']) ? "&framed=yes" : "" ; ?>"><?php echo _("published") ?></a><a href="rss.php"><img class="valign" border="0" src="rss.gif"></a></li>
<li><a href="dino.php"><?php echo _("dinosaurs") ?></a></li>
</ul>
<ul>
<li><a href="prefs.php<?php echo ($_REQUEST['framed']) ? "?framed=yes" : "" ; ?>">prefs</a></li>
<li><a href="javascript:<?php echo ($_REQUEST['framed']) ? "parent.items." : ""; ?>toggle_expand_all()"><?php echo _("toggle collapse") ?></a></li>
<li><a href="javascript:<?php echo ($_REQUEST['framed']) ? "parent.items." : ""; ?>toggle_flags_all()"><?php echo _("toggle flags") ?></a></li>
<li><a href="update.php"><?php echo _("update all") ?></a></li>
</ul>
<ul>
<form id="SearchForm" method="get" action="framesview.php">
<?php
	echo ($_REQUEST['framed']) ? "<input type='hidden' name='framed' value='yes'>" : "";
?>
<input type="hidden" name="how" value="paged"><input type="hidden" name="what" value="search"><input style=".input" type="reset" value="<?php echo _("Clear") ?>"></input><input style=".input" type="text" height="8" size="8" maxlength="40" name ="search" value=""><input style=".input" type="submit" value="<?php echo _("Find") ?>">
</form>
</ul>
<ul>
<?php
//document.getElementById("TagsForm").NewTags.checked
if ($_REQUEST['framed']) {
	$onchangerequest  ="javascript:";
    $onchangerequest .="if(document.getElementById('TagsForm').NewTags.checked){";
    $onchangerequest .="parent.menu.location.href='feeds.php?newonly=yes&";
    $onchangerequest .=($_REQUEST['framed']) ? "framed=yes&" : "";
	$onchangerequest .="tags='+document.getElementById('TagsForm').tags.value;";
	$onchangerequest .="parent.items.location.href='framesview.php?";
	$onchangerequest .=($_REQUEST['framed']) ? "framed=yes&how=paged&" : "";
	$onchangerequest .="tags='+document.getElementById('TagsForm').tags.value;";
	$onchangerequest .="parent.controls.location.reload();";
    $onchangerequest .= "}else if(!document.getElementById('TagsForm').NewTags.checked){";
    $onchangerequest .="parent.menu.location.href='feeds.php?";
    $onchangerequest .=($_REQUEST['framed']) ? "framed=yes&" : "";
	$onchangerequest .="tags='+document.getElementById('TagsForm').tags.value;";
	$onchangerequest .="parent.items.location.href='framesview.php?";
	$onchangerequest .=($_REQUEST['framed']) ? "framed=yes&how=paged&" : "";
	$onchangerequest .="tags='+document.getElementById('TagsForm').tags.value;";
	$onchangerequest .="parent.controls.location.reload();";
    $onchangerequest .= "};";
?>

	<form id="TagsForm">
New Feeds
<input name="NewTags" type="checkbox" id="NewTags" onCheck="parent.menu.location = parent.menu.location + '&newonly=yes';" onUnCheck="parent.menu.location = parent.menu.location + '&newonly='" onclick="if(NewTags.checked){eval(NewTags.getAttribute('onCheck'));}else if(!NewTags.checked){eval(NewTags.getAttribute('onUnCheck'));};" >
<select name="tags" fontsize="8" onchange="<?php echo $onchangerequest ?>">
<?php
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

?></select>
	</form>
	<input name="reapplyTagsButton" onclick="<?php echo $onchangerequest ?>" style=".input" type="submit" value="<?php echo _("Reapply") ?>">
</p>
	<?php
} #end framed
?>
</ul>
<ul>
<li><a href="javascript:<?php echo ($_REQUEST['framed']) ? "parent.items." : ""; ?>mark_read()"><?php echo _("mark as read") ?></a></li>
<li><a href="javascript:<?php echo ($_REQUEST['framed']) ? "parent.items." : ""; ?>mark_unread()"><?php echo _("mark as unread") ?></a></li>
<li><a href="javascript:<?php echo ($_REQUEST['framed']) ? "parent.items." : ""; ?>flag_all();<?php echo ($_REQUEST['framed']) ? "parent.items." : ""; ?>mark_read()"><?php echo _("mark all read") ?></a></li>
</ul>
<ul>
<li><a href="<?php echo ($_REQUEST['framed']) ? "index" : "frames" ; ?>.php" target="_top"><?php echo ($_REQUEST['framed']) ? _("panel") : _("frames") ?></a></li>
<li><a href="add.php"><?php echo _("add feeds") ?></a></li>
<li><a href="javascript:<?php echo ($_REQUEST['framed']) ? "parent.items." : ""; ?>location.reload()<?php echo ($_REQUEST['framed']) ? ";parent.menu.location.reload();" : ""; ?>"><?php echo _("refresh view") ?></a></li>
<li><a href="http://www.monkeychow.org"><?php echo _("about") ?></a></li>
<li><a href="logout.php" target="_top">log out</a></li>
</ul>
</div>
<?php 
	if($_REQUEST['framed']) 
	{ 
?>
		</body></html>
		<?php 
	}
		?>
