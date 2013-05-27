<?php

    if (file_exists("config.php"))
    {
	include_once("fof-main.php");

        if ($fof_user_prefs['frames'] == 1)
		{
				header("Location: frames.php?newOnly=");
		}
		else
		{
				header("Location: feeds.php?framed=no");
		}
    }
    else
    {
        if (file_exists("install.php"))
        {
	    header("Location:install.php");
        }
    }
?>
