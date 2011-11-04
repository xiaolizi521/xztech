<?

require_once("CORE_app.php");

$GLOBAL_db->BeginTransaction();

if (!(isset($result) and $result)) {
    $result = 0;
}

?>

<HTML><HEAD>

<LINK REL="stylesheet" TYPE="text/css" HREF="/css/core2_basic.css">

<? if ( $result ) { ?>

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
<TR><TH class="blueman">MySQL Network subscription for #<?=$computer_number ?></TH></TR>
<TR><TD class="blueman">
<P>

<?
if ( $result ) {
?>
    Order Submitted! <br><br>
    MySQL Order# is <? print $order_id ;?>
<?
    if (isset($computer_number) and isset($account_number)) {
        $computer = new RackComputer;
        $computer->Init($account_number,$computer_number,&$GLOBAL_db);
        $computer->addMySQLNetworkOrder($order_id);
        $GLOBAL_db->CommitTransaction();
        //print "Successfully changed passwords!";
    } else {
        print "Error! Computer/Account # not passed in!";
    }
} else {
 print "There was an error submitting the order information automatically to MySQL <br>";
 print "<!-- $error_field -->"; 
 print $error ; 
 
}
?>

</P>
</TD></TR>
</TABLE>

</BODY>
</HTML>
