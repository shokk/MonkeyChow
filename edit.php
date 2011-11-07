<?php
/*
 * This file is part of Monkeychow - http://monkeychow.org
 *
 * edit.php - displays form to edit a feed
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

$sql = "SELECT tags FROM `feeds` WHERE tags != '' group by tags";
$result = fof_do_query($sql);
while($row = mysql_fetch_array($result))
{
    $tagarray = array_unique(array_merge($tagarray, parse_tag_string($row['tags'])));
}
sort($tagarray);
$tags_list = join("\",\"", $tagarray);
$tags_list = "\"" . $tags_list . "\"";

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html>
<head><title>MonkeyChow - edit a feed</title>
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
		<link rel="stylesheet" href="fof-common.css" media="all" />
		<script src="fof.js" type="text/javascript"></script>
		<meta name="ROBOTS" content="NOINDEX, NOFOLLOW" />
</head>

<body>

<BR>
<?php
    $edit_flag = $_REQUEST['edit_flag'];
    $feed = $_REQUEST['feed'];
    if ($edit_flag == 0)
    {
        $sql = "select $FOF_FEED_TABLE.url, $FOF_FEED_TABLE.image, $FOF_FEED_TABLE.title, $FOF_FEED_TABLE.link, $FOF_FEED_TABLE.description, $FOF_FEED_TABLE.date_added, $FOF_SUBSCRIPTION_TABLE.tags, $FOF_FEED_TABLE.aging, $FOF_FEED_TABLE.expir, $FOF_FEED_TABLE.private from $FOF_FEED_TABLE,$FOF_SUBSCRIPTION_TABLE where $FOF_FEED_TABLE.id = $FOF_SUBSCRIPTION_TABLE.feed_id and $FOF_FEED_TABLE.id = " . $feed;

		if(!fof_is_admin())
		{
			$sql .= " and $FOF_SUBSCRIPTION_TABLE.user_id=" . current_user();
		}
        $result = fof_do_query($sql);
        $row = mysql_fetch_array($result);
        $title = $row['title'];
        $url = $row['url'];
        $description = $row['description'];
        $link = $row['link'];
        $date_added = $row['date_added'];
        $tags = $row['tags'];
        $aging = $row['aging'];
        $expir = $row['expir'];
        $feed_icon = $row['image'];
        $private = $row['private'];
        #echo ":SQL: " . $sql . "<br />\n";
?>

<BR><br>
<form method="get" action="edit.php" enctype="multipart/form-data">
<input type="hidden" name="edit_flag" value="1">
<input type="hidden" name="framed" value="yes">
<input type="hidden" name="feed" value="<?php echo $feed ?>">
<input type="hidden" name="ref" value="<?php echo $_SERVER['HTTP_REFERER'] ?>">

<table>
<tr>
<td>URL:</td><td>
<?php
if(fof_is_admin())
{
		echo "<input type=\"text\" name=\"rss_url\" size=\"40\" value=\"" . $url . "\">";
}
else
{
		echo $url;
}
?>
</td>
</tr><tr>
<td>TITLE:</td><td>
<?php
if(fof_is_admin())
{
		echo "<input type=\"text\" name=\"rss_title\" size=\"40\" value=\"" . $title . "\">";
}
else
{
		echo $title;
}
?>
</td>
</tr><tr>
<td>LINK:</td><td>
<?php
if(fof_is_admin())
{
		echo "<input type=\"text\" name=\"rss_link\" size=\"40\" value=\"". $link . "\">";
}
else
{
		echo $link;
}
?>
</td>
</tr><tr>
<td>DESCR:</td><td>
<?php
if(fof_is_admin())
{
		echo "<input type=\"text\" name=\"rss_description\" size=\"40\" value=\"" . $description . "\">";
}
else
{
		echo $description;
}
?>
</td>
</tr><tr>
<td>DATE SUBSCRIBED:</td><td>
<?php
if(fof_is_admin())
{
		echo "<input type=\"text\" name=\"rss_date_added\" size=\"40\" value=\"" . $date_added . "\">";
}
else
{
		echo $date_added;
}
?>
</td>
</tr><tr>
<td>TAGS:</td><td><input type="text" name="rss_tags" size="40" value="<?php echo $tags ?>"> (space separated)</td>
</tr><tr>
<td>ARTICLE AGING:</td><td>
<?php
if(fof_is_admin())
{
		echo "<input type=\"text\" name=\"rss_aging\" size=\"40\" value=\"" . $aging . "\"> days. (zero == no expiration)";
}
else
{
		echo $aging;
}
?>
</td>
</tr><tr>
<td>FEED EXPIRATION:</td><td>
<?php
if(fof_is_admin())
{
		echo "<input type=\"text\" name=\"rss_expir\" size=\"40\" value=\"" . $expir  . "\"> days. (zero == no expiration)";
}
else
{
		echo $expir;
}
?>
</td>
</tr><tr>
<td>FEED ICON:</td><td>
<?php
if(fof_is_admin())
{
		echo "<input type=\"text\" name=\"rss_icon\" size=\"40\" value=\"" . $feed_icon  . "\">";
}
else
{
		echo $feed_icon;
}
?>
</td>
</tr><tr>
<td>PRIVATE:</td><td><input type="checkbox" name="rss_private" size="40" <?php  
if ($private == 1)
{
    echo " checked=\"checked\"";
}
?>> (feed or its articles will not be shown in RSS or aggregate outputs)</td>
</tr>
</table>
<input type="Submit" value="Save feed edits"><br><br>
</form>


<?php
     if (eregi("feeds.php",$_SERVER['HTTP_REFERER']))
     {
			 echo "<a href=\"framesview.php?how=paged";
			 	 echo ($_REQUEST['framed']) ? "&framed=yes" : "";
			 echo "\">Return to new items.</a>";
     }
     else
     {
        echo "<a href=\"index.php\">Return to new items.</a>";
     }
    }

    if ($edit_flag == 1)
    {
        $url = $_REQUEST['rss_url'];
        $title = $_REQUEST['rss_title'];
        $link = $_REQUEST['rss_link'];
        $description = $_REQUEST['rss_description'];
        $date_added = $_REQUEST['rss_date_added'];
        $tags = $_REQUEST['rss_tags'];
        $aging = $_REQUEST['rss_aging'];
        $expir = $_REQUEST['rss_expir'];
        $private = $_REQUEST['rss_private'];
        $feed_icon = $_REQUEST['rss_icon'];
        fof_edit_feed($feed, $url, $title, $link, $description, $date_added, $tags, $aging, $expir, $private, $feed_icon);
?>


<?php
     if (eregi("feeds.php",$_REQUEST['ref']))
     {
        echo "Entry saved.";
			echo "<a href=\"";
			echo "index.php";
			#echo "framesview.php?how=paged";
			#echo ($_REQUEST['framed']) ? "&framed=yes" : "";
			echo "\">Return to new items.</a>";
     }
     else
     {
        echo "Entry saved.  <a href=\"index.php\">Return to new items.</a>";
     }
    }
?>

</body>
</html>
