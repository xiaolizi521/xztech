<?php
require_once("act/ActFactory.php");

Header("Content-type: text/plain");
require ("CORE.php");
	if ($customer_number!="")
	{
    $i_account = ActFactory::getIAccount();
    $account = $i_account->getAccountByAccountNumber($db, $customer_number);
    $contact = $account->getPrimaryContact();

	  $customer = $contact->individual->firstName." ".$contact->individual->lastName."\n".$contact->individual->getPrimaryEmailAddress()."\n".$contact->primaryCompanyName;
		$db->CloseConnection();
		print ($customer);
	}
?>
	
		

	
