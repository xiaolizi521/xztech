<?
require_once("menus.php");
require_once( "CORE_app.php" );
require_once("class.mailer.php");

if( empty($account_number) or empty($contract_id) ) {
    DisplayError("Unable to display this customer/computer because you are missing the account_number or contract_id");
    exit();
}
$query = '
        SELECT
            computer_number
        FROM
            "CNTR_xref_Contract_Server"
        WHERE
            "CNTR_ContractID" = ' . $contract_id; 
$result = $GLOBAL_db->SubmitQuery( $query );
if ($result->numRows() > 0) {
    $computer_number = $result->getResult(0, "computer_number");
}
if ( !($computer_number) ) { 
    DisplayError("Unable to display this customer/computer because contract_id is not valid");
    exit();
}
$computer=new RackComputer;
$computer->Init($account_number,$computer_number,$db);
if( !$computer->IsComputerGood() ) {
    DisplayError("Unable to load any information about computer number $computer_number This computer may no longer exist.  If you continue to have problems contact the database administrator");
}
if ( !$computer->ContractCompleted() ) {  
    DisplayError("The contract hasn't yet gone online.  Cannot resend online message");
    exit();
}
if ( in_array($computer->WhatOS(),array("Firewall - Cisco ASA", "Firewall - Cisco PIX","Load-Balancer","Netscreen")) ) {
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
    $processed_inds = array();
    $txt = 'Resending Online Msg to: ';
    foreach( $cids as $cid ) {
        $chunks = split( '-', $cid );
        $contact_id = $chunks[0];
        $individual_id = $chunks[1];
        if (in_array( $individual_id, $processed_inds ) ) {
            continue;
        }
        $i_contact = ActFactory::getIContact();
        $contact = $i_contact->getExternalContact( $GLOBAL_db, $contact_id);
        $email = $contact->individual->getPrimaryEmailAddress();
        $full_name = $contact->individual->getFullName();
        $info = array('full_name' => $full_name,
                      'email' => $email);
        $user_info[] = $info;
        $processed_inds[] = $individual_id;
        if ( $computer->isManagedCustomer() ) {
            $computer->CreateManagedContractCompletedTicket();
        } else {
            $computer->CreateIntensiveContractCompletedTicket();
        }
    }
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

echo "<P>Contract ID: <b>$contract_id</b><P>";
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
