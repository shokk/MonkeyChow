<?php
/*
 * This file is part of FEED ON FEEDS - http://feedonfeeds.com/
 * This has been adapted for use with MonkeyChow by Ernie Oporto
 *
 * logout.php - kills user cookie, redirects to login page
 *
 * Copyright (C) 2004-2007 Stephen Minutillo
 * steve@minutillo.com - http://minutillo.com/steve/
 *
 * Distributed under the GPL - see LICENSE
 *
 */

ob_start();
$fof_no_login = true;

include_once("fof-main.php");
fof_logout();

header("Content-Type: text/html; charset=utf-8");

header("Location: login.php");

ob_end_flush();
?>
