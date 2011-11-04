<?
require_once("menus.php");
require_once( "CORE_app.php" );
require_once("class.mailer.php");
require_once("act/ActFactory.php");
if( empty($account_number) or empty($computer_number) ) {
    DisplayError("Unable to display this customer/computer because you are missing the account_number or computer_number");
    exit();
}

$computer=new RackComputer;
$computer->Init($account_number,$computer_number,$db);
if( !$computer->IsComputerGood() ) {
    DisplayError("Unable to load any information about computer number $computer_number This computer may no longer exist.  If you continue to have problems contact the database administrator");
}
if ( in_array($computer->WhatOS(),array("Firewall - Cisco ASA", "Firewall - Cisco PIX", "Load-Balancer","Netscreen")) ) {
    // No online message sent for firewalls since
    // the customer does not get any login information
    // and a customer must already have a server to
    // use a firewall.
    DisplayError('Firewalls and Load Balancers do not have'
        . ' an Online/Complete message.'
        . ' The security or networking tech is responsible for'
        . ' notifying the customer.');
}
$user_info = array();
if (!empty($cids)) {
    if ( ! is_array( $cids ) ) {
        $cids = array( $cids );
    }
    $processed_inds = array();
    $txt = 'Resending Online Msg to: ';
    $contact_list = array();
    foreach( $cids as $cid ) {
        $chunks = split( '-', $cid );
        $contact_id = $chunks[0];
        $individual_id = $chunks[1];
        if (in_array( $individual_id, $processed_inds ) ) {
            continue;
        }
        $i_contact = ActFactory::getIContact();
        $contact = $i_contact->getExternalContact( $GLOBAL_db, $contact_id);
        $contact_list[] = $contact->getCoreContactId( $GLOBAL_db );
        $email = $contact->individual->getPrimaryEmailAddress();
        $full_name = $contact->individual->getFullName();
        $info = array('full_name' => $full_name,
                      'email' => $email);
        $user_info[] = $info;
        $processed_inds[] = $individual_id;
        // the following has been removed from sendCustomerOnlineNotice
        // $this->_sendSupportRackwatchNotice();
        // We COULD call it here, but I don't think we actually want to.
        $txt .= $info['full_name'].', ';
    }
    $txt .="<BR>";
    $computer->log( $txt );
    $computer->ResendCustOnlineNotice($contact_list);
    
}
?>

<HTML>
<HEAD>
    <title> Confirmation Online Message Resent </title>
    <LINK HREF="/css/core2_basic.css" REL="stylesheet">
</HEAD>
<body leftmargin="2"
      topmargin="2"
      rightmargin="2"
      bottommargin="2"
      marginwidth="2">
<TABLE class="blueman">
<TR>
<th class=blueman> Online Message Resent Confirmation </th>
</TR>
<tr><td>
<?

echo "<P>Computer Number: <b>$computer_number</b><P>";
echo "Customer Number: <b>$account_number</b><P>";
echo "Online Message Resent to the following users:<P>";
foreach($user_info as $user) {
    echo $user['full_name'].':  '.$user['email'].' <BR>';
}

?>
</td></tr>
<tr><td align=right>
<input type="button" value="Close Window" class="form_button" onClick="javascript:window.close();">
</td></tr>
</table>
</body>
</html>
