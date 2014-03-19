<?php
/*
 * This file is part of Monkeychow - http://shokk.wordpress.com/tag/monkeychow/
 *
 * update.php - updates all feeds with feedback
 *
 *
 * Copyright (C) 2006 Ernie Oporto
 * ernieoporto@yahoo.com - http://shokk.wordpress.com
 *
 * Distributed under the GPL - see LICENSE
 *
 */

include_once("init.php");
include_once("fof-main.php");

header("Content-Type: text/html; charset=utf-8");

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">

<head><title>MonkeyChow - <?php echo _("dinosaur feeds") ?></title>
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
		<link rel="stylesheet" href="fof-common.css" media="all" />
		<script src="fof.js" type="text/javascript"></script>
		<meta name="ROBOTS" content="NOINDEX, NOFOLLOW" />
</head>
<body>
<center><h1><?php echo _("dinosaurs") ?></h1></center>
<?php echo _("The following feeds have not been updated in at least 30 days.  Consider deleting them from your feed list to save update time.") ."</br></br>" ?>
<table cellspacing="0" cellpadding="1" border="0">
<tr class="heading">

<td><nobr></nobr></td>
<td><nobr><b><?php echo _("Title") ?></b></nobr></td>
<td><nobr><b><?php echo _("Last Updated") ?></b></nobr></td>
</tr>
<?php

$num_feed_list=fof_get_subscribed_feeds_list();
$sql = "select `" . $FOF_FEED_TABLE . "`.`link`,`" . $FOF_FEED_TABLE . "`.`url`,`" . $FOF_FEED_TABLE . "`.`id`,`" . $FOF_FEED_TABLE . "`.`title` from `" . $FOF_FEED_TABLE . "` where `" . $FOF_FEED_TABLE . "`.`id` in (" . $num_feed_list . ") order by title";
//echo "$sql</br>";
$result = fof_do_query($sql);
while($row = mysql_fetch_array($result))
{
    $id = $row['id'];
    if ($id)
    {
        $age = fof_rss_age($id);
        if ($age == FOF_MAX_INT) {
            $agestr = "never";
            $agestrabbr = "&infin;";
        } else {
            $seconds = $age % 60;
            $minutes = $age / 60 % 60;
            $hours = $age / 60 / 60 % 24;
            $days = floor($age / 60 / 60 / 24);
            if ($seconds) {
                $agestrabbr = $seconds . "s";
            }
            if ($minutes) {
                $agestrabbr = $minutes . "m";
            }
            if ($hours) {
                $agestrabbr = $hours . "h";
            }
            if ($days) {
                $agestrabbr = $days . "d";
            }
        }
        #$feeds[$i]['agestrabbr'] = $agestrabbr;
        if ($age > 2592000)
        {
            print "<tr><td>";
    ?><a href="delete.php?feed=<?php echo $row['id'] ?>" title="<?php echo _("delete") ?>" onclick="return confirm('<?php echo _("Are you sure?") ?>')"><?php echo _("delete") ?></a> &nbsp; &nbsp; &nbsp; &nbsp; &nbsp;<?php
            print "</td><td>";
            print "<a href=\"" . $row['link'] . "\">" . $row['title'] . "</a>";
            print "</td><td>";
            print $agestrabbr; #.$age.$row['timestamp'];
            print "</td></tr>";
        }
    }
}
?>
</table>
<BR>

<a href="view.php"><?php echo _("Return to new items") ?>.</a><br />

</body></html>
