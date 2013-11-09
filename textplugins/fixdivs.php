<?php 

fof_add_item_filter('fixdivs');

function fixdivs($text)
{
   $text = str_replace('<div"', '<div "', $text);
   return $text;
}
?>
