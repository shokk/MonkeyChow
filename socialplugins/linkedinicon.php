<?php 

fof_add_social_filter('linkedinicon');

function linkedinicon($text)
{
   $content = "";
   echo "<i ";
   echo ($li_app_id) ? "style=\"color: #FFF\"" : "style=\"color: #999\"";
   echo " class=\"fa fa-linkedin-square fa-lg\"></i>";
   return $content;
}
?>
