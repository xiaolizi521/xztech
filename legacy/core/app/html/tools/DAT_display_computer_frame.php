<?php
require_once("CORE_app.php");
$tree_url = "$py_app_prefix/account/tree.pt?" .
"account_number=$account_number&".
"computer_number=$computer_number";

?>
<HTML>
<HEAD>
    <TITLE>
        CORE
    </TITLE>
	<FRAMESET COLS="160,*" FRAMEBORDER="1" FRAMESPACING="2">
		<FRAME  id="left" SRC="<?=$tree_url?>" NAME="left" ID="left" SCROLLING="Auto" MARGINWIDTH="0" MARGINHEIGHT="0" FRAMEBORDER=1></FRAME>
		<FRAME id="content_page" src="/tools/DAT_display_computer.php3?computer_number=<?=$computer_number ?>" name="content" FRAMEBORDER=1 SCROLLING="auto" MARGINWIDTH=0 MARGINHEIGHT=0></FRAME>
	</FRAMESET>
</HEAD>
<NOFRAMES>
<BODY>Frames Not Supported.</BODY>
</NOFRAMES>
</HTML>
