<?

require_once("ACCT_main_frame_content_logic.php");
require_once("menus.php");
require_once("CORE_app.php");
require_once("act/ActFactory.php");

function PrintCustomers() {
    global $account;

    $count=0;
    foreach($account->getExternalContacts() as $contact) {
        #if ( $count == 0 ) {
        #    pre_print_r( $contact );
        #}
        if($count++%2) {
            $color = "#FFFFFF";
        } 
        else {
            $color = "#E8E8F8";
        }   
        
        $roles = $contact->getRoleName();
             
        if(empty($roles)) {
            $roles = "ERROR";
        }
        
        $name = $contact->individual->getFullName();
        if(empty($name)) {
            $name = "ERROR";
        }
        
        $phone = $contact->individual->getPrimaryPhoneNumber();
        $individual = $contact->individual;
        if(empty($phone)) {
            $phone = "ERROR";
        }
        echo "<tr>\n";
        echo "<td bgcolor=\"$color\"> <input type=\"checkbox\" name=\"cids[]\" value=\"$contact->primaryId-$individual->primaryId\"></td>\n";
        echo "<td bgcolor=\"$color\"> $roles </td>\n";
        echo "<td bgcolor=\"$color\"> $name </td>\n";
        echo "<td bgcolor=\"$color\"> $phone </td>\n";

        echo "</tr>\n";       
    }
}

if (empty($contract_id))
    $contract_id = '';
if (empty($computer_number))
    $computer_number = '';
if (empty($computer_number) and empty($contract_id)) {
    DisplayError("Unable to display this menu because you are missing the computer_number or contract_id");
    exit();
}
if (empty($account_number)) {
    DisplayError("Unable to display this menu because you are missing the account_number");
    exit();
}
$i_account = ActFactory::getIAccount();
$account = $i_account->getAccountByAccountNumber($GLOBAL_db, $account_number);
$account_id = $account->account_id;

?>
<HTML>
<HEAD>
    <TITLE>Resend Online Message</TITLE>
    <LINK HREF="/css/core2_basic.css" REL="stylesheet">
</HEAD>
<body leftmargin="2"
      topmargin="2"
      rightmargin="2"
      bottommargin="2"
      marginwidth="2">
<TABLE class="blueman">
<TR>
<th class=blueman> Customer Contacts </th>
</TR>
<TR><TD>
<? if (!empty($computer_number)) 
    echo '<form action="ACCT_send_online.php">';
   else 
    echo '<form action="ACCT_contract_send_online.php">';
?>
<table class="blueman" cellpadding=3>
 <?php PrintCustomers(); ?>
</table>
</td></tr>
<tr><td align=right>
 <input type="hidden" name=computer_number value="<?=$computer_number?>">
 <input type="hidden" name=account_number value="<?=$account_number?>">
 <input type="hidden" name=contract_id value="<?=$contract_id?>">
 <input type="submit" value="ReSend Online Message" class="form_button">
 </form>
</td></tr>
</table>
</body>
</html>
