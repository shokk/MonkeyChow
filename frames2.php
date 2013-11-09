<?php
/*
 * This file is part of Monkeychow - http://shokk.wordpress.com/tag/monkeychow/
 *
 * index.php - frameset for frames mode
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
$facebook = new Facebook($fb_app_id, $fb_app_secret, $fb_callback);
#if($facebook->validateAccessToken())
#{
#        $fb_response = $facebook->makeRequest('https://graph.facebook.com/me');
#	print_r($fb_response);
#}

if (preg_match("/(wap|midp|cldc|mmp|Symbian|Smartphone|iPhone|WebKit.*Mobile)/si",$_SERVER[HTTP_USER_AGENT]))
{
	//header("Location: feeds.php?order=unread&newonly=yes&direction=desc");
	header("Location: framesview.php?how=paged");
}
else
{
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Frameset//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-frameset.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">

	<head>
		<title>MonkeyChow</title>
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
      <script type="text/javascript">
              imgPreload = new Image();
       
              ImageUrl = new Array();
<?php
		$n=0;
		foreach (array("ipodarrowright.jpg","ipodarrowdown.jpg","rss.gif","star_on.gif","star_off.gif") as $imageurl)
		{
				echo "		ImageUrl[$n] = \"$imageurl\";\n";
				$n++;
		}
		$sql="SELECT image FROM `$FOF_FEED_TABLE` WHERE image != ''";
		$result = fof_do_query($sql);
		while($row = mysql_fetch_array($result))
		{
			$imageurl = $row['image'];	
				echo "		ImageUrl[$n] = \"$imageurl\";\n";
				$n++;
		}
?>
        for(i = 0; i <= ImageUrl.length; i++)
        {
	       imgPreload.src = ImageUrl[i];
        }
      </script>
	</head>

<?php
/*
//
// This part gets moved to user prefs
if(isset($_COOKIE['fof_layout']))
{
	$cookie_info = explode("$", $_COOKIE['fof_layout']);
	$cols = $cookie_info[0];
	$rows = $cookie_info[1];
}
else
*/
{
	$cols = "26%, *";
	$rows = "15%, *";
}
//
// end prefs -
//
?>
<frameset id="hframeset" cols="<?php echo $cols?>" >
<frameset id="vframeset" rows="<?php echo $rows?>" >
<frame src="http://<?php echo $_REQUEST['baserequest'];?>/framesmenu.php?framed=yes" name="controls" />
<frame src="http://<?php echo $_REQUEST['baserequest'];?>/feeds.php?framed=yes" name="menu" />
</frameset>
<frame src="http://<?php echo $_REQUEST['baserequest'];?>/framesview.php?how=paged&framed=yes" name="items" />
</frameset>
</html>
<?php
}
?>
