<?php 
$file_title = "Bittorrent FAQ";
?>
<br />
<span class="VerdanaSize1Main"><b>Bleach7 &gt; Help Section &gt; FAQ Central</b><br />
<br />
</span><span class="VerdanaSize2Main"><b>FAQ Central</b><br />
</span><span class="VerdanaSize1Main"><i>&nbsp;- Your source to all your Bleach7 and related questions!</i><br />
<br />
</span><span class="VerdanaSize2Main"><b><span style="text-decoration:underline">BitTorrent FAQ</span></b><br />
</span><span class="VerdanaSize1Main">&nbsp;- Questions pertaining to BitTorrent are in here.<br />
<br />
[THIS IS MORE OF A TUTORIAL]<br />
<br />
<b>Q. Where can I get anime torrents?</b><br />
<br />
Because the series has been licensed by Viz back in March of 2006, it's a lot harder to find older releases of the series.  For episode 50 to the present, you can download from <a href="http://yhbt.mine.nu/">Dattebayo</a>.  However for previous releases, you can search <a href="http://www.tokyotosho.com/">Tokyotosho</a> for any releases.  Tokyotosho also gives links to torrents for the English dubbing of the series.  But your best bet for watching episodes is to check out <a href="http://www.youtube.com">YouTube</a>.  Many fans are willing to upload each episode onto YouTube.<br />
<br />
<b>Q. How do I use BitTorrent?</b><br />
<br />
Read Below!<br />
<br />
<b>Q. And I have other questions about Bleach-related BT!</b><br />
<br />
Read Below too! <br />
<br />
<br />
</span><span class="VerdanaSize2Main"><b>|| BitTorrent TUTORIAL ||</b><br />
</span><span class="VerdanaSize1Main"><b>created by <a href="?page=member/member&amp;id=Hinalover">Hinalover</a></b><br />
<br />
<b>Q. What is Bittorrent?</b><br />
<br />
Bittorrent is a peer-to-peer program that allows the distribution of files to a large number of people in a relatively short period of time.<br />
<br />
<b>Q. How does Bittorrent work?</b><br />
<br />
When someone creates a file for use by bittorrent, they create what is called a "torrent file," which usually has an extention ending with ".torrent." That file consists of information about the file or folder that is to be downloaded or uploaded. That information contains information such as how big the file is, the hash information of the file or folder, where the file is going to be uploaded to, and any comments that the creator of the torrent file wishes to have. The file by itself cannot do anything. As stated, the torrent file just has information on where the file is going to be uploaded to. What is needed is for the file to be uploaded onto a server to serve as a basis. This server is called a tracker. A tracker is basically a centralized area that distributes information to a computer stating other ip addresses that are uploading and downloading the file you wish to download.<br />
<br />
Take for example you wish to download a Bleach episode. You click on a link and download a small, generally a twelve-or-so kilobyte size torrent file. However Bleach episodes generally are 170 megabytes. Where did the 169,988 kilobytes go? Well, that torrent file is not the Bleach episode. It is actually a miniature tracking file that is used to help download the file. In conjunction with a Bittorrent program, which I will get to soon, you are able to download the file. Say for example you have already set up a Bittorrent program. When you download that torrent file your Bittorrent program will read where you downloaded that torrent file from. It will then send a message asking the tracker who else has the file you wish to download. The tracker will then respond by sending back a list of ip addresses that are either finished downloading the file and are uploading the file, or people who are currently downloading the file. The Bittorrent program will then send a request to all of those ip addresses asking what parts of the file or folder do each have. The reason for this, is when the original uploader makes the torrent file, he breaks up the file that is going to uploaded into smaller pieces, ranging anywere between two kilobytes to one megabyte and higher. When your program gets a list of what pieces each ip address has, it starts downloading random pieces at a time. So if your file is broken into 1000 pieces, it will download, for example, piece 400, then 675, and so on, until the file is done.<br />
<br />
<b>Q. Where can I download a Bittorrent program?</b><br />
<br />
There are several major Bittorrent programs available on the internet, all of which are free. The original Bittorrent program was designed by the originator of Bittorrent, Bram Cohen. You can download the file off of <a href="http://www.bittorrent.com/index.html">his website.</a><br />
<br />
If you are a fan of Java, there is a fully functional Bittorrent program made entirely in Java. This program is called Azureus. It has many functions for the advanced users as well as a well designed front-end for easy management of all of your torrent files. You can download the file at <a href="http://azureus.sourceforge.net/">http://azureus.sourceforge.net/</a>.<br />
<br />
If you are more of C++ fan, there is another fully functional Bittorrent program made entirely in C++ called BitComet. Like Azureus, this is designed with many functions for the advanced user, as well as a nice looking front end for management's sake. You can download the program at <a href="http://www.bitcomet.com/">http://www.bitcomet.com/</a>.<br />
<br />
For those who wish for just a program, but no real advanced functions, you can download the simple BitTornado. This doesn't have much of a user interface, but it does the job nicely. You can download the file from <a href="http://www.bittornado.com/">http://www.bittornado.com/</a>.<br />
<br />
<b>Q. I've downloaded Azureus, but I still cannot get the program to work?</b><br />
<br />
Like I stated before, Azureus is designed entirely in Java. Java is a programming language that is constantly upgrading and being streamlined. In this case, several functions that were introduced in previous releases, are being dropped for more universal functions. So you need to also download the <a href="http://java.sun.com/javase/downloads/index.jsp">latest Java release</a>. This download will also enhance any Java programs run on the internet, and is a lot more stable than Window's version of Java.<br />
<br />
<b>Q. Ok, I've got the program installed, but I keep getting a NAT error?</b><br />
<br />
A NAT error is caused when either a user's computer or a user's router blocks a port. Bittorrent runs off of several ports in order to run. You basically have to open up those ports. These ports include port 6969 and 6881 to 6889.  I will explain how to unlock them in Windows XP, service pack 2.<br />
<br />
To unlock ports in Windows XP, service pack 2, first go to your start button, than to Control Panel. Next go to Security Center. In your Security Center, under your "Manage Security Settings for," click on Windows Firewall. You will now be in your Windows Firewall screen. At the top you should see "General," "Exceptions," and "Advance" tabs. Click on the "Exceptions" tab. Near the bottom, you should see a few buttons. Click on the button that says "Add Port..." The program will bring up a "Add a Port" screen. Next input a name for the port that you want to open. For example, for port 6969 you can say "Port 6969" or something along those lines. It's up to you what you want to remind yourself if you want to change. Anyway, in the next field type in the port you want to add. So to keep with the example, you would type "6969." If the radio button is on "TCP" keep it there else, please click the TCP radio button. Once you have all of that done, just click the "OK" button. When all of the ports have been added click the "OK" button on the "Windows Firewall" Screen and you are done. You may have to reset your computer though.<br />
<br />
<b>Q. What about for routers and Windows 2000, ME, and XP Service Pack 1 users?</b><br />
<br />
<b>Thanks to <a href="?page=member/member&amp;id=Kami5909">Kami5909</a> for this information.</b><br />
To set your IP address within the network, open up your network connections, right click your connection, and click on properties. Select "Internet Protocol (TCP/IP)" and click on properties. This should open up a screen that says "Obtain an IP address automatically" and "Use the following IP address." Select the second one.<br />
<br />
Now, define an IP address. If you're lazy and don't really care, I'll do it for you. Use 192.168.0.111. Type that into the first box ("IP address"). Click on the second box ("Subnet Mask") and enter 255.255.255.0. In the third box ("Default Gateway"), enter your router address (192.168.0.1 for almost all of you). Under that box, there should be two more boxes, "Preferred DNS server" and "Alternate DNS server." We're only concerned with the top one, so put 192.168.0.1 into that box, too.<br />
<br />
You now have a static IP address.<br />
<br />
The next step is to route the ports through your router to your computer. Open up firefox or Internet Explorer (or whatever browser you use, and go to the address bar. Remember your router's address? 192.168.0.1? Yeah, that's not just a bunch of random numbers, it's actually a place that you can visit. So type it into the address bar and go there. It will prompt you for a password. This varies depending on your router, but if you're reading this guide and nobody else in your house has touched it, it's likely to be something like admin/admin or admin/1234. ometimes it won't even have a password. If you need to, go to your router manual to find out what it is.<br />
<br />
Now, find something that says "port forwarding" or something. It differs from router to router, but it'll be something like that. This is where it gets a little tricky. You have to specify the start and end ports (start:6881 and end:6889 are the ports most commonly used for bittorrent, so go with those for now. You can change them later if you want to, or if your internet provider is blocking those ports. This requires an advanced knowledge of bittorrent, though, so I wouldn't recommend it.) We're not done yet, though, so don't click anything.<br />
<br />
It's going to ask you where you want them to go. This is usually in a box that says "0.0.0.0" on the same line as the one you typed your ports into, so put in the address you specified earlier (Remember? The one we made your computer? 192.168.0.111 if you followed my advice.) Click okay.<br />
<br />
That's it. You have successfully routed past your router. If you still have other crap in the way (firewalls, ANOTHER ROUTER, etc.), I can't really help you, because that's too detailed to explain, but this should help most of you who complain about not getting more than x kbps on a fully seeded torrent with a connection that you KNOW can go faster. If you mess around with this a bit more, you'll figure out more stuff (like how to make your other peer to peer programs go faster =P), but if you don't feel like it, don't, because it sucks if you mess up.<br />
<br />
That's it! You're done! Go test a torrent!<br />
<br />
<b>Q. I'm confused. What do the terms "seeding" and "leeching" mean?</b><br />
<br />
These are terms that basically mean whether a user is uploading or downloading the specified file. Seeding means that you have completely downloaded the file and you are uploading the file for everyone's use. Leeching means that you have not finished downloading the file, but you are in the process of downloading or finish downloading the file. It is a general understanding that you allow the file to be uploaded until you have uploaded as much as how big the file is. For example, if the file is 170 megs, then you should at least upload 170 megs worth. This is generally wavered for people who are still running off of a phone line.<br />
<br />
<b>Q. Where can I find torrent web sites?</b><br />
<br />
There are many torrent web sites out there for many different things. The most common usage is for the transferring of tv and movie files. For anime, the most common place is <a href="http://www.animesuki.com">Animesuki</a>. For manga, you can find some at <a href="http://www.manganews.com">Manganews</a>. Another decent site is <a href="http://www.baka-updates.com/">Baka-Updates</a>, as well as their <a href="http://www.mangaupdates.com/">Manga-Updates counterpart.</a>  For RAWs, two of the most popular releasers is <a href="http://bt.saiyaman.info/">Saiyaman</a> and <a href="http://www.l33t-raws.org/">L33-RAWS.</a>  There are many torrent sites out there, so if you are looking for Hentai, Yaoi, Movies, American tv shows, etc. you will just have to search the internet.<br />
<br />
<b>Q. I've downloaded the file, but I am getting audio, but no video?</b><br />
<br />
Ok, this is what is called a codec problem. To compress the file into the smallest amount of space, the file is run through a codec. Each Codec is different and there are several out there. Luckly there are several compilation program you can download to get several of the major ones out there. If you have already have any codec programs installed, please uninstall them before installing proceding any further.  One such example is <a href="http://www.free-codecs.com/download/Codec_Pack_All_in_1.htm">"Codec Pack All in One"</a>. Another you can use is <a href="http://www.cole2k.net/download_mirror.asp?file=CP-S">"Cole2k Media Pack."</a>  For MKV files, you can download <a href="http://www.free-codecs.com/download/Lazy_Man_MKV.htm">"Lazy Man's MKV Pack"</a>. Finally for those who wish to download the Quicktime only H.264 codec, Quicktime has a version for Apple as well as for Windows. You can download the file from <a href="http://www.apple.com/quicktime/">Apple's Quicktime webpage</a>.<br />
<br />
Animesuki has a list of very good codecs one could use.  These codecs do work, I've tried them myself, and I've only had one problem since installing them.  <a href="http://animesuki.rut.org/doc.php/help/playback.html">Click here to view Animesuki's Playback Page.</a>  Or if you do not wish to view the page, I'll just give you the pertinent information.  All you have to do, is download three files; the rest of the files, you don't really need, unless you want to install them:<br />
<br />
<a href="http://haali.cs.msu.ru/mkv/MatroskaSplitter.exe">Haali's Matroska Splitter</a><br />
<a href="http://www.free-codecs.com/FFDShow_download.htm">ffdshow</a><br />
<a href="http://www.lythka.com/playback/ffdshow_configured_settings.zip">ffdshow's Settings Registry File</a><br />
<br />
Now for the fun part of installing these three files.<br />
<i>1) Haali's Matroska Splitter</i><br />
Start the .exe and go through the installation. Disable the splitter's AVI support during installation. Make the settings according to the screenshot below:<br />
<img src="http://www.rpguru.com/downloads/HMSscreen.jpg" alt="" /><br />
<br />
<i>2) ffdshow</i><br />
Start the .exe and go through the installation. If you are not prepared to use our ffdshow's Settings Registry File described in the next step, it is recommended that you turn off Vorbis audio decoding and Postprocessing during ffdshow's installation.<br />
<br />
<i>3) ffdshow's Settings Registry File</i><br />
The .reg file is the one with all the ffdshow's settings. Open it. It will ask you if you want to add the information in the .reg file to the registry. You confirm it. With this, all of our recommended settings have been loaded to ffdshow.<br />
<br />
<b>Q. Now that I've installed the codecs, I'm still having troubles playing the file in [insert video player here]?</b><br />
<br />
If you have installed your codecs correctly, you shouldn't have a problem with your video player.  However if you want a dedicated video player, that can handle any codec, I can recommend two video players.  The first is <a href="http://www.divx-digest.com/software/media_player_classic.html">Media Player Classic.</a> This program has the look of the classic Windows Media Player, but can handle any codec out there.  A lot more stable then the current Windows Media Player.  The only change you have to make to the options is the picture below:<br />
<a href="http://www.rpguru.com/downloads/MPCscreen.jpg"><img src="http://www.rpguru.com/downloads/MPCscreen.jpg" alt="" style="width:483px; height: 330px;"/></a><br />
<br />
The other player is <a href="http://www.videolan.org/vlc/">VLC Media Player.</a>  This is a cross-platform player that has a unique ability.  If a video file seems to be corrupted, while in transfer, this player is able to play the video no matter what.  Real handy if you have a problem with Bittorrent at the time.<br />
<br />
<b>Q. I heard something about a Supreme Court ruling?</b><br />
<br />
Yes, that was Grokster vs. the Entertainment Industry. Grokster is another peer-to-peer program that suposedly allowed for the transfer of illegal properties. In the ruling, the Supreme Court ruled that Grokster purposely tried to promote illegal activity. However, because of the ruling, some are wondering if Bittorrent will follow the same fate. However, the creator of Bittorrent, Bram Cohen, has been outspoken that he did not purposely create Bittorrent to promote illegal activity. This can be true since companies can legally transfer large files to multiple places within a relatively quick timeframe.  Also a few US Anime Distributors are starting to using bittorrent.  One such example is <a href="http://www.advfilms.com">ADV Films</a>.  They are using the program to help promote current and upcoming properties that they own.</span>
