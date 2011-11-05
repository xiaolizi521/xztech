<?

require_once("CORE_app.php");

$GLOBAL_db->BeginTransaction();

if (!(isset($confirm) and $confirm)) {
    $confirm = 0;
}
if (!(isset($reload_parent) and $reload_parent)) {
    $reload_parent= 0;
}

?>

<HTML><HEAD>

<LINK REL="stylesheet" TYPE="text/css" HREF="/css/core2_basic.css">

<? if ( $reload_parent and $confirm ) { ?>

<!-- Refresh calling view -->
<SCRIPT LANGUAGE="JavaScript">
<!--
window.opener.location = window.opener.location;
//-->
</SCRIPT>

<? } ?>

</HEAD>
<BODY>

<TABLE class="blueman">
<TR><TH class="blueman">Reset Passwords #<?=$computer_number ?></TH></TR>
<TR><TD class="blueman">
<P>

<?
if ( $confirm ) {
    if (isset($computer_number) and isset($account_number)) {
        $computer = new RackComputer;
        $computer->Init($account_number,$computer_number,&$GLOBAL_db);
        $computer->_assignPasswords();
        $GLOBAL_db->CommitTransaction();
        print "Successfully changed passwords!";
    } else {
        print "Error! Computer/Account # not passed in!";
    }
} else {
    print "Warning!  This will NOT reset the actual passwords on the server, only the passwords stored in CORE.  If you continue, you must follow through and update the passwords on the server.";
    print "<BR>";
    print "<P style='text-align:right'>";
    print "<A HREF=/tools/reset_passwords.php?computer_number=$computer_number&account_number=$account_number&reload_parent=$reload_parent&confirm=1 class='text_button'>Confirm</A>";
    print "</P>";
}

?>

</P>
</TD></TR>
</TABLE>

</BODY>
</HTML>
