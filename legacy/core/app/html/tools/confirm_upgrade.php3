<? require_once("CORE_app.php"); 
require_once("computerStatus.php");
?>
<?	
	//Load up the customer profile and status
	$computer = new RackComputer;
	$computer->Init($customer_number,$computer_number,$db);
?>
<HTML id="mainbody">
<TITLE>Confirm Upgrade</TITLE>
<script language="JavaScript" src="/script/action_functions.js"></script>
<script language="JavaScript" src="/script/date-picker.js"></script>
<LINK HREF="/css/core2_basic.css" REL="stylesheet">
<LINK HREF="/css/core_ui.css" REL="stylesheet">

<body onload='testforupgradeactions(<?=$computer_number ?>,<?=$new_status ?>);'>

<?php
if( $computer->isNetworked() ) {
   $server_name = $computer->getData("server_name" );
} else {
   $server_name = "Not Needed";
}

// if this is a Pix and we can't determine its server's type,
// disallow upgrades to Contract Received
if( $new_status == 3 and
    $computer->isPix501() ) {

        $pix_error = false;

    $firewalled_server = $computer->getPixConnectedDevice();
    if ( !$firewalled_server ) {
        $pix_error = true;
    } else {
        $type = $firewalled_server->getType();
        if ( $type == 'UNKNOWN' ) {
            $pix_error = true;
        }
    }
    if ( $pix_error ) {
        ?>
        <TABLE BORDER="1"
               CELLSPACING="0"
               CELLPADDING="0">
        <TR>
            <TD>
                <TABLE BORDER="0"
                       CELLSPACING="2"
                       CELLPADDING="2">
                <TR>
                    <TD BGCOLOR="#003399"
                        CLASS="hd3rev"> ERROR: Cannot Upgrade to Received Contract </TD>
                </TR>
                <TR>
                    <TD><P>
                    This Pix device does not appear to be connected to a configured server.</P>
                    <P>
                    Please 
                    <a href='/tools/DAT_display_computer.php3?computer_number=<?=$computer_number ?>'>
                    <b>go back</b></a> and select <b>Computer-&gt; IP-DNS Info-&gt; Organize Firewall or Load-Balancer</b>         
                    and make sure this device is connected to a computer, and that the computer has  been configured.
                    </p>  </TD>
                </TR>
                
                </TABLE>
            </TD>
        </TR>
        </TABLE>
        </BODY>         
        </HTML>        
        <?php
        exit;
    }
}
// end Pix section

if( $new_status == 3
    and empty($server_name) ) {
?>
<TABLE BORDER="1"
       CELLSPACING="0"
       CELLPADDING="0">
<TR>
	<TD>
		<TABLE BORDER="0"
		       CELLSPACING="2"
		       CELLPADDING="2">
		<TR>
			<TD BGCOLOR="#003399"
			    CLASS="hd3rev"> ERROR: Cannot Upgrade to Received Contract </TD>
		</TR>
		<TR>
			<TD><P>
			You cannot mark a computer as contract received until it has a 
			server name.</P>
			<P>
			Please 
			<a href='/tools/DAT_display_computer.php3?computer_number=<?=$computer_number ?>'>
			<b>go back</b></a> and select <b>Action -&gt; Edit Computer Info</b>         
			and set the server name.
			</p>  </TD>
		</TR>
		
		</TABLE>
	</TD>
</TR>
</TABLE>
</BODY>         
</HTML>        
<?php
    exit;
}



if ($new_status == 12 and $computer->canHaveFailback() and !$computer->hasFailback())
{
?>
<TABLE BORDER="1"
       CELLSPACING="0"
       CELLPADDING="0">
<TR>
        <TD>
                <TABLE BORDER="0"
                       CELLSPACING="2"
                       CELLPADDING="2">
                <TR>
                        <TD BGCOLOR="#003399" CLASS="hd3rev"> ERROR: Cannot Upgrade to Online Complete </TD>
                </TR>
                <TR>
                        <TD><p>
                        The computer you wish to upgrade requires a Failover device.
                        </p>
                        <p>
                        Please
                        <a href='/tools/DAT_display_computer.php3?computer_number=<?=$computer_number ?>'>
                        <b>go back</b></a> and select <b>Action -&gt; Assign Failover Device</b>.
                        </p> </TD>
                </TR>
                </TABLE>
        </TD>
</TR>
</TABLE>
</BODY>
</HTML>
<?php
    exit;
}

$os = $computer->OS();
if( $new_status == 12 and
    ( empty($os) or $os == "Unknown" ) ) {
?>
<TABLE BORDER="1"
       CELLSPACING="0"
       CELLPADDING="0">
<TR>
	<TD>
		<TABLE BORDER="0"
		       CELLSPACING="2"
		       CELLPADDING="2">
		<TR>
			<TD BGCOLOR="#003399" CLASS="hd3rev"> ERROR: Cannot Upgrade to Online Complete </TD>
		</TR>
		<TR>
			<TD><p>
			The computer you wish to upgrade does not have an OS assigned to it.
			You cannot mark a computer as Online Complete until it has an OS assigned
			to it.
			</p>
			<p>
			Please 
			<a href='/tools/DAT_display_computer.php3?computer_number=<?=$computer_number ?>'>
			<b>go back</b></a> and select <b>Action -&gt; Edit Parts/Platform</b>         
			and select an Operating System.
			</p> </TD>
		</TR>
		</TABLE>
	</TD>
</TR>
</TABLE>
</BODY>         
</HTML>
<?php
    exit;
}

$PROVISIONING_HANDLER = "/py/computer/changeStatus.pt";

//special case!! when provisioning to received contract, it is possible for the page
//to be forwarded to the organize_firewall page. this page has a dynamic variable for
//its return page. don't know how to set php session variables via python. plus don't
//want to modify that page in anyway whatsoever. set this variable by default, everytime
//this section of code is executed
$firewall_return = htmlspecialchars("/tools/DAT_display_computer.php3?"
                    . "account_number=$customer_number"
                    . "&customer_number=$customer_number"
                    . "&computer_number=$computer_number");


?>

<FORM ACTION="<?=$PROVISIONING_HANDLER?>" METHOD="POST" id='upgrade_form' name="calform">
<?php
    if (isset($firewall_return) && $firewall_return)
    {
        print "<input type='hidden' name='firewall_return' value='$firewall_return'>";
    }
?>



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
			    CLASS="hd3rev"> Confirm Upgrade: Server #<?=$computer_number?> </TD>
		</TR>
		<TR>
			<TD BGCOLOR="#CCCCCC"
			    CLASS="label"> Current Status: </TD>
			<TD> <?print($computer->getData("status"));?> </TD>
		</TR>
<?
//Test to see if there are any higher levels
$max_status = $db->GetVal("
    SELECT status_rank 
    FROM status_options 
    ORDER BY status_rank DESC
    ");

$min_status = $db->GetVal("
    SELECT status_rank 
    FROM status_options 
    ORDER BY status_rank ASC
    ");

$current_status = $computer->getData("status_rank");

if ($new_status=="") {
    $info = $computer->GetNextStatusRank();
    if (count($info) < 1) {
        $next_status = $info["status"];
    }
    else {
        DisplayError("Unknown upgrade status");
    }
} else {
    $next_status = $db->GetVal("
        SELECT status 
        FROM status_options 
        WHERE status_rank = $new_status
        ");
}

$migration_form = "";
if( $new_status == STATUS_MIGRATION_SERVER ) {
    $migration_form = '
<tr>
  <td>New Server#<BR><FONT COLOR=#FF0000><i>Required</i></FONT></td>
  <td><INPUT type="text" name="migr_new_server"></td>
</tr>
<tr>
  <td>Free Migration Period (Days)<BR><FONT COLOR=#FF0000><i>Required</i></FONT></td>
  <td><INPUT type="text" name="migr_days"></td>
</tr>
<tr>
  <td>Old MRR<BR><FONT COLOR=#FF0000><i>Required</i></FONT></td>
  <td><INPUT type="text" name="old_mrr"></td>
</tr>
<tr>
  <td>New MRR<BR><FONT COLOR=#FF0000><i>Required</i></FONT></td>
  <td><INPUT type="text" name="new_mrr"></td>
</tr>
<tr>
  <td>MRR Currency<BR><FONT COLOR=#FF0000><i>Required</i></FONT></td>
  <td><select name="mrr_currency"><option value="usd">US Dollar</option><option value="gbp">GB Pound</option><option value="euro">Euro</option><option value="hkd">HK Dollar</option></select></td>
</tr>



';
}
function get_waiting_on_parts_form() {
  return '
<tr>
  <td> Select Reason </td>
  <td> 
    <input type="radio" name="reason" value="Parts just ordered">Parts just ordered</input>
    <input type="radio" name="reason" value="Parts are on order">Parts are on order</input>
    <input type="radio" name="reason" value="Parts date of arrival (if known)">Parts date of arrival (if known)</input>
    <input type="text" name="datebox" size="10" value="" />
    <a href="javascript:show_calendar(\'calform.datebox\');" onmouseover="window.status=\'Date Picker\';return true;" onmouseout="window.status=\'\';return true;">
    <img style="vertical-align: bottom; margin-left: 2ex;" src="/images/show-calendar.gif" width="24" height="22" border="0" /></a>
  </td>
</tr>';
}
$waiting_on_parts_form = '';
if ( $new_status == STATUS_WAITING_ON_PARTS ) {
  $waiting_on_parts_form = get_waiting_on_parts_form();
}

$ip_assignment_stuff = "";
if( $new_status == STATUS_RECEIVED_CONTRACT and
    !$computer->isVirtual() ) {
    $ip_assignment_stuff = "<tr><td> 
<p style=\"background: #CCF; padding: 0.5ex; font-size: large;\">
Please check if this server is going behind a dedicated load balancer or firewall (excluding 501s and netscreens)
</p>
</td><td>
<INPUT type=\"checkbox\" name=\"dont_auto_assign_ip\" value=\"1\" checked>
</td></tr>
";
}

function status_change_requires_comment($from, $to ) {
  /* this code needs to be refactored and moved into a rules engine -this is very brittle
  * only reason i'm not attacking it here is that I don't want to add extra php code
  * i'd rather have this become a service which returns all the required attributes for an upgrade or downgrade
  * these transition requirements can also be stored in the database and then the php code can access it readily
  * as it is, this code will need to be looked at again when another status is added */
  if ( $to == STATUS_RACKED_AND_READY || $from == STATUS_RACKED_AND_READY ) {
    return false;
  }
  if ( $to == STATUS_WAITING_ON_PARTS || $to == STATUS_SENT_TO_PRODUCTION || 
       $to == STATUS_WAITING_ON_CONFIGURATION_IMPLEMENTATION || $to == STATUS_WAITING_ON_CABINET_PROVISIONING ||
       $to == STATUS_WAITING_ON_IPS || $to == STATUS_WAITING_ON_MIGRATION || $to == STATUS_WAITING_ON_OTHER ||
       $to == STATUS_FINAL_CONFIGURATION_RACKED) {
    return false;
  }
  if ( $to > 12 ||
        $from == STATUS_COMPROMISED_SYSTEM_LEVEL ||
        $from == STATUS_COMPROMISED_APP_LEVEL ) {
    return true;
  }

}

if ( status_change_requires_comment($current_status, $new_status)  ){
    $num_rows = 30;
    $num_cols = 60;
    if( $new_status ==  STATUS_COMPROMISED_SYSTEM_LEVEL ) {
          $body = $computer->getCompromisedServerSystemLevelGoingInText();
    }
    elseif ( $new_status ==  STATUS_COMPROMISED_APP_LEVEL ) {
          $body = $computer->getCompromisedServerAppLevelGoingInText();
    }
    elseif (!($new_status == STATUS_WAIT_SUSPENDED_AUP) && 
            !($new_status == STATUS_SUSPENDED_AUP) &&
            needsRekick( $computer ) ) {
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
	    <TR>
	    	<TD>Comments:
	        <BR><FONT COLOR=#FF0000><i>Required</i></FONT></TD>
	    	<TD><TEXTAREA COLS="<?=$num_cols?>"
	    	              ROWS="<?=$num_rows?>"
	    	              NAME="reason"><?=$body ?></TEXTAREA></TD>
	    </TR>
<? } if ( !empty( $body ) ) { ?>
 
	    <TR>
	    	<TD>Existing Ticket # (Optional):</td>
	    	<TD><input type="text" name="ticket_num" value="" size="15"></input></TD>
	    </TR>
<? } ?>
<INPUT TYPE="hidden"
       NAME="command"
       VALUE="UPGRADE_STATUS">
<INPUT TYPE="hidden"
       NAME="customer_number"
       VALUE="<?print($customer_number);?>">
<INPUT TYPE="hidden"
       NAME="computer_number"
       VALUE="<?print($computer_number);?>">
<INPUT TYPE="hidden"
       NAME="new_status"
       VALUE="<?print($new_status);?>">
<INPUT TYPE="hidden"
       NAME="computer_redirect"
       VALUE="<?print($computer_redirect);?>">
        <?=$migration_form?>
        <?=$waiting_on_parts_form ?>
		<TR>
			<TD BGCOLOR="#CCCCCC"
			    CLASS="label"> Upgrade Status To: </TD>
			<TD> <?=$next_status?> </TD>			
		</TR>		
		<?=$ip_assignment_stuff?>
		<TR>
			<TD COLSPAN="2">
			<INPUT TYPE="image"
			       SRC="/images/button_command_continue_off.jpg"
			       BORDER="0" ONCLICK="if (this.style) { this.style.display='none'; document.getElementById('workingtext').style.display='inline' } else { this.display='none'; document.getElementById('workingtext').display='inline'; }">
              <DIV ID="workingtext" STYLE="display: none; font-weight: bold; font-size: large;">Working...</DIV>
			</TD>
		</TR>
		</TABLE>
	</TD>
</TR>
</TABLE>
</FORM>
<div id = "upgrade_info"></div>


<? EndPage();?>
</body>
</HTML>
