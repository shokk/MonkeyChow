<?php
function add_feed_link($name, $url)
{
	echo "<a target=\"items\" href=\"";
	echo "http://" . $_SERVER["HTTP_HOST"] . dirname($_SERVER["SCRIPT_NAME"]) . "/add.php?rss_url=" . urlencode($url);
	echo "\">$name</a><br />\n";
}

#add_feed_link("",""); # Sample line
add_feed_link("Apple Blog", "http://www.apple.com/main/rss/hotnews/hotnews.rss");
add_feed_link("BBC Front Page", "http://newsrss.bbc.co.uk/rss/newsonline_world_edition/front_page/rss.xml");
add_feed_link("BBC Sports","http://newsrss.bbc.co.uk/rss/sportonline_world_edition/front_page/rss.xml");
add_feed_link("CNN Top Stories", "http://rss.cnn.com/rss/cnn_topstories.rss");
add_feed_link("CNN Money", "http://rss.cnn.com/rss/money_topstories.rss");
add_feed_link("Digg Popular Stories", "http://feeds.digg.com/digg/popular.rss");
add_feed_link("Engadget", "http://www.engadget.com/rss.xml");
add_feed_link("ESPN","http://sports-ak.espn.go.com/espn/rss/news");
add_feed_link("Google Blog", "http://googleblog.blogspot.com/");
add_feed_link("Lifehacker", "http://feeds.gawker.com/lifehacker/full");
add_feed_link("Microsoft Feeds", "http://www.microsoft.com/windows/rss/default.mspx");
add_feed_link("New York Times Politics Column","http://www.nytimes.com/services/xml/rss/nyt/Politics.xml");
add_feed_link("Shokk.COM", "http://www.shokk.com/blog/feed/");
add_feed_link("Silicon Alley Insider", "http://feedproxy.google.com/typepad/alleyinsider/silicon_alley_insider");
add_feed_link("Slashdot", "http://rss.slashdot.org/Slashdot/slashdot");
add_feed_link("Slate", "http://feedproxy.google.com/slate");
add_feed_link("Wikimedia Blog", "http://blog.wikimedia.org/feed/");

?>
