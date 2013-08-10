<?php 

fof_add_item_filter('atarget');

function atarget($text)
{
   $text = str_replace('<a href', '<a target="_blank" href', $text);
   return $text;
}
?>
