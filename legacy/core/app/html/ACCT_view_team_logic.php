<?php

require_once("CORE_app.php");
require_once("helpers.php");
checkDataOrExit( array( "team_id" => "Team ID" ) );
require_once("team_display.php");


function PrintStaffOptions() {
    global $GLOBAL_db, $cids;
    $query = '
SELECT 
  contact."ID" as cid,
  person."FirstName" || \' \' || person."LastName" as name
FROM
  "CONT_Contact" contact,
  "CONT_Person" as person,
  "xref_employee_number_Contact" xenc
WHERE
-- JOINS      
      contact."CONT_PersonID" = person."ID"
  AND xenc."CONT_ContactID" = contact."ID"
  AND contact."ID" not in ('.join(',',$cids).')
ORDER BY name
';
    $result = $GLOBAL_db->SubmitQuery( $query );
    $num_items = $result->numRows();
    for( $i=0; $i<$num_items ; $i++ ) {
        $cid = $result->getCell($i,0);
        $name = $result->getCell($i,1);
        echo "<option value=\"$cid\"> $name </option>\n";
    }    
    $result->freeResult();
}

function PrintStaffRoles() {
    global $GLOBAL_db, $in_core;
    
    $query = '
SELECT 
  "ID", "Name"
FROM
  "ACCT_val_TeamMemberRole"
';

    if( !$in_core ) {
        $query .= ' WHERE "ID" >= 4 AND "ID" <= 12 ';
    }
    $query .= ' ORDER BY "Description", "Name" ';

    $result = $GLOBAL_db->SubmitQuery( $query );
    $num_items = $result->numRows();
    for( $i=0; $i<$num_items ; $i++ ) {
        $id = $result->getCell($i,0);
        $name = $result->getCell($i,1);
        echo "<option value=\"$id\"";
        if( $id == 2 ) {
            echo " selected";
        }
        echo "> $name </option>\n";
    }    
    $result->freeResult();
}

?>