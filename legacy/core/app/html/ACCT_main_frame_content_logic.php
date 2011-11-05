<?php
require_once('CORE_app.php');
require_once("class.mailer.php");
require_once("class.parser.php");
require_once("act/ActFactory.php");

define("MAX_NOTES_ON_PAGE", 5);

$large_opportunity_reps = '';
$is_large_opportunity_account = '';

$account_name = '';
$account_manager = '';
$bdc = '';
$sla_type = '';
$support_contact = '';
$source = '';
$account_status = '';
$account_created = '';
$notes = '';
$contact_id = '';
$rackspace101 = '';
$welcomepacket = '';
$customer_profitability = '';
$delinquent_account = '';
$segment = '';
$closed_revenue = '';
$sales_territory= '';
$company_type_name= '';
global $external_contacts;
global $internal_contacts;
global $onyx_account;
$i_account = ActFactory::getIAccount();
$i_contact = ActFactory::getIContact();
$onyx_account = $i_account->getAccountByAccountNumber($GLOBAL_db, $account_number);

if($onyx_account->account_number != "") {

    //$external_contacts = $onyx_account->getExternalContacts();
    $internal_contacts = $onyx_account->getInternalContacts();
    $account_name = $onyx_account->account_name;
    $account_emergency_instructions = $onyx_account->emergency_instructions;
    $account_id = $onyx_account->account_id;

    $lors =  $onyx_account->getLORs($GLOBAL_db);
    $is_large_opportunity_account = $onyx_account->isLargeOpportunityAccount($GLOBAL_db);
    $large_opportunity_reps = '';
    for ( $i=0; $i < count($lors); $i++  ) {
        $large_opportunity_reps.= $lors[$i]->formatted_name."<BR>";
    }

    $hack101 = $i_account->getRackspace101($GLOBAL_db, $onyx_account->account_id);
    if ( $hack101->rackspace101_ticket_number != "" ) {
        $rs101_ticket_number = $hack101->rackspace101_ticket_number;
        $rs101_ticket_status_name = $hack101->rackspace101_ticket_status;
        $rackspace101 = "<a target=\"_new\" href=\"/py/ticket/view.pt?ref_no=$rs101_ticket_number\">$rs101_ticket_number</a> ($rs101_ticket_status_name)";
    } else {
        if( $onyx_account->status_id != 'New' ) {
            $rackspace101 = "No Ticket: Try <tt>Action -&gt; View Account Log</tt>";
        } else {
            $rackspace101 = "Not Online";
        }
    }
    $customer_profitability = $onyx_account->customer_profitability;
        
    $sla = $onyx_account->getSlaType();
    $sla_id = $sla->getId();   
    $sla_type = $sla->getName();     

    $companyStatusLookups = $i_account->getLookupValues('company.status');
    for($i=0; $i<count($companyStatusLookups); $i++) {
        if ($onyx_account->status_id == $companyStatusLookups[$i]->parameter_id) {
        $account_status = $companyStatusLookups[$i]->desc;
        }
    }

    
    $company_type_name = $onyx_account->company_type_name;
    
    $segment = $onyx_account->segment_name;
    #$temp = $account->getAccountStatus();
    if ($account_status == 'Pending A/R') { # is the account delinquent?
        $account_status = "<span style='font-weight: bold; font-size: large; color: red; text-decoration: blink;'>".$account_status." Account</span>";
        $delinquent_account = $account_status;
    } 
    $account_created = $onyx_account->account_created_date;

    if( in_dept("CORE") ) {
        $deletable = true;
    } else {
        $deletable = false;
    }

    $company_id = $onyx_account->crm_company_id;
    $accountNotes = $onyx_account->getNotes(true, MAX_NOTES_ON_PAGE);    
    $noteCount = 0;       
    
    $fgcolor = "#FF6666";
    $bgcolor = "#FFCCCC";
    foreach ($accountNotes as $a_note) {
        if(!$a_note->userComment) {
            continue;
        }
        $noteCount++;
        if($noteCount <= MAX_NOTES_ON_PAGE) {
            $note_id = $a_note->notePrimaryId;
            $name = $a_note->getUserIdentifier();
            $date = $a_note->insertDate;
            $text = $a_note->noteText;
            $notes .= '<TABLE WIDTH="160" BORDER="0" CELLSPACING="0" CELLPADDING="0">
                          <TR>
                            <TD VALIGN="top"
                                BGCOLOR="'.$fgcolor.'">
                                <IMG SRC="/images/note_corner_red.gif"
                                     WIDTH="10"
                                     HEIGHT="10"
                                     HSPACE="0"
                                     VSPACE="0"
                                     BORDER="0"
                                     ALIGN="TOP"
                                     ALT="">';
                                if( $deletable ) {
                                    $notes .= '<br><a href="javascript:makePopUpNamedWin(\'/ACCT_delete_note_popup.php?account_number='.$account_number.'&note_id='.$note_id.'\',370,300,\'\',4,\'delete_acct_note\')">' .
                                         '<img src="/images/ex-small.gif" width="10" height="10" border="0" alt="Delete" valign="top" VSPACE="2" HSPACE="1"></a>';
                                }
                                $notes .= '</TD>
                        	<TD BGCOLOR="'.$fgcolor.'">' .$name. ' 
                        	<BR> ' . $date . ' CDT </TD>
                          </TR>
                          <TR>
                          	<TD WIDTH="10" BGCOLOR="'.$bgcolor.'"> &nbsp; </TD>
                            <TD BGCOLOR="'.$bgcolor.'">' . $text . '</TD>
                          </TR>
                        </TABLE><BR CLEAR="all">
                        ';
        }
    }
    if($noteCount > MAX_NOTES_ON_PAGE) {
        $notes .= "<A HREF=\"javascript:makePopUpNamedWin('/ACCT_notes_popup_page.php?account_number=$account_number&ucOnly=t',500,250,'',3,'view_notes')\">More notes...</A>\n";
    }
   
    // Get the Business Development Consultant
    // Account executive is now assigned when server goes to Contract Received
    $bdc = $onyx_account->getBusinessDevelopmentConsultant();
    if( $bdc == "" ) {
        $bdc = "<font style=\"background: red; color: white\">None Assigned</font>";
    } else {
        $bdc = $bdc->formatted_name;
    }
    if(isTeamLeader() and $onyx_account->segment_id != INTENSIVE_SEGMENT) {
        $bdc .= ' <a href="javascript:makePopUpNamedWin('.
             "'/ACCT_recalculate_bdc_handler.php?account_id=".
             "$account_id',300,300,'',3,'RecalcBDC'".
             ')" class="text_button" style="font-size:x-small">Recalc</a>';
    }

    // Get the Account Manager
    // Account executive is now assigned when server goes to Contract Received
    $account_manager = $onyx_account->getAccountExecutive();
    if( $account_manager == "" ) {
        $account_manager = "<font style=\"background: red; color: white\">None Assigned</font>";
    } else {
        $account_manager = $account_manager->formatted_name;
    }

    if(isTeamLeader() and $onyx_account->segment_id != INTENSIVE_SEGMENT) {
        $account_manager .= ' <a href="javascript:makePopUpNamedWin('.
             "'/ACCT_recalculate_account_manager_handler.php?account_id=".
             "$account_id',300,300,'',3,'RecalcAcctMgr'".
             ')" class="text_button" style="font-size:x-small">Recalc</a>';
    }

    // Get the Support Team
    $support_contact = $i_account->getSupportTeamContactNames($GLOBAL_db, $account_id, ACCOUNT_ROLE_SUPPORT);
    $support_contact = join( ", ", $support_contact );
    
    $team_name = $onyx_account->getSupportTeamName(true);
    
    if(!empty($support_contact) and !empty($team_name)) {
        $support_contact .= ", ";
    }
    $support_contact .= $team_name;

    if(isTeamLeader() and $onyx_account->segment_id != INTENSIVE_SEGMENT) {
        $support_contact .= ' <a href="javascript:makePopUpNamedWin('.
             "'/ACCT_recalculate_support_team_handler.php?account_id=".
             "$account_id',300,300,'',3,'RecalcSupportTeam'".
             ')" class="text_button" style="font-size:x-small">Recalc</a>';
    }

    if( empty($support_contact) ) {
        $support_contact = "!!NONE ASSIGNED!!";
    }

} else {

    checkDataOrExit(array( "account_number" => array("The account number" ,
                              1, array("data_number"))));
//They have a number for the account number - it must not be in the database
    require_once("menus.php");
    echo "<html>";
    print "<head>\n";
    print menu_headers();
    print "<LINK HREF=\"/css/core2_basic.css\" REL=\"stylesheet\">";
    print "</head>";
	print page_start();
    echo '<p style="text-align: center; margin-top: 30%;">';
    echo "Sorry CORE was unable to load any information about\n";
    echo "<font color=\"red\">account number #$account_number</font>.";
    set_title("Account #$account_number Not Found");
    print page_stop();
    echo "</html>\n";
    exit();
}

// Local Variables:
// mode: php
// c-basic-offset: 4
// End:
?>
