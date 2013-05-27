<?php

/*
 * This file is part of Monkeychow - http://shokk.wordpress.com/tag/monkeychow/
 *
 * index.php - the 'control panel'
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

include_once("init.php");
include_once("fof-main.php");

header("Content-Type: text/html; charset=utf-8");

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">

	<head>
		<title>MonkeyChow - <?php echo _("control panel") ?></title>
<?php
	if (eregi("feeds.php",$_REQUEST['PHP_SELF']) && isset($fof_user_prefs['feedsrefresh']) && $fof_user_prefs['feedsrefresh'] != 0)
	{
		echo "        <meta http-equiv=\"refresh\" content=\"" . $fof_user_prefs['feedsrefresh']*60 . ";url=" . $_REQUEST['REQUEST_URI'] . "\">\n";
	}
?>
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
		<script src="fof.js" type="text/javascript"></script>
        <script src="behindthescenes.js"></script>
		<link rel="stylesheet" href="fof-common.css" media="all" />
		<meta name="ROBOTS" content="NOINDEX, NOFOLLOW" />
		<?php 
			if ($_REQUEST['framed']) {
					print '<base target="items" />';
			}
		?>

	</head>
