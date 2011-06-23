<?php
/*
 * This file is part of Monkeychow - http://monkeychow.org
 *
 * view-action.php - marks selected items as read (or unread)
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

/* See what we've been asked to do */
switch ($_REQUEST['action'])
{
    case 'read':
	$to_set = '`read`=1';
	break;
    case 'unread':
	$to_set = '`read`=NULL';
	break;
    case 'publish':
	$to_set = '`publish`=1';
	break;
    case 'unpublish':
	$to_set = '`publish`=0';
	break;
    case 'star':
	$to_set = '`star`=1';
	break;
    case 'unstar':
	$to_set = '`star`=0';
	break;
    default:
	/* XXX - Probably ought to complain */
	break;
}

/* Build lists of all of the checked and starred items we need to
 * apply the action to.
 */
$ids = array();
while (list($key, $val) = each ($_REQUEST))
{
	if ($val == "checked")
        {
           if (eregi("_",$key))
           {
               $ids[] = substring_between($key,"c","_");
           }
           else
           {
		$ids[] = substr($key, 1);
           }
        }
	elseif ($val == "starred")
		$ids[] = substr($key, 4);
}

/* Now apply the action to all of the items. */
$id_list = implode(",", $ids);

$sql = <<<EOT
UPDATE	items
SET	$to_set,
	timestamp = timestamp
WHERE	id in ($id_list)
EOT;

#echo $sql;
fof_do_query($sql);
header("Location: " . urldecode($_REQUEST['return']));

?>
