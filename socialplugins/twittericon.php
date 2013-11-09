<?php 

fof_add_social_filter('twittericon');

function twittericon($text)
{
   $content = "";
   echo "<i ";
   echo ($tw_app_id) ? "style=\"color: #FFF\"" : "style=\"color: #999\"";
   echo " class=\"fa fa-twitter-square fa-lg\"></i>";
   return $content;
}
?>
