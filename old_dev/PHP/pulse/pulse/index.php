<?
session_start();
include "config.php";
include "designtop.php";
include "functions/news.php";
?>
<h3>Sample signature image:</h3><br>
<img src='http://offbeat-zero.net/pulse/sig/RadarListener.png'><br><br>
<h3>What is WhatPulse?</h3>
Direct from <a href='http://www.whatpulse.org'>Whatpulse.org</a>
Sure we have a fancy website, loads of stats and lotsa users, but many people still wonder what WhatPulse exactly is. This page tries to explain a bit more about what it is, a simple keycounter.<br><br>

The purpose of WhatPulse is simply to collect statistics about your computer behavior. Some people (Like me) use it to determine how long they've worked on something, like a programming project, a school essay, chatting by all means.<br><br>

<h4>How does it work?</h4>

WhatPulse opens a so-called "keyhook" when it's booted, which listens to incoming Windows messages about the keyboard, the keyhook sends these messages to the program itself, and the program first determines if the key pressed is a proper key (Every standard keyboards' keys are counted) and then simply does PreviousKeyCount + 1.<br><br>

Pulsing: When you "pulse", you're actually sending your keycounts in the program to the server along with other information about your Account, so the server can decide where to put the keycount. If the pulse goes according to plan, the server sends back an "OK", and the client's keycount is reset to 0 (zero), and your previous keys are added to your profile in the server database! :)<br><br>

And for all you people who are thinking "oeh mi god a keylogger, no way!", well, exactly, No way. WhatPulse is not and never will be any sort of keylogger, it does not collect the keys you type, but only how many keys you type. (see our <a href='http://whatpulse.org/policy/'>privacy policy</a>)
<br><br>
<h3>WSI?</h3>
WSI stands for WhatPulse Signature Images and is just that; signature images generated for WhatPulse. The way the images are generated is when you send a pulse to the WhatPulse server it counts the number of keys and stores it away in a database. Then a big XML file is generated and we grab that directly from the WhatPulse server. Then we parse this 17MB+ (plain-text) XML file into the signature images like the one shown at the top of this box.<br><br>

We're open to suggestions on improving the system, and we always love it when people submit bug reports on the <a href='forums'>forums.</a><br><br>

On the menu bar on the left you may be wondering what the two letters mean next to the dates. In order they are:<br><br>
Last Update<br>
Time Now<br>
Next Update<br>
<?
include "menu.php";
echo "</div>";
include "designbottom.php";

?>