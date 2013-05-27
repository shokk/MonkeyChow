<?php

include_once("init.php");
include_once("rss10.inc");

$rss =new RSSWriter(
	REPUBLISH_CHANNEL_URL,
	REPUBLISH_CHANNEL_TITLE,
	REPUBLISH_CHANNEL_DESC
);

$sql="select '$FOF_FEED_TABLE.tags' as 'feed_tags', 'items.read' as 'item_read', '$FOF_FEED_TABLE.title' as 'feed_title', '$FOF_FEED_TABLE.link' as 'feed_link', $FOF_FEED_TABLE.description as feed_description, items.id as item_id, items.link as item_link, items.title as item_title, UNIX_TIMESTAMP(items.timestamp) as timestamp, items.content as item_content, items.dcdate as dcdate, items.dccreator as dccreator, items.dcsubject as dcsubject, items.publish as item_publish, items.star as item_star from $FOF_FEED_TABLE, items where items.feed_id=$FOF_FEED_TABLE.id and items.publish=1 and $FOF_FEED_TABLE.private=0";

$mytags = mysql_escape_string($_REQUEST['tags']);
$sql .= ($_REQUEST['tags']) ? " and $FOF_FEED_TABLE.tags LIKE \"%" . $mytags . "%\"" : "";

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
