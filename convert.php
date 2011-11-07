<?php
error_reporting(E_ERROR);
include_once("init.php");
include_once("fof-main.php");
/*
This script is a quick alternative to running install.php.  It will add the flags table and its required values, as well as subscribing all users to the existing feeds and adding all existing publish, starred, and read information to their account.  If you prefer that they do not have these settings, click cancel now.  Otherwise click OK.
*/

/* Table creation */
$tables[] = <<<EOQ
CREATE TABLE IF NOT EXISTS `flags` (
  `flag_id` int(11) NOT NULL auto_increment,
  `flag_name` varchar(100) NOT NULL,  PRIMARY KEY  (`flag_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;
EOQ;
foreach($tables as $table)
{
    if(!fof_do_query($table, 1))
    {
        echo "Can't create table.  MySQL says: <b>" . mysql_error() . "</b><br>";
    }
}

/* Data creation */
if(mysql_num_rows(fof_do_query("select * from flags ORDER BY flag_id ASC LIMIT 1")) == 0)
{
    if(!fof_do_query("INSERT INTO `flags` (`flag_id`, `flag_name`) VALUES (1,'read'), (2,'star'), (3,'publish'), (4,''), (5,''), (6,''), (7,''), (8,''), (9,''), (10,'');",1) && mysql_errno() != 1061)      
	{
		exit (_("Can't create flags.") . "  " . _("Mysql says") . ": <b>" . mysql_error() . "</b><br />");
	}
}
else
{
		echo "Flag values already exist</b><br />";
}

/* Required queries for conversion */
$sql_users_query = "SELECT user_id FROM users";
$user_result = mysql_query($sql_users_query);

$sql_feeds_query = "SELECT id, tags FROM feeds";
$feeds_result = mysql_query("$sql_feeds_query");

$sql_items_query = "SELECT `id`, `read`, `publish`, `star` FROM `items`";
$items_result = mysql_query("$sql_items_query");

/* Conversion 1: subscribe all users to all feeds */
if(mysql_num_rows(fof_do_query("select * from subscription")) == 0)
{
	echo "Converting Subscriptions table...<br /><br />";
	while($user_id_row = mysql_fetch_array($user_result))
	{
		while($feed_id_row = mysql_fetch_array($feeds_result))
		{
			$sql_insert_query="INSERT INTO `subscription` (`feed_id`, `user_id`, `tags`) VALUES ('" . $feed_id_row['id'] . "', '" . $user_id_row['user_id'] . "', $feed_id_row['tags']); ";
			#echo $sql_insert_query . "<br />\n";
			fof_do_query($sql_insert_query);
		}
		mysql_data_seek($feeds_result, 0);
	}
}
else
{
	echo "Subscription tables already converted<br /><br />";
}

mysql_data_seek($user_result, 0);
mysql_data_seek($feeds_result, 0);
mysql_data_seek($items_result, 0);

/* Conversion 2: add existing article settings to all articles for all users */
if(mysql_num_rows(fof_do_query("select * from user_items")) == 0)
{
	echo "Converting User Items table...<br /><br />";
	while($user_id_row = mysql_fetch_array($user_result))
	{
		
		while($item_id_row = mysql_fetch_array($items_result))
		{
			$sql_insert_query="";
			if ($item_id_row['read'] == "1")
			{
				$sql_insert_query = "INSERT INTO `user_items` (`user_id`, `item_id`, `flag_id`) VALUES ('" . $user_id_row['user_id'] . "', '" . $item_id_row['id'] ."', '1'); ";
				if(!fof_do_query($sql_insert_query)  && mysql_errno() != 1061)
				{
					echo $sql_insert_query . "<br /><br />";
					exit (_("Can't insert user_items.") . "  " . _("Mysql says") . ": <b>" . mysql_error() . "</b><br />$sql_insert_query");
				}
			}
			if ($item_id_row['star'] == "1")
			{
				$sql_insert_query = "INSERT INTO `user_items` (`user_id`, `item_id`, `flag_id`) VALUES ('" . $user_id_row['user_id'] . "', '" . $item_id_row['id'] . "', '2'); ";
				if(!fof_do_query($sql_insert_query)  && mysql_errno() != 1061)
				{
					echo $sql_insert_query . "<br /><br />";
					exit (_("Can't insert user_items.") . "  " . _("Mysql says") . ": <b>" . mysql_error() . "</b><br />$sql_insert_query");
				}
			}
			if ($item_id_row['publish'] == "1")
			{
				$sql_insert_query = "INSERT INTO `user_items` (`user_id`, `item_id`, `flag_id`) VALUES ('" . $user_id_row['user_id'] . "', '" . $item_id_row['id'] . "', '3'); ";
				if(!fof_do_query($sql_insert_query)  && mysql_errno() != 1061)
				{
					echo $sql_insert_query . "<br /><br />";
					exit (_("Can't insert user_items.") . "  " . _("Mysql says") . ": <b>" . mysql_error() . "</b><br />$sql_insert_query");
				}
			}
		}
		mysql_data_seek($items_result, 0);
	}
}
else
{
	echo "User Item tables already converted<br /><br />";
}

mysql_data_seek($user_result, 0);
mysql_data_seek($items_result, 0);
mysql_data_seek($feeds_result, 0);

echo "convert.php Done."
?>
