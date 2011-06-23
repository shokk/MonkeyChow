<?php
/*
 * This file is part of Monkeychow - http://monkeychow.org
 *
 * update-quiet.php - updates all feeds without producing output
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

$debug = 0;

if (!$debug) ob_start();
include_once("fof-main.php");
include_once("init.php");

$count = mysql_fetch_array(mysql_query("select count(*) from feeds"));
$feedsnum = $count[0];
$feedsfrom=1;
$feedsto=$feedsnum;
if ($debug) echo "$feedsnum total feeds<br />";

$mins = date(i);
$hourseg=($mins/60);
$numseg=intval($feedsnum/5) + 1;
if ($debug)
{
	print $numseg . " feeds per fifth<br />";
	print $mins . " mins into the hour<br />";
	print $hourseg * 100 . "% into the hour<br />";
}
$segment = intval($hourseg * 5) + 1;
if ($debug) print "segment " . $segment . " (fifth of the hour)<br />";
$feedsfrom = intval($numseg * ($segment - 1));
$feedsto = intval($numseg * $segment);
if ($debug) print "updating feeds $feedsfrom thru $feedsto<br />";

$numcount = 0;
if ($debug) exit(0);
$sql_query="select url, id, title from feeds"; #" order by title";
if ($debug) print "$sql_query<br />";
$result = fof_do_query($sql_query);
while($row = mysql_fetch_array($result))
{
    if (($numcount >= $feedsfrom) && ($numcount <= $feedsto))
    {
	    $title = $row['title'];
	    $id = $row['id'];
        fof_prune_feed($row['id']);
	    fof_update_feed($row['url']);
		if ($debug) print "updating feed " . $numcount . ": " . $title . "<br />\n";
    }
    $numcount++;
}
$result = fof_do_query("optimize table feeds,items");
$result = fof_do_query("flush tables;");
flush();

ob_end_clean();
?>
