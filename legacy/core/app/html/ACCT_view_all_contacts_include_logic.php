<?php

require_once( "CORE_app.php" );
require_once('helpers.php');

function arrayColumnSort()
{
    $n = func_num_args();
    $ar = func_get_arg( $n - 1 );
    if ( ! is_array( $ar ))
        return false;
        
    if ( ! count( $ar ))
        return false;
    
    for ( $i = 0; $i < $n-1; $i++ )
        $col[ $i ] = func_get_arg( $i );
    
    foreach( $ar as $key => $val )
        foreach( $col as $kkey => $vval )
            if ( is_string( $vval ))
                ${"subar$kkey"}[ $key ] = $val[ $vval ];
    
    $arv = array();
    foreach ( $col as $key => $val )
        $arv[] = ( is_string( $val ) ? ${"subar$key"} : $val );
    $arv[] = $ar;
    
    call_user_func_array( "array_multisort", $arv );
    return $ar;
}

function PrintRackers() {
    global $onyx_account, $py_app_prefix, $GLOBAL_db;
    echo "\n";

    $account_number = $onyx_account->account_number;

    $count = 0;
    $color = "#FFE8E8";                             
        
    $internal_contacts = array();
    $special_internal_contacts = array( array(), array(), array() );

    foreach ( $onyx_account->getInternalContacts() as $contact ) 
    {       
        foreach ( $contact->account_roles as $role ) 
        {                   
            $x = array(); 
            
            $x[ 'account_role_name' ] = $role->account_role_name;
            $x[ 'account_role_id' ] = $role->account_role_id;
            $x[ 'full_name' ] = $contact->getFullName();
            $x[ 'primary_phone_number' ] = $contact->getPrimaryPhoneNumber();
            $x[ 'contact_id' ] = $contact->contact_id;

            if ( $role->account_role_id == ACCOUNT_ROLE_ACCOUNT_EXECUTIVE )
                $special_internal_contacts[ 0 ] = $x;
            elseif ( $role->account_role_id == ACCOUNT_ROLE_ACCOUNTS_RECEIVABLE )
                $special_internal_contacts[ 1 ] = $x;
            elseif ( $role->account_role_id == ACCOUNT_ROLE_BUSINESS_DEVELOPMENT )
                $special_internal_contacts[ 2 ] = $x;
            else
                $internal_contacts[] = $x;
        }
    }
    
    $row_color_choices = array();
    $row_color_choices[] = "#FFE8E8";
    $row_color_choices[] = "#E8E8F8";
    $row_color_choice = 1;

    $role_info = array(); 
    $role_info[] = array( 'type_id' => ACCOUNT_ROLE_ACCOUNT_EXECUTIVE, 'abbrev' => "am" );
    $role_info[] = array( 'type_id' => ACCOUNT_ROLE_ACCOUNTS_RECEIVABLE, 'abbrev' => "ar" );
    $role_info[] = array( 'type_id' => ACCOUNT_ROLE_BUSINESS_DEVELOPMENT, 'abbrev' => "bdc" );
    
    
    $role_index = 0;
    foreach ( $special_internal_contacts as $special_internal_contact ) 
    {
        $row_color_choice = 1 - $row_color_choice;
        $row_color = $row_color_choices[ $row_color_choice ];

        $role_type_id = $role_info[ $role_index ][ 'type_id' ];
        $role_type_abbrev = $role_info[ $role_index ][ 'abbrev' ];
        $role_index += 1;
        
        if ( empty( $special_internal_contact ))
        {
            $result = $GLOBAL_db->SubmitQuery( 
                'SELECT "Name" FROM "ACCT_val_AccountRole" 
                WHERE "ID" = \'' . $role_type_id . '\'' );
                
            $role = $result->getResult( 0, "Name" );
            $name = '-- None Assigned --';
            $phone = '';
        }
        else
        {
            $role = $special_internal_contact[ 'account_role_name' ];
            $name = $special_internal_contact[ 'full_name' ];
            $phone = $special_internal_contact[ 'primary_phone_number' ];
        }
        
        echo "<tr>\n";
        
        echo "<td bgcolor=\"$row_color\" nowrap> " . $role . " </td>\n";
        echo "<td bgcolor=\"$row_color\"> " . $name . " </td>\n";
        echo "<td bgcolor=\"$row_color\" align=\"right\" nowrap> " . $phone . " </td>\n";
        
        echo "<td bgcolor=\"$row_color\" align=\"right\">\n";
        echo '<a href="javascript:makePopUpWin(\'ACCT_change_' . $role_type_abbrev . '_page.php';
        echo "?account_number=" . $account_number;
        echo '\',200,500,\'\',4)"><IMG SRC="/images/button_command_tiny_edit.gif" WIDTH="26" HEIGHT="13" BORDER="0" ALT="Edit" /></a>';

        echo "</td>\n";
        echo "</tr>\n";
    }

    if ( ! empty( $internal_contacts ))
    {
        $internal_contacts_sorted = arrayColumnSort( "account_role_name", SORT_ASC, SORT_STRING, $internal_contacts );

        foreach ( $internal_contacts_sorted as $internal_contact ) 
        {
            $row_color_choice = 1 - $row_color_choice;
            $row_color = $row_color_choices[ $row_color_choice ];
            
            $role = $internal_contact[ 'account_role_name' ];
            $role_id = $internal_contact[ 'account_role_id' ];
            $name = $internal_contact[ 'full_name' ];
            $phone = $internal_contact[ 'primary_phone_number' ];
            $contact_id = $internal_contact[ 'contact_id' ];
            
            echo "<tr>\n";
            echo "<td bgcolor=\"$row_color\" nowrap> " . $role . " </td>\n";
            echo "<td bgcolor=\"$row_color\"> " . $name . " </td>\n";
            echo "<td bgcolor=\"$row_color\" align=\"right\" nowrap> " . $phone . " </td>\n";
            echo "<td bgcolor=\"$row_color\" align=\"right\">\n";
    
            // View Button
            $rosterLink = $GLOBALS["roster_url"] . "/view_employee.jsf?contact_id=" . $contact_id;
            echo '<a href="' . $rosterLink . '"><IMG SRC="/images/button_command_tiny_view.gif" WIDTH="26" HEIGHT="13" BORDER="0" ALT="View Info" /></a>';
            
            echo "&nbsp;\n";
            
            // Delete Button
            echo '<a href="javascript:makePopUpWin(\'ACCT_delete_contact_page.php';
            echo "?account_number=" . $account_number;
            echo "&contact_id=" . $contact_id;
            echo "&role_id=" . $role_id;
            echo '\',280,400,\'\',4)"><IMG SRC="/images/button_command_tiny_delete.gif" WIDTH="26" HEIGHT="13" BORDER="0" ALT="Delete" /></a>';
            echo "</td>\n";
            echo "</tr>\n";
        }
    }

    $row_color_choice = 1 - $row_color_choice;
    $row_color = $row_color_choices[ $row_color_choice ];

    //now output the support team               
    $team_count = 0;
    echo "<tr>\n";
    echo "<td bgcolor=\"$row_color\" valign=\"top\">Support</td>\n";
    echo "<td colspan=\"3\"> \n";
    echo '<table border="0" cellspacing="0" cellpadding="1" bgcolor="#c0c0c0">';
    echo " <tr><td> \n";
    echo '<table border="0" cellspacing="0" cellpadding="1" bgcolor="#ffffff">';
    echo " <tr><td> \n";
    echo '<table border="0" cellspacing="0" cellpadding="1" bgcolor="#ffffff">';
    
    echo " <tr><td colspan=\"4\" bgcolor=\"#cccccc\"> ";
    echo " <a href=\"$py_app_prefix/account/team/popupSummary.esp?team_id=" . $onyx_account->getSupportTeamId() . "&noclose=1\">" . $onyx_account->getSupportTeamName() . "</a> ";
    echo " </td></tr>\n";
    
    $team = new ACCT_Team;
    $team->loadID( $onyx_account->getSupportTeamId() );        
    if (! empty( $team->_id ))
        $teamMembers = $team->getAllContacts();
    else
        $teamMembers = array();

    $row_color_choice = 1 - $row_color_choice;
    $row_color = $row_color_choices[ $row_color_choice ];
    
    //now output the team members
    foreach ( $teamMembers as $teamMember ) 
    {
        if( ! ( $team_count++ % 2 ))
            $color = $row_color;
        else
            $color = "#FFFFFF";
        
        $temp =& $teamMember->getPhoneNumbers();
        $phones = $temp->getByContactTypeID( CONTACT_PHONE_TYPE_PRIMARY);
        if( count( $phones ) >= 1 )
            $phone = $phones[0]->getFullNumber();
        else
            $phone = "NO PHONE";
        
        // Team Member
        echo "<tr>\n";
        echo "<td bgcolor=\"$color\"> " . $teamMember->getName() . " </td>\n";
        echo "<td bgcolor=\"$color\"> " . implode(", ", $team->getRoles($teamMember->_id)) . " </td>\n";
        echo "<td bgcolor=\"$color\"> " . $phone . " </td>\n";
        echo "<td bgcolor=\"$color\">\n";
        
        // View Button
		$rosterLink = $GLOBALS["roster_url"] . "view_employee.jsf?contact_id=" . $teamMember->_id;
        echo '<a href="' . $rosterLink . '"><IMG SRC="/images/button_command_tiny_view.gif" WIDTH="26" HEIGHT="13" BORDER="0" ALT="View Info" /></a>';
        echo ' <IMG SRC="/images/spacer.gif" WIDTH="26" HEIGHT="13" ALT="" />';
        echo "</td></tr>\n";
    }     
    
    //close the team table
    echo "</table>\n";
    echo "</td></tr></table>\n";
    echo "</td></tr></table>\n";
    echo "<!-- End a Team fallback -->\n";
    echo "</td></tr>\n";
}


// Local Variables:
// mode: php
// c-basic-offset: 4
// End:
?>
