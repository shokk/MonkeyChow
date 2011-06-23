<?php
/*
 * This file is part of Monkeychow - http://monkeychow.org
 *
 * update.php - updates all feeds with feedback
 *
 *
 * Copyright (C) 2006 Ernie Oporto
 * ernieoporto@yahoo.com - http://www.shokk.com/blog/
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
<?php echo _("The following feeds have not been updated in 30 days.  Consider deleting them from your feed list to save update time.") ?>
<table cellspacing="0" cellpadding="1" border="0">
<tr class="heading">

<td><nobr></nobr></td>
<td><nobr><b><?php echo _("Title") ?></b></nobr></td>
<td><nobr><b><?php echo _("Last Updated") ?></b></nobr></td>
</tr>
<?php
$rightnow=time();

$sql = "select link, url, id, title from feeds order by title";
$result = fof_do_query($sql);

while($row = mysql_fetch_array($result))
{
        $sql2 = "select `timestamp` from items where feed_id = " . $row['id'] . " ORDER BY `timestamp` DESC LIMIT 0,1";
        $result2 = fof_do_query($sql2);
        $row2 = mysql_fetch_array($result2);

#test $row2['timestamp'] to see if it older than 30 days and then proceed if so
    $difftime = $rightnow - strtotime($row2['timestamp']);
    if ($difftime > 2592000)
    {


        print "<tr><td>";
?><a href="delete.php?feed=<?php echo $row['id'] ?>" title="<?php echo _("delete") ?>" onclick="return confirm('<?php echo _("Are you sure?") ?>')"><?php echo _("delete") ?></a> &nbsp; &nbsp; &nbsp; &nbsp; &nbsp;<?php
        print "</td><td>";
        print "<a href=\"" . $row['link'] . "\">" . $row['title'] . "</a>";
        print "</td><td>";
        print $row2['timestamp'];
#print "xx" . strtotime($row2['timestamp']) . "xx<br/>";
#print "yy" . $difftime . "yy<br />";
#print "zz" .  $rightnow . "zz<br />";
        print "</td></tr>";
    }
}
?>
</table>
<BR>

<a href="view.php"><?php echo _("Return to new items") ?>.</a><br />

</body></html>
