<?
require_once("CORE_app.php");
?>
<HTML>
<HEAD>
<LINK REL="stylesheet" TYPE="text/css" HREF="/css/core2_basic.css">

<? if (isset($reload_parent) and $reload_parent) { ?>
<!-- Refresh calling view -->
<SCRIPT LANGUAGE="JavaScript">
window.opener.location = window.opener.location;
</SCRIPT>
<? } 
else if (isset($cancel) and $cancel) { ?>
<SCRIPT LANGUAGE="JavaScript">
window.close();
</SCRIPT>
<? } ?>
</HEAD>
<BODY>

<TABLE class="blueman">
<TR><TH class="blueman">Remove Computer # <?=$computer_number ?> from ComplexManaged?</TH></TR>
<TR><TD class="blueman">
<P>

<?
if ( isset($confirm) and $confirm ) {
    if (isset($computer_number) and isset($vid)) {
        $GLOBAL_db->BeginTransaction();
        $res = $GLOBAL_db->SubmitQuery("delete from xref_computer_complexmanaged where computer_number=$computer_number and vlan_id=$vid");
        $GLOBAL_db->CommitTransaction();
        print "Successfully removed computer from complex vlan!";
    } else {
        print "Error! Computer# not passed in!";
    }
} else {
    print "<BR>";
    print "<P style='text-align:right'>";
    print "<A HREF=/tools/popupRemoveComplexNet.php?computer_number=$computer_number&vid=$vid&reload_parent=1&confirm=1 class='text_button'>Confirm</A>";
    print "<A HREF=/tools/popupRemoveComplexNet.php?cancel=1 class='text_button'>Cancel</A>";
    print "</P>";
}
?>

</P>
</TD></TR>
</TABLE>
</BODY>
</HTML>
