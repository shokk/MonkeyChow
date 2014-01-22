<?php
/*
 * This file is part of Monkeychow - http://shokk.wordpress.com/tag/monkeychow/
 *
 * update-quiet.php - updates all feeds without producing output
 *
 *
 * Copyright (C) 2006 Ernie Oporto
 * ernieoporto@yahoo.com - http://shokk.wordpress.com/
 *
 * Copyright (C) 2004 Stephen Minutillo
 * steve@minutillo.com - http://minutillo.com/steve/
 *
 * Distributed under the GPL - see LICENSE
 *
 */

// this script should be protected by .htaccess so nobody runs it when not intended

$debug = 0;           // admin mode to make sure everything is running smoothly
$segmentsperhour = 5; // is used to ok dividing feed updates across the hour
                      // set segmentsperhour to 0 to disable
                      // set the cronjob accordingly

if (!$debug) ob_start();
include_once("fof-main.php");
include_once("init.php");

$sql_query = "select count(*) from " . $FOF_FEED_TABLE ;

$count = mysql_fetch_array(mysql_query("$sql_query"));
$feedsnum = $count[0];

$feedsfrom=1;
$feedsto=$feedsnum;

if ($debug) echo "$feedsnum total feeds<br />";
if ($segmentsperhour)
{
    $mins = date(i);
    $hourseg=($mins/60);
    $numseg=intval($feedsnum/$segmentsperhour) + 1;
    if ($debug)
    {
	    print $numseg . " feeds per fifth<br />";
	    print $mins . " mins into the hour<br />";
	    print $hourseg * 100 . "% into the hour<br />";
        print "cronjob should be run every " . 60/$segmentsperhour . " minutes.<br />";
    }
    $segment = intval($hourseg * $segmentsperhour) + 1;
    if ($debug) print "segment " . $segment . " (1/" . $segmentsperhour . " of the hour)<br />";
    $feedsfrom = intval($numseg * ($segment - 1));
    $feedsto = intval($numseg * $segment);
}
if ($debug) print "updating feeds $feedsfrom thru $feedsto<br />";
if ($debug) exit;
$numcount = 0;
if ($debug) exit(0);
$sql_query="select distinct " . $FOF_FEED_TABLE . ".url, " . $FOF_FEED_TABLE . ".id, " . $FOF_FEED_TABLE . ".title from " . $FOF_FEED_TABLE . ", " . $FOF_SUBSCRIPTION_TABLE . " where " . $FOF_FEED_TABLE . ".id = " . $FOF_SUBSCRIPTION_TABLE . ".feed_id ";
$sql .= " order by title";
if ($debug) print "$sql_query<br />";
$result = fof_do_query($sql_query);

while($row = mysql_fetch_array($result))
{
    if (($numcount >= $feedsfrom) && ($numcount <= $feedsto))
    {
	    $title = $row['title'];
	    $id = $row['id'];
        //fof_prune_feed($row['id']);
	    fof_update_feed($row['url']);
		if ($debug) print "updating feed " . $numcount . ": " . $title . "<br />\n";
    }
    $numcount++;
}
$result = fof_do_query("optimize table " . $FOF_FEED_TABLE . "," . $FOF_ITEM_TABLE);
$result = fof_do_query("flush tables;");
flush();

if (!$debug) ob_end_clean();
?>
