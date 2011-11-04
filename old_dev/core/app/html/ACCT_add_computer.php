<?
require_once("CORE_app.php");
require_once("act/ActFactory.php");

$i_account = ActFactory::getIAccount();
$onyx_account = $i_account->getAccountByAccountNumber($GLOBAL_db, $account_number);

if (!$onyx_account->isClosed()) {
	$computer_number=create_computer($account_number,"","","","",$datacenter_number);
	ForceReload("/tools/display_computer.php3?computer_number=$computer_number&customer_number=$account_number");
}
else {
	ForceReload("/py/account/view.pt?account_number=$account_number");
}
?>
