<?php
require_once("CORE_app.php");
require_once("menus.php");
require_once("act/ActFactory.php");

$VAL_ERROR = '';

if ( isset($search_number) &&  ereg('[^0-9]', $search_number) ) {
# no non-digits allowed, sorry
    $VAL_ERROR = '<span style="color: red;">Invalid Account/Computer Number: '. $search_number. '</span><br />';
    unset($search_number);
}

?>
<HTML id="mainbody"
<HEAD>
    <TITLE>CORE: Information Search</TITLE>
    <LINK href="/css/core_ui.css" rel="stylesheet">
    <?=menu_headers()?>
</HEAD>
<?=page_start()?>
<BR>
<TABLE BORDER="1"
       CELLSPACING="0"
       CELLPADDING="0">
<TR>
	<TD>
		<TABLE BORDER="0"
		       CELLSPACING="2"
		       CELLPADDING="2">
		<TR>
			<TD COLSPAN="2"
			    BGCOLOR="#003399"
			    CLASS="hd3rev"> Information Search </TD>
		</TR>
		<TR>
			<TD COLSPAN="2"> <?=$VAL_ERROR?>Please enter an account number or server number: </TD>
		</TR>
		<TR>
			<TD VALIGN=TOP ALIGN=RIGHT>
			<FORM ACTION="<?=$PHP_SELF ?>">
			<INPUT TYPE="text"
			       NAME="search_number"
			       SIZE="5"></TD>
			<TD VALIGN=TOP>	   
			<INPUT TYPE="image"
			       SRC="/images/button_command_search_off.jpg"
			       ALIGN="texttop"
			       BORDER="0">
			</FORM></TD>
		</TR>
		</TABLE>
	</TD>
</TR>
</TABLE>
<?php

if(!empty($search_number)) {
    $found = false;
    $search_number = trim($search_number);
    $i_account = ActFactory::getIAccount();
    $onyx_account = $i_account->getAccountByAccountNumber($GLOBAL_db, $search_number);
    if($onyx_account) {        
        $account_name = $onyx_account->account_name;
        $account_number = $onyx_account->account_number;

        // Get the Account Manager
        $account_manager = $onyx_account->getAccountExecutive();
        if(empty($account_manager)) {
            $account_manager = "<font style=\"background: red; color: white\">None Assigned</font>";
        } else {
            $account_manager = $account_manager->getFullName();
        }

        $support_contact = $i_account->getSupportTeamContactNames($GLOBAL_db, $onyx_account->account_id, ACCOUNT_ROLE_SUPPORT);
        $support_contact = join( ", ", $support_contact );
    
        $team_name = $onyx_account->getSupportTeamName(true);
    
        if(!empty($support_contact) and !empty($team_name)) {
            $support_contact .= ", ";
        }
        $support_contact .= $team_name;

        $account_status_id = $onyx_account->status_id;
        $account_status = $onyx_account->getStatusName();
       
        switch( $account_status_id ) {
        case ACCOUNT_STATUS_NEW:
                $color = '"blue"'; break;
        case ACCOUNT_STATUS_ACTIVE:
                $color = '"darkgreen"'; break;
        case ACCOUNT_STATUS_DELINQUENT:
                $color = '"red"'; break;
        case ACCOUNT_STATUS_CLOSED:
                $color = '"#606060"'; break;
        default:
                $color = '"black"'; break;
        }

        $account_status = "<font color=$color>$account_status</font>";
?>
<BR>
<TABLE BORDER="1"
       CELLSPACING="0"
       CELLPADDING="0">
<TR>
	<TD>
		<TABLE BORDER="0"
		       CELLSPACING="2"
		       CELLPADDING="2"
			   WIDTH=400>
		<TR>
			<TD BGCOLOR="#003399"
			    CLASS="hd3rev"
				COLSPAN="2"> Match for Account #<?=$search_number ?> </TD>
		</TR>
		<TR>
			<TD CLASS=label BGCOLOR="#F0F0F0"> Account Name: </TD>
			<TD> <?=$account_name ?> </TD>
		</TR>
		<TR>
			<TD CLASS=label BGCOLOR="#F0F0F0"> Account Status: </TD>
			<TD> <?=$account_status ?> </TD>
		</TR>
		<TR>
			<TD CLASS=label BGCOLOR="#F0F0F0"> Account Executive: </TD>
			<TD> <?=$account_manager ?> </TD>
		</TR>
		<TR>
			<TD CLASS=label BGCOLOR="#F0F0F0"> Support: </TD>
			<TD> <?=$support_contact ?> </TD>
		</TR>
		<TR>
			<TD COLSPAN="2"> <?include('ACCT_view_all_contacts_include.php') ?> </TD>
		</TR>		
		</TABLE>
	</TD>
</TR>
</TABLE>
<?php

    $found = true;
    }
    
    $account_number = GetCustomerNumberByComputerNumber($GLOBAL_db, $search_number);
    if(!empty($account_number)) {
        $i_account = ActFactory::getIAccount();
        $onyx_account = $i_account->getAccountByAccountNumber($GLOBAL_db, $account_number);
    }
    else {
        $onyx_account = null;
    }

    if(!empty($onyx_account)) {
        $computer=new RackComputer;
        $computer->Init($account_number,$search_number,$GLOBAL_db);

        $account_name = $onyx_account->account_name;

        $accountExecutive =& $onyx_account->getAccountExecutive();
        if(empty($accountExecutive)) {           
            $account_manager = "!!NONE ASSIGNED!!";            
        } else {
            $account_manager = $accountExecutive->getFullName();
        }
        
        $supportContacts = $onyx_account->getSupportContacts();
        if(count($supportContacts) < 1) {
            $support_contact = "!!NONE ASSIGNED!!";
        } else {
            $support_contact = $supportContacts[0]->getFullName();            
            for($i=1; $i<count($supportContacts); $i++) {
                $support_contact .= ", " . $supportContacts[i]->getFullName();
            }
        }        
?>
<BR>
<TABLE BORDER="1"
       CELLSPACING="0"
       CELLPADDING="0">
<TR>
	<TD>
		<TABLE BORDER="0"
		       CELLSPACING="2"
		       CELLPADDING="2"
			   WIDTH=400>
		<TR>
			<TD BGCOLOR="#003399"
			    CLASS="hd3rev"
				COLSPAN="2"> Match for Server #<?=$search_number ?> 
				(<?=$computer->OS() ?>) </TD>
		</TR>
		<TR>
			<TD CLASS=label BGCOLOR="#F0F0F0"> Account #: </TD>
			<TD> <?=$account_number ?> </TD>
		</TR>		
		<TR>
			<TD CLASS=label BGCOLOR="#F0F0F0"> Account Name: </TD>
			<TD> <?=$account_name ?> </TD>
		</TR>
		<TR>
			<TD CLASS=label BGCOLOR="#F0F0F0"> Account Executive: </TD>
			<TD> <?=$account_manager ?> </TD>
		</TR>
		<TR>
			<TD CLASS=label BGCOLOR="#F0F0F0"> Support: </TD>
			<TD> <?=$support_contact ?> </TD>
		</TR>
		<TR>
			<TD COLSPAN="2"> <?include('ACCT_view_all_contacts_include.php') ?> </TD>
		</TR>		
		</TABLE>
	</TD>
</TR>
</TABLE>
<?php

        $found = true;
    }
    if(!$found) {
?><H2>No account or server found for number <?=$search_number ?></H2>
<?php
    }
}
?>
<?=page_stop()?>
</html>
