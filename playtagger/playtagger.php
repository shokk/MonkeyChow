<?php
    require_once '../URLResolver/URLResolver.php';
    #$url = "http://media.blubrry.com/chainsawsuit/traffic.libsyn.com/chainsawsuit/Chainsawsuit_-_Episode_33_-_A_Christmas_Carol.mp3";
?>
<html>
<head>
</head>
<body>
<?php
// protect the url urldecode etc
$url = $_REQUEST['url'];
$resolver = new URLResolver();
$theuseragent='Mozilla/5.0 (compatible; MonkeyChow/1.0; +http://www.shokk.com)';
$resolver->setUserAgent($theuseragent);
$resolver->setCookieJar("/tmp/url_resolver.cookies");
$url_result = $resolver->resolveURL($url);

// Test to see if any error occurred while resolving the URL:
if ($url_result->didErrorOccur()) {
    //print "there was an error resolving $url:\n  ";
    //print $url_result->getErrorMessageString();
}
else
{
    $theurlresult = $url_result->getURL();
}

echo '<br />';
echo '<a href="' . $theurlresult . '"><--- play now</a>';
echo '<br />';
?>
    <script src="playtagger.js" type="text/javascript"></script>
</body>
</html>
