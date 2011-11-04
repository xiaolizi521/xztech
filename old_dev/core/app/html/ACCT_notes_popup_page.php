<?php
require_once('ACCT_notes_popup_logic.php');
?>
<HTML>
<HEAD>
    <TITLE><?=$note_count ?> Notes for Account #<?=$account_number ?></TITLE>
    <LINK HREF="/css/core_ui.css" REL="stylesheet">
    <SCRIPT LANGUAGE="JavaScript" SRC="/script/popup.js" TYPE="text/javascript"></SCRIPT>
</HEAD>
<BODY BGCOLOR="white">
<DIV ALIGN="center">
<?=$notes ?>
</DIV>
</BODY>

</HTML>
