<?php

include_once("init.php");
include_once("rss10.inc");

$rss =new RSSWriter(
	REPUBLISH_CHANNEL_URL,
	REPUBLISH_CHANNEL_TITLE,
	REPUBLISH_CHANNEL_DESC
);

$sql="select 'feeds.tags' as 'feed_tags', 'items.read' as 'item_read', 'feeds.title' as 'feed_title', 'feeds.link' as 'feed_link', feeds.description as feed_description, items.id as item_id, items.link as item_link, items.title as item_title, UNIX_TIMESTAMP(items.timestamp) as timestamp, items.content as item_content, items.dcdate as dcdate, items.dccreator as dccreator, items.dcsubject as dcsubject, items.publish as item_publish, items.star as item_star from feeds, items where items.feed_id=feeds.id and items.publish=1 and feeds.private=0";

$mytags = mysql_escape_string($_REQUEST['tags']);
$sql .= ($_REQUEST['tags']) ? " and feeds.tags LIKE \"%" . $mytags . "%\"" : "";

$sql.=" order by timestamp desc limit 15";
$result = fof_do_query($sql);

$timestamp;
while($row = mysql_fetch_array($result)) {
	$moredata = array();
	#$desc = RSSWriter::deTag($row['item_content']);
	$desc = $row['item_content'];
	if ( $desc && isset($_REQUEST['crop']) ) {
		$desc = crop($desc, intval($_REQUEST['crop']));
	}
	$moredata['description'] = $desc;	
	
	if ( $row['dcdate'] ) {
		$moredata['dc:date'] = $row['dcdate'];
	}
	if ( $row['dccreator'] ) {
		$moredata['dc:creator'] = $row['dccreator'];
	}
	if ( $row['dcsubject'] ) {
		$moredata['dc:subject'] = $row['dcsubject'];
	}
	$moredata['dc:identifier'] = $row['item_link'];
	$rss->addItem(
		$row['item_link'],
		$row['item_title'],
		$moredata);
}

$rss->serialize();


function crop($str, $len) {
	if ( strlen($str) < $len ) {
		return $str;
	}
	
	if ( strpos($str, '.') && strpos($str, '.') < $len ) {
		return substr($str, 0, strpos($str, '.')+1 );
	}
	elseif ( strpos($str, '?') && strpos($str, '?') < $len ) {
		return substr($str, 0, strpos($str, '?')+1 );
	}
	elseif ( strpos($str, '!') && strpos($str, '!') < $len ) {
		return substr($str, 0, strpos($str, '!') +1);
	}
	else {
		return substr($str, 0, strpos($str, ' ', $len)) . '...';
	}
}

?>
