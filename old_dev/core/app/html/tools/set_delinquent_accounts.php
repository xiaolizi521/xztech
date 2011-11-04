<?php

include('CORE.php');

// we need to know who the user is 
// for logging purposes
$cid = GetRackSessionContactID();
$user = new CONT_Contact;
$user->loadID( $cid );

$i_account = ActFactory::getIAccount();

if ( ! in_dept( 'AR' )) 
{
    DisplayError( 'You do not have access to this page.' );
}

$account_list = null;

include( 'tools_body.php' );

if (isset($_SESSION[ 'delinq_msg' ])) {
    $status_msg = @$_SESSION[ 'delinq_msg' ];
    if ( ! empty( $status_msg )) 
    {
        print $status_msg;
    }
    unset( $_SESSION[ 'delinq_msg' ] );
}

include('form_wrap_begin.php');
?>

<FORM ACTION="set_delinquent_accounts_handler.php?account_list=<?=@$account_list?>" METHOD=POST>
<B>Set Pending A/R Account Numbers:</B><BR>
Entering a list of numbers into this form will <b>set</b> those accounts Pending A/R.<br>
<I>(Enter one Account Number per line)</I><BR>
<TEXTAREA COLS=40 ROWS=20 NAME=account_list></TEXTAREA>
<BR>
<INPUT TYPE=SUBMIT VALUE="Set Pending A/R Accounts">
</FORM>

<? include('form_wrap_end.php'); ?>
<?=page_stop()?>
</html>
