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
<TR><TH class="blueman">Send Customer #<?=$account_number ?> To Psoft</TH></TR>
<TR><TD class="blueman">
<P>

<?
if ( $confirm ) {
    if (isset($account_number)) {
        $customer = new RackCustomer;
        $customer->Init($account_number,&$GLOBAL_db);
        $customer->sendToPsoft();
        $GLOBAL_db->CommitTransaction();
        print "Successfully marked Sent to Psoft!";
    } else {
        print "Error! Account # not passed in!";
    }
} else {
    print "Please confirm that you want to mark this customer as having been sent to Psoft for HSphere support";
    print "<BR>";
    print "<P style='text-align:right'>";
    print "<A HREF=/send_to_psoft.php?account_number=$account_number&reload_parent=$reload_parent&confirm=1 class='text_button'>Confirm</A>";
    print "</P>";
}

?>

</P>
</TD></TR>
</TABLE>

</BODY>
</HTML>
