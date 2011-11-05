<?
require_once('network_diagram.php');

//tell the browser not to cache this page
header( "Expires: Mon, 20 Dec 1998 01:00:00 GMT" );
header( "Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT" );
header( "Cache-Control: no-cache, must-revalidate" );
header( "Pragma: no-cache" );

define("MAX_DEPTH_OF_NETWORK", 20); //if the depth of nesting of devices in this account exceeds this number, the 
                                    // account's network is assumed to have a circular relationship                                    

include('CORE_app.php');
include('tools_body.php');
?>
<SCRIPT LANGUAGE="JavaScript" type="text/javascript">
<!--
//this is the function that gets called when a node in the diagram is clicked.
//for this page, we want it to toggle the checkbox for the clicked node.
function node_click(num, canBeOrganized) {
    box = eval("document.form_1.C" + num); 
    box.checked = !box.checked;
}
//-->
</SCRIPT>
<?

if ( !in_dept("PERM_IP_ADMIN|NETWORK|PRODUCTION_SUPERVISOR|" .
              "PROFESSIONAL_SERVICES|ACCOUNT_EXECUTIVE|SALES|SUPPORT") ) {
    DisplayError("You do not have access to this page.");
}

$customer_number = $db->GetVal("
    SELECT customer_number
    FROM server
    WHERE computer_number = $device_number
    ");

$net_device = new RackComputer;
$net_device->Init($customer_number, $device_number, $db);
$os = determine_os($device_number);

if (isset($command) and $command == 'DONE') {
    if ($os == 'Netscreen' and count($computer_list) > 1) {
        DisplayError("Netscreens can only have one computer behind them!");
    } else {
        $db->BeginTransaction();
        $result = $db->SubmitQuery("
            SELECT computer_number
            FROM computer_behind_network_device
            WHERE device_number = $device_number");
        $old_computer_list = array();
        for ($i=0;$i<$result->numRows();$i++) {
            array_push($old_computer_list,$result->getCell($i,0));
        }
        if (empty($computer_list)) {
            $computer_list = array();
        }
        if (!empty($old_computer_list)) {
            foreach($old_computer_list as $computer_number) {
                if ( !in_array($computer_number,$computer_list) ) {
                    $db->SubmitQuery("
                        DELETE FROM computer_behind_network_device
                        WHERE device_number = $device_number
                        AND computer_number = $computer_number");
                    $net_device->Log("Removed computer $computer_number from behind device $device_number.");
                    $computer = new RackComputer;
                    $computer->Init($customer_number, $computer_number, $db);
                    $computer->Log("Removed computer $computer_number from behind device $device_number.");
                }
            }
        }
        foreach($computer_list as $computer_number) {
            if (!in_array($computer_number,$old_computer_list)) {
                $db->SubmitQuery("
                    INSERT INTO computer_behind_network_device
                    (computer_number, device_number)
                    VALUES ($computer_number, $device_number)
                    ");
                $net_device->Log("Added computer $computer_number behind device $device_number.");
                $computer = new RackComputer;
                $computer->Init($customer_number, $computer_number, $db);
                $computer->Log("Added computer $computer_number behind device $device_number.");
            }
        }
        $db->CommitTransaction();
        if (isset($firewall_return) && $firewall_return)
        {
            JSForceReload($firewall_return, '', 'content');
        }
    }
}

function CausesCircular($frontDeviceNum, $behindDeviceNum, $callCount, $db) {
    //see if putting behindDevice behind frontDevice will cause a circular relationship, where a circular relationship 
    // here is defined as frontDevice being behind behindDevice, or behind another set of devices that is behind 
    // behindDevice (confused yet?)
    //The way to check for this is to look through the chain of devices behind behindDevice, and make sure that 
    // frontDevice is not included among that set of devices. This is of course, easily solved with recursion    
    
    //first, make sure we got input that we can work with
    if($frontDeviceNum == null 
       or $frontDeviceNum < 0 
       or $frontDeviceNum == ""
       or $behindDeviceNum == null 
       or $behindDeviceNum < 0 
       or $behindDeviceNum == "") {
        return false;
    }       
    
    if($callCount > MAX_DEPTH_OF_NETWORK) {
        //we've looped around a lot.  There is probably a circular relationship we are traversing.        
        print ("<font color=\"red\"><p>There appears to be a circular relationship between devices in this "
             . "account. A circular relationship should not exist within accounts.  A circular relationship " 
             . "between devices occurs when device A is behind device B, but device B is also behind device A " 
             . "(note that there may be 1 or more devices in the middle).</p><p>Please correct this problem in "
             . "the account immediately! </p></font>");   
        return true;
    }
    
    //get the list of devices that are behind behindDevice
    $result = $db->SubmitQuery("
            SELECT computer_number
            FROM computer_behind_network_device
            WHERE device_number = $behindDeviceNum
            ORDER BY computer_number
            ");
    $connectedComputers = array();
    for ($i = 0; $i < $result->numRows(); $i++) {
        $connectedComputers[$i] = $result->getResult($i, 0);
    }        
    
    foreach($connectedComputers as $connectedComputer) {
        //basecase - the current device is the same as our front device
        if($connectedComputer == $frontDeviceNum) {
            return true;
        }   
        if(CausesCircular($frontDeviceNum, $connectedComputer, $callCount + 1, $db)) {
            return true;
        }        
    }
      
    return false;     
}

function IsBehind($deviceNum, $possibleFront, $db) {
    //get the list of devices that are behind $possibleFront 
    $result = $db->SubmitQuery("
            SELECT computer_number
            FROM computer_behind_network_device
            WHERE device_number = $possibleFront
            ORDER BY computer_number
            ");
    $connectedComputers = array();
    for ($i = 0; $i < $result->numRows(); $i++) {
        $connectedComputers[$i] = $result->getResult($i, 0);
    }       
    return (in_array($deviceNum, $connectedComputers));
}

//we're only interested in devices that are at least STATUS_ORDER_SUBMITTED
$otherDevicesInAccount = $net_device->getOtherDevicesInAccount(STATUS_ORDER_SUBMITTED); 

?>
 
<?                  
  $os_icon = getIconForServer($device_number);
  print("<center><br /><font size=\"+2\"><b>Organize $os <br /><IMG SRC='$os_icon'> $device_number</b></font></center>");  
?>

<TABLE BORDER="0" WIDTH="100%">
<TR>
<TD valign="top" width="290">
<FORM NAME="form_1" ACTION="organize_net_device.php" METHOD="POST">
<TABLE BORDER="1"  width="285">
<INPUT TYPE=HIDDEN NAME=device_number VALUE="<? print $device_number; ?>">
<?
    if (isset($firewall_return) && $firewall_return)
    {
        print "<input type='hidden' name='firewall_return' value='".htmlspecialchars($firewall_return)."'>";
    }
print("<TR><TD colspan=\"2\" nowrap=\"true\" bgcolor=\"#E0E0E0\">Device is behind <IMG SRC='$os_icon'> $device_number</TH><TD nowrap=\"true\" bgcolor=\"#E0E0E0\">Device is also behind</TH></TR>");
$cached_pt_icon = strIMG( getIconForOS( 'PrevenTier' ) );
$numRowsPrinted = 0;
foreach($otherDevicesInAccount as $computerNumber) {           
    if(IsBehind($computerNumber, $device_number, $db)) {  //if this row's device is already behind this device
        $checkBox = 'CHECKED';                                                             
    }
    else { //if this row's device is not yet behind a device        
        if(CausesCircular($device_number, $computerNumber, 0, $db)) { 
            $checkBox = 'GRAY';
        }
        else {
            $checkBox = '';            
        }
    }
    
    $computer = new RackComputer();
    $computer->init($customer_number, $computerNumber, $db);
    
    //get the other devices this computer is behind                 
    $otherDevices = $computer->getNetworkDeviceNumbers();    
        
    $os_icon = getIconForServer($computer->computer_number);
    if( hasPrevenTierRequired($computer->computer_number) ) {
        $pt_icon = " ($cached_pt_icon)";
    } else {
        $pt_icon = "";
    }    
    $color = NEUTRAL_COLOR;

    print "<TR bgcolor=\"$color\">";
    print "<TD ";
    if($checkBox == "GRAY") {
        print "align=\"right\"><INPUT TYPE=CHECKBOX NAME=\"computer_list[]\" VALUE=\"$computer->computer_number\" DISABLED";
        print "<TD><i>";
        print("<a href=\"organize_net_device.php?device_number=$computer->computer_number\" onmouseover=\"return escape('click to organize this device')\">");
        print "<IMG SRC='$os_icon'> $computer->computer_number";
        print("</a>");
        print "</i> $pt_icon</TD>";
    }
    else {
        if($checkBox == CYCLE_COLOR) {
            print "BGCOLOR=\"" . CYCLE_COLOR . "\" ";
        }    
        print "align=\"right\"><INPUT TYPE=CHECKBOX ID=C" . $computer->computer_number . " NAME=\"computer_list[]\" VALUE=\"$computer->computer_number\" ";
        if($checkBox == "CHECKED" or $checkBox == CYCLE_COLOR) {
            print "CHECKED";
        }
        print "></TD>";        
        print "<TD><b>";
        print("<a href=\"organize_net_device.php?device_number=$computer->computer_number\" onmouseover=\"return escape('click to organize this device')\">");
        print "<IMG SRC='$os_icon'> $computer->computer_number";
        print("</a>");
        print "</b> $pt_icon</TD>";    
    }     
    print("<TD>");
    foreach($otherDevices as $otherDevice) {
        if($otherDevice != $device_number) {
            $os_icon = getIconForServer($otherDevice);
            print("<a href=organize_net_device.php?device_number=$otherDevice onmouseover=\"return escape('click to organize this device')\">");
            print("<IMG SRC='$os_icon'>");
            print("$otherDevice <br />");
            print("</a>");
        }
    }
    print("&nbsp;");
    print("</TD>");         
    print "</TR>\n";
    $numRowsPrinted++;
}
if($numRowsPrinted == 0) {
  ?>
  <TR>
    <TD ALIGN="CENTER" COLSPAN="3">
      <i>There are no other devices in this account that can be organized behind this device.</i>
    </TD>
  </TR>
  <TR>
    <TD ALIGN="CENTER" COLSPAN="3">      
      <button onclick="window.open('/organize_net_device_help.html','help_window','width=500,height=600,scrollbars=yes,status=yes,toolbar=no,menubar=no,location=no');">Help</button>  
    </TD>
  </TR>
  <?    
} 
else {
  ?>
  <TR>
    <TD ALIGN="CENTER" COLSPAN="3">
      <INPUT TYPE=SUBMIT NAME=command VALUE=DONE><br />
      <button onclick="window.open('/organize_net_device_help.html','help_window','width=500,height=600,scrollbars=yes,status=yes,toolbar=no,menubar=no,location=no');">Help</button>  
    </TD>
  </TR>
  <?
}
?>
</TABLE>
</FORM>     
</TD>
<TD rowspan="3" valign="top" align="center">
 <? 
    $diagram = new network_diagram($device_number, $db);
    $diagram->printDiagram();
 ?>
</TD>
</TR>
</TABLE>

<br /><br />

<script language="JavaScript" type="text/javascript" src="/script/tooltip.js"></script>
<?= page_stop() ?>
</html>
