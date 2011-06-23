<?php
/*
 * This file is part of FEED ON FEEDS - http://feedonfeeds.com/
 * This has been adapted for use with MonkeyChow by Ernie Oporto
 *
 * fof-main.php - initializes and contains functions used from other scripts
 *
 *
 * Copyright (C) 2004-2007 Stephen Minutillo
 * steve@minutillo.com - http://minutillo.com/steve/
 *
 * Distributed under the GPL - see LICENSE
 *
 */

//$LOG = fopen("fof.log", 'a');

require_once("config.php");
require_once("fof-db.php");

init_plugins();

if(!$fof_no_login)
{
    require_user();
}

function fof_log($message)
{
	global $LOG;
	#fwrite($LOG, "$message\n");
}

function require_user()
{
    if(!isset($_COOKIE["mc_info"]) )
    {
        Header("Location: login.php");
    }
    
	$pieces = explode("::", $_COOKIE["mc_info"]);
    $user_name = $pieces[0];
    $user_password_hash = $pieces[1];
    
    if(!fof_authenticate($user_name, $user_password_hash))
    {
        Header("Location: login.php");
    }
}

function fof_authenticate($user_name, $user_password_hash)
{
    global $fof_user_name;
        
    if(fof_db_authenticate($user_name, $user_password_hash))
    {
		setcookie("mc_info","$user_name::$user_password_hash",time()+(60*60*24*365),COOKIE_DIR);
        return true;
    }
}

function fof_logout()
{
	setcookie("mc_info","",time()-3601,"/");
}

function current_user()
{
    global $fof_user_id;
    
    return $fof_user_id;
}

function fof_username()
{
    global $fof_user_name;
    
    return $fof_user_name;
}

function fof_prefs()
{
    global $fof_user_prefs;
        
    return $fof_user_prefs;
}

function fof_is_admin()
{
    global $fof_user_level;
    
    return $fof_user_level == "admin";
}

function init_plugins()
{
    global $item_filters;
    
    $item_filters = array();
    
    $dirlist = opendir(FOF_DIR . "/plugins");
    while($file=readdir($dirlist))
    {
    	fof_log("considering " . $file);
        if(ereg('\.php$',$file))
        {
        	fof_log("including " . $file);

            include(FOF_DIR . "/plugins/" . $file);
        }
    }

    closedir();
}

function fof_add_item_filter($function)
{
    global $item_filters;
    
    $item_filters[] = $function;
}

function strip_punctuation( $text )
{
    $urlbrackets    = '\[\]\(\)';
    $urlspacebefore = ':;\'_\*%@&?!' . $urlbrackets;
    $urlspaceafter  = '\.,:;\'\-_\*@&\/\\\\\?!#' . $urlbrackets;
    $urlall         = '\.,:;\'\-_\*%@&\/\\\\\?!#' . $urlbrackets;
 
    $specialquotes  = '\'"\*<>';
 
    $fullstop       = '\x{002E}\x{FE52}\x{FF0E}';
    $comma          = '\x{002C}\x{FE50}\x{FF0C}';
    $arabsep        = '\x{066B}\x{066C}';
    $numseparators  = $fullstop . $comma . $arabsep;
 
    $numbersign     = '\x{0023}\x{FE5F}\x{FF03}';
    $percent        = '\x{066A}\x{0025}\x{066A}\x{FE6A}\x{FF05}\x{2030}\x{2031}';
    $prime          = '\x{2032}\x{2033}\x{2034}\x{2057}';
    $nummodifiers   = $numbersign . $percent . $prime;
 
    return preg_replace(
        array(
        // Remove separator, control, formatting, surrogate,
        // open/close quotes.
            '/[\p{Z}\p{Cc}\p{Cf}\p{Cs}\p{Pi}\p{Pf}]/u',
        // Remove other punctuation except special cases
            '/\p{Po}(?<![' . $specialquotes .
                $numseparators . $urlall . $nummodifiers . '])/u',
        // Remove non-URL open/close brackets, except URL brackets.
            '/[\p{Ps}\p{Pe}](?<![' . $urlbrackets . '])/u',
        // Remove special quotes, dashes, connectors, number
        // separators, and URL characters followed by a space
            '/[' . $specialquotes . $numseparators . $urlspaceafter .
                '\p{Pd}\p{Pc}]+((?= )|$)/u',
        // Remove special quotes, connectors, and URL characters
        // preceded by a space
            '/((?<= )|^)[' . $specialquotes . $urlspacebefore . '\p{Pc}]+/u',
        // Remove dashes preceded by a space, but not followed by a number
            '/((?<= )|^)\p{Pd}+(?![\p{N}\p{Sc}])/u',
        // Remove consecutive spaces
            '/ +/',
        ),
        ' ',
        $text );
}

?>
