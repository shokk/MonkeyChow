// Set this:
var playtagger_url = 'playtagger/';
var playurl = '';

var all = document.getElementsByTagName('a');
for (var i = 0, o; o = all[i]; i++) {
    if (o.href.match(/\.mp3$/i)) {
        playurl = o.href;
        o.href = playtagger_url + 'playtagger.php?url=' + playurl;
        o.innerHTML = 'click to play in new window';
        o.target = "_blankz";
    }
}
