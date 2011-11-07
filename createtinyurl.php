<?php
$url=$_REQUEST['url'];

function tinyurl($url){
return(trim(file_get_contents('http://tinyurl.com/api-create.php?url='.$url)));
}

echo tinyurl($url);
?>
