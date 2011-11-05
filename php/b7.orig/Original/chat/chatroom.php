<?php
$file_title = "Chatroom";
$nick = $_GET['nick'];
$chan = $_GET['chan'];
if ( !empty( $nick ) ) {
	echo '
<center><h1>Bleach7.com\'s Chatroom</h1><hr>
<applet code="IRCApplet.class" archive="irc.jar,pixx.jar" width="640" height="480">
<param name="CABINETS" value="irc.cab,securedirc.cab,pixx.cab">

<param name="nick" value="'.$nick.'">
<param name="alternatenick" value="BleachFan???">
<param name="name" value="B7">
<param name="host" value="irc.irchighway.net">
<param name="gui" value="pixx">

<param name="quitmessage" value="BLEACH IS TEH SMEXY! <3">
<param name="asl" value="true">
<param name="useinfo" value="true">
<param name="command1" value="join #'.$chan.'">

<param name="style:bitmapsmileys" value="true">
<param name="style:smiley1" value=":) img/sourire.gif">
<param name="style:smiley2" value=":-) img/sourire.gif">
<param name="style:smiley3" value=":-D img/content.gif">
<param name="style:smiley4" value=":d img/content.gif">
<param name="style:smiley5" value=":-O img/OH-2.gif">
<param name="style:smiley6" value=":o img/OH-1.gif">
<param name="style:smiley7" value=":-P img/langue.gif">
<param name="style:smiley8" value=":p img/langue.gif">
<param name="style:smiley9" value=";-) img/clin-oeuil.gif">
<param name="style:smiley10" value=";) img/clin-oeuil.gif">
<param name="style:smiley11" value=":-( img/triste.gif">
<param name="style:smiley12" value=":( img/triste.gif">
<param name="style:smiley13" value=":-| img/OH-3.gif">
<param name="style:smiley14" value=":| img/OH-3.gif">
<param name="style:smiley15" value=":\'( img/pleure.gif">
<param name="style:smiley16" value=":$ img/rouge.gif">
<param name="style:smiley17" value=":-$ img/rouge.gif">
<param name="style:smiley18" value="(H) img/cool.gif">
<param name="style:smiley19" value="(h) img/cool.gif">
<param name="style:smiley20" value=":-@ img/enerve1.gif">
<param name="style:smiley21" value=":@ img/enerve2.gif">
<param name="style:smiley22" value=":-S img/roll-eyes.gif">
<param name="style:smiley23" value=":s img/roll-eyes.gif">
<param name="style:backgroundimage" value="false">
<param name="style:sourcefontrule1" value="all all Serif 12">
<param name="style:floatingasl" value="true">

<param name="pixx:timestamp" value="true">
<param name="pixx:highlight" value="true">
<param name="pixx:highlightnick" value="true">
<param name="pixx:nickfield" value="true">
<param name="pixx:styleselector" value="true">
<param name="pixx:setfontonstyle" value="true">

</applet>
<hr></center>';
}
else {
$file_title = "Chatroom";
echo '
<table width=100%" height="60%" valign="center" align="center">
<tr><td align="center">
<font face="Verdana" size="4"><b>Bleach7 Chatroom</b></font><br /><br />
<font face="Verdana" size="2">#Bleach7 @ irc.irchighway.net  
</font>  
<br /><br />
<form method="get" action="chatroom.php">
<font face="Verdana" size="2" color="red"><b>You must choose a nickname!</b></font><br /><p></p>
<font face="Verdana" size="2">Username:  
</font>  

<input type="text" name="nick"></p>
<font face="Verdana" size="2">Channel:  
</font>
<select name="chan">
			<option value="bleach7">Bleach7</option>
			<option value="maximumt">Maximum7</option>
			<option value="max7trainee">Max7Trainee</option>
		</select>

<p><input type="submit" value="Socialize!"></p>
</form>
</td></tr></table>
';
}


?>

