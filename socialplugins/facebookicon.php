<?php 

fof_add_social_filter('facebookicon');

function facebookicon($text)
{
   $content = "";
   echo "<i ";
   echo ($fb_app_id) ? "style=\"color: #FFF\"" : "style=\"color: #999\"";
   echo " class=\"fa fa-facebook-square fa-lg\"></i>";
   return $content;
}
?>
