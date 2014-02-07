/**
 * Playtagger- Mod
 * Adaptation of original del.icio.us Playtagger (now defunct)
 * -Uses local images and script
 * -Adds class "mp3" to img
 * Author: http://hearingvoices.com/news/2011/10/playtagger-mod/
 * Version: 0.1 (2011-10-06)
 *
 * Based on:
 * http://www.consumingexperience.com/2006/02/mp3s-align-delicious-playtagger-icon.html
 *
 * Dual licensed under the MIT and GPL licenses:
 * http://www.opensource.org/licenses/mit-license.php
 * http://www.gnu.org/licenses/gpl.html
 * 
 * Upload /playtagger_mod folder on your site.
 * Set the playtagger_url variable form: http://YOUR_DOMAIN.COM/PATH_TO_FOLDER/playtagger_mod/
 * to the URL of your /playtagger_mod folder (needs trailing "/").
 */

// Set this:
//var playtagger_url = 'http://YOUR_DOMAIN.COM/PATH_TO_FOLDER/playtagger_mod/';
//var playtagger_url = 'playtagger/';
var playtagger_url = '';

// The rest stays the same:
if(typeof(Delicious) == 'undefined') Delicious = {}
Delicious.Mp3 = {
playimg: null,
player: null,
go: function() {
var all = document.getElementsByTagName('a')
for (var i = 0, o; o = all[i]; i++) {
   var img = document.createElement('img')
   img.src = playtagger_url + 'play.gif';
   img.title = 'listen'
   img.height = img.width = 12
   img.style.marginRight = '0.5em'
   img.className = 'mp3';
   img.style.cursor = 'pointer'
   img.onclick = Delicious.Mp3.makeToggle(img, o.href )
   o.parentNode.insertBefore(img, o)
}},
toggle: function(img, url) {
if (Delicious.Mp3.playimg == img) Delicious.Mp3.destroy()
else {
if (Delicious.Mp3.playimg ) Delicious.Mp3.destroy()
var a = img.nextSibling, c=''
if (document.defaultView && document.defaultView.getComputedStyle) c = document.defaultView.getComputedStyle(a,null).getPropertyValue('color')
else if (a.currentStyle) c = a.currentStyle['color']
c = c.replace(/^rgb|[ ()]/g, '')
var hex = c.match(/^#?([a-f0-9]{6})$/), hexSm = c.match(/^#?([a-f0-9]{3})$/), rgb = c.match (/^([0-9]+),([0-9]+),([0-9]+)$/)
if (rgb) { var c16 = "0123456789abcdef", c=''; for(var i=1,d;d=rgb[i];i++) { var j=d%16; c=c+c16.charAt((d-j)/16)+c16.charAt(j) } }
else if (hex) c = hex[1]
else if (hexSm) c = hexSm[1].charAt(0) + hexSm[1].charAt(0) + hexSm[1].charAt(1) + hexSm[1].charAt(1) + hexSm[1].charAt(2) + hexSm[1].charAt(2)
else c = "000000"
img.src = playtagger_url + 'stop.gif';
Delicious.Mp3.playimg = img;
Delicious.Mp3.player = document.createElement('span')
Delicious.Mp3.player.innerHTML = '<object style="vertical-align:bottom;margin-right:0.2em" classid="clsid:d27cdb6e-ae6d-11cf-96b8-444553540000"' +
'codebase=" http://fpdownload.macromedia.com/pub/shockwave/cabs/flash/swflash.cab#version=6,0,0,0 <http://fpdownload.macromedia.com/pub/shockwave/cabs/flash/swflash.cab#version=6,0,0,0> "' +
'width="100" height="14" id="player" align="middle">' +
'<param name="wmode" value="transparent" />' +
'<param name="allowScriptAccess" value="sameDomain" />' +
'<param name="flashVars" value="theLink='+url+'&amp;fontColor='+c+'" />' +
'<param name="movie" value="' + playtagger_url + 
'playtagger.swf" /><param name="quality" value="high" />' +
'<embed style="vertical-align:bottom;margin-right:0.2em" src="' + playtagger_url + 
'playtagger.swf" flashVars="theLink='+url+'&amp;fontColor='+c+'"'+
'quality="high" wmode="transparent" width="100" height="14" name="player"' +
'align="middle" allowScriptAccess="sameDomain" type="application/x-shockwave-flash"' +
' pluginspage="http://www.macromedia.com/go/getflashplayer" /></object>'
img.parentNode.insertBefore(Delicious.Mp3.player , img.nextSibling)
}},
destroy: function() {
Delicious.Mp3.playimg.src =  playtagger_url + 'stop.gif'; Delicious.Mp3.playimg = null
Delicious.Mp3.player.removeChild(Delicious.Mp3.player.firstChild); Delicious.Mp3.player.parentNode.removeChild(Delicious.Mp3.player); Delicious.Mp3.player = null
},
makeToggle: function(img, url) { return function(){ Delicious.Mp3.toggle(img, url) }} }

Delicious.addLoadEvent = function(f) { var old = window.onload
if (typeof old != 'function') window.onload = f
else { window.onload = function() { old(); f() }} }

Delicious.addLoadEvent(Delicious.Mp3.go)
