<?php
/*
 * This file is part of Monkeychow - http://shokk.wordpress.com/tag/monkeychow/
 *
 * view-action.php - marks selected items as read (or unread)
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

include_once("fof-main.php");
include_once("init.php");

header("Content-Type: text/html; charset=utf-8");

/* See what we've been asked to do */
switch ($_REQUEST['action'])
{
    case 'read':
		$to_set = '1';
		$who_set = '';
		break;
    case 'publish':
		$to_set = '3';
		$who_set = '';
		break;
    case 'star':
		$to_set = '2';
		$who_set = '';
		break;
    case 'unread':
		$who_set = '1';
		$to_set = '0';
		break;
    case 'unpublish':
		$who_set = '3';
		$to_set = '0';
		break;
    case 'unstar':
		$who_set = '2';
		$to_set = '0';
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
#$id_list = implode(",", $ids);

# foreach through the ids to build the correct query
#INSERT INTO `$FOF_ITEM_TABLE` (`user_id`, `item_id`, `flag_id`) VALUES ('2', '843132', '1'), ('2', '843131', '1');

switch ($_REQUEST['action'])
{
    case 'read':
    case 'star':
    case 'publish':
		$sql = "INSERT IGNORE INTO `$FOF_USERITEM_TABLE` (`user_id`, `item_id`, `flag_id`) VALUES ";
		$sqlvalues="";
		foreach ($ids as $id)
		{
			if ($sqlvalues == "")
			{
				$sqlvalues = "('" . current_user() . "', '" . $id . "', '" . $to_set . "')";
			}
			else
			{
				$sqlvalues .= ", ('" . current_user() . "', '" . $id . "', '" . $to_set . "')";
			}
		}
		$sql .= $sqlvalues;
		break;
    case 'unread':
    case 'unstar':
    case 'unpublish':
		foreach ($ids as $id)
		{
			$sql .= "DELETE FROM `$FOF_USERITEM_TABLE` WHERE `user_id`=" . current_user() . " AND `item_id`=" . $id . " AND `flag_id`=" . $who_set . "; ";
		}
		break;
}

/*
echo "*** SQL: " . $sql;
exit;
*/
fof_do_query($sql);
header("Location: " . urldecode($_REQUEST['return']));

?>
