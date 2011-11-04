<?php

require_once("CORE_app.php");
require_once('helpers.php');
require_once("act/ActFactory.php");

session_register("SESSION_contact_id");
session_register("SESSION_role_id");
session_register("SESSION_person_fname");
session_register("SESSION_person_lname");
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
$iContact = ActFactory::getIContact();
$acct = $iAccount->getAccountByAccountId( $GLOBAL_db, $SESSION_account_id );
$crm_company_id = $acct->crm_company_id;
$data = array(
                "accountId"       => $SESSION_account_id,
                "crmId"           => $crm_company_id,
                "contactId"       => $SESSION_contact_id,
                "coreContactId"   => getRackSessionContactID(),
                "roleId"          => "",
                "firstName"       => stripslashes( $SESSION_person_fname ),
                "lastName"        => stripslashes( $SESSION_person_lname ),
                "orgName"         => stripslashes( $SESSION_org_name ),
                "jobTitle"        => stripslashes( $SESSION_job_title ),
                "street1"         => stripslashes( $SESSION_street1 ),
                "street2"         => stripslashes( $SESSION_street2 ),
                "street3"         => stripslashes( $SESSION_street3 ),
                "city"            => stripslashes( $SESSION_city ),
                "state"           => stripslashes( $SESSION_state ),
                "zip"             => stripslashes( $SESSION_zip ),
                "countryId"       => $SESSION_country_id,
                "phoneNumber"     => stripslashes( $SESSION_primary_phone_number ),
                "phoneTypeId"     => $SESSION_primary_phone_type_id,
                "phoneCountry"    => $SESSION_country_id, //this is due to a limitation of Onyx. phone countries must be country of primary address
                "email"           => stripslashes( $SESSION_email ),
                "secret"          => stripslashes( $SESSION_secret ),
                "question"        => stripslashes( $SESSION_question ),
                "answer"          => stripslashes( $SESSION_answer )
            );

foreach($SESSION_role_id as $currentRoleId) {
    
    $data["roleId"] = $currentRoleId;
    $result = $iAccount->addExternalContact($data);
    if(!empty($result["addExternalContactReturn"]["errorText"])) {
        print "<font color='red'><h1>Error:</h1>"
            . $result["addExternalContactReturn"]["errorText"]
            . "</font>";
        exit();
    }
    if (empty($data["contactId"]) && sizeof($SESSION_role_id) > 1) {
        # If the contactId is not set, the first time you call addExternalContact()
        # it creates a new individual. The userData variable contacts the new
        # external contact id.
        # We really should have an addIndividual() method that returns the 
        # individual id, as opposed to addExternalContact() having two behaviors.
        $external_contact_id = $result["addExternalContactReturn"]["userData"];
        $external_contact_headers = $iContact->getExternalContactHeaders(
                                        $GLOBAL_db, $crm_company_id);
        foreach($external_contact_headers as $c) {
            if ($c->externalContactPrimaryId == $external_contact_id) {
                $data["contactId"] = $c->individualPrimaryId;
                break;
            }
        }
    }
}

foreach($_SESSION as $name => $value) {
    unset($_SESSION[$name]);
}
$SESSION_role_id = $role_id = '';
?>
<HTML> 
<HEAD>
<script type="text/javascript">
opener.top.location.href= '/ACCT_main_workspace_page.php?account_number=<?=$acct->account_number?>';
window.close()
</script>
