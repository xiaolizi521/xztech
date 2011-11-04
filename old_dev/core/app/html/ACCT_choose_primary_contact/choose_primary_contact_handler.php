<?php

require_once("CORE_app.php");
require_once('helpers.php');
require_once("act/ActFactory.php");

session_register("SESSION_contact_id");
session_register("SESSION_role_id");
session_register("SESSION_first_name");
session_register("SESSION_last_name");
session_register("SESSION_org_name");
session_register("SESSION_job_title");
session_register("SESSION_street1");
session_register("SESSION_street2");
session_register("SESSION_street3");
session_register("SESSION_city");
session_register("SESSION_state");
session_register("SESSION_zip");
session_register("SESSION_country_id");
session_register("SESSION_primary_phone_number");
session_register("SESSION_primary_phone_type_id");
session_register("SESSION_email");
session_register("SESSION_emer_email");
session_register("SESSION_fax_phone_number");
session_register("SESSION_secret");
session_register("SESSION_question");
session_register("SESSION_answer");
session_register("SESSION_account_id");

if (!empty($role_id)) {
       $SESSION_role_id = $role_id;
}

if (empty($SESSION_account_id)) {
    trigger_error('Account ID missing in session', ERROR);
}

$iAccount = ActFactory::getIAccount();
$acct = $iAccount->getAccountByAccountId( $GLOBAL_db, $SESSION_account_id );
$crm_company_id = $acct->crm_company_id;
$iContact = ActFactory::getIContact();

$data = array(
	"accountId"			=> $SESSION_account_id,
    "crmId"             => $crm_company_id,
	"contactId" 		=> $SESSION_contact_id,
	"coreContactId"		=> getRackSessionContactID(),
	"roleId" 			=> $SESSION_role_id,
	"firstName" 		=> $SESSION_first_name,
	"lastName" 			=> $SESSION_last_name,
	"orgName" 			=> $SESSION_org_name,
	"jobTitle" 			=> $SESSION_job_title,
	"street1" 			=> $SESSION_street1,
	"street2" 			=> $SESSION_street2,
	"street3" 			=> $SESSION_street3,
	"city" 				=> $SESSION_city,
	"state" 			=> $SESSION_state,
	"zip" 				=> $SESSION_zip,
	"countryId" 		=> $SESSION_country_id,
	"phoneNumber" 		=> $SESSION_primary_phone_number,
	"phoneTypeId" 		=> $SESSION_primary_phone_type_id,
	"phoneCountry" 		=> $SESSION_country_id, //this is due to a limitation of Onyx. phone countries must be country of primary address
	"email" 			=> $SESSION_email,
	"emerEmail" 		=> $SESSION_emer_email,
	"faxPhoneNumber" 	=> '',
	"faxPhoneCountry" 	=> '',
	"secret" 			=> $SESSION_secret,
	"question" 			=> $SESSION_question,
	"answer" 			=> $SESSION_answer
	);

$result = $iAccount->addExternalContact($data);

foreach($_SESSION as $name => $value) {
    unset($_SESSION[$name]);
}
$SESSION_role_id = $role_id = '';

?>
<HTML>
<HEAD>
<script type="text/javascript">
opener.top.location.href= '/ACCT_main_workspace_page.php?account_number=<?=$acct->account_number?>';
window.close();
</script>

