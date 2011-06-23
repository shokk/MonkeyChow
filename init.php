<?php
/*
 * This file is part of Monkeychow - http://monkeychow.org
 *
 * init.php - initializes MC, and contains functions used from other scripts
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

require_once('config.php');

define('FOF_MAX_INT', 2147483647);

$FOF_FEED_TABLE = FOF_FEED_TABLE;
$FOF_ITEM_TABLE = FOF_ITEM_TABLE;
$FOF_ITEM_TAG_TABLE = FOF_ITEM_TAG_TABLE;
$FOF_SUBSCRIPTION_TABLE = FOF_SUBSCRIPTION_TABLE;
$FOF_TAG_TABLE = FOF_TAG_TABLE;
$FOF_USER_TABLE = FOF_USER_TABLE;


// Suppress magpie's warnings. We'll handle those ourselves
//error_reporting(E_ERROR);
error_reporting(ALL);

require_once('simplepie/simplepie.inc');

// If not in 'safe mode', increase the maximum execution time:
if (!ini_get('safe_mode')) {
  set_time_limit(240);
}

if (!function_exists("gettext")) 
{
    function _($str)
    { 
        return $str; 
    }
}

if ( !function_exists('htmlspecialchars_decode') )
{
   function htmlspecialchars_decode($text)
   {
       return strtr($text, array_flip(get_html_translation_table(HTML_SPECIALCHARS)));
   }
}

$fof_connection = mysql_connect(FOF_DB_HOST, FOF_DB_USER, FOF_DB_PASS) or 
  die ( _("Cannot connect to database.  Check your configuration.") . "  " . _("Mysql says") . ":  <b>" . mysql_error() . "</b>");
mysql_select_db(FOF_DB_DBNAME, $fof_connection) or
  die ( _("Cannot select database.  Check your configuration") . "  " . _("Mysql says") . ":  " . mysql_error());

if(!$installing) 
    is_writable( FOF_CACHE_DIR ) or 
    die ( _("Cache directory is not writable or does not exist.") . "  " . _("Have you run") . " <a href=\"install.php\"><code>install.php</code></a>?");

function fof_prune_feed($id)
{
    $sql = "select aging from feeds where id='$id'";
    #$result=fof_do_query($sql);
    $row = mysql_fetch_array(fof_do_query($sql));
    $keep_days = $row['aging'];

    if($keep_days > 0)
    {
        $sql = "delete from items where `feed_id`=$id and `star`!=1 and `read`=1 and to_days(CURDATE()) - to_days(`timestamp`)  > $keep_days";
        #print "<br />" . $sql . "<br />";
        fof_do_query($sql);
    }
}

function prune_feeds()
{
		$sql="SELECT id FROM `feeds` ORDER BY `feeds`.`id` ASC;";
		$result=fof_do_query($sql);
		while($row = mysql_fetch_array($result))
		{
				$feedsidarray[]=$row['id'];	
		}
		
		$sql="SELECT DISTINCT feed_id FROM `items` ORDER BY `items`.`feed_id` ASC;";
		$result=fof_do_query($sql);
		while($row = mysql_fetch_array($result))
		{
				$feedsitemsarray[]=$row['feed_id'];	
		}
		
		$result = array_diff($feedsitemsarray, $feedsidarray);
		#$sql="delete FROM `items` ";
		$sql="";
		foreach ($result as $resultthing)
		{
				#print "::" . $resultthing . "::<br />\n";
				if ($sql == "")
				{
						$sql = "delete FROM `items` WHERE `items`.`feed_id` LIKE $resultthing ";
				}
				else
				{
						$sql .= "OR `items`.`feed_id` LIKE $resultthing ";
				}
		}
		#print_r($result);
		#print "<br />" . $sql;
		$result=fof_do_query($sql,1);
		flush();
}

function fof_get_feeds($order = 'title', $direction = 'asc', $tags = '')
{
   fof_prune_expir_feeds();
   $sql = "select id, url, title, link, image, description from feeds";
   if ($tags != '' && $tags != _("All tags") && $tags != _("No tags"))
   {
       $sql .= " where tags LIKE '%$tags%'";
   }
   if ($tags == _("No tags"))
   {
       $sql .= " where tags IS NULL or tags LIKE ''";
   }
   $sql .= " order by title";
   $result = fof_do_query($sql);

   $i = 0;

   while($row = mysql_fetch_array($result))
   {
      #$id = $row['id'];
      $feeds[$i]['id'] = $row['id'];
      $feeds[$i]['url'] = $row['url'];
      $feeds[$i]['title'] = $row['title'];
      $feeds[$i]['link'] = $row['link'];
      $feeds[$i]['description'] = $row['description'];
      $feeds[$i]['image'] = $row['image'];

      $age = fof_rss_age($feeds[$i]['url']);
      $feeds[$i]['age'] = $age;


      if($age == FOF_MAX_INT)
      {
         $agestr = "never";
         $agestrabbr = "&infin;";
      }
      else
      {
         $seconds = $age % 60;
         $minutes = $age / 60 % 60;
         $hours = $age / 60 / 60 % 24;
         $days = floor($age / 60 / 60 / 24);

         if($seconds)
         {
            $agestr = "$seconds sec";
            if($seconds != 1) $agestr .= "s";
            $agestr .= " ago";

            $agestrabbr = $seconds . "s";
         }

         if($minutes)
         {
            $agestr = "$minutes min";
            if($minutes != 1) $agestr .= "s";
            $agestr .= " ago";

            $agestrabbr = $minutes . "m";
         }

         if($hours)
         {
            $agestr = "$hours hr";
            if($hours != 1) $agestr .= "s";
            $agestr .= " ago";

            $agestrabbr = $hours . "h";
         }

         if($days)
         {
            $agestr = "$days day";
            if($days != 1) $agestr .= "s";
            $agestr .= " ago";

            $agestrabbr = $days . "d";
         }
      }
      $feeds[$i]['agestr'] = $agestr;
      $feeds[$i]['agestrabbr'] = $agestrabbr;

      $i++;

   }

   $result = fof_do_query("select count( feed_id ) as count, feed_id as id from feeds, items where feeds.id = items.feed_id and `read` is null group by feed_id order by feeds.title");

   while($row = mysql_fetch_array($result))
   {
     for($i=0; $i<count($feeds); $i++)
     {
      if($feeds[$i]['id'] == $row['id'])
      {
         $feeds[$i]['unread'] = $row['count'];
      }
     }
   }

   $result = fof_do_query("select count( feed_id ) as count, feed_id as id from feeds, items where feeds.id = items.feed_id group by feed_id order by feeds.title");

   while($row = mysql_fetch_array($result))
   {
     for($i=0; $i<count($feeds); $i++)
     {
      if($feeds[$i]['id'] == $row['id'])
      {
         $feeds[$i]['items'] = $row['count'];
      }
     }
   }

   $feeds = fof_multi_sort($feeds, $order, $direction != "asc");

   return $feeds;
}

function fof_prune_expir_feeds()
{
#from feeds, items where feeds.id = items.feed_id and `read` is null group by feed_id order by feeds.title
    $sql = "select id from feeds where expir != 0 AND ((to_days( CURDATE(  )  )  - to_days( date_added )) > expir)";
    $result = fof_do_query($sql);
    while($row = mysql_fetch_array($result))
    {
	 $feed_id = $row['id'];
         $result = fof_do_query("delete from feeds where id = $feed_id");
         $result = fof_do_query("delete from items where feed_id = $feed_id");
    }

}

function fof_view_title($feed=NULL, $what="new", $when=NULL, $start=NULL, $limit=NULL)
{
		$title = "";
		$pieces = explode("::", $_COOKIE["mc_info"]);
		$user_name = $pieces[0];
		$title = "<i>[" . $user_name . ":" . current_user() ."]</i> - ";
	    $title .= "MonkeyChow";

   if(!is_null($when) && $when != "")
   {
      $title .= ' - ' . $when ;
   }
   if(!is_null($feed) && $feed != "")
   {
      $r = fof_feed_row($feed);
      $title .= ' - <a href="' . $r['link'] . '" title="' . htmlspecialchars($r['feed_description']) . '">' . htmlspecialchars($r['title']) . '</a> ';
   }
   if(is_numeric($start))
   {
      if(!is_numeric($limit)) $limit = FOF_HOWMANY;
      $title .= " - items $start to " . ($start + $limit);
   }
   if ($what == "published")
   {
      $title .=' - ' . _("published items");
   }
   else if($what == "search")
   {
      $title .=' - ' . _("custom search");
   }
   else if($what != "all")
   {
      $title .=' - ' . _("new items");
   }
   else
   {
      $title .= ' - ' . _("all items");
   }

   return $title;
}

function fof_get_items($feed=NULL, $what="new", $when=NULL, $start=NULL, $limit=NULL, $order="desc", $tags=NULL, $search)
{
   if(!is_null($when) && $when != "")
   {
     if($when == "today")
     {
      $whendate = date( "Y/m/d", time() - (FOF_TIME_OFFSET * 60 * 60) );
     }
     else
     {
      $whendate = $when;
     }

     $begin = strtotime($whendate);
     $begin = $begin + (FOF_TIME_OFFSET * 60 * 60);
     $end = $begin + (24 * 60 * 60);

     $tomorrow = date( "Y/m/d", $begin + (24 * 60 * 60) );
     $yesterday = date( "Y/m/d", $begin - (24 * 60 * 60) );
   }

   if(is_numeric($start))
   {
      if(!is_numeric($limit))
      {
         $limit = FOF_HOWMANY;
      }

      $limit_clause = " limit $start, $limit ";
   }

   $query = "select feeds.tags as feed_tags, items.read as item_read, feeds.image as feed_image, feeds.title as feed_title, feeds.link as feed_link, feeds.description as feed_description, items.id as item_id, items.link as item_link, items.title as item_title, UNIX_TIMESTAMP(items.timestamp) as timestamp, items.content as item_content, items.dcdate as dcdate, items.dccreator as dccreator, items.dcsubject as dcsubject, items.publish as item_publish, items.star as item_star from feeds, items where items.feed_id=feeds.id";

   if(!is_null($feed) && $feed != "")
   {
     $query .= " and feeds.id = $feed";
   }

   if(!is_null($when) && $when != "")
   {
     $query .= " and UNIX_TIMESTAMP(items.timestamp) > $begin and UNIX_TIMESTAMP(items.timestamp) < $end";
   }

   if(!is_null($tags) && $tags!="" && $tags!="All tags")
   {
	   if ($tags=="No tags")
	   {
           $query .= " and feeds.tags IS NULL";# or tags LIKE ''";
	   }
	   else
	   {
           $query .= " and feeds.tags LIKE \"%" . $tags . "%\"";
	   }
   }

   if ($what == "published")
   {
     $query .= " and items.publish=1";
   }
   else if($what == "public")
   {
     $query .= " and feeds.private=0";
   }
   else if ($what == "starred")
   {
     $query .= " and items.star=1";
   }
   else if($what == "search")
   {
      $query .= " and items.title LIKE \"%" . $search . "%\"";
   }
   else if($what != "all")
   {
     $query .= " and items.read is null";
   }

   $query .= " order by timestamp desc $limit_clause";
   $result = fof_do_query($query);

   while($row = mysql_fetch_array($result))
   {
      $array[] = $row;
   }

   $array = fof_multi_sort($array, 'timestamp', $order != "asc");

   return $array;
}

function fof_get_frame_nav_links($feed=NULL, $what="new", $when=NULL, $start=NULL, $limit=NULL, $framed=NULL, $tags=NULL)
{
   $string = "";

   if(!is_null($when) && $when != "")
   {
     if($when == "today")
     {
      $whendate = date( "Y/m/d", time() - (FOF_TIME_OFFSET * 60 * 60) );
     }
     else
     {
      $whendate = $when;
     }

     $begin = strtotime($whendate);
     $begin = $begin + (FOF_TIME_OFFSET * 60 * 60);
     $end = $begin + (24 * 60 * 60);

     $tomorrow = date( "Y/m/d", $begin + (24 * 60 * 60) );
     $yesterday = date( "Y/m/d", $begin - (24 * 60 * 60) );

	 $string .= "<a href=\"framesview.php?feed=$feed&amp;what=$what&amp;when=$yesterday&amp;how=$how&amp;howmany=$howmany";
	 $string .= ($framed) ? "&amp;framed=yes" : "";
	 $string .= ($tags) ? "&amp;tags=" . $tags : "";
	 $string .= "\">[&laquo; $yesterday]</a> ";
	 if($when != "today") 
	 {
		$string .= "<a href=\"framesview.php?feed=$feed&amp;what=$what&amp;when=today&amp;how=$how&amp;howmany=$howmany";
		$string .= ($framed) ? "&amp;framed=yes" : "";
		$string .= ($tags) ? "&amp;tags=" . $tags : "";
		$string .= "\">[today]</a> ";
	 }
	 if($when != "today") 
	 {
		$string .= "<a href=\"framesview.php?feed=$feed&amp;what=$what&amp;when=$tomorrow&amp;how=$how&amp;howmany=$howmany";
		$string .= ($framed) ? "&amp;framed=yes" : "";
		$string .= ($tags) ? "&amp;tags=" . $tags : "";
		$string .= "\">[$tomorrow &raquo;]</a> ";
	 }
   }

   if(is_numeric($start))
   {
      if(!is_numeric($limit)) $limit = FOF_HOWMANY;

      $earlier = $start + $limit;
      $later = $start - $limit;

	  $string .= "<a href=\"framesview.php?feed=$feed&amp;what=$what&amp;when=$when&amp;how=paged&amp;which=$earlier&amp;howmany=$limit";
	  $string .= ($framed) ? "&amp;framed=yes" : "";
	  $string .= ($tags) ? "&amp;tags=" . $tags : "";
	  $string .= "\">[&laquo; previous $limit]</a> ";
	  if($later >= 0) 
	  {
			$string .= "<a href=\"framesview.php?feed=$feed&amp;what=$what&amp;when=$when&amp;how=paged&amp;howmany=$limit";
	  		$string .= ($framed) ? "&amp;framed=yes" : "";
	  		$string .= ($tags) ? "&amp;tags=" . $tags : "";
			$string .= "\">[current items]</a> ";
	  }
	  if($later >= 0) 
	  {
			$string .= "<a href=\"framesview.php?feed=$feed&amp;what=$what&amp;when=$when&amp;how=paged&amp;which=$later&amp;howmany=$limit";
	  		$string .= ($framed) ? "&amp;framed=yes" : "";
	  		$string .= ($tags) ? "&amp;tags=" . $tags : "";
	  		$string .= "\">[next $limit &raquo;]</a> ";
	  }
   }

   return $string;
}

function fof_get_nav_links($feed=NULL, $what="new", $when=NULL, $start=NULL, $limit=NULL)
{
   $string = "";

   if(!is_null($when) && $when != "")
   {
     if($when == "today")
     {
      $whendate = date( "Y/m/d", time() - (FOF_TIME_OFFSET * 60 * 60) );
     }
     else
     {
      $whendate = $when;
     }

     $begin = strtotime($whendate);
     $begin = $begin + (FOF_TIME_OFFSET * 60 * 60);
     $end = $begin + (24 * 60 * 60);

     $tomorrow = date( "Y/m/d", $begin + (24 * 60 * 60) );
     $yesterday = date( "Y/m/d", $begin - (24 * 60 * 60) );

      $string .= "<a href=\"view.php?feed=$feed&amp;what=$what&amp;when=$yesterday&amp;how=$how&amp;howmany=$howmany\">[&laquo; $yesterday]</a> ";
      if($when != "today") $string .= "<a href=\"view.php?feed=$feed&amp;what=$what&amp;when=today&amp;how=$how&amp;howmany=$howmany\">[today]</a> ";
      if($when != "today") $string .= "<a href=\"view.php?feed=$feed&amp;what=$what&amp;when=$tomorrow&amp;how=$how&amp;howmany=$howmany\">[$tomorrow &raquo;]</a> ";
   }

   if(is_numeric($start))
   {
      if(!is_numeric($limit)) $limit = FOF_HOWMANY;

      $earlier = $start + $limit;
      $later = $start - $limit;

      $string .= "<a href=\"view.php?feed=$feed&amp;what=$what&amp;when=$when&amp;how=paged&amp;which=$earlier&amp;howmany=$limit\">[&laquo; previous $limit]</a> ";
      if($later >= 0) $string .= "<a href=\"view.php?feed=$feed&amp;what=$what&amp;when=$when&amp;how=paged&amp;howmany=$limit\">[current items]</a> ";
      if($later >= 0) $string .= "<a href=\"view.php?feed=$feed&amp;what=$what&amp;when=$when&amp;how=paged&amp;which=$later&amp;howmany=$limit\">[next $limit &raquo;]</a> ";
   }

   return $string;
}

function fof_do_query($sql, $live=0)
{
   global $fof_connection, $fof_query_log;
   
   if (defined('FOF_QUERY_LOG') && FOF_QUERY_LOG)
   {
     list($usec, $sec) = explode(" ", microtime()); 
     $t1 = (float)$sec + (float)$usec;
   }
   
   $result = mysql_query($sql, $fof_connection);

   if (defined('FOF_QUERY_LOG') && FOF_QUERY_LOG)
   {
     list($usec, $sec) = explode(" ", microtime()); 
     $t2 = (float)$sec + (float)$usec;
     $elapsed = $t2 - $t1;
     $fof_query_log .= "[$sql]: $elapsed\n";
   }
   
   if($live)
   {
      return $result;
   }
   else
   {
      if(mysql_errno()) die( _("Cannot query database.") . "  " . _("Have you run") . " <a href=\"install.php\"><code>install.php</code></a>?  " .  _("MySQL says") . ": <b>". mysql_error() . "</b>");
      return $result;
   }
}

function fof_rss_age($url)
{
   #sha or md5?
   $filename = FOF_CACHE_DIR . "/" . md5($url) . '.spc';
   if ( file_exists( $filename ) )
   {
      // find how long ago the file was added to the cache
      // and whether that is longer then MAX_AGE
      $mtime = filemtime( $filename );
      $age = time() - $mtime;
      return $age;
   }
   else
   {
      return FOF_MAX_INT;
   }
}

function fof_getRSSLocation($html, $location)
{
    if(!$html or !$location){
        return false;
    }else{
        #search through the HTML, save all <link> tags
        # and store each link's attributes in an associative array
        preg_match_all('/<link\s+(.*?)\s*\/?>/si', $html, $matches);
        $links = $matches[1];
        $final_links = array();
        $link_count = count($links);
        for($n=0; $n<$link_count; $n++){
            $attributes = preg_split('/\s+/s', $links[$n]);
            foreach($attributes as $attribute){
                $att = preg_split('/\s*=\s*/s', $attribute, 2);
                if(isset($att[1])){
                    $att[1] = preg_replace('/([\'"]?)(.*)\1/', '$2', $att[1]);
                    $final_link[strtolower($att[0])] = $att[1];
                }
            }
            $final_links[$n] = $final_link;
        }
        #now figure out which one points to the RSS file
        for($n=0; $n<$link_count; $n++){
            if(strtolower($final_links[$n]['rel']) == 'alternate'){
                if(strtolower($final_links[$n]['type']) == 'application/rss+xml'){
                    $href = $final_links[$n]['href'];
                }
                if(!$href and strtolower($final_links[$n]['type']) == 'text/xml'){
                    #kludge to make the first version of this still work
                    $href = $final_links[$n]['href'];
                }
                if($href){
                    if(strstr($href, "http://") !== false){ #if it's absolute
                        $full_url = $href;
                    }else{ #otherwise, 'absolutize' it
                        $url_parts = parse_url($location);
                        #only made it work for http:// links. Any problem with this?
                        $full_url = "http://$url_parts[host]";
                        if(isset($url_parts['port'])){
                            $full_url .= ":$url_parts[port]";
                        }
                        if($href{0} != '/'){ #it's a relative link on the domain
                            $full_url .= dirname($url_parts['path']);
                            if(substr($full_url, -1) != '/'){
                                #if the last character isn't a '/', add it
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

function fof_render_feed_link($row)
{
   $link = $row['link'];
   $description = htmlspecialchars($row['description']);
   $title = htmlspecialchars($row['title']);
   $url = $row['url'];

   $s = "<b><font class=\"feed_title\"><a class=\"item_title\" href=\"$link\" title=\"$description\">$title</a></font></b> ";
   //$s .= "<a href=\"$url\"><img class=\"valign\" src=\"rss.gif\" border=\"0\"></a>";

   return $s;
}

function fof_opml_to_array($opml)
{
   $rx = "/xmlurl=\"(.*?)\"/mi";

   if (preg_match_all($rx, $opml, $m))
   {
      for($i = 0; $i < count($m[0]) ; $i++)
      {
         $r[] = $m[1][$i];
      }
  }

  return $r;
}

function fof_add_feed($url)
{
   if(!$url) return;
   $url = trim($url);

   if(substr($url, 0, 7) != 'http://' && substr($url, 0, 8) != 'https://')
   {
     $url = 'http://' . $url;
   }

   echo _("Attempting to subscribe to ") . "<a href=\"$url\">$url</a>...<br />";

   if($row = fof_is_subscribed($url))
   {
      print "<font color='red'><u>" . _("You are already subscribed to ") . fof_render_feed_link($row) . "</u></font><br /><br />";
      return true;
   }

   $piefeed = new SimplePie();
   $piefeed->set_image_handler();
   //$piefeed->strip_ads(true);
   $piefeed->set_feed_url($url);
   $piefeed->set_cache_location(FOF_CACHE_DIR);
   $piefeed->init();
   $piefeed->handle_content_type();

   if (!$piefeed->data)
   {
      echo "&nbsp;&nbsp;<font color=\"red\">" . _("URL is not RSS or is invalid.") . "</font><br />";
      echo "&nbsp;&nbsp;(<font color=\"red\">" . _("error was") . "</font>: <B>" . $piefeed->error . "</b>)<br />";
      echo "&nbsp;&nbsp;<a href=\"http://feedvalidator.org/check?url=$url\">" . _("The FEED validator may give more information.") . "</a><br />";
      echo "&nbsp;&nbsp;<a href=\"http://validator.w3.org/check?uri=$url\">" . _("The XHTML validator may give more information.") . "</a><br />";

      echo "<font color=\"red\"><b>" . _("Can't load URL.  Giving up.") . "</b></font><br />";
      echo "<font color=\"red\"><b>" . _("Autodiscovery failed.  Giving up.") . "</b></font><br />";
   }
   else
   {
      echo _("Adding feed...") . "<br />";
      fof_actually_add_feed($url, $piefeed);
      echo "<font color=\"green\"><b>" . _("Subscribed") . ".</b></font><br />";
   }
	$safeurl = mysql_escape_string( $url );
	$result = fof_do_query("select id from feeds where url='$safeurl'");
	$row = mysql_fetch_array($result);
	$feed_id = $row['id'];

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

function fof_actually_add_feed($url, $piefeed)
{
   $title = $piefeed->get_title();
   $title = str_replace('"', '', $title);
   $title = str_replace("'", '', $title);
   $title = htmlspecialchars($title, ENT_QUOTES);
   $title = htmlspecialchars($title);
   $link = $piefeed->get_link();
   $link = str_replace('"', '', $link);
   $link = str_replace("'", '', $link);
   #$link = htmlspecialchars($link, ENT_QUOTES);
   #$link = htmlspecialchars($link);
   $description = $piefeed->get_description();
   $description = str_replace('"', '', $description);
   $description = str_replace("'", '', $description);
   $description = htmlspecialchars($description, ENT_QUOTES);
   #$description = htmlspecialchars($description);
   #$description = htmlspecialchars_decode($description);
   #$description = htmlspecialchars_decode($description);

   $sql = "insert into feeds (url,title,link,description) values ('$url','$title','$link','$description')";
   //echo "$url $title $link $description<br />";
   //echo "$sql<br />";
   fof_do_query($sql);

   fof_update_feed($url);
}

function fof_edit_feed($id, $url, $title, $link, $description, $date_added, $tags, $aging, $expir, $private, $image)
{
	global $FOF_FEED_TABLE;
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
   if ($private == '')
   {
      $private="0";
   }
   if ($private == 'on')
   {
      $private="1";
   }

   $sql = "update " . $FOF_FEED_TABLE . " SET `url` = '$url', `title` = '$title', `link` = '$link', `description` = '$description', `date_added` = '$date_added', `tags` = '$tags', `private` = '$private'";
   if ($aging != '')
   {
       $sql .= ", `aging` = '$aging' ";
   }
   if ($expir != '')
   {
       $sql .= ", `expir` = '$expir' ";
   }
   #if ($image != '')
   #{
       $sql .= ", `image` = '$image' ";
   #}


   $sql .= " WHERE `id` = '$id'";
   //print ":SQL: " . $sql;
   fof_do_query($sql);
}

function fof_is_subscribed($url)
{
   $safe_url = mysql_escape_string($url);

   $result = fof_do_query("select url, title, link, id from feeds where url = '$safe_url'");

   if(mysql_num_rows($result) == 0)
   {
      return false;
   }
   else
   {
      $row = mysql_fetch_array($result);
      return $row;
   }
}

function fof_feed_row($id)
{
   $result = fof_do_query("select url, title, link, id from feeds where id = '$id'");

   if(mysql_num_rows($result) == 0)
   {
      return false;
   }
   else
   {
      $row = mysql_fetch_array($result);
      return $row;
   }
}

function fof_search_word($word)
{
   $result = fof_do_query("select url, title, link, id from feeds where title LIKE '$word'");
   if(mysql_num_rows($result) == 0)
   {
      return false;
   }
   else
   {
      $row = mysql_fetch_array($result);
      return $row;
   }
}

function fof_update_feed($url)
{
		global $FOF_FEED_TABLE;
   #
   # Get feed data.
   #
   if(!$url) return 0;

   if (!empty($url)) {
       $piefeed = new SimplePie();
       $piefeed->set_feed_url($url);
   }
   else
   {
       return 0;
   }
   $piefeed->set_cache_location(FOF_CACHE_DIR);
   $piefeed->init();
   $piefeed->handle_content_type();

   if (!$piefeed->data)
   {
      print "<font color=\"red\">" . _("error was") . "</font>: <B>" . $piefeed->error . "</b> ";
      print "<a href=\"http://feedvalidator.org/check?url=$url\">" . _("try to validate it?") . "</a> ";
      unset($piefeed);
      return 0;
   }

   $title = $piefeed->get_title();
   $link = $piefeed->get_link();
   $description = $piefeed->get_description();

   $safeurl = mysql_escape_string( $url );
   $result = fof_do_query("select id, url, aging from feeds where url='$safeurl'");

   $row = mysql_fetch_array($result);
   $feed_id = $row['id'];
   $keep_days = $row['aging'];
   if ($keep_days < 0)
   {
       $keep_days = 60;
   }

	$result2 = fof_do_query("select image from feeds where `id`='$feed_id'");
	$row2 = mysql_fetch_array($result2);
	$image2 = $row2['image'];

	if (!$image2)
	{
		$imagelink = $piefeed->get_favicon(true, '');
		$HTTPRequest = @fopen($imagelink, 'r'); 
		if ($HTTPRequest) 
		{ 
			stream_set_timeout($HTTPRequest, 0.1);
			$favicon = fread($HTTPRequest, 8192);
			$HTTPRequestData = stream_get_meta_data($HTTPRequest);
			fclose($HTTPRequest);
			if (!$HTTPRequestData['timed_out'] && strlen($favicon) < 42) 
			{ 
				$imagelink = "";
			} 
		}
		else
		{
			$imagelink = $piefeed->get_image_url();
			$HTTPRequest = @fopen($imagelink, 'r'); 
			if ($HTTPRequest) 
			{ 
				stream_set_timeout($HTTPRequest, 0.1);
				$favicon = fread($HTTPRequest, 8192);
				$HTTPRequestData = stream_get_meta_data($HTTPRequest);
				fclose($HTTPRequest);
				if (!$HTTPRequestData['timed_out'] && strlen($favicon) < 42) 
				{ 
					$imagelink = "";
				} 
			}
			else
			{ 
					$imagelink="";
			}
		}
	
		$sql = "update `$FOF_FEED_TABLE` set `image`='$imagelink' where `id`='$feed_id'";
		$result = fof_do_query($sql);
	}


   #
   # Get article items and attributes
   #
   foreach($piefeed->get_items() as $item) {
      $ageflag= "0";
      $dccreator = "";
      $dcsubject = "";
      $link = mysql_escape_string($item->get_permalink());
      if(!$link)
      {
         $link = $item->get_id();
      }  

      $title = mysql_escape_string($item->get_title());
      if(!$title)
      {
         $title = "[" . _("no title") . "]";
      }

      # get <dc:creator> or <author>
      foreach($item->get_authors() as $author) {
          $authorname = $author->get_name() . " " . $author->get_email();
          if (!empty($authorname))
          {
              $dccreator .= $authorname . ', ';
          }
      }
      $dccreator = mysql_escape_string(substr("$dccreator", 0, -2));
   
      # get <dc:date> and <pubdate>
      $dcdate = mysql_escape_string($item->get_date());
      $dcdate = eregi_replace("," , "", $dcdate);

      # get <dc:subject> or <category>
      $category_array = $item->get_category();
      $category_array = array_unique($category_array);
      foreach($category_array as $category) {
         if (!empty($category))
         {
            $dcsubject .= $category . ', ' ;
         }
      }
      $dcsubject = substr(mysql_escape_string($dcsubject), 0, -2);
      unset($category_array);

      # get article content
      $content = mysql_escape_string($item->get_description());
      $content = str_replace('"?i=http', '"http', $content); # dont know why
                                                            # this creeps in
      if ($enclosure = $item->get_enclosure(0)) {
          $content .= '<br />(' . $enclosure->get_type() . '; ' . $enclosure->get_size() . ' MB)<br />';
      }

      #
      # Now manage the article data
      #
      $sql = "select id from items where feed_id='$feed_id' and link='$link'";
      #print "<br />" . $sql . "<br />";
      $result = fof_do_query($sql);
      $row = mysql_fetch_array($result);
      $id = $row['id'];

      # if the item does not already exist, add it
      if(mysql_num_rows($result) == 0)
      {
         # dcdate   : August 2, 2006, 1:30 am
         # timestamp: 2006-09-16 15:51:53
         # add it only if it's not older than keep_days
         $dcdatetime = strtotime($dcdate);
		 # We set ageflag == 1 if its OK to add the item to the database
         if ($dcdatetime < 1)
         {
             $dcdatetime = NULL;
             $ageflag = 1;
         }
         else 
         {
             if ((time() - $dcdatetime) < ($keep_days * 24 * 60 * 60))
             {
                 $ageflag = 1;
             }
         }
         if ($ageflag)
         {
             $n++;
             $sql = "insert into items (feed_id,link,title,content,dcdate,dccreator,dcsubject) values ('$feed_id','$link','$title','$content','$dcdatetime','$dccreator','$dcsubject')";
             #print "<br />" . $sql . "<br />";
             $result = fof_do_query($sql);
             $ids[] = $id; #keep track of it so we don't delete it below
             $ageflag = 0;
         }
      }
   }

   #
   # Clean up old articles that are not starred
   #
   #
   #if(defined('FOF_KEEP_DAYS'))
   #{
   #   $sql="select aging from feeds WHERE id=" . $feed_id;
   #   $result = fof_do_query($sql);
   #   $row = mysql_fetch_array($result);
   #   $keep_days = $row['aging'];
      
   #if($keep_days > 0)
   #{
   #   # keep_days should come from the feeds.aging column
   #   $sql = "delete from items where `star`!=1 and feed_id = $feed_id ";
   #   $sql .= " and `read`=1 ";
   #   $sql .= " and to_days( CURDATE(  )  )  - to_days( timestamp )  > $keep_days";
   #   #print "<br />" . $sql . "<br />";
   #      fof_do_query($sql);
   #}
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

function fof_balanceTags($text) {

   $tagstack = array(); $stacksize = 0; $tagqueue = ''; $newtext = '';

   # WP bug fix for comments - in case you REALLY meant to type '< !--'
   $text = str_replace('< !--', '<    !--', $text);
   # WP bug fix for LOVE <3 (and other situations with '<' before a number)
   $text = preg_replace('#<([0-9]{1})#', '&lt;$1', $text);

   while (preg_match("/<(\/?\w*)\s*([^>]*)>/",$text,$regex)) {
      $newtext = $newtext . $tagqueue;

      $i = strpos($text,$regex[0]);
      $l = strlen($tagqueue) + strlen($regex[0]);

      // clear the shifter
      $tagqueue = '';
      // Pop or Push
      if ($regex[1][0] == "/") { // End Tag
         $tag = strtolower(substr($regex[1],1));
         // if too many closing tags
         if($stacksize <= 0) {
            $tag = '';
            //or close to be safe $tag = '/' . $tag;
         }
         // if stacktop value = tag close value then pop
         else if ($tagstack[$stacksize - 1] == $tag) { // found closing tag
            $tag = '</' . $tag . '>'; // Close Tag
            // Pop
            array_pop ($tagstack);
            $stacksize--;
         } else { // closing tag not at top, search for it
            for ($j=$stacksize-1;$j>=0;$j--) {
               if ($tagstack[$j] == $tag) {
               // add tag to tagqueue
                  for ($k=$stacksize-1;$k>=$j;$k--){
                     $tagqueue .= '</' . array_pop ($tagstack) . '>';
                     $stacksize--;
                  }
                  break;
               }
            }
            $tag = '';
         }
      } else { // Begin Tag
         $tag = strtolower($regex[1]);

         // Tag Cleaning

         // Push if not img or br or hr
         if($tag != 'br' && $tag != 'img' && $tag != 'hr') {
            $stacksize = array_push ($tagstack, $tag);
         }

         // Attributes
         // $attributes = $regex[2];
         $attributes = $regex[2];
         if($attributes) {
            $attributes = ' '.$attributes;
         }
         $tag = '<'.$tag.$attributes.'>';
      }
      $newtext .= substr($text,0,$i) . $tag;
      $text = substr($text,$i+$l);
   }

   // Clear Tag Queue
   $newtext = $newtext . $tagqueue;

   // Add Remaining text
   $newtext .= $text;

   // Empty Stack
   while($x = array_pop($tagstack)) {
      $newtext = $newtext . '</' . $x . '>'; // Add remaining tags to close
   }

   // WP fix for the bug with HTML comments
   $newtext = str_replace("< !--","<!--",$newtext);
   $newtext = str_replace("<    !--","< !--",$newtext);

   return $newtext;
}

function fof_multi_sort($tab,$key,$rev){
   if($rev)
   {
   $compare = create_function('$a,$b','if (strtolower($a["'.$key.'"]) == strtolower($b["'.$key.'"])) {return 0;}else {return (strtolower($a["'.$key.'"]) > strtolower($b["'.$key.'"])) ? -1 : 1;}');
   }
   else
   {
   $compare = create_function('$a,$b','if (strtolower($a["'.$key.'"]) == strtolower($b["'.$key.'"])) {return 0;}else {return (strtolower($a["'.$key.'"]) < strtolower($b["'.$key.'"])) ? -1 : 1;}');
   }

   usort($tab,$compare) ;
   return $tab ;
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
 */
function parse_tag_string($sTagString)
{
	$arTags = array();			// Array of Output
	$cPhraseQuote = null;		// Record of the quote that opened the current phrase
	
	// Define some constants
	static $sTokens = " \r\n\t";	// Space, Return, Newline, Tab
	static $sQuotes = "'\"";		// Single and Double Quotes
	
	// Start the State Machine
	do
	{
		// Get the next token, which may be the first
		$sToken = isset($sToken)? strtok($sTokens) : strtok($sTagString, $sTokens);
		if ($sToken === false) break;
		
		// Are we within a phrase or not?
		if ($cPhraseQuote !== null)
		{
			// Will the current token end the phrase?
			if (substr($sToken, -1, 1) === $cPhraseQuote)
			{
				// Trim the last character and add to the current phrase, with a single leading space if necessary
				if (strlen($sToken) > 1)
					$arTags[sizeof($arTags) - 1] .= ((strlen($arTags[sizeof($arTags) - 1]) > 0)? ' ' : null) . substr($sToken, 0, -1);
					
				$cPhraseQuote = null;
			}
			else
			{
				// If not, add the token to the phrase, with a single leading space if necessary
				$arTags[sizeof($arTags) - 1] .= ((strlen($arTags[sizeof($arTags) - 1]) > 0)? ' ' : null) . $sToken;
			}
		}
		else
		{
			// Will the current token start a phrase?
			if (strpos($sQuotes, $sToken[0]) !== false)
			{
				// Will the current token end the phrase?
				if ((strlen($sToken) > 1) && ($sToken[0] === substr($sToken, -1, 1)))
				{
					// The current token begins AND ends the phrase, trim the quotes and add it
					$arTags[] = substr($sToken, 1, -1);
				}
				else
				{
					// Remove the leading quote and add to array
					$arTags[] = substr($sToken, 1);
					$cPhraseQuote = $sToken[0];
				}
			}
			else
			{
				// If not, simply add the token to the array
				$arTags[] = $sToken;
			}
		}
	}
	while ($sToken !== false);	// Stop when we receive FALSE from strtok()
	return $arTags;
}

function substring_between($haystack,$start,$end) 
{
   if (strpos($haystack,$start) === false || strpos($haystack,$end) === false) {
       return false;
   } else {
       $start_position = strpos($haystack,$start)+strlen($start);
       $end_position = strpos($haystack,$end);
       return substr($haystack,$start_position,$end_position-$start_position);
   }
}
?>
