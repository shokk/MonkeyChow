<?php
/*
 * This file is part of Monkeychow - http://shokk.wordpress.com/tag/monkeychow/
 *
 * ompl.php - exports subscription list as OPML
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

header("Content-Type: text/xml; charset=utf-8");
include_once("init.php");
include_once("config.php");
$tags = ereg_replace("[^A-Za-z0-9 ]", "", $tags);
$tags = strip_tags($_REQUEST['tags']);

echo '<?xml version="1.0"?>';

$result = fof_do_query("SELECT date_added FROM `$FOF_FEED_TABLE` ORDER BY `date_added` DESC LIMIT 1");
$row = mysql_fetch_array($result);
$date_modified=htmlspecialchars($row['date_added']);

$result = fof_do_query("SELECT date_added FROM `$FOF_FEED_TABLE` ORDER BY `date_added` ASC LIMIT 1");
$row = mysql_fetch_array($result);
$date_created=htmlspecialchars($row['date_added']);
?>

<opml version="1.0">
  <head>
    <title><?php echo REPUBLISH_CHANNEL_TITLE; ?></title>
    <dateCreated><?php echo $date_created; ?></dateCreated>
    <ownerName><?php echo FULLNAME; ?></ownerName>
    <ownerEmail><?php echo EMAIL; ?></ownerEmail>
    <dateModified><?php echo $date_modified; ?></dateModified>
  </head>
  <body>
<?php

$sql = "select url, title, link, description, date_added from `$FOF_FEED_TABLE` where private=0";
if (isset($tags) && ($tags != _("All tags")) && ($tags != _("No tags")) )
{
    $sql .= " and tags LIKE '%$tags%'";
}
$sql .= " order by title";
$result = fof_do_query($sql);

while($row = mysql_fetch_array($result))
{
	$url = htmlspecialchars($row['url']);
	$title = htmlspecialchars($row['title']);
	$link = htmlspecialchars($row['link']);
	$description = htmlspecialchars($row['description']);

	echo <<<HEYO
    <outline description="$description"
             type="rss" 
             htmlUrl="$link"
             title="$title"
             text="$title"
             xmlUrl="$url"
    />
HEYO;
}
?>
  </body>
</opml>
