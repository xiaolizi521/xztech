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

$iAccount = ActFactory::getIAccount();
$iContact = ActFactory::getIContact();
if($SESSION_account_id ){
    $acct = $iAccount->getAccountByAccountId( $GLOBAL_db, $SESSION_account_id );
    $crm_company_id = $acct->crm_company_id;
}else{
    $crm_company_id = "";
}

if(!empty($last_name)) {
     $last_name = trim(@$last_name);
     $SESSION_person_lname = $last_name;
}

if(!empty($first_name)) {
     $first_name = trim(@$first_name);
     $SESSION_person_fname = $first_name;
}

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

?>
<HTML> 
<HEAD><title>view info</title></head>
<body>
<table>
<tr><th bgcolor="#cccccc">SESSION_contact_id</th><td><?= $SESSION_contact_id ?></td></tr>
<? if($SESSION_role_id ){ 
    foreach($SESSION_role_id as $currentRoleId) { ?>
<tr><th bgcolor="#cccccc">Role</th><td><?= $currentRoleId ?></td></tr>
<? }
 } ?>
<tr><th bgcolor="#cccccc">SESSION_person_fname</th><td><?= $SESSION_person_fname ?></td></tr>
<tr><th bgcolor="#cccccc">SESSION_person_lname</th><td><?= $SESSION_person_lname ?></td></tr>
<tr><th bgcolor="#cccccc"> SESSION_org_name </th><td><?= $SESSION_org_name ?></td></tr>
<tr><th bgcolor="#cccccc"> SESSION_job_title </th><td><?= $SESSION_job_title ?></td></tr>
<tr><th bgcolor="#cccccc"> SESSION_street1 </th><td><?= $SESSION_street1 ?></td></tr>
<tr><th bgcolor="#cccccc"> SESSION_street2 </th><td><?= $SESSION_street2 ?></td></tr>
<tr><th bgcolor="#cccccc"> SESSION_street3 </th><td><?= $SESSION_street3 ?></td></tr>
<tr><th bgcolor="#cccccc"> SESSION_city </th><td><?= $SESSION_city ?></td></tr>
<tr><th bgcolor="#cccccc"> SESSION_state </th><td><?= $SESSION_state ?></td></tr>
<tr><th bgcolor="#cccccc"> SESSION_zip </th><td><?= $SESSION_zip ?></td></tr>
<tr><th bgcolor="#cccccc"> SESSION_country_id </th><td><?= $SESSION_country_id ?></td></tr>
<tr><th bgcolor="#cccccc"> SESSION_primary_phone_number </th><td><?= $SESSION_primary_phone_number ?></td></tr>
<tr><th bgcolor="#cccccc"> SESSION_primary_phone_type_id </th><td><?= $SESSION_primary_phone_type_id ?></td></tr>
<tr><th bgcolor="#cccccc"> SESSION_email </th><td><?= $SESSION_email ?></td></tr>
<tr><th bgcolor="#cccccc"> SESSION_secret </th><td><?= $SESSION_secret ?></td></tr>
<tr><th bgcolor="#cccccc"> SESSION_question </th><td><?= $SESSION_question ?></td></tr>
<tr><th bgcolor="#cccccc"> SESSION_answer </th><td><?= $SESSION_answer ?></td></tr>
<tr><th bgcolor="#cccccc"> SESSION_account_id </th><td><?= $SESSION_account_id ?></td></tr>

</table>
</body>
</HTML>
