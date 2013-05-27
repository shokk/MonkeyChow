<?php
/*
 * This file is part of Monkeychow - http://shokk.wordpress.com/tag/monkeychow/
 *
 * uninstall.php - if confirmed, drops FoF's tables
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

include_once("fof-main.php");
include_once("init.php");

header("Content-Type: text/html; charset=utf-8");

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">

	<head>
		<title>MonkeyChow - <?php echo _("uninstallation")?></title>
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
		<link rel="stylesheet" href="fof-common.css" media="all" />
		<script src="fof.js" type="text/javascript"></script>
		<meta name="ROBOTS" content="NOINDEX, NOFOLLOW" />
	</head>

	<body id="panel-page">


<?php
if($_REQUEST['really'])
{

$query = <<<EOQ
DROP TABLE `feeds`;
EOQ;

fof_do_query($query);

$query = <<<EOQ
DROP TABLE `items`;
EOQ;

fof_do_query($query);

echo _("Done.  Now just delete this entire directory and we'll forget this ever happened.");
}
else
{
?>
<script>
if(confirm( _("Do you really want to uninstall MonkeyChow?") ))
{
	document.location = document.location + '?really=really';
}
</script>
<a href="."><b>panel</b></a>
</body></html>
<?php } ?>
