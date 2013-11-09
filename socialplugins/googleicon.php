<?php 

fof_add_social_filter('googleicon');

function googleicon($text)
{
   $content = "";
   echo "<i ";
   echo ($gg_app_id) ? "style=\"color: #FFF\"" : "style=\"color: #999\"";
   echo " class=\"fa fa-google-plus-square fa-lg\"></i>";
   return $content;
}
?>
