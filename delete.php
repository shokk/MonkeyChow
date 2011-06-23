<?php
/*
 * This file is part of Monkeychow - http://monkeychow.org
 *
 * delete.php - deletes a feed and all items
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

include_once("init.php");
include_once("fof-main.php");

header("Content-Type: text/html; charset=utf-8");

?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">

<head><title>MonkeyChow - delete</title>
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
		<link rel="stylesheet" href="fof-common.css" media="all" />
		<script src="fof.js" type="text/javascript"></script>
		<meta name="ROBOTS" content="NOINDEX, NOFOLLOW" />
</head>
<body>

<?php

$feed = $_REQUEST['feed'];
$framed = $_REQUEST['framed'];

$result = fof_do_query("delete from feeds where id = $feed");
$result = fof_do_query("delete from items where feed_id = $feed");


if (eregi("feeds.php",$_SERVER['HTTP_REFERER']))
{
    echo "Deleted.  <a href=\"";
    echo ($framed) ? "framesview.php" : "index.php";
    echo ($framed) ? "?framed=yes" : "";
    echo "\">" . _("Return to new items") . "</a>";
}
else
{
   echo $_REQUEST['ref'] . ":";
   echo "Deleted.  <a href=\"index.php\">Return to new items.</a>";
}
?>

</body></html>
