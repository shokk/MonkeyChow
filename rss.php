<?php

include_once("init.php");
include_once("rss10.inc");

$rss =new RSSWriter(
	REPUBLISH_CHANNEL_URL,
	REPUBLISH_CHANNEL_TITLE,
	REPUBLISH_CHANNEL_DESC
);

$sql="select '$FOF_FEED_TABLE.tags' as 'feed_tags', '$FOF_ITEM_TABLE.read' as 'item_read', '$FOF_FEED_TABLE.title' as 'feed_title', '$FOF_FEED_TABLE.link' as 'feed_link', $FOF_FEED_TABLE.description as feed_description, $FOF_ITEM_TABLE.id as item_id, $FOF_ITEM_TABLE.link as item_link, $FOF_ITEM_TABLE.title as item_title, UNIX_TIMESTAMP($FOF_ITEM_TABLE.timestamp) as timestamp, $FOF_ITEM_TABLE.content as item_content, $FOF_ITEM_TABLE.dcdate as dcdate, $FOF_ITEM_TABLE.dccreator as dccreator, $FOF_ITEM_TABLE.dcsubject as dcsubject, $FOF_ITEM_TABLE.publish as item_publish, $FOF_ITEM_TABLE.star as item_star from $FOF_FEED_TABLE, $FOF_ITEM_TABLE where $FOF_ITEM_TABLE.feed_id=$FOF_FEED_TABLE.id and $FOF_ITEM_TABLE.publish=1 and $FOF_FEED_TABLE.private=0";
#$flag_sql = "SELECT blah FROM `" . $FOF_USERITEM_TABLE . "` WHERE `flag_id`="3" AND `item_id`=" . $item_id . " AND `user_id`=" . current_user();

$mytags = mysql_escape_string($_REQUEST['tags']);
$sql .= ($_REQUEST['tags']) ? " and $FOF_FEED_TABLE.tags LIKE \"%" . $mytags . "%\"" : "";

$sql.=" order by timestamp desc limit 15";

print $sql . "<br/>\n";
exit;

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
