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
    $pre_account_array = array_unique( explode("\n", $account_list) );
    $not_delinquent_array = array();

    foreach ( $pre_account_array as $account_number ) 
    {
        $account_number = trim( $account_number );
        
        // skip blank lines
        if ( $account_number == "" ) 
        {
            continue;
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
            $account_array[] = $account_number;
        }
    }
    
    reset( $account_array );
    foreach ( $account_array as $account_number ) 
    {  
        $account = $i_account->getAccountByAccountNumber($db, $account_number);
        $account->setNotDelinquent();            
        $msg .= "<H1><FONT COLOR=BLACK>Account number $account_number is set not delinquent.</FONT></H1>";
    }
    
    $_SESSION[ 'not_delinq_msg' ] = $msg;
    
//    ForceReload( "set_not_delinquent.php?status_msg=$msg" );
    ForceReload( "set_not_delinquent.php" );
}
?>
