<?php 

fof_add_social_filter('tumblricon');

function tumblricon($text)
{
   $content = "";
   echo "<i ";
   echo ($tu_app_id) ? "style=\"color: #FFF\"" : "style=\"color: #999\"";
   echo " class=\"fa fa-tumblr-square fa-lg\"></i>";
   return $content;
}
?>
