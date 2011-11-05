<?php

include('CORE.php');

$msg = "";

$cid = GetRackSessionContactID();
$user = new CONT_Contact;
$user->loadID( $cid );

$i_account = ActFactory::getIAccount();

if ( ! empty( $account_list )) 
{
    // validate all account numbers
    $success = true;
    $account_array = array();
    $pre_account_array = explode( "\n", $account_list );
    $dup_account_array = array();
    
    foreach ( $pre_account_array as $account_number ) 
    {
        if ( empty( $dup_account_array[ trim( $account_number ) ] )) 
        {
            $account_number = trim( $account_number );
            // skip blank lines
            if ( $account_number == "" ) 
            {
                continue;
            }
            if ( preg_match( "/[^0-9]+/", $account_number )) 
            {
                $success = false;
                break;
            }
            $result = $db->SubmitQuery('
                SELECT "ID"
                FROM "ACCT_Account"
                WHERE "AccountNumber" = TEXT(' . $account_number . ')'
                );
                
            if ( $result->numRows() != 1 ) 
            {
                $msg .= "<H1><FONT COLOR=RED>Account number $account_number is invalid.</FONT></H1>";
            }
            else
            {
                $dup_account_array[ $account_number ] = 1;
                $account_array[] = $account_number;
            }
        }
    }
    
    reset( $account_array );
    foreach ( $account_array as $account_number ) 
    {              
        $account = $i_account->getAccountByAccountNumber( $db, $account_number );
        $account->setDelinquent();
        $msg .= "<H1><FONT COLOR=BLACK>Account number $account_number is set delinquent.</FONT></H1>";
    }
    
    $_SESSION[ 'delinq_msg' ] = $msg;
    
    ForceReload( "set_delinquent_accounts.php" );
}
?>
