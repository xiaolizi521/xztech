<? require_once("CORE_app.php"); ?>
<?
//Load up the customer profile and status
$computer = new RackComputer;
$computer->Init($customer_number,$computer_number,$db);
$current_status = $computer->getData("status_rank");
$next_status = $db->GetVal("
SELECT status
FROM status_options
WHERE status_rank = $new_status
");

if ($new_status == 12 and $computer->canHaveFailback() and !$computer->hasFailback())
{
?>
<HTML>
<head>
<LINK HREF="/css/core2_basic.css" REL="stylesheet">
<LINK HREF="/css/core_ui.css" REL="stylesheet">
</head>
<body>
<TABLE BORDER="1"
       CELLSPACING="0"
       CELLPADDING="0">
<TR>
        <TD>
                <TABLE BORDER="0"
                       CELLSPACING="2"
                       CELLPADDING="2">
                <TR>
                        <TD BGCOLOR="#003399" CLASS="hd3rev"> ERROR: Cannot Downgrade to Online Complete </TD>
                </TR>
                <TR>
                        <TD><p>
                        The computer you wish to downgrade requires a Failover device.
                        </p>
                        <p>
                        Please
                        <a href='/tools/DAT_display_computer.php3?computer_number=<?=$computer_number ?>'>
                        <b>go back</b></a> and select <b>Action -&gt; Assign Failover Device</b>.
                        </p> </TD>
                </TR>
                </TABLE>
</td></tr></table></body></html>

<?php
 
   exit;
}





if ($current_status == STATUS_COMPROMISED_SYSTEM_LEVEL || $current_status == STATUS_COMPROMISED_APP_LEVEL ) {
    $num_rows = 30;
    $num_cols = 60;
    if( $new_status ==  STATUS_COMPROMISED_SYSTEM_LEVEL ) {
          $body = $computer->getCompromisedServerSystemLevelGoingInText();
    }
    elseif ( $new_status ==  STATUS_COMPROMISED_APP_LEVEL ) {
          $body = $computer->getCompromisedServerAppLevelGoingInText();
    }
    elseif ( $current_status ==  STATUS_COMPROMISED_SYSTEM_LEVEL ) {
          $body = $computer->getCompromisedServerSystemLevelComingOutText();
    }
    elseif ( $current_status ==  STATUS_COMPROMISED_APP_LEVEL ) {
          $body = $computer->getCompromisedServerAppLevelComingOutText();
    }   
    else {
        $body = '';
        $num_rows = 3;
        $num_cols = 40;
    } 
    ?>
<HTML id="mainbody">
<TITLE>Confirm Downgrade Status</TITLE>
    <LINK HREF="/css/core2_basic.css" REL="stylesheet">
    <LINK HREF="/css/core_ui.css" REL="stylesheet">
    <script language="JavaScript" src="/script/action_functions.js"></script>
    <body onload='testfordowngradeactions(<?=$computer_number ?>,<?=$new_status ?>);'>
<?php
	//don't use provisioning if downgrading to no longer active prov-304
    if( $new_status != -1 ) {
        $PROVISIONING_HANDLER = "/py/computer/changeStatus.pt";
    }
    else {
    	$PROVISIONING_HANDLER = "display_computer.php3";
    }
?>
    
<FORM ACTION="<?=$PROVISIONING_HANDLER?>" METHOD="POST">

<TABLE BORDER="1"
       CELLSPACING="0"
       CELLPADDING="0">
<TR>
        <TD>
                <TABLE BORDER="0"
                       CELLSPACING="2"
                       CELLPADDING="2">
                <TR>
                        <TD COLSPAN="2"
                            BGCOLOR="#003399"
                            CLASS="hd3rev"> Confirm Downgrade: Server #<?=$computer_number?> </TD>
                </TR>
   
            <TR>
                <TD>Comments:
                <BR><FONT COLOR=#FF0000><i>Required</i></FONT></TD>
                <TD><TEXTAREA COLS="<?=$num_cols?>"
                              ROWS="<?=$num_rows?>"
                              NAME="reason"><?=$body ?></TEXTAREA></TD>
            </TR>
<? if ( !empty( $body ) ) { ?>

            <TR>
                <TD>Existing Ticket # (Optional):</td>
                <TD><input type="text" name="ticket_num" value="" size="15"></input></TD>
            </TR>
<? } ?>
<INPUT TYPE="hidden"
       NAME="command"
       VALUE="DOWNGRADE_STATUS">
<INPUT TYPE="hidden"
       NAME="customer_number"
       VALUE="<?print($customer_number);?>">
<INPUT TYPE="hidden"
       NAME="computer_number"
       VALUE="<?print($computer_number);?>">
<INPUT TYPE="hidden"
       NAME="new_status"
       VALUE="<?print($new_status);?>">
                <TR>
                        <TD BGCOLOR="#CCCCCC"
                            CLASS="label"> Change Status To: </TD>
                        <TD> <?=$next_status?> </TD>
                </TR>
                <TR>
                        <TD COLSPAN="2">
                        <INPUT TYPE="image"
                               SRC="/images/button_command_continue_off.jpg"
                               BORDER="0">
                        </TD>
                </TR>
                </TABLE>
        </TD>
</TR>
</TABLE>
</FORM>
<div id = "downgrade_info"></div>
</body>
<? }
   else {
        $url =  "customer_number=$customer_number"
                . "&computer_number=$computer_number"
                . "&command=DOWNGRADE_STATUS"
                . "&new_status=$new_status";

	//don't use provisioning if downgrading to no longer active prov-304
	if($new_status != -1){
        ForceReload("/py/computer/changeStatus.pt?" . $url);
    }
    else{
   	    ForceReload("display_computer.php3?" . $url);
    }
	
        
   }

   EndPage();
?>

</HTML>
