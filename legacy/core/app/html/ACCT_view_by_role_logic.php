<?php

require_once("CORE_app.php");
require_once( "reporter.php" );
$show_teams = true;
$show_contacts = true;

if(empty($role_id)) {
        $role_id = ONYX_ACCOUNT_ROLE_PRIMARY;
        $role = new ACCT_AccountRole;
        $role->loadID( $role_id );
        print '<html><head><title>Enter Info</title></head><body>';
        print "\n";
        print '<form action="' . $GLOBALS['REQUEST_URI'] . '">';
        print 'Please enter a role id ';
        print "<br>\n";
        print "<select name='role_id'>";
        print $role->getSelectOptions();
        print "</select>";
        print "<br>\n";
        print "<input type='submit' name='Submit'>\n";
        print "</form>\n";
        print '</body></html>';
        print "\n";
        exit;
}

if($role_id == ONYX_ACCOUNT_ROLE_PRIMARY or
   $role_id == ONYX_ACCOUNT_ROLE_ADMINISTRATIVE or
   $role_id == ONYX_ACCOUNT_ROLE_TECHNICAL or
   $role_id == ONYX_ACCOUNT_ROLE_BILLING or    
   $role_id == ONYX_ACCOUNT_ROLE_ABUSE or
   $role_id == ONYX_ACCOUNT_ROLE_REVIEWER or
   $role_id == ONYX_ACCOUNT_ROLE_PURCHASER) {    
    print "Error: external contact types may not be used for this report.";
    exit();
}

// This is to be called by the _page
function printCReport() {
        global $role_id;

        $rep = new CORE_Reporter;
        
        $rep->setPageSize( 0 );

        $rep->ignoreField( 'CID' );
        
        $rep->setQuery( '
SELECT
       "CONT_ContactID" as "CID",
       "Title",
       "FirstName" || \' \' || "LastName" as "Name",
       0 as "Accounts"
FROM 
     (select distinct on ("CONT_ContactID")
             "CONT_ContactID"
        from "ACCT_xref_Account_Contact_AccountRole"
       WHERE "ACCT_val_AccountRoleID" = ' . $role_id . '
     ) as roles,
     "CONT_Contact",
     "CONT_Person"
WHERE
       "CONT_ContactID"= "CONT_Contact"."ID"
   AND "CONT_PersonID"= "CONT_Person"."ID"
ORDER BY "Name"
 ' );

        $rep->setCountQuery( '
select count(*)
  from "ACCT_xref_Account_Contact_AccountRole"
 WHERE "ACCT_val_AccountRoleID" = ' . $role_id . '
 group by "CONT_ContactID"
 ' );

        $rep->setFieldRule( 'Name',
                            "<a href=\"" . $GLOBALS["roster_url"] . "view_employee.jsf?contact_id=" . "%CID\">%Name</a>" );

        $rep->printHTML();
}

function printTReport() {
        global $role_id;

        $rep = new CORE_Reporter;
        
        $rep->setPageSize( 0 );
        
        $rep->ignoreField( 'tid' );
        
        $rep->setQuery( '
SELECT
       "ID" as tid,
       "Name",
       0 as "Accounts"
FROM 
     "ACCT_Team"
ORDER BY "Name"
 ' );

        $rep->setCountQuery( '
select count(*)
  from "ACCT_Team"
 ' );

        $rep->setFieldRule( 'Accounts',
                            "<a href=\"/ACCT_view_associated_accounts_page.php?team_id=%tid&role_id=$role_id\">View</a>" );

        $rep->printHTML();
}

?>
