<?php 

require_once("CORE_app.php");

if( empty($customer_number) and !empty($computer_number) ) {
$customer_number = $db->GetVal("
select customer_number
from server
where computer_number = $computer_number");
}

if( empty($customer_number) or empty($computer_number) ) {
    trigger_error("Requires Account Number and Computer Number.  Something must be wrong with referer: '$HTTP_REFERER'", FATAL);
}

$computer=new RackComputer;
$computer->Init($customer_number,$computer_number,$db);
if( !empty($command) and
    $command=="SET_COMPLETION" ) {
    $stamp=time();
    $info["customer_number"]=$customer_number;
    $info["computer_number"]=$computer_number;
    if( $db->TestExist("select completion_message 
                        from completion_message 
                        where computer_number=$computer_number 
                          and customer_number=$customer_number;")
        ) {
        $db->Update( "completion_message",
                     $info,
                     "computer_number=$computer_number AND
                      customer_number=$customer_number");
    } else {
        $db->Insert("completion_message",$info);
    }
    #Update expedited flag
    if (array_key_exists('expedite', $info) and ($info["expedite"] == 'on'))
        $exp_flag = 'true';
    else
        $exp_flag = 'false';
    
    $db->SubmitQuery("update sales_speed set expedite=$exp_flag where computer_number=$computer_number and customer_number=$customer_number");

    $log = "";
    if( !empty($info["pre_build_message"]) || $prebuild_message_set ) {
        $log .= "Pre-Production Instructions (Hardware) set to: ";
        $log .= $info["pre_build_message"];
    }
    if( (!empty($info["pre_build_message"]) || $prebuild_message_set) 
        and (!empty($info["completion_message"]) || $completion_message_set ) ) {
        $log .= "<br>\n";
    }

    if( !empty($info["completion_message"]) || $completion_message_set ) {
        $log .= "Support Instructions (Software) set to: ";
        $log .= $info["completion_message"];
    }
    $computer->Log($log);
    
    JSForceReload("/ACCT_main_workspace_page.php","content_page=" 
                  . urlencode("/tools/DAT_display_computer.php3") 
                  . "&args=" . urlencode("account_number=$customer_number&customer_number=$customer_number&computer_number=$computer_number"),"workspace");
}
?>
<HTML id="mainbody">
<head>
    <TITLE>CORE: Set Build Instructions</TITLE>
    <LINK HREF="/css/core_ui.css" REL="stylesheet">
    <LINK HREF="/css/core2_basic.css" REL="stylesheet">
<?
//SET THE COMPUTER ACTION MENU IN THE NAV FRAME
$action_menu_type = "computer";
$action_menu_arg  = "computer_number=$computer_number";
require_once("tools_body.php");
?>
<table class="blueman">
<tr>
    <th class="blueman">Set Build Instructions: #<?print ($customer_number."-".$computer_number);?> </th>
</tr>
<tr>
    <td>
    
<TABLE class="datatable">
<TR>
<TD COLSPAN=2 ALIGN="CENTER">
<FORM ACTION="set_completion.php3" METHOD="POST">
<INPUT TYPE=HIDDEN name=command value="SET_COMPLETION">
<INPUT TYPE=HIDDEN name=customer_number value="<?print($customer_number);?>">
<INPUT TYPE=HIDDEN name=computer_number value="<?print($computer_number);?>">
<?
    $pre_build_message=$computer->getData("pre_build_message");
    $completion_message=$computer->getData("completion_message");
    $expedite = "";
    if ($computer->getData("expedite") == 't')
    {
        $expedite = "CHECKED";
    }
    if ( $pre_build_message != "" ) {
        print "<INPUT TYPE=HIDDEN name=prebuild_message_set value=1>\n";
    } else {
        print "<INPUT TYPE=HIDDEN name=prebuild_message_set value=0>\n";
    }
    if ( $completion_message != "" ) {
        print "<INPUT TYPE=HIDDEN name=completion_message_set value=1>\n";
    } else {
        print "<INPUT TYPE=HIDDEN name=completion_message_set value=0>\n";
    }
?>
<TABLE class="datatable">
<TR>
    <TH style="width: 10%"> Account Number-Server Number </TH>
    <TD><?print ($customer_number."-".$computer_number);?></TD>
</TR>
<TR>
    <TH> Contact Name </TH>
    <TD>
    <?
        $primaryContact = $computer->account->getPrimaryContact();
        print($primaryContact->individual->firstName.' '.$primaryContact->individual->lastName);
    ?>
    </TD>
</TR>
<TR>
	<td colspan="2"
	    bgcolor="#FF9999"
        style="color:#990033"> <strong><div align="center">Customers Can See This Information</div></strong> </td>
</TR>
<TR>
    <TH colspan="2" class="blueman"> Pre Production Instructions (Hardware): </TH>
</tr>
<tr>    
    <td colspan="2"> Please use this box to include any special instructions for the server that need 
    to take place during the build process.  This includes special hardware requirements 
    and/or special partioning requirments.  No ticket will be opened for these requests. </td>
</tr>
<tr>    
    <TD colspan="2"><TEXTAREA ROWS=6 COLS=50 WRAP=VIRTUAL NAME="info[pre_build_message]"><?
    print($pre_build_message);
    ?></TEXTAREA></TD>
</TR>
<TR>
    <TH colspan="2"> Support Instructions (Software): </th>
</tr>
<tr>
    <td colspan="2">
    This message is sent to support after the server goes online. Please use this 
    box to list any special software configuration or install issues related to 
    the server. A ticket will be opened for all requests listed here.  </i></td>
</tr>
<tr>    
    <TD colspan="2"><TEXTAREA ROWS=7 COLS=50 WRAP=VIRTUAL NAME="info[completion_message]"><?
    print($completion_message);
    ?></TEXTAREA></TD>
</TR>
<TR>
    <th colspan="2"> <strong><INPUT TYPE=CHECKBOX NAME="info[expedite]" <? print($expedite); ?>> EXPEDITE </strong> </th>
</TR>
<TR>
    <TD COLSPAN=2 ALIGN=CENTER><INPUT TYPE=SUBMIT VALUE="Submit Changes" class="form_button"></TD>
</TR>
</TABLE>
</FORM></TD>
</TR>
</TABLE>
</td>
</tr>
</table>
<?= page_stop() ?>
</HTML>
