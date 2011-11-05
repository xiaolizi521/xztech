<?
require_once("CORE_app.php");
?>

<HTML>
<HEAD>
<LINK REL="stylesheet" TYPE="text/css" HREF="/css/core2_basic.css">
</HEAD>
<BODY>
<?
function getVLANName($vlan_id) {
    global $GLOBAL_db;
    $res = $GLOBAL_db->GetVal("select name from complexmanaged_vlan where vlan_id=$vlan_id");
    return $res;
}

function showVLANList($computer_number, $customer_number, $type) {
    global $GLOBAL_db;
    // compile list
    $vlan_list = array();
    $query = '';
    $blah = '';
    if ($type == 'new') {
        $query = 'select cm.vlan_id, cm.name, g.id from complexmanaged_vlan cm join "NTWK_Group" g on (g.id = cm."NTWK_GroupID") where vlan_id not in ( select distinct vlan_id from xref_computer_complexmanaged ) and g.datacenter_number in ( select datacenter_number from server where computer_number = '. $computer_number . ')';
        $blah = 'Please select from list of NEW VLANs.';
    }
    // this is really pointless since this option is no longer available for complex managed. accounts sharing complex vlans shouldn't happen.
    /*
    else
    {
        $query = "select vlan_id, name from complexmanaged_vlan join xref_computer_complexmanaged using (vlan_id) where account_number != $customer_number";
        $blah = 'Please select from list of VLANs used by other accounts.';
    }
    */
    $result = $GLOBAL_db->SubmitQuery($query);
    for ($i=0; $i<$result->numRows(); $i++ ) {
        $vlan_list[] = array(
        "number" => $result->getCell($i, 0),
        "name" => $result->getCell($i, 1)
        );
    }

    ?>
    <form name="vlanform" action="popupConfigureComplexNet.php" method="POST">
    <table>
    <tr>
    <td>
    <fieldset>
    <?
    print "<legend>Choose VLAN for $customer_number - $computer_number </legend>";
    ?>
    <p>
    <select name="selected" size='10'>
        <?
        foreach($vlan_list as $vlan) {
            print "<option value=\"$vlan[number]\"> $vlan[name] </option>\n";
        }
        ?>
    </select>
    </fieldset>
    </td>
    <td><?=$blah?></td>
    </tr>
    </table>
    <input type="hidden" name="computer_number" value="<?=$computer_number?>" />
    <input type="hidden" name="customer_number" value="<?=$customer_number?>" />
    </form>
    <a class="text_button" href="javascript:vlanform.submit();">Continue</a>
    <a class="text_button" href="javascript:window.close();">Cancel</a>
    <?
} 

function assignComputer($computer_number, $customer_number, $vlan_id) {
    print "Assign computer $computer_number to ". getVLANName($vlan_id). "?";

    // show buttons
    print "<BR>";
    print "<P style='text-align:left'>";
    print "<A HREF=/tools/popupConfigureComplexNet.php?customer_number=$customer_number&computer_number=$computer_number&vlan_id=$vlan_id&confirm=1 class='text_button'>Continue</A>";
    print "<A HREF=\"javascript:window.close()\" class='text_button'>Cancel</A>";
    print "</P>";
}

?>

<TABLE class="blueman">
<TR><TH class="blueman">Configure Computer # <?=$computer_number ?> for ComplexNet</TH></TR>
<TR><TD class="blueman">
<P>
<?
if (isset($confirm) and 
    isset($computer_number) and 
    isset($customer_number) and 
    isset($vlan_id))
{
    global $GLOBAL_db;
    $GLOBAL_db->BeginTransaction();
    // Make sure computer is not already assigned a VLAN
    $res = $GLOBAL_db->SubmitQuery("insert into xref_computer_complexmanaged values ($computer_number, $customer_number, $vlan_id)");
    $GLOBAL_db->CommitTransaction();
    print "Successfully configured computer for ComplexManaged!";
?>
    <SCRIPT LANGUAGE="JavaScript">
    window.opener.location = window.opener.location;
    </SCRIPT>
<?
}
else {
    if (!isset($selected)) {
        print "Error! Incorrect selection!";
    }
    else {
        switch( $selected ) {
            case 'new':
            case 'used':
                showVLANList($computer_number, $customer_number, $selected);
                break;
            default:
                // assign computer to VLAN
                assignComputer($computer_number, $customer_number, $selected);
                break;
        } 
    }
}
?>

</P>
</TD></TR>
</TABLE>
</BODY>
</HTML>
