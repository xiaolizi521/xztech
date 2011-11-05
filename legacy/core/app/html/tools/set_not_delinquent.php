<?php

include( 'CORE.php' );

// Mark a list of accounts as being not delinquent

// we need to know who the user is 
// for logging purposes
$cid = GetRackSessionContactID();
$user = new CONT_Contact;
$user->loadID( $cid );

$i_account = ActFactory::getIAccount();

if ( ! in_dept( 'AR' )) 
{
    DisplayError('You do not have access to this page.');
}

$account_list = null;

include( 'tools_body.php' );

if (isset($_SESSION[ 'not_delinq_msg' ])) {
    $status_msg = @$_SESSION[ 'not_delinq_msg' ];
    if ( ! empty( $status_msg )) 
    {
        print $status_msg;
    }
    unset( $_SESSION[ 'not_delinq_msg' ] );
}

include('form_wrap_begin.php');
?>
<FORM ACTION="set_not_delinquent_handler.php?account_list=<?=@$account_list?>" METHOD=POST>
<B>Reactivate Pending A/R Account Numbers:</B><BR>
Entering a list of numbers into this form will <b>reactivate</b> those accounts.<br>
<I>(Enter one Account Number per line)</I><BR>
<TEXTAREA COLS=40 ROWS=20 NAME=account_list></TEXTAREA>
<BR>
<INPUT TYPE=SUBMIT VALUE="Reactivate Pending A/R Accounts">
</FORM>

<? include('form_wrap_end.php'); ?>
<?=page_stop()?>
</html>
