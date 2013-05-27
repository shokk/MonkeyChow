<?php 
include_once("fof-main.php");
include_once("init.php"); 
?>

<div class="menu">

<ul>
<li><a href="view.php?how=paged"><?php echo _("unread") ?></a></li>
<li><a href="view.php?what=all&amp;how=paged"><?php echo _("all") ?></a></li>
<li><a href="view.php?what=all&amp;when=today&how=paged"><?php echo _("today") ?></a></li>
<li><a href="view.php?what=starred&amp;how=paged"><?php echo _("starred") ?></a></li>
<li><a href="view.php?what=published&amp;how=paged"><?php echo _("published") ?></a></li>
<li><a href="dino.php"><?php echo _("dinosaurs") ?></a></li>
</ul>

<ul>
<li><a href="javascript:parent.items.toggle_expand_all()"><?php echo _("collapse all") ?></a></li>
<li><a href="javascript:parent.items.toggle_flags_all()"><?php echo _("toggle flags") ?></a></li>
<li><a href="update.php"><?php echo _("update all") ?></a></li>
</ul>

<ul>
<li><center><table><tr><td><form method="get" action="view.php?what=search&amp;how=paged"><input type="hidden" name="what" value="search"><input style=".input" type="reset" value="<?php echo _("Clear") ?>"></input><input style=".input" type="text" height="8" size="8" maxlength="40" name ="search" value=""><input style=".input" type="submit" value="<?php echo _("Find") ?>"></form></td><td><form><select name="tags" fontsize="8" onchange="javascript:parent.menu.location.href=\"feeds.php?tags=\"+this.value;"><?php 

$sql = "SELECT tags FROM `feeds` WHERE tags != '' group by tags";
$result = fof_do_query($sql);
print "<OPTION VALUE=\"" . _("All tags") . "\">" . _("All tags") . "\n";
print "<OPTION VALUE=\"" . _("No tags") . "\">" . _("No tags") . "\n";
while($row = mysql_fetch_array($result))
{
    $tagarray = array_unique(array_merge($tagarray, parse_tag_string($row['tags'])));
}
sort($tagarray);
foreach ($tagarray as $piece)
{
   print "<OPTION VALUE=\"$piece\">$piece\n";
}

?></select></form></td></tr></table></center></li>
</ul>

<ul>
<li><a href="javascript:parent.items.mark_read()"><?php echo _("mark as read") ?></a></li>
<li><a href="javascript:parent.items.mark_unread()"><?php echo _("mark as unread") ?></a></li>
<li><a href="javascript:parent.items.flag_all();parent.items.mark_read()"><b><?php echo _("mark all read") ?></b></a></li>
</ul>

<ul>
<li><a href="index.php" target="_top"><b><?php echo _("panel") ?></b></a></li>
<li><a href="add.php"><b><?php echo _("add feeds") ?></b></a></li>
<li><a href="javascript:parent.items.location.reload()"><b><?php echo _("refresh view") ?></b></a></li>
<li><a href="http://shokk.wordpress.com/tag/monkeychow/"><b><?php echo _("about") ?></b></a></li>
<li><a href="logout.php">log out</a></li>
</ul>

</div>
