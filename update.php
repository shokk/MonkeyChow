<?php
/*
 * This file is part of Monkeychow - http://shokk.wordpress.com/tag/monkeychow/
 *
 * update.php - updates all feeds with feedback
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

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">

<head><title>MonkeyChow - <?php _("update") ?></title>
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
		<link rel="stylesheet" href="fof-common.css" media="all" />
		<script src="fof.js" type="text/javascript"></script>
		<meta name="ROBOTS" content="NOINDEX, NOFOLLOW" />
</head>
<body id="panel-page">

<?php // Menu panel ?>
<script type="text/javascript" />
if (top==self) document.writeln('<?php
$handle = fopen("panel-menu.html", "r");
   $xml = "";
   if ($handle)
   {
       while (!feof($handle))
       {
           $xml .= fread($handle, 128);
       }
       fclose($handle);
   }
   $xml = substr("$xml", 0, -1); // Perl style chop
   print $xml;
?>');
</script>
<BR>
<?php
//prune_feeds();

$feed = $_REQUEST['feed'];
$tags = $_REQUEST['tags'];

$sql = "select DISTINCT " . $FOF_FEED_TABLE . ".url, " . $FOF_FEED_TABLE . ".id, " . $FOF_FEED_TABLE . ".title from " . $FOF_FEED_TABLE . ", " . $FOF_SUBSCRIPTION_TABLE . " where " . $FOF_FEED_TABLE . ".id = " . $FOF_SUBSCRIPTION_TABLE . ".feed_id and " . $FOF_SUBSCRIPTION_TABLE . ".user_id = " . current_user();

if($feed)
{
    $sql .= " and " . $FOF_FEED_TABLE . ".id = $feed";
}

if($tags)
{
    $sql .= " and " . $FOF_SUBSCRIPTION_TABLE . ".tags like '%" . $tags . "%'";
}

$sql .= " order by title";

#echo "$sql<br />";
$result = fof_do_query($sql);

while($row = mysql_fetch_array($result))
{
	#$title = $row['title'];
	#$id = $row['id'];
	print _("Updating") . " <b>" . $row['title'] . "</b>...";
        flush();

    //fof_prune_feed($row['id']);
	$count = fof_update_feed($row['url']);

	print "<font color=\"green\">" . _("done. ") . "</font>";

	if($count)
	{
		print "<b><font color=red>$count " . _("new items") . "</font></b>";
	}
	print "<br>";
#        flush();
}


// Menu panel
?>
<BR>

<script type="text/javascript" />
if (top==self) document.writeln('<?php
$handle = fopen("panel-menu.html", "r");
   $xml = "";
   if ($handle)
   {
       while (!feof($handle))
       {
           $xml .= fread($handle, 128);
       }
       fclose($handle);
   }
   $xml = substr("$xml", 0, -1); // Perl style chop
   print $xml;
?>');
</script>

<?php
		flush();
?>
</body></html>
