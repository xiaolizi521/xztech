<?php 
$file_title = "IRC FAQ";
?>
<br />
<span class="VerdanaSize1Main"><b>Bleach7 &gt; Help Section &gt; FAQ Central</b><br />
<br />
</span><span class="VerdanaSize2Main"><b>FAQ Central</b></span><span class="VerdanaSize1Main"><br />
<i>&nbsp;- Your source to all your Bleach7 and related questions!</i><br />
<br />
</span><span class="VerdanaSize2Main"><b>|| IRC TUTORIAL ||</b></span><span class="VerdanaSize1Main"><br />
<b>written by <a href="?page=member/member&amp;id=Hinalover">Hinalover</a></b><br />
<br />
<b>Q. What is IRC?</b><br />
<br />
IRC, which stands for Internet Relay Chat is an online chat forum. Think of it as a multitude of Instant Messages that are accessable to anyone.<br />
<br />
<b>Q. What do I need to access this IRC?</b><br />
<br />
All you need is a program that is designed to work with IRC.  There are many programs out there, some more advanced and complicated than the other.  Programs such as <a href="http://www.mirc.com/">mIRC</a> and <a href="http://www.sysreset.com/">SysReset</a> are designed for advanced users who wish to supply files to other people.  For the purpose of this FAQ, I'm going to be working with a very simple IRC program that is designed more for beginners called <a href="http://www.hydrairc.com/">HydraIRC</a>.<br />
<br />
<b>Q. What is HydraIRC?</b><br />
<br />
HydraIRC is a very simple IRC program that has a very clean looking and is easy to customize.  The program is coded in C++ (a very reliable programming language), and has many useful features that beginner and advance users can use.  Just as a side-note, the creator of this program took ideas from 30 different IRC programs, and combined them into this program.<br />
<br />
<b>Q. Ok, now how do I get started?</b><br />
<br />
First of all you need to install the program.  To download the file, <a href="http://www.hydrairc.com/download.php?directory=files&amp;file=HydraIRC.exe">just click this link.</a>  From there just follow the installation instructions.<br />
<br />
<b>Q. I installed HydraIRC, what now?</b><br />
<br />
Well, there are a few things you need to understand before you start charging the gates.  First of all you need to understand how IRC works.  With IRC, you must know where your going before you can do anything.  That includes knowing what server and channel you are heading to.  For the purpose of this FAQ, I'm going to be working with Bleach7's and Manga7 (aka Maximum7)'s channels.<br />
<br />
<b>Q. Servers? Channels? You've lost me.</b><br />
<br />
The way IRC works, is it connects to servers that are dedicated to IRC's usage.  Channels are sub-sections of the servers.  To put it in perspective, think of the server as a drive on your computer, and a channel is a folder within that drive.  Some channels require special permission in order to access them.  These are usually set up by the people who operate these channel.  Most channels are open to the public, however distribution groups, like Manga7, generally have additional private channels so that they can work without having release information leaked.<br />
<br />
<b>Q. How do I access Bleach7's channel?</b><br />
<br />
Patience young padawan.  All will be revealed in due time.  When you first boot up the program, it will ask you to set up an identity for yourself.  Click yes, to bring up the username configuration screen. Here, click the new button, to bring up a new identity.  Then enter in the information for Description and Real Name (both optional).  As for Username, this is going to be the grouping name.  I left mine as my Screenname, but it doesn't matter.  The nick is the Display name('s) you want to be known as.  If a user is already using the nick you want, say "Kon", it will call back saying that a new nick is needed.  So instead, it will send out a new nick, like Kon[1], and increment until it gets a good nick.  The last two are also optional, but I filled mine anyway.<br />
<br />
Next thing we need to do is register your username to the server.  Think of this as registering yourself to a forum on the internet.  This basically reserves your nick so no one else can use it. First, we need to connect to the server. Click the far left button on the button bar (looks like a power cable) to open up a new server. In the URL text box, type in "IRC://irc.irchighway.net:6664".  Why irc.irchighway.net? Well that is the server the Bleach7 channel is located.  Most anime and manga groups are in either irc.irchighway.net or irc.rizon.net servers.  I will be getting to Rizon later.  The 6664 is a port number that the program uses to connect.  HydraIRC tends to default to 6667, which does work, but not as well as port 6664.  So whenever you connect to a server in Hydra make sure it's connecting to port 6664.  Once you connect, a lot of text will scroll down your screen but you don't need to worry about that.  However a channel box will pop up if this is your first time connecting.  All it's asking is to search for a channel to connect to.  Since we already know where we are heading, just cancel out.<br />
<br />
Now to register.  At the bottom of the window is place to input text. In there, type "/nickserv register &lt;password&gt; &lt;email address&gt;"  The &lt;password&gt; is the password you wish to use to lock that nick to you.  The email address is where your password will be sent in case you forget it. So if my password is abc, I would type "/nickserv register abc hinalover@abc.com"  Now here is the catch: you have to identify yourself every time you connect to that server.  To do so, all you have to do is type "/msg nickserv identify &lt;password&gt;".  "msg" is short of Message.  All you are doing is sending a quick message to the nick server saying you are who you say you are.<br />
<br />
<b>Q. I have to identify every time log in?  Why bother registering then?</b><br />
<br />
Actually, register has it's perks.  If a user who is of higher rank on a channel is impressed with you, they can give you a promotion, but only if you are registered.  Also, if you want to start serving files it's imperative that you register your nick.  But with Hydra, there is a quick way around this.  If you go to the top of the screen, and press "Option", then "Pref..", and go down to "Command Profiles" you can set up automatic scripts that will run when the program opens up or when a server is logged onto.  To set it up so that you will automatically log onto irc.irchway.net, click the "OnStartUp" label, then click inside where it says "&lt;&lt;Enter commands here!&gt;&gt;".  Delete that message and type "/server irc.irchighway.net:6664".  This will automatically log you onto the irchighway server every time you open up the program. However if you want to set it up so that the program automatically registers you when you log onto the irc.irchighway.net server, you need to set up a new command profile. At the top of this screen, in the text bar, type in "irc.irchighway.net_OnLoggedIn".  The "OnLoggedIn" part is a keyword in the program to run this script whenever you log onto irchighway's server.  Then click the add button.  The profile will be added to the list of profiles.  click the "OnLoggedIn" profile and click inside the text box to the right.  Next just type "/msg nickserv identify &lt;password&gt;", except with your password.<br />
<br />
<b>Q. Now can I join a channel?</b><br />
<br />
Yes.  In order to join a channel, all you have to do is type "/j #&lt;channel name&gt;".  The "j" stands for join.  So in Bleach7's case, just type "/j #bleach7".  You can also automatically log onto a channel whenever you log onto a server.  Go back to your Command Profile option in the preference page, and click the "irc.irchighway.net_OnLoggedIn" profile.  Under your identify line, type "/j #bleach7".  Now everytime you log onto Irchighway's server you will automatically log onto Bleach7's channel.  You can do this for any channel you wish.<br />
<br />
<b>Q. What do I do in order to log into another server, such as irc.rizon.net? </b><br />
<br />
 In order to log onto said irc.rizon.net (Rizon for short), just type in any window &quot;/newserver irc.rizon.net:6664&quot;. If you wish to add Rizon to your start-up servers, just follow the instructions in the &quot;Why bother&quot; question above. Just change where ever it says &quot;irc.irchighway.net&quot; to &quot;irc.rizon.net&quot;. Everything else can stay the same.<br />
 <br />
<b>Q. I see Admin, Ops, Half-Ops, and Voice. What do they mean?</b><br />
<br />
These are something like the hierarchy on IRC. Each channel has their own rules on who is able to get what rank. The major difference between each rank, is the ability to kick and/or ban users of lower rank. However the upper ranks, such as Operator and Administrator, can change the setting of the channel, as well as the channel topic (It's that bar at the top of the channel display screen).<br />
<br />
<b>Q. Ok, how do I download files using IRC?</b><br />
<br />
Ah, the big reason why people want to use IRC. The most common way to find files, is to search manually for them. Say for example Manga7 just released the new Bleach chapter. You first need to connect to their channel. In any window connected to IRCHighways' server, type &quot;/j #maximumt&quot;. This will connect to Manga7's IRC channel. Next you need to search for the file. To start things off type &quot;!list&quot;. This will list all of the users who have set up their computers to serve files. For the purpose of this FAQ, I will not go into how to set up a file server, since this service is for more advanced users. Anyway, as stated you will get a list of users and a lot of information on each. Each user has set up one of three different types of request systems. <br />
<br />
The first one to look for is &quot;XDCC&quot; These are generally bots, and not actual users, and run on very fast connections. One thing to note before we move on, if you want to copy something in IRC, just select the text you want, then let go, and it will be copied. Now, look in the information for the words &quot;LIST&quot; or &quot;PACKLIST&quot;. It will say something like &quot;/msg &lt;bot name&gt; xdcc list&quot;. This will send to you only a list of all the files on that system. A lot of times an XDCC will have a large amount of files, so the owner of that bot will set up a &quot;pack list&quot;. These lists are generally accessed through a website, and not through IRC itself. Once you find the file you want, it will provide a message you must send to get the file, such as  &quot;/msg &lt;botname&gt; xdcc send #&lt;filenumber&gt;&quot;. The filenumber corresponds to the file you wish to get. So for example, if you want to get Bleach 256, and it's number is &quot;#461&quot;, you would type &quot;/msg &lt;botname&gt; xdcc send #461&quot;. <br />
<br />
If you are unable to locate an XDCC system, or the XDCC system doesn't have the file, the next system to look for is &quot;TDCC&quot;. These systems are designed for one file transfer, meaning that they are set up to send only one file. So say the latest Bleach chapter is 270. the TDCC would only have chapter 270 for download. To acquire the file, you have to search for the system's &quot;TRIGGER&quot;. The trigger is similar to XDCC's send message.<br />
<br />
The final type of system, and the most common, is a Fserve.  These are systems that normal users set up to distribute their files. The owners of these Fserves, don't necessarily have the same type of files that are being distributed on that channel. For example, say you are on Maximumt's channel (who work on Bleach, Trigun, Gintama, Hellsing, Mieru Hito, Kingdom Hearts 2, and Mx0), but you may find people offering Tenjou Tenge or Naruto. Another thing to note is that not all of these will have great download speeds. To check out what the user has, again, look for the &quot;TRIGGER&quot;. When you send the Trigger, you will open up what looks like a chat screen. Most users will have these set to automatically accept your connection and you can go about your business. However there are some people who want to monitor who is connecting to their computer, so they have set it up so that they have to authorize the connection before the person can download. Once you are connected, the layout will look similar for those of you who have worked with DOS or LINUX/UNIX before. For those who have not worked with either, there are only four commands you have to worry about. Those four are &quot;DIR&quot;, &quot;LS&quot;, &quot;CD&quot;, and &quot;GET&quot;. DIR and LS list the files or folder in the current folder you are in. The only difference is that DIR list each item on a separate line, while LS list everything on one line. If you need to enter a folder, just type &quot;CD &lt;folder name&gt;&quot;. If you want to move out of a folder, just type &quot;CD..&quot; Once you found the file you wish to download, type &quot;GET &lt;file name&gt;&quot;. Some systems have set it up so that you just have to type a code word. These systems will have a number and the letter &quot;D&quot; to enter a folder, while files are numbers with the letter &quot;F&quot;. With these systems you just type the number and letter to either go into the folder, or get the file you wish to get. Also of note, many systems have a time limit to how long you can be connected, so using copy paste helps a lot in this screen.<br />
<br />
If you wish to download mulitple files from the same user, the user will generally have a &quot;QUEUE&quot; or line that places your request in. Once your number comes up, the system will send the file placed in that &quot;QUEUE&quot; to you. Some systems have a 99 position &quot;QUEUE&quot;, while others have only 1 file &quot;QUEUE&quot;. It all depends on how the user configured the system. Also, with larger &quot;QUEUE&quot;s, the person also generally sets a limit to how many you can put in the &quot;QUEUE&quot;. Generally this is set to five files. Any more, and you have to wait for your first file to finish before adding another. <br />
<br />
<b>Q. Are there any rules I have to follow?</b><br />
<br />
Well, considering all channels have their own rules, you just have to ask. Most channels have set up a rules script; generally the trigger is &quot;!rules&quot;. Most rules are also given in the topic. This function will generally get you banned for a bit:<br />
<br />
@find = This will search for the file you are looking for in that channel. This function is generally reserved for higher privileged users of that channel.<br />
<br />
Other than that, you just have to ask in the channel. <br />
<br />
<b>Q. I'm competant at IRC. What are the barebones I need to download manga?</b></span><ol class="VerdanaSize1Main">
 	<li>"/server irc.irchighway.net"</li>
	<li>"/j #Maximumt"</li>
	<li>"!bl" (Trigger on #MaximumT that will send packlist # for latest Bleach chapter)</li>
</ol><br /><br />
<span class="VerdanaSize1Main">Any of these not answer your questions? <a href="mailto:webmaster@bleach7.com"><b>EMAIL US</b></a>!</span>