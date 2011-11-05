<?php
require_once("CORE_app.php");
require_once("class.mailer.php");
require_once("xmlrpc_api.php");
require_once("act/ActFactory.php");
require_once("computerStatus.php");

function compromisedServerPrivateComment($ticket_number) {
    global $GLOBAL_db;

    $ticket_id = $GLOBAL_db->getVal( 'SELECT
                        "TCKT_TicketID"
                    FROM
                        "TCKT_Ticket" ticket
                    WHERE
                        ticket."ReferenceNumber" = \''
                        . $ticket_number . "'" );
    if( empty($ticket_id) ) {
        trigger_error( "Unable to lookup compromised server ticket, something has gone wrong", FATAL );
    }
    $parser =& new core_parser;
    $body = $parser->parseMessageLabel("Comp Private Tkt");
    $body = str_replace( "\r\n","\n", $body );
    $body = str_replace( "\r","\n", $body );
    $subject = $parser->subject;
    addTicketMessage( $ticket_number, $body, GetRackSessionContactID(), 1, 0 );
    return;
}

function putServerIntoCompromisedAppLevel( $body, &$computer, $ticket_number='') {
    $ticket_queue = $computer->account->getSupportSegmentQueue();
    $computer_number = $computer->computer_number;
    $subject = "Server $computer_number Compromised at App Level";
    $ticket_category = 1892;
    if ( !empty( $ticket_number )) {
        addTicketMessage( $ticket_number,
                            $body,
                            GetRackSessionContactID(),  #source_contact
                            0, # is_private_message
                            0  # send_message_text
                            );
    }
    else {
        $ticket_number = $computer->GenTicket2(
            $ticket_queue,
            $ticket_category,
            TICKET_SEVERITY_EMERGENCY,
            $subject,
            $body,
            0, // is private
            0, // is internal
            TICKET_PRIORITY_HIGHEST,
            GetRackSessionContactID(GetRackSessionEmployeeNumber())
        );

        // pseudo:  Using general routine emailAllOnAccount
        // pseudo:+ SUBJECT: "Server $computer_number Compromised at App Level"
        // pseudo:+ BODY: ?

        $computer->account->emailAllOnAccount($subject, $body);
    }
    compromisedServerPrivateComment($ticket_number);
    return $ticket_number;
}

function putServerIntoCompromisedSystemLevel( $body, &$computer, $ticket_number='') {
    $ticket_queue = $computer->account->getSupportSegmentQueue();
    $computer_number = $computer->computer_number;
    $subject = "Server $computer_number Compromised at System Level";
    $ticket_category = 1891;
    if ( !empty( $ticket_number )) {
        addTicketMessage( $ticket_number,
                            $body,
                            GetRackSessionContactID(),  #source_contact
                            0, # is_private_message
                            0  # send_message_text
                            );
    }
    else {
        $ticket_number = $computer->GenTicket2(
            $ticket_queue,
            $ticket_category,
            TICKET_SEVERITY_EMERGENCY,
            $subject,
            $body,
            0, // is private
            0, // is internal
            TICKET_PRIORITY_HIGHEST,
            GetRackSessionContactID(GetRackSessionEmployeeNumber())
        );

        // pseudo:  Using general routine emailAllOnAccount
        // pseudo:+ SUBJECT: "Server $computer_number Compromised at System Level";
        // pseudo:+ BODY: ?

        $computer->account->emailAllOnAccount($subject, $body);
    }
    compromisedServerPrivateComment($ticket_number);
    return $ticket_number;
}

if( empty( $customer_number ) and !empty($computer_number) ) {
    $customer_number = $db->GetVal("
        select customer_number
        from server
        where computer_number = $computer_number");
}
$dont_let_them_upgrade_reserve=false;
if( empty($customer_number) or empty($computer_number) ) {
    DisplayError("Unable to display this customer/computer because you are missing the customer_number or computer_number");
    exit();
}

$computer=new RackComputer;
$computer->Init($customer_number,$computer_number,$db);
if( !$computer->IsComputerGood() ) {
    DisplayError("Unable to load any information about computer number $computer_number This computer may no longer exist.  If you continue to have problems contact the database administrator");
}
if (!isset($reason)) {
    $reason="";
}
$reason = stripslashes( $reason );
if (!isset($ticket_num)) {
    $ticket_num="";
}
$ticket_num = trim( $ticket_num );
if (!isset($auto_assign_ip)) {
    $auto_assign_ip="";
}
if (!isset($migr_new_server)) {
    $migr_new_server="";
}
if (!isset($migr_days)) {
    $migr_days="";
}



MakeNotEmpty($command);
switch ($command) {
    case "MONITOR_OFF":
        $computer->MonitoringOff();
        break;
    case "RESEND_VAL_ADD":
        $computer->sendOnlineNotice();
        if ($computer->hasManagedBackupClient()) {
            $computer->genManagedBackupClientTicket();
        }

        break;
    case "RESEND_ONLINE":
        if ( in_array($computer->WhatOS(),array("Firewall - Cisco ASA", "Firewall - Cisco PIX","Load-Balancer","Netscreen")) ) {
            // No online message sent for firewalls since
            // the customer does not get any login information
            // and a customer must already have a server to
            // use a firewall.
            DisplayError('Firewalls and Load Balancers do not have'
                . ' an Online/Complete message.'
                . ' The security or networking tech is responsible for'
                . ' notifying the customer.');
        }
        $computer->sendCustOnlineNotice(true);
        // the following has been removed from sendCustomerOnlineNotice
        // $this->_sendSupportRackwatchNotice();
        // We COULD call it here, but I don't think we actually want to.

        break;
    case "MONITOR_ON":
        $computer->MonitoringOn();
        break;
    case "MARK_BUILD_TECH":
        $computer->SetBuildTech( GetRackSessionUserid() );
        break;
    case "MARK_AUDITED":
        $computer->MarkAudited();
        break;
    case "MARK_COMPLETE":
        $computer->MarkComplete();
        break;
    case "TURN_OFF_ONE_HOUR":
        $computer->OneHourFlagOff();
        break;
    case "DELETE_COMPUTER":
        $result = $db->SubmitQuery("
            SELECT sec_finished_order, sec_contract_received
            FROM sales_speed
            WHERE computer_number = $computer_number
        ");
        if ($result->numRows()) {
            $sales_speed = $result->fetchArray(0);
            if( $computer->getData("status_number") >= STATUS_RECEIVED_CONTRACT
                or $sales_speed['sec_contract_received'] > 0
                or $sales_speed['sec_finished_order'] > 0) {

                DisplayError('You cannot delete a computer if we'
                             . ' have ever received a contract for it.');
            }
        }
        //now test to see if we remove the customer as well
        $computer->Delete(); // this uses a transaction


        JSForceReload("/ACCT_main_workspace_page.php?account_number=$account_number");
        break;
    case "QUICK_COMMENT":
        $computer->Log($quick_comment);
        break;
    case "UPGRADE_STATUS":
        $auto_assign_ip = empty($dont_auto_assign_ip);
        upgradeComputerStatus($computer, $new_status, $reason, $ticket_num, $auto_assign_ip, $migr_new_server, $migr_days);

        // redirect to firewall organization page if there's a firewall on the account
        if ($new_status == STATUS_RECEIVED_CONTRACT)
        {
            $customer= new RackCustomer;
            $customer->Init($customer_number, $db);
            $customer->LoadComputers();
            foreach ($customer->computer_list as $comp)
            {
                $comp->LoadStatusInfo();
                if ($comp->isNetDevice()
                    and ($comp->data['status_number'] == STATUS_ONLINE || $comp->computer_number == $computer_number))
                {
                    $firewall_return = urlencode("DAT_display_computer.php3?"
                        . "account_number=$customer_number"
                        . "&customer_number=$customer_number"
                        . "&computer_number=$computer_number");
                    ForceReload("/tools/organize_firewall.php?account_number=$customer_number&firewall_return=$firewall_return");
                    exit();
                }
            }
        }
        ForceReload("DAT_display_computer.php3?"
                     . "account_number=$customer_number"
                     . "&customer_number=$customer_number"
                     . "&computer_number=$computer_number");
         exit();
         break;
     case "DOWNGRADE_STATUS":
         switch (downgradeComputerStatus($computer, $new_status, $reason, $ticket_num, $auto_assign_ip)) {
             case 0: // Success
                 break;
             case -1:
                 DisplayError("Server cannot be downgraded below Offline/No longer Active");                                                          
               break;                                                            
             case -2:
                 // Don't DowngradeStatus until comments have been made.
                ForceReload("cancel_computer.php3?command=CONFIRM_CANCELLATION&computer_number=$computer_number");
               break;                                                                                                                                
          }
                                                                                                                               
          break;                                                         
                                                       
}

JSForceReload("/ACCT_main_workspace_page.php?computer_number=$computer_number");
?>
