<?php

/**
 * This file is part of Monkeychow - http://shokk.wordpress.com/tag/monkeychow/
 * 
 * init.php - initializes MC, and contains functions used from other scripts
 *
 * PHP version 5
 *
 * Copyright (C) 2006 Ernie Oporto
 * ernieoporto@yahoo.com - http://shokk.wordpress.com
 *
 * Copyright (C) 2004 Stephen Minutillo
 * steve@minutillo.com - http://minutillo.com/steve/
 *
 * LICENSE: Distributed under the GPL - see LICENSE
 *
 * @category  Stand-alone_Multi-user_RSS_Feed_Reader
 * @package   MonkeyChow
 * @author    Ernie Oporto <ernieoporto@yahoo.com>
 * @copyright 2006-2013 Ernie Oporto, 2004 Steve Minutillo
 * @license   http://www.gnu.org/licenses/gpl.html GPL License
 * @version   GIT: <git_id>
 * @link      http://shokk.wordpress.org/tag/monkeychow/
 *
 **/

error_reporting(E_ERROR);
require_once('config.php');

define('FOF_MAX_INT', 2147483647);
$_REQUEST['baserequest']=$_SERVER['SERVER_NAME'] . dirname($_SERVER['REQUEST_URI']);
// $_REQUEST['baserequest']= "shokk.shokk.com" . dirname($_SERVER['REQUEST_URI']);

$FOF_FEED_TABLE = FOF_FEED_TABLE;
$FOF_ITEM_TABLE = FOF_ITEM_TABLE;
$FOF_SUBSCRIPTION_TABLE = FOF_SUBSCRIPTION_TABLE;
$FOF_FLAG_TABLE = FOF_FLAG_TABLE;
$FOF_USER_TABLE = FOF_USER_TABLE;
$FOF_USERITEM_TABLE = FOF_USERITEM_TABLE;
$MC_PATH = MC_PATH;

require_once('simplepie/autoloader.php');

// If not in 'safe mode', increase the maximum execution time:
if (!ini_get('safe_mode')) {
    set_time_limit(240);
}

if (!function_exists("gettext")) {
    /**
      * Convert string with gettext
      *
      * @param string $str the text to convert
      *
      * @return string
      */
    function _($str)
    {
        /**
        * @str string
        */
        return $str; 
    }
}

if ( !function_exists('htmlspecialchars_decode') ) {
    /**
      * Converts htmlspecialchars
      *
      * @param string $text the text to convert
      *
      * @return string
      */
    function htmlspecialchars_decode($text)
    {
        // @text string
        return strtr($text, array_flip(get_html_translation_table(HTML_SPECIALCHARS)));
    }
}

$fof_connection = mysql_connect(FOF_DB_HOST, FOF_DB_USER, FOF_DB_PASS) or 
  die ( _("Cannot connect to database.  Check your configuration.") . "  " . _("Mysql says") . ":  <b>" . mysql_error() . "</b>");
mysql_select_db(FOF_DB_DBNAME, $fof_connection) or
  die ( _("Cannot select database.  Check your configuration") . "  " . _("Mysql says") . ":  " . mysql_error());

if (!$installing) {
    is_writable(FOF_CACHE_DIR) or die ( _("Cache directory is not writable or does not exist.") . "  " . _("Have you run") . " <a href=\"install.php\"><code>install.php</code></a>?");
}

/**
 * Prune a feed's old stuff
 *
 * @param string $id the ID of the feed that needs to be pruned
 *
 * @return void
 */
function fof_prune_feed($id)
{
    /**
     * @id string
     */
    $sql = "select aging from" . $FOF_FEED_TABLE . "where id='$id'";
    //$result=fof_do_query($sql);
    $row = mysql_fetch_array(fof_do_query($sql));
    $keep_days = $row['aging'];

    if ($keep_days > 0) {
        $sql = "delete from " . $FOF_ITEM_TABLE . " where `feed_id`=$id AND `star`!=1 AND `read`=1 AND to_days(CURDATE()) - to_days(`timestamp`)  > $keep_days";
        //print "<br />" . $sql . "<br />";
        fof_do_query($sql);
    }
}

/**
 * Nothing to See Here 
 *
 * @return void
 */
function prune_feeds()
{
    global $FOF_FEED_TABLE;
    $sql="SELECT id FROM `" . $FOF_FEED_TABLE . "` ORDER BY `" . $FOF_FEED_TABLE . "`.`id` ASC;";
    $result=fof_do_query($sql);
    while ($row = mysql_fetch_array($result)) {
        $feedsidarray[]=$row['id'];
    }

    $sql="SELECT DISTINCT feed_id FROM `" . $FOF_ITEM_TABLE . "` ORDER BY `" . $FOF_ITEM_TABLE . "`.`feed_id` ASC;";
    $result=fof_do_query($sql);
    while ($row = mysql_fetch_array($result)) {
         $feedsitemsarray[]=$row['feed_id'];
    }
 
    $result = array_diff($feedsitemsarray, $feedsidarray);
    //$sql="delete FROM `" . $FOF_ITEM_TABLE . "` ";
    $sql="";
    foreach ($result as $resultthing) {//print "::" . $resultthing . "::<br />\n";
        if ($sql == "") {
            $sql = "delete FROM `" . $FOF_ITEM_TABLE . "` WHERE `" . $FOF_ITEM_TABLE . "`.`feed_id` LIKE $resultthing ";
        } else {
                $sql .= "OR `" . $FOF_ITEM_TABLE . "`.`feed_id` LIKE $resultthing ";
        }
    }
    //print_r($result);
    //print "<br />" . $sql;
    $result=fof_do_query($sql, 1);
    flush();
}

/** Returns a comma separated list of subscribed feeds with optional tag support.
 *
 * @param string $tags the tags to display feeds for
 *
 * @return string
 */ 
function fof_get_subscribed_feeds_list($tags = null) //Suitable for input into SQL queries
{
    global $FOF_FEED_TABLE, $FOF_SUBSCRIPTION_TABLE;
    $sql = "select $FOF_FEED_TABLE.id from $FOF_FEED_TABLE, $FOF_SUBSCRIPTION_TABLE where $FOF_SUBSCRIPTION_TABLE.user_id = " . current_user() . " and $FOF_FEED_TABLE.id = $FOF_SUBSCRIPTION_TABLE.feed_id ";

    if ($tags != '' && $tags != _("All tags") && $tags != _("No tags")) {
        $sql .= " AND $FOF_SUBSCRIPTION_TABLE.tags LIKE '%$tags%'";
    }
    if ($tags == _("No tags")) {
        $sql .= " AND ($FOF_SUBSCRIPTION_TABLE.tags IS NULL or $FOF_SUBSCRIPTION_TABLE.tags LIKE '')";
    }

    //echo $sql . "<br />\n";
    $result = fof_do_query($sql);

    $i = 0;
    while ($row = mysql_fetch_array($result)) {
        if ($i == 0) {
            $list = $row['id'];
        } else {
            $list .= ", " . $row['id'];
        }
        $i++;
    }
    return $list;
}

/**
 * Get the list of subscribed feeds
 *
 * @param string $order     the order to sort the feed
 * @param string $direction normal or reverse sort?
 * @param string $tags      the tags to display feeds for
 *
 * @return array
 */
function fof_get_feeds($order = 'title', $direction = 'asc', $tags = null)
{
    global $FOF_FEED_TABLE, $FOF_USER_TABLE, $FOF_FLAG_TABLE, $FOF_ITEM_TABLE, $FOF_SUBSCRIPTION_TABLE, $FOF_USERITEM_TABLE;
    //fof_prune_expir_feeds();
    // subscriptions query returns results
    // need to provide tag so that list can be filtered
    // fof_db_get_subscriptions(current_user(), $order, $direction, $tags);    
    // $sql = "SELECT id, url, title, link, image, description FROM " . $FOF_FEED_TABLE;

    if (fof_is_admin()) {
        $sql2 = "select distinct ";
    } else {
        $sql2 = "select ";
    }
    $sql2 .= "$FOF_FEED_TABLE.id, $FOF_FEED_TABLE.url, $FOF_FEED_TABLE.title, $FOF_FEED_TABLE.link, $FOF_FEED_TABLE.image, $FOF_FEED_TABLE.description from $FOF_FEED_TABLE, $FOF_SUBSCRIPTION_TABLE where $FOF_FEED_TABLE.id = $FOF_SUBSCRIPTION_TABLE.feed_id";

    if (!fof_is_admin()) {
        $sql2 .= " and $FOF_SUBSCRIPTION_TABLE.user_id = " . current_user();
    }

    $feedlist=fof_get_subscribed_feeds_list($tags);
    
    if ($feedlist != "") {
        if (!fof_is_admin()) {
            $sql2 .= " AND $FOF_FEED_TABLE.id in ($feedlist)";
        }
        //echo "FIRSTSQL: " . $sql2 . "<br />\n";
        $result = fof_do_query($sql2);
    } else {
        $result = "";
    }

    $i = 0;
    while ($row = mysql_fetch_array($result)) {
        //$id = $row['id'];
        $feeds[$i]['id'] = $row['id'];
        $feeds[$i]['url'] = $row['url'];
        $feeds[$i]['title'] = $row['title'];
        $feeds[$i]['link'] = $row['link'];
        $feeds[$i]['description'] = $row['description'];
        $feeds[$i]['image'] = $row['image'];
  
        $age = fof_rss_age($feeds[$i]['url']);
        $feeds[$i]['age'] = $age;


        if ($age == FOF_MAX_INT) {
            $agestr = "never";
            $agestrabbr = "&infin;";
        } else {
            $seconds = $age % 60;
            $minutes = $age / 60 % 60;
            $hours = $age / 60 / 60 % 24;
            $days = floor($age / 60 / 60 / 24);
 
            if ($seconds) {
                $agestr = "$seconds sec";
                if ($seconds != 1) {
                    $agestr .= "s";
                }
                $agestr .= " ago";
   
                $agestrabbr = $seconds . "s";
            }

            if ($minutes) {
                $agestr = "$minutes min";
                if ($minutes != 1) {
                    $agestr .= "s";
                }
                $agestr .= " ago";

                $agestrabbr = $minutes . "m";
            }

            if ($hours) {
                $agestr = "$hours hr";
                if ($hours != 1) {
                    $agestr .= "s";
                }
                $agestr .= " ago";

                $agestrabbr = $hours . "h";
            }

            if ($days) {
                $agestr = "$days day";
                if ($days != 1) {
                    $agestr .= "s";
                }
                $agestr .= " ago";

                $agestrabbr = $days . "d";
            }
        }
        $feeds[$i]['agestr'] = $agestr;
        $feeds[$i]['agestrabbr'] = $agestrabbr;

        $i++;
  
    }

    // unread articles count    
    $sql = "SELECT count( feed_id ) AS count, feed_id AS id FROM " . $FOF_FEED_TABLE . ", " . $FOF_ITEM_TABLE . " WHERE " . $FOF_FEED_TABLE . ".id = " . $FOF_ITEM_TABLE . ".feed_id ";
    $sql .= " AND " .  $FOF_ITEM_TABLE . ".id NOT IN ( SELECT `$FOF_ITEM_TABLE`.id FROM `$FOF_ITEM_TABLE`,`$FOF_FEED_TABLE`,`$FOF_FLAG_TABLE`,`$FOF_USER_TABLE` WHERE `$FOF_USER_TABLE`.user_id=" . current_user() . " AND flag_id=1) ";
    $sql .= " AND " . $FOF_FEED_TABLE . ".id IN (SELECT  `feed_id` FROM  `" . $FOF_SUBSCRIPTION_TABLE . "` WHERE user_id =" . current_user() . ")";
    $sql .= " group by feed_id order by " . $FOF_FEED_TABLE . ".title";
    //print "SQL: $sql <br/>";
    $result = fof_do_query($sql);

    while ($row = mysql_fetch_array($result)) {
        for ($i=0; $i<count($feeds); $i++) {
            if ($feeds[$i]['id'] == $row['id']) {
                $feeds[$i]['unread'] = $row['count'];
            }
        }
    }

    //total articles count
    $result = fof_do_query("SELECT count( feed_id ) as count, feed_id as id from " . $FOF_FEED_TABLE . ", " . $FOF_ITEM_TABLE . " where " . $FOF_FEED_TABLE . ".id = " . $FOF_ITEM_TABLE . ".feed_id group by feed_id order by " . $FOF_FEED_TABLE . ".title");

    while ($row = mysql_fetch_array($result)) {
        for ($i=0; $i<count($feeds); $i++) {
            if ($feeds[$i]['id'] == $row['id']) {
                $feeds[$i]['items'] = $row['count'];
            }
        }
    }

   $feeds = fof_multi_sort($feeds, $order, $direction != "asc");

   return $feeds;
}

/**
 * Actually prunes the feed
 *
 * @return void
 */
function fof_prune_expir_feeds()
{
        global $FOF_FEED_TABLE;
//from `$FOF_FEED_TABLE`, " . $FOF_ITEM_TABLE . " where `$FOF_FEED_TABLE`.id = " . $FOF_ITEM_TABLE . ".feed_id AND `read` is null group by feed_id order by " . $FOF_FEED_TABLE . ".title
    $sql = "select id from " . $FOF_FEED_TABLE . " where expir != 0 AND ((to_days( CURDATE(  )  )  - to_days( date_added )) > expir)";
    $result = fof_do_query($sql);
    while ($row = mysql_fetch_array($result)) {
     $feed_id = $row['id'];
         $result = fof_do_query("delete from " . $FOF_FEED_TABLE . " where id = $feed_id");
         $result = fof_do_query("delete from " . $FOF_ITEM_TABLE . " where feed_id = $feed_id");
    }

}

/**
 * Get the name of the user based on the id
 *
 * @return string
 */
function get_user_name()
{
        global $FOF_USER_TABLE;
        $sql = "SELECT user_name from $FOF_USER_TABLE where user_id =" . current_user();
        //$pieces = explode("::", $_COOKIE["mc_info"]);
        //$user_name = $pieces[0];
        $result = fof_do_query($sql);
        $row= mysql_fetch_array($result);
        $user_name = $row['user_name'];
        return $user_name;
}

/**
 * Gets the title of a feed
 *
 * @param string $feed  the feed to display
 * @param string $what  what kind of articles
 * @param string $when  date
 * @param int    $start the starting number
 * @param int    $limit how may to display
 *
 * @return string
 */
function fof_view_title($feed=null, $what="new", $when=null, $start=null, $limit=null)
{
    $title = "";
    //$pieces = explode("::", $_COOKIE["mc_info"]);
    //$user_name = $pieces[0];
    $title = "<i>[" . get_user_name() . ":" . current_user() ."]</i> - ";
    $title .= "MonkeyChow";
 
    if (!is_null($when) && $when != "") {
        $title .= ' - ' . $when ;
    }
    if (!is_null($feed) && $feed != "") {
        $r = fof_feed_row($feed);
        $title .= ' - <a href="' . $r['link'] . '" title="' . htmlspecialchars($r['feed_description']) . '">' . htmlspecialchars($r['title']) . '</a> ';
    }
    if (is_numeric($start)) {
        if (!is_numeric($limit)) {
            $limit = FOF_HOWMANY;
        }
        $title .= " - items $start to " . ($start + $limit);
    }
    if ($what == "published") {
        $title .=' - ' . _("published items");
    } else if ($what == "search") {
        $title .=' - ' . _("custom search");
    } else if ($what != "all") {
        $title .=' - ' . _("new items");
    } else {
        $title .= ' - ' . _("all items");
    }

   return $title;
}

/**
 * Get the articles of a feed
 *
 * @param string $feed   the feed we are displaying
 * @param string $what   what kind of feeds
 * @param string $when   date
 * @param int    $start  starting number
 * @param int    $limit  how many to show
 * @param string $order  most recent or oldest?
 * @param string $tags   the tag of the feeds to diplay
 * @param string $search are we searching for something in particular
 *
 * @return array
 */
function fof_get_items($feed=null, $what="new", $when=null, $start=null, $limit=null, $order="desc", $tags=null, $search=null)
{
    global $FOF_FEED_TABLE;
    global $FOF_ITEM_TABLE;
    global $FOF_USERITEM_TABLE;
    global $FOF_SUBSCRIPTION_TABLE;
    if (!is_null($when) && $when != "") {
        if ($when == "today") {
            $whendate = date("Y/m/d", time() - (FOF_TIME_OFFSET * 60 * 60));
        } else {
            $whendate = $when;
        }

        $begin = strtotime($whendate);
        $begin = $begin + (FOF_TIME_OFFSET * 60 * 60);
        $end = $begin + (24 * 60 * 60);

        $tomorrow = date("Y/m/d", $begin + (24 * 60 * 60));
        $yesterday = date("Y/m/d", $begin - (24 * 60 * 60));
    }

       if (is_numeric($start)) {
          if (!is_numeric($limit)) {
             $limit = FOF_HOWMANY;
          }
    
              $limit_clause = " limit $start, $limit ";
       }

    $query = "select " . $FOF_FEED_TABLE . ".tags as feed_tags, " . $FOF_FEED_TABLE . ".image as feed_image, " . $FOF_FEED_TABLE . ".title as feed_title, " . $FOF_FEED_TABLE . ".link as feed_link, " . $FOF_FEED_TABLE . ".description as feed_description, " . $FOF_ITEM_TABLE . ".id as item_id, " . $FOF_ITEM_TABLE . ".link as item_link, " . $FOF_ITEM_TABLE . ".title as item_title, UNIX_TIMESTAMP(" . $FOF_ITEM_TABLE . ".timestamp) as timestamp, " . $FOF_ITEM_TABLE . ".content as item_content, " . $FOF_ITEM_TABLE . ".dcdate as dcdate, " . $FOF_ITEM_TABLE . ".dccreator as dccreator, " . $FOF_ITEM_TABLE . ".dcsubject as dcsubject ";
  $query .= " from " . $FOF_FEED_TABLE . ", " . $FOF_ITEM_TABLE . ", " . $FOF_SUBSCRIPTION_TABLE;

    if (($what == "published")||($what == "starred")||($what == "private")) {
        $query .= ", " . $FOF_USERITEM_TABLE;
    }

    $feedlist = fof_get_subscribed_feeds_list($tags);
    //$query .= " where " . $FOF_ITEM_TABLE . ".feed_id=" . $FOF_FEED_TABLE . ".id  and " . $FOF_SUBSCRIPTION_TABLE . ".feed_id = " . $FOF_FEED_TABLE . ".id and " . $FOF_FEED_TABLE . ".id in ($feedlist)";
    $query .= " WHERE " . $FOF_ITEM_TABLE . ".feed_id=" . $FOF_FEED_TABLE . ".id  and " . $FOF_SUBSCRIPTION_TABLE . ".feed_id = " . $FOF_FEED_TABLE . ".id ";
    if (($what == "published")||($what == "starred")||($what == "private")) {
        $query .= " AND " . $FOF_USERITEM_TABLE . ".item_id = " . $FOF_ITEM_TABLE . ".id ";
    }


   if (!is_null($feed) && $feed != "") {
     $query .= " AND " . $FOF_FEED_TABLE . ".id = $feed";
   }

   if (!is_null($when) && $when != "") {
     $query .= " AND UNIX_TIMESTAMP(" . $FOF_ITEM_TABLE . ".timestamp) > $begin AND UNIX_TIMESTAMP(" . $FOF_ITEM_TABLE . ".timestamp) < $end";
   }

   if (!is_null($tags) && $tags!="" && $tags!="All tags") {
       if ($tags=="No tags") {
           $query .= " AND " . $FOF_SUBSCRIPTION_TABLE . ".tags IS NULL";// or tags LIKE ''";
       } else {
           $query .= " AND " . $FOF_SUBSCRIPTION_TABLE . ".tags LIKE \"%" . $tags . "%\"";
       }
   }

    $query .= " AND " . $FOF_SUBSCRIPTION_TABLE . ".user_id = " . current_user();
    if (($what == "published")||($what == "starred")||($what == "private")) {
        $query .= " AND " . $FOF_SUBSCRIPTION_TABLE . ".user_id=" . $FOF_USERITEM_TABLE . ".user_id ";
    }

    if ($what == "published") {
        //$query .= " AND " . $FOF_ITEM_TABLE . ".publish=1";
        //$query .= " AND " . $FOF_ITEM_TABLE . ".id IN ( SELECT  `item_id` FROM `" . $FOF_USERITEM_TABLE . "` WHERE `user_id`=" . current_user() . " AND  `flag_id`=3) ";
        $query .= " AND " . $FOF_USERITEM_TABLE . ".flag_id=3 ";
    } else if ($what == "public") {
        //$query .= " AND " . $FOF_FEED_TABLE . ".private=0";
        $query .= " AND " . $FOF_ITEM_TABLE . ".id NOT IN ( SELECT  `item_id` FROM `" . $FOF_USERITEM_TABLE . "` WHERE `user_id`=" . current_user() . " AND  `flag_id`=4) ";
    } else if ($what == "starred") {
        //$query .= " AND " . $FOF_ITEM_TABLE . ".star=1";
        //$query .= " AND " . $FOF_ITEM_TABLE . ".id IN ( SELECT  `item_id` FROM `" . $FOF_USERITEM_TABLE . "` WHERE `user_id`=" . current_user() . " AND  `flag_id`=2) ";
        $query .= " AND " . $FOF_USERITEM_TABLE . ".flag_id=2 ";
    } else if ($what == "search") {
        $query .= " AND " . $FOF_ITEM_TABLE . ".title LIKE \"%" . $search . "%\"";
    } else if ($what != "all") {
        //$query .= " AND " . $FOF_ITEM_TABLE . ".read is null";
        $query .= " AND " . $FOF_ITEM_TABLE . ".id NOT IN ( SELECT item_id FROM " . $FOF_USERITEM_TABLE . " WHERE user_id =" . current_user() . " AND  flag_id=1) ";
    }

    $query .= " order by timestamp desc $limit_clause";    
    if ($feedlist != "") {
        $result = fof_do_query($query);
    } else {
        $result="";
    }

   while ($row = mysql_fetch_array($result)) {
      $array[] = $row;
   }

   //$array = fof_multi_sort($array, 'timestamp', $order != "asc");
   $array = fof_multi_sort($array, 'dcdate', $order != "asc");

   return $array;
}

/**
 * Get the navigation links for the framed view
 *
 * @param string $feed   the feed we are displaying
 * @param string $what   what kind of articles
 * @param string $when   date
 * @param int    $start  the starting number
 * @param int    $limit  how many to display
 * @param string $framed are we in the framed view
 * @param string $tags   the tags to show
 *
 * @return string
 */
function fof_get_frame_nav_links($feed=null, $what="new", $when=null, $start=null, $limit=null, $framed=null, $tags=null)
{
    $string = "";

    if (!is_null($when) && $when != "") {
        if ($when == "today") {
            $whendate = date("Y/m/d", time() - (FOF_TIME_OFFSET * 60 * 60));
        } else {
            $whendate = $when;
        }

        $begin = strtotime($whendate);
        $begin = $begin + (FOF_TIME_OFFSET * 60 * 60);
        $end = $begin + (24 * 60 * 60);

        $tomorrow = date("Y/m/d", $begin + (24 * 60 * 60));
        $yesterday = date("Y/m/d", $begin - (24 * 60 * 60));

        $string .= "<a href=\"framesview.php?feed=$feed&amp;what=$what&amp;when=$yesterday&amp;how=$how&amp;howmany=$howmany";
        $string .= ($framed) ? "&amp;framed=yes" : "";
        $string .= ($tags) ? "&amp;tags=" . $tags : "";
        $string .= "\">[&laquo; $yesterday]</a> ";
        if ($when != "today") {
            $string .= "<a href=\"framesview.php?feed=$feed&amp;what=$what&amp;when=today&amp;how=$how&amp;howmany=$howmany";
            $string .= ($framed) ? "&amp;framed=yes" : "";
            $string .= ($tags) ? "&amp;tags=" . $tags : "";
            $string .= "\">[today]</a> ";
        }
        if ($when != "today") {
            $string .= "<a href=\"framesview.php?feed=$feed&amp;what=$what&amp;when=$tomorrow&amp;how=$how&amp;howmany=$howmany";
            $string .= ($framed) ? "&amp;framed=yes" : "";
            $string .= ($tags) ? "&amp;tags=" . $tags : "";
            $string .= "\">[$tomorrow &raquo;]</a> ";
        }
    }

    if (is_numeric($start)) {
        if (!is_numeric($limit)) {
            $limit = FOF_HOWMANY;
        }

        $earlier = $start + $limit;
        $later = $start - $limit;

        $string .= "<a href=\"framesview.php?feed=$feed&amp;what=$what&amp;when=$when&amp;how=paged&amp;which=$earlier&amp;howmany=$limit";
        $string .= ($framed) ? "&amp;framed=yes" : "";
        $string .= ($tags) ? "&amp;tags=" . $tags : "";
        $string .= "\">[&laquo; previous $limit]</a> ";
        if ($later >= 0) {
            $string .= "<a href=\"framesview.php?feed=$feed&amp;what=$what&amp;when=$when&amp;how=paged&amp;howmany=$limit";
            $string .= ($framed) ? "&amp;framed=yes" : "";
            $string .= ($tags) ? "&amp;tags=" . $tags : "";
            $string .= "\">[current items]</a> ";
        }
        if ($later >= 0) {
            $string .= "<a href=\"framesview.php?feed=$feed&amp;what=$what&amp;when=$when&amp;how=paged&amp;which=$later&amp;howmany=$limit";
            $string .= ($framed) ? "&amp;framed=yes" : "";
            $string .= ($tags) ? "&amp;tags=" . $tags : "";
            $string .= "\">[next $limit &raquo;]</a> ";
        }
    }

    return $string;
}

/**
 * Renders the navigation links
 *
 * @param string $feed  the feed to get
 * @param string $what  what kind of articles
 * @param string $when  date 
 * @param int    $start starting number
 * @param int    $limit how many articles to show
 *
 * @return string
 */
function fof_get_nav_links($feed=null, $what="new", $when=null, $start=null, $limit=null)
{
   $string = "";

    if (!is_null($when) && $when != "") {
        if ($when == "today") {
            $whendate = date("Y/m/d", time() - (FOF_TIME_OFFSET * 60 * 60));
        } else {
            $whendate = $when;
        }

        $begin = strtotime($whendate);
        $begin = $begin + (FOF_TIME_OFFSET * 60 * 60);
        $end = $begin + (24 * 60 * 60);

        $tomorrow = date("Y/m/d", $begin + (24 * 60 * 60));
        $yesterday = date("Y/m/d", $begin - (24 * 60 * 60));

         $string .= "<a href=\"view.php?feed=$feed&amp;what=$what&amp;when=$yesterday&amp;how=$how&amp;howmany=$howmany\">[&laquo; $yesterday]</a> ";
         if ($when != "today") {
             $string .= "<a href=\"view.php?feed=$feed&amp;what=$what&amp;when=today&amp;how=$how&amp;howmany=$howmany\">[today]</a> ";
         }
         if ($when != "today") {
             $string .= "<a href=\"view.php?feed=$feed&amp;what=$what&amp;when=$tomorrow&amp;how=$how&amp;howmany=$howmany\">[$tomorrow &raquo;]</a> ";
         }
    }

    if (is_numeric($start)) {
        if (!is_numeric($limit)) {
            $limit = FOF_HOWMANY;
        }

        $earlier = $start + $limit;
        $later = $start - $limit;

        $string .= "<a href=\"view.php?feed=$feed&amp;what=$what&amp;when=$when&amp;how=paged&amp;which=$earlier&amp;howmany=$limit\">[&laquo; previous $limit]</a> ";
        if ($later >= 0) {
            $string .= "<a href=\"view.php?feed=$feed&amp;what=$what&amp;when=$when&amp;how=paged&amp;howmany=$limit\">[current items]</a> ";
        }
        if ($later >= 0) {
            $string .= "<a href=\"view.php?feed=$feed&amp;what=$what&amp;when=$when&amp;how=paged&amp;which=$later&amp;howmany=$limit\">[next $limit &raquo;]</a> ";
        }
    }

    return $string;
}

/**
 * Performs the workhorse function of makimg simpler SQL queries and returning an array of rows
 *
 * @param string $sql  the sql to be executed
 * @param int    $live whether we are getting error messages for sql queries
 *
 * @return array
 */
function fof_do_query($sql, $live=0)
{
    global $fof_connection, $fof_query_log;
   
    if (defined('FOF_QUERY_LOG') && FOF_QUERY_LOG) {
        list($usec, $sec) = explode(" ", microtime()); 
        $t1 = (float)$sec + (float)$usec;
    }
    //echo $sql . "<br/>";
    $result = mysql_query($sql, $fof_connection);

    if (defined('FOF_QUERY_LOG') && FOF_QUERY_LOG) {
        list($usec, $sec) = explode(" ", microtime()); 
        $t2 = (float)$sec + (float)$usec;
        $elapsed = $t2 - $t1;
        $fof_query_log .= "[$sql]: $elapsed\n";
    }
   
    if ($live) {
        return $result;
    } else {
        if (mysql_errno()) {
            die( _("Cannot query database.") . "  " . _("Have you run") . " <a href=\"install.php\"><code>install.php</code></a>?  " .  _("MySQL says") . ": <b>". mysql_error() . "</b>");
        }
        return $result;
    }
}

/**
 * Get the age of the RSS feed
 *
 * @param string $url the feed's url
 *
 * @return int
 */
function fof_rss_age($url)
{
   //sha or md5?
   $filename = FOF_CACHE_DIR . "/" . md5($url) . '.spc';
   if (file_exists($filename)) {
      // find how long ago the file was added to the cache
      // and whether that is longer then MAX_AGE
      $mtime = filemtime($filename);
      $age = time() - $mtime;
      return $age;
   } else {
      return FOF_MAX_INT;
   }
}

/**
 * Gets an RSS feed given an html url
 *
 * @param string $html     the url of the site
 * @param string $location another url of the site
 *
 * @return $bool
 */
function fof_getRSSLocation($html, $location)
{
    if (!$html or !$location) {
        return false;
    } else {
        //search through the HTML, save all <link> tags
        // and store each link's attributes in an associative array
        preg_match_all('/<link\s+(.*?)\s*\/?>/si', $html, $matches);
        $links = $matches[1];
        $final_links = array();
        $link_count = count($links);
        for ($n=0; $n<$link_count; $n++) {
            $attributes = preg_split('/\s+/s', $links[$n]);
            foreach ($attributes as $attribute) {
                $att = preg_split('/\s*=\s*/s', $attribute, 2);
                if (isset($att[1])) {
                    $att[1] = preg_replace('/([\'"]?)(.*)\1/', '$2', $att[1]);
                    $final_link[strtolower($att[0])] = $att[1];
                }
            }
            $final_links[$n] = $final_link;
        }
        //now figure out which one points to the RSS file
        for ($n=0; $n<$link_count; $n++) {
            if (strtolower($final_links[$n]['rel']) == 'alternate') {
                if (strtolower($final_links[$n]['type']) == 'application/rss+xml') {
                    $href = $final_links[$n]['href'];
                }
                if (!$href and strtolower($final_links[$n]['type']) == 'text/xml') {
                    //kludge to make the first version of this still work
                    $href = $final_links[$n]['href'];
                }
                if ($href) {
                    if (strstr($href, "http://") !== false) { //if it's absolute
                        $full_url = $href;
                    } else { //otherwise, 'absolutize' it
                        $url_parts = parse_url($location);
                        //only made it work for http:// links. Any problem with this?
                        $full_url = "http://$url_parts[host]";
                        if (isset($url_parts['port'])) {
                            $full_url .= ":$url_parts[port]";
                        }
                        if ($href{0} != '/') { //it's a relative link on the domain
                            $full_url .= dirname($url_parts['path']);
                            if (substr($full_url, -1) != '/') {
                                //if the last character isn't a '/', add it
                                $full_url .= '/';
                            }
                        }
                        $full_url .= $href;
                    }
                    return $full_url;
                }
            }
        }
        return false;
    }
}

/**
 * Print the link in the article, given the $row
 *
 * @param array $row the row that is the item that will be rendered
 *
 * @return string
 */
function fof_render_feed_link($row)
{
   $link = $row['link'];
   $description = htmlspecialchars($row['description']);
   $title = htmlspecialchars($row['title']);
   $url = $row['url'];

   $s = "<b><font class=\"feed_title\"><a target=\"_blank\" class=\"item_title\" href=\"$link\" title=\"$description\">$title</a></font></b> ";
   //$s .= "<a href=\"$url\"><img class=\"valign\" src=\"rss.gif\" border=\"0\"></a>";

   return $s;
}

/**
 * Changes opml text into an array of URLs
 *
 * @param string $opml the text of the whole OPML being imprted
 *
 * @return array
 */
function fof_opml_to_array($opml)
{
   $rx = "/xmlurl=\"(.*?)\"/mi";

   if (preg_match_all($rx, $opml, $m)) {
      for ($i = 0; $i < count($m[0]); $i++) {
         $r[] = $m[1][$i];
      }
   }

   return $r;
}

/**
 * Subscribe someone to a feed
 *
 * @param string $id the user id that will subscribe to a feed
 *
 * @return bool
 */
function subscribe_user($id)
{
    global $FOF_SUBSCRIPTION_TABLE;
    $sql = "INSERT INTO `" . $FOF_SUBSCRIPTION_TABLE . "` (`feed_id`,`user_id`)  VALUES (" . $id . ", " . current_user() . ")";
    fof_do_query($sql);

    echo "<font color=\"green\"><b>" . _("User Subscribed") . ".</b></font><br />";

    return true;
}

/**
 * Checks if someone is already subscribed to a feed
 *
 * @param string $id the user id
 *
 * @return $bool
 */
function user_is_subscribed($id)
{
    // checks to see if a user is subscribed to a particular feed
    global $FOF_SUBSCRIPTION_TABLE;
    //$id = $row['id'];
    $sql = "select * from $FOF_SUBSCRIPTION_TABLE WHERE feed_id=$id AND user_id=" . current_user();
    if (mysql_fetch_array($result = fof_do_query($sql))) {
        return true;
    } else {
        return false;
    }
}

/*
function add_feed_link($name, $url)
{
    echo "<a href=\"";
    echo "http://" . $_SERVER["HTTP_HOST"] . dirname($_SERVER["SCRIPT_NAME"]) . "monkeychow/add.php?rss_url=" . urlencode($url);
    echo "\">$name</a><br />";
}
*/

/**
 * Checks if the user is new so that we can display a list of recommended feeds
 * 
 * @return bool
 */
function user_is_new()
{
    global $FOF_SUBSCRIPTION_TABLE;
    $sql = "select feed_id from $FOF_SUBSCRIPTION_TABLE WHERE user_id=" . current_user() . " LIMIT 1";
    if (mysql_fetch_array($result = fof_do_query($sql)) ) {
        return false;
    } else {
        return true;
    }
}


/**
 * Matches a feed url string with its ID
 *
 * @param string $url the url we will get the feed id by
 *
 * @return string
 */
function get_feedid_by_url($url)
{
    global $FOF_FEED_TABLE;

    $result = fof_do_query("select id,title from " . $FOF_FEED_TABLE . " where url='" . mysql_escape_string($url) . "'");
    while ($row = mysql_fetch_array($result)) {
        if ($row['title'] != "") {
            return $row['id'];
            break;
        }
    }
}


/**
 * Set up the feed information so it can be added to the subscription list
 *
 * @param string $url the RSS/XML link of the feed
 *
 * @return void
 */
function fof_add_feed($url)
{
    global $FOF_FEED_TABLE;
    if (!$url) {
        return;
    }
    $url = trim($url);

    if (substr($url, 0, 7) != 'http://' && substr($url, 0, 8) != 'https://') {
        $url = 'http://' . $url;
    }

    echo _("Attempting to subscribe to ") ."<a href=\"$url\">$url</a>...<br />";
    if ($row = fof_is_subscribed($url)) {
        print "<font color='red'><u>" . _("Site is already subscribed to ") . fof_render_feed_link($row) . "</u></font><br />";

    } else {
        $piefeed = new SimplePie();
        $piefeed->set_image_handler();
        //$piefeed->strip_ads(true);
        $piefeed->set_feed_url($url);
        $piefeed->set_cache_location(FOF_CACHE_DIR);
        $piefeed->init();
        $piefeed->handle_content_type();

        if (!$piefeed->data) {
            echo "&nbsp;&nbsp;<font color=\"red\">" . _("URL is not RSS or is invalid.") . "</font><br />";
            echo "&nbsp;&nbsp;(<font color=\"red\">" . _("error was") . "</font>: <B>" . $piefeed->error . "</b>)<br />";
            echo "&nbsp;&nbsp;<a href=\"http://feedvalidator.org/check?url=$url\">" . _("The FEED validator may give more information.") . "</a><br />";
            echo "&nbsp;&nbsp;<a href=\"http://validator.w3.org/check?uri=$url\">" . _("The XHTML validator may give more information.") . "</a><br />";

            echo "<font color=\"red\"><b>" . _("Can't load URL.  Giving up.") . "</b></font><br />";
            echo "<font color=\"red\"><b>" . _("Autodiscovery failed.  Giving up.") . "</b></font><br />";
                return false;
        } else {
            echo _("Adding feed...") . "<br />";
            fof_actually_add_feed($url, $piefeed);
            echo "<font color=\"green\"><b>" . _("Site Subscribed") . ".</b></font><br />";
        }
    }

    $feed_id=get_feedid_by_url($url);
    if (user_is_subscribed($feed_id)) {
        print "<font color='red'><u>" . _("You are already subscribed to ") . fof_render_feed_link($row) . "</u></font><br /><br />";
        //return true;
    } else {
        subscribe_user($feed_id);
    }    

?>
<script type="text/javascript">
if (top==self) document.writeln('<?php
      echo "<a href=\"edit.php?feed=$feed_id\">Edit feed attributes.</a><br />";
      echo "<a href=\"delete.php?feed=$feed_id\">Delete this feed.</a><br />";
      echo "<a href=\"index.php\">Return to new items.</a>";
?>')
else document.writeln('<?php
      echo "<a href=\"edit.php?framed=yes&feed=$feed_id\">Edit feed attributes.</a><br />";
      echo "<a href=\"delete.php?framed=yes&feed=$feed_id\">Delete this feed.</a><br />";
      echo "<a href=\"framesview.php\">Return to new items.</a>";
?>');
</script>
<?php
   unset($piefeed);
}

/**
 * Add a feed given the piefeed object
 *
 * @param string    $url     the RSS/XML url of the feed
 * @param SimplePie $piefeed the feed object
 *
 * @return void
 */
function fof_actually_add_feed($url, $piefeed)
{
    global $FOF_FEED_TABLE;
    $title = $piefeed->get_title();
    $title = str_replace('"', '', $title);
    $title = str_replace("'", '', $title);
    $title = htmlspecialchars($title, ENT_QUOTES);
    $title = htmlspecialchars($title);
    $link = $piefeed->get_link();
    $link = str_replace('"', '', $link);
    $link = str_replace("'", '', $link);
    $description = $piefeed->get_description();
    $description = str_replace('"', '', $description);
    $description = str_replace("'", '', $description);
    $description = htmlspecialchars($description, ENT_QUOTES);
    $sql = "insert into " . $FOF_FEED_TABLE . " (url,title,link,description) values ('$url','$title','$link','$description')";

    fof_do_query($sql);

    fof_update_feed($url);
}

/**
 * Edit the available fields of a subscribed feed
 *
 * @param int    $id          the ID for the feed
 * @param string $url         the RSS/XML link
 * @param string $title       the name of the feed
 * @param string $link        the link for visiting the site
 * @param string $description a text description for this feed
 * @param string $date_added  the date the feed was added
 * @param string $tags        the tags assigned by the user to this feed
 * @param string $aging       is aging on for this feed
 * @param string $expir       expiration date of this feed
 * @param string $private     is this private?
 * @param string $image       the favicon image
 *
 * @return void
 */
function fof_edit_feed($id, $url, $title, $link, $description, $date_added, $tags, $aging, $expir, $private, $image)
{
    global $FOF_FEED_TABLE,$FOF_SUBSCRIPTION_TABLE;
    $id = mysql_escape_string($id);
    $url = mysql_escape_string($url);
    $title = mysql_escape_string($title);
    $link = mysql_escape_string($link);
    $description = mysql_escape_string($description);
    $date_added = mysql_escape_string($date_added);
    $tags = mysql_escape_string($tags);
    $aging = mysql_escape_string($aging);
    $expir = mysql_escape_string($expir);
    $private = mysql_escape_string($private);
    $image = mysql_escape_string($image);
    if ($private == '') {
       $private="0";
    }
    if ($private == 'on') {
       $private="1";
    }

    // need to repeat for each tag in $tags
    if (fof_is_admin()) {
        $sql = "update $FOF_FEED_TABLE,$FOF_SUBSCRIPTION_TABLE SET " . $FOF_FEED_TABLE . ".url = '$url', " . $FOF_FEED_TABLE . ".title = '$title', " . $FOF_FEED_TABLE . ".link = '$link', " . $FOF_FEED_TABLE . ".description = '$description', " . $FOF_FEED_TABLE . ".date_added = '$date_added', " . $FOF_SUBSCRIPTION_TABLE . ".tags = '$tags', " . $FOF_FEED_TABLE . ".private = '$private'";

        if ($aging != '') {
            $sql .= ", " . $FOF_FEED_TABLE . ".aging = '$aging' ";
        }
        if ($expir != '') {
            $sql .= ", " . $FOF_FEED_TABLE . ".expir = '$expir' ";
        }
        $sql .= ", " . $FOF_FEED_TABLE . ".image = '$image' ";
    
        $sql .= " WHERE $FOF_FEED_TABLE.id = $FOF_SUBSCRIPTION_TABLE.feed_id and $FOF_SUBSCRIPTION_TABLE.feed_id = '" . $id . "'";
             // and $FOF_SUBSCRIPTION_TABLE.user_id=" . current_user();
    } else {
        $sql = "update $FOF_SUBSCRIPTION_TABLE SET $FOF_SUBSCRIPTION_TABLE.tags = '" . $tags . "'";
        $sql .= " WHERE $FOF_SUBSCRIPTION_TABLE.feed_id = '" . $id . "' and $FOF_SUBSCRIPTION_TABLE.user_id=" . current_user();
    }

   //echo ":SQL: " . $sql . "<br />\n";
   fof_do_query($sql);
}

/**
 * Returns an array of subscribed feeds
 *
 * @param string $url the url to check
 *
 * @return array
 */
function fof_is_subscribed($url)
{
    global $FOF_FEED_TABLE;
   $safe_url = mysql_escape_string($url);

   $result = fof_do_query("select url, title, link, id from " . $FOF_FEED_TABLE . " where url = '$safe_url'");

   if (mysql_num_rows($result) == 0) {
      return false;
   } else {
      $row = mysql_fetch_array($result);
      return $row;
   }
}

/**
 * Returns a row from the database
 *
 * @param int $id the row for the particular feed
 *
 * @return array
 */
function fof_feed_row($id)
{
        global $FOF_FEED_TABLE;
   $result = fof_do_query("select url, title, link, id from " . $FOF_FEED_TABLE . " where id = '$id'");

   if (mysql_num_rows($result) == 0) {
      return false;
   } else {
      $row = mysql_fetch_array($result);
      return $row;
   }
}

/**
 * Search for a word among titles
 *
 * @param string $word the word we will be searching for
 *
 * @return string
 */
function fof_search_word($word)
{
    //sanitize $word!!!!
    global $FOF_FEED_TABLE;
    $result = fof_do_query("select url, title, link, id from " . $FOF_FEED_TABLE . " where title LIKE '$word'");
    if (mysql_num_rows($result) == 0) {
       return false;
    } else {
       $row = mysql_fetch_array($result);
       return $row;
    }
}

/**
 * Get a favicon
 *
 * @param string $url get the favicon for this url
 *
 * @return string
 */
function mygetfavicon($url)
{
    //1 check for rel "shortcut icon"
    $doc = new DOMDocument();
    $doc->strictErrorChecking = false;
    $doc->loadHTML(file_get_contents($url));
    $xml = simplexml_import_dom($doc);
    $arr = $xml->xpath('//link[@rel="shortcut icon"]');
    $myfavicon=$arr[0]['href'];
    if ($myfavicon) {
        echo $myfavicon . " ";
        return $myfavicon;
    } else {
        $myfavicon="";
    }

    //2 check for rel "icon"
    $doc = new DOMDocument();
    $doc->strictErrorChecking = false;
    $doc->loadHTML(file_get_contents($url));
    $xml = simplexml_import_dom($doc);
    $arr = $xml->xpath('//link[@rel="icon"]');
    $myfavicon=$arr[0]['href'];
    if ($myfavicon) {
        echo $myfavicon . " ";
        return $myfavicon;
    } else {
        $myfavicon="";
    }

    //3 check http://site/favicon.ico as the old method since it is old school
    $strendtest="/";
    if (substr_compare($url, $strendtest, -strlen($strendtest), strlen($strendtest)) === 00) {
        $favicon=$url . "favicon.ico";
    } else {
        $favicon=$url . "/favicon.ico";
    }
    $HTTPRequest = @fopen($favicon, 'r');
    if ($HTTPRequest) {
        stream_set_timeout($HTTPRequest, 0.1);
        $faviconico = fread($HTTPRequest, 8192);
        $HTTPRequestData = stream_get_meta_data($HTTPRequest);
        fclose($HTTPRequest);
        if (!$HTTPRequestData['timed_out'] && strlen($faviconico) < 42) {
            $favicon = "";
        } else {
            return $favicon;
        }
    }

}
/**
 * Updates the article content of a feed as necessary
 * 
 * @param string $url update this feed
 *
 * @return int
 */
function fof_update_feed($url)
{
    global $FOF_FEED_TABLE;
    global $FOF_ITEM_TABLE;
    //
    // Get feed data.
    //
    if (!$url) {
        return 0;
    }
    //echo " from URL: " . $url . " ";

    if (!empty($url)) {
        $piefeed = new SimplePie();
        $piefeed->set_feed_url($url);
    } else {
        return 0;
    }
    $piefeed->set_cache_location(FOF_CACHE_DIR);
    $piefeed->init();
    $piefeed->handle_content_type();

    if (!$piefeed->data) {
       print "<font color=\"red\">" . _("error was") . "</font>: <B>" . $piefeed->error . "</b> ";
       print "<a href=\"http://feedvalidator.org/check?url=$url\">" . _("try to validate it?") . "</a> ";
       unset($piefeed);
       return 0;
    }

    $title = $piefeed->get_title();
    $link = $piefeed->get_link();
    $description = $piefeed->get_description();

    $safeurl = mysql_escape_string($url);
    $result = fof_do_query("select id, url, aging from " . $FOF_FEED_TABLE . " where url='$safeurl'");

    $row = mysql_fetch_array($result);
    $feed_id = $row['id'];
    $keep_days = $row['aging'];
    if ($keep_days < 0) {
        $keep_days = 60;
    }

    $result2 = fof_do_query("select image,link from " . $FOF_FEED_TABLE . " where `id`='$feed_id'");
    $row2 = mysql_fetch_array($result2);
    $image2 = $row2['image'];
    $link2 = $row2['link'];
    
    if ($image2) {
        echo "we already have an image ";
        echo "<img width=\"16\" height=\"16\" border=\"0\" src=\"" . $image2 . "\">";
    } else {
        echo "oops we don't have a favicon for $url, lets grab a new one. ";
        $imagelink = mygetfavicon($link2);
        if ($imagelink) {
            $image2 = $imagelink;
            $sql = "update `$FOF_FEED_TABLE` set `image`='$imagelink' where `id`='$feed_id'";
            $result = fof_do_query($sql);
            echo " got " . $imagelink . " "; 
        } else {
            echo " could not get Favicon. ";
        }
    }

    $glomd=0;
    if ((!$image2) && ($glomd)) {
        $imagelink = $piefeed->get_favicon(true, '');
        $HTTPRequest = @fopen($imagelink, 'r'); 
        if ($HTTPRequest) {
            stream_set_timeout($HTTPRequest, 0.1);
            $favicon = fread($HTTPRequest, 8192);
            $HTTPRequestData = stream_get_meta_data($HTTPRequest);
            fclose($HTTPRequest);
            if (!$HTTPRequestData['timed_out'] && strlen($favicon) < 42) {
                $imagelink = "";
            } 
        } else {
            $imagelink = $piefeed->get_image_url();
            $HTTPRequest = @fopen($imagelink, 'r'); 
            if ($HTTPRequest) {
                stream_set_timeout($HTTPRequest, 0.1);
                $favicon = fread($HTTPRequest, 8192);
                $HTTPRequestData = stream_get_meta_data($HTTPRequest);
                fclose($HTTPRequest);
                if (!$HTTPRequestData['timed_out'] && strlen($favicon) < 42) {
                    $imagelink = "";
                } 
            } else {
                $imagelink = "";
            }
        }
    
        $sql = "update `$FOF_FEED_TABLE` set `image`='$imagelink' where `id`='$feed_id'";
        $result = fof_do_query($sql);
    }


   //
   // Get article items and attributes
   //
   foreach ($piefeed->get_items() as $item) {
      $ageflag= "0";
      $dccreator = "";
      $dcsubject = "";
      $link = mysql_escape_string($item->get_permalink());
      if (!$link) {
         $link = $item->get_id();
      }  

      $title = mysql_escape_string($item->get_title());
      if (!$title) {
         $title = "[" . _("no title") . "]";
      }

      // get <dc:creator> or <author>
      foreach ($item->get_authors() as $author) {
          $authorname = $author->get_name() . " " . $author->get_email();
          if (!empty($authorname)) {
              $dccreator .= $authorname . ', ';
          }
      }
      $dccreator = mysql_escape_string(substr("$dccreator", 0, -2));
   
      // get <dc:date> and <pubdate>
      $dcdate = mysql_escape_string($item->get_date());
      $dcdate = eregi_replace(",", "", $dcdate);

      // get <dc:subject> or <category>
      $category_array = $item->get_category();
      $category_array = array_unique($category_array);
      foreach ($category_array as $category) {
         if (!empty($category)) {
            $dcsubject .= $category . ', ' ;
         }
      }
      $dcsubject = substr(mysql_escape_string($dcsubject), 0, -2);
      unset($category_array);

      // get article content
      $content = mysql_escape_string($item->get_description());
      $content = str_replace('"?i=http', '"http', $content); // dont know why
                                                            // this creeps in
      if ($enclosure = $item->get_enclosure(0)) {
          $content .= '<br />(' . $enclosure->get_type() . '; ' . $enclosure->get_size() . ' MB)<br />';
      }

      //
      // Now manage the article data
      //
      $sql = "select id from " . $FOF_ITEM_TABLE . " where feed_id='$feed_id' AND link='$link'";
      //print "<br />" . $sql . "<br />";
      $result = fof_do_query($sql);
      $row = mysql_fetch_array($result);
      $id = $row['id'];

      // if the item does not already exist, add it
      if (mysql_num_rows($result) == 0) {
         // dcdate   : August 2, 2006, 1:30 am
         // timestamp: 2006-09-16 15:51:53
         // add it only if it's not older than keep_days
         //$dcdatetime = date("Y-m-d H:i:s",strtotime($dcdate));
         $dcdatetime = strtotime($dcdate);
         // We set ageflag == 1 if its OK to add the item to the database
         if ($dcdatetime < 1) {
             $dcdatetime = null;
             $ageflag = 1;
         } else {
             if ((time() - $dcdatetime) < ($keep_days * 24 * 60 * 60)) {
                 $ageflag = 1;
             }
         }
         if ($ageflag) {
             $n++;
             $sql = "insert into " . $FOF_ITEM_TABLE . " (feed_id,link,title,content,dcdate,dccreator,dcsubject) values ('$feed_id','$link','$title','$content','$dcdatetime','$dccreator','$dcsubject')";
             //print "<br />" . $sql . "<br />";
             $result = fof_do_query($sql);
             $ids[] = $id; //keep track of it so we don't delete it below
             $ageflag = 0;
         }
      }
   }

   //
   // Clean up old articles that are not starred
   //
   //
   //if (defined('FOF_KEEP_DAYS'))
   //{
   //   $sql="select aging from `$FOF_FEED_TABLE` WHERE id=" . $feed_id;
   //   $result = fof_do_query($sql);
   //   $row = mysql_fetch_array($result);
   //   $keep_days = $row['aging'];
   //  
   //if ($keep_days > 0)
   //{
   //   // keep_days should come from the feeds.aging column
   //   $sql = "delete from items where `star`!=1 AND feed_id = $feed_id ";
   //   $sql .= " AND `read`=1 ";
   //   $sql .= " AND to_days( CURDATE(  )  )  - to_days( timestamp )  > $keep_days";
   //   //print "<br />" . $sql . "<br />";
   //      fof_do_query($sql);
   //}
   unset($piefeed);
   return $n;
}

/*
 balanceTags

 Balances Tags of string using a modified stack.

 @param text      Text to be balanced
 @return          Returns balanced text
 @author          Leonard Lin (leonard@acm.org)
 @version         v1.1
 @date            November 4, 2001
 @license         GPL v2.0
 @notes
 @changelog
             1.2  ***TODO*** Make better - change loop condition to $text
             1.1  Fixed handling of append/stack pop order of end text
                  Added Cleaning Hooks
             1.0  First Version
*/

/**
 * Checks that the provided text has all the tags it needs
 *
 * @param string $text check this tag to see if we have enough of them
 *
 * @return string
 */
function fof_balanceTags($text)
{
    $tagstack = array();
    $stacksize = 0;
    $tagqueue = '';
    $newtext = '';
    // WP bug fix for comments - in case you REALLY meant to type '< !--'
    $text = str_replace('< !--', '<    !--', $text);
    // WP bug fix for LOVE <3 (and other situations with '<' before a number)
    $text = preg_replace('#<([0-9]{1})#', '&lt;$1', $text);
    while (preg_match("/<(\/?\w*)\s*([^>]*)>/", $text, $regex)) {
        $newtext = $newtext . $tagqueue;
        $i = strpos($text, $regex[0]);
        $l = strlen($tagqueue) + strlen($regex[0]);
        // clear the shifter
        $tagqueue = '';
        // Pop or Push
        if ($regex[1][0] == "/") { // End Tag
            $tag = strtolower(substr($regex[1], 1));
            // if too many closing tags
            if ($stacksize <= 0) {
                $tag = '';
                //or close to be safe $tag = '/' . $tag;
            } else if ($tagstack[$stacksize - 1] == $tag) {// found closing tag // if stacktop value = tag close value then pop
                $tag = '</' . $tag . '>'; // Close Tag
                // Pop
                array_pop($tagstack);
                $stacksize--;
            } else {// closing tag not at top, search for it
                for ($j=$stacksize-1;$j>=0;$j--) {
                    if ($tagstack[$j] == $tag) {
                    // add tag to tagqueue
                        for ($k=$stacksize-1;$k>=$j;$k--) {
                            $tagqueue .= '</' . array_pop($tagstack) . '>';
                            $stacksize--;
                        }
                        break;
                    }
                }
                $tag = '';
            }
        } else {// Begin Tag
            $tag = strtolower($regex[1]);
            // Tag Cleaning
            // Push if not img or br or hr
            if ($tag != 'br' && $tag != 'img' && $tag != 'hr') {
                $stacksize = array_push($tagstack, $tag);
            }
            // Attributes
            // $attributes = $regex[2];
            $attributes = $regex[2];
            if ($attributes) {
                $attributes = ' '.$attributes;
            }
            $tag = '<'.$tag.$attributes.'>';
    
            $newtext .= substr($text, 0, $i) . $tag;
            $text = substr($text, $i+$l);
    
            // Clear Tag Queue
            $newtext = $newtext . $tagqueue;
    
            // Add Remaining text
            $newtext .= $text;
            // Empty Stack
            while ($x = array_pop($tagstack)) {
                $newtext = $newtext . '</' . $x . '>'; // Add remaining tags to close
            }
        }
    // WP fix for the bug with HTML comments
    $newtext = str_replace("< !--", "<!--", $newtext);
    $newtext = str_replace("<    !--", "< !--", $newtext);
    return $newtext;
    }
}

/**
 * Sorts an array forward or reverse
 *
 * @param array $tab the table to sort
 * @param array $key the key for sorting by
 * @param array $rev by reverse or not
 *
 * @return $array
 */
function fof_multi_sort($tab, $key, $rev)
{
    if ($rev) {
        $compare = create_function('$a, $b', 'if (strtolower($a["'.$key.'"]) == strtolower($b["'.$key.'"])) {return 0;}else {return (strtolower($a["'.$key.'"]) > strtolower($b["'.$key.'"])) ? -1 : 1;}');
    } else {
        $compare = create_function('$a, $b', 'if (strtolower($a["'.$key.'"]) == strtolower($b["'.$key.'"])) {return 0;}else {return (strtolower($a["'.$key.'"]) < strtolower($b["'.$key.'"])) ? -1 : 1;}');
    }

    usort($tab, $compare);
    return $tab;
}

/**
 * Parses a String of Tags
 * by Stephen Martindale http://blue-wildebeest.blogspot.com
 *
 * Tags are space delimited. Either single or double quotes mark a phrase.
 * Odd quotes will cause everything on their right to reflect as one single
 * tag or phrase. All white-space within a phrase is converted to single
 * space characters. Quotes burried within tags are ignored!
 *
 * Returns an array of tags.
 *
 * @param string $sTagString use this string as a tag for parsing
 *
 * @return array
 */
function parse_tag_string($sTagString)
{
    $arTags = array();            // Array of Output
    $cPhraseQuote = null;        // Record of the quote that opened the current phrase
    
    // Define some constants
    static $sTokens = " \r\n\t";    // Space, Return, Newline, Tab
    static $sQuotes = "'\"";        // Single and Double Quotes
    
    // Start the State Machine
    do {
        // Get the next token, which may be the first
        $sToken = isset($sToken)? strtok($sTokens) : strtok($sTagString, $sTokens);
        if ($sToken === false) {
            break;
        }
        
        // Are we within a phrase or not?
        if ($cPhraseQuote !== null) {
            // Will the current token end the phrase?
            if (substr($sToken, -1, 1) === $cPhraseQuote) {
                // Trim the last character and add to the current phrase, with a single leading space if necessary
                if (strlen($sToken) > 1) {
                    $arTags[sizeof($arTags) - 1] .= ((strlen($arTags[sizeof($arTags) - 1]) > 0)? ' ' : null) . substr($sToken, 0, -1);
                }
                    
                $cPhraseQuote = null;
            } else {
                // If not, add the token to the phrase, with a single leading space if necessary
                $arTags[sizeof($arTags) - 1] .= ((strlen($arTags[sizeof($arTags) - 1]) > 0)? ' ' : null) . $sToken;
            }
        } else {
            // Will the current token start a phrase?
            if (strpos($sQuotes, $sToken[0]) !== false) {
                // Will the current token end the phrase?
                if ((strlen($sToken) > 1) && ($sToken[0] === substr($sToken, -1, 1))) {
                    // The current token begins AND ends the phrase, trim the quotes and add it
                    $arTags[] = substr($sToken, 1, -1);
                } else {
                    // Remove the leading quote and add to array
                    $arTags[] = substr($sToken, 1);
                    $cPhraseQuote = $sToken[0];
                }
            } else {
                // If not, simply add the token to the array
                $arTags[] = $sToken;
            }
        }
    } while ($sToken !== false);    // Stop when we receive FALSE from strtok()
    return $arTags;
}

/**
 * Gets a substring between characters
 *
 * @param string $haystack the whole string
 * @param string $start    starting string
 * @param string $end      end string
 *
 * @return mixed
 */
function substring_between($haystack, $start, $end)
{
    if (strpos($haystack, $start) === false || strpos($haystack, $end) === false) {
        return false;
    } else {
        $start_position = strpos($haystack, $start)+strlen($start);
        $end_position = strpos($haystack, $end);
        return substr($haystack, $start_position, $end_position-$start_position);
    }
}

?>
