<?

require_once("xmlrpc_core_api.php");

// Several functions were moved from cancel_computer.php3 as they were only
// ever used in this file and I didn't want to have to copy it to
// the newly created cancel_multi_computer.php

function SendConfirmation($computer, $cancel_data)
// Send a confirmation message to customers
// who have been suspended, UNLESS suspention
// is due to AUP violation or non-payment.
{
    // I would have passed these as parameters,
    // but it looks like the other functions
    // in this file use a lot of global data too.
    GLOBAL $db;

    $is_AUP = $db->GetVal("
        select 1
        from offline_reasons
        where reason_number = $cancel_data[reason_number]
        and category_group in ( 500, 2900)
        limit 1
        ");

    $is_NonPay = $db->GetVal("
        select 1
        from offline_reasons
        where reason_number = $cancel_data[reason_number]
        and category_group in ( 400, 3000 )
        limit 1
        ");

    // Set up Mail Message
    $parser = new core_parser;
    $mailer = new core_mailer;
    $mailer->SetAccountNumber( $computer->customer_number );
    $mailer->AddAccountAddress(ONYX_ACCOUNT_ROLE_PRIMARY);
    $mailer->SetAccountFrom(ACCOUNT_ROLE_ACCOUNT_EXECUTIVE);

    $target_date = $cancel_data["sec_due_offline"];
    $target_date = date("D F j, Y", $target_date);
    $parser->AddVar('target_date', $target_date);

    $parser->SetAccount($computer->account);
    $parser->SetComputer($computer);

    if( $is_NonPay ) {
        // this call actually set variables inside th parser
        //hence it needs to come before access to "subject"
        $mailer->Body = $parser->ParseTextMessage( 27 );
        $mailer->Subject = $parser->subject; // "Device Cancellation: Failure to Pay";

        // pseudo: SUBJECT: "Device Cancellation: Failure to Pay"
        // pseudo:+ FROM: AE
        // pseudo:+ TO:  Primary Contact
        // pseudo:+ BODY: Prefab(27)
        // pseudo:+ is NonPay
        // pseudo:+ Send a confirmation message to customers who have been suspended, unless nonpay

        $mailer->Send();
    } elseif ( $is_AUP ) {
        // Do Nothing
    } else { // Normal Cancellation
        error_log("**normal cancellation\n");
        $mailer->Body = $parser->ParseTextMessage( 12 );
        $mailer->Subject = $parser->subject; // "Device Cancellation Confirmation";

        // pseudo: SUBJECT: "Device Cancellation Confirmation"
        // pseudo:+ FROM: AE
        // pseudo:+ TO:  Primary Contact
        // pseudo:+ BODY: Prefab(12)
        // pseudo:+ is AUP
        // pseudo:+ Send a confirmation message to customers who have been suspended, unless AUP
        $mailer->Send();
    }
}

function YesNo($boolean)
{
    if ($boolean == "1" or $boolean == "t")
    {
        return "Yes";
    }
    else if ($boolean == "0" or $boolean == "f")
    {
        return "No";
    }
    else
    {
        return "Unknown";
    }
}

function CancellationInfoString($cancel_data)
{
    return("\nOFFLINE DUE DATE: "
	   . strftime("%m/%d/%Y", $cancel_data['sec_due_offline'])
	   . "\nREASON CATEGORY: "
	   .getReasonCategory($cancel_data['reason_number']).": "
	   . $cancel_data['reason_category']
	   . "\nREASON INFO: \n"
	   . $cancel_data['reason_info']
	   . "\nCONTINUING HOSTING:" . YesNo($cancel_data['continue_hosting'])
	   . "\nFUTURE HOSTING METHOD: $cancel_data[future_hosting_method]"
	   . "\nSWITCHED TO COMPETITOR: "
	   . YesNo($cancel_data['chose_competitor'])
	   . "\nCOMPETITOR: $cancel_data[competitor]"
	   . "\nCOMPETITOR BENEFITS: \n"
	   . $cancel_data['competitor_benefits']
	   . "\nRACKSPACE IS FUTURE CANDIDATE: "
	   . YesNo($cancel_data['future_consideration'])
	   . "\nPRESERVE PRIMARY IPS: "
	   . YesNo($cancel_data['preserve_primary_ip'])
	   . "\nPRESERVE ADDITIONAL IPS: "
	   . YesNo($cancel_data['preserve_additional_ip'])
	   . "\nIP PRESERVATION NOTES: \n"
	   . $cancel_data['ip_notes']
	   );
}

function LogCancellation(
    $computer, $message, $cancel_data)
{
    $computer->Log($message
        . CancellationInfoString($cancel_data));
}

//Given a reason number, it resolves the numeric category group to a name
function getReasonCategory($reason_number)
{
    global $db;
    $category_group_info  = $db->GetVal("
        SELECT
            category_group_name
        FROM
            offline_reasons
            JOIN offline_reason_groups USING (category_group)
        WHERE
            reason_number = $reason_number");

    return($category_group_info);
}

function handleCancellation(
    $form, $computer, $already_queued, $already_queued_data, $account_number) {
# Form Data:
#   Also table cancelled_server_info
#  create table queue_cancel_server
#  (
#   sec_created            int4,
#   sec_last_mod        int4,
#   sec_due_offline        int4,
#   computer_number        int4,
#   reason_number         int2,
#   reason_info            text,
#   chose_competitor    bool,
#   competitor_benefits    text,
#   support_incidents    text,
#   completed            boolean default 'f',
#    billing_updated        boolean default 'f',
#
#   continue_hosting    boolean
#   future_hosting_method text,
#   competitor text,
#   future_consideration boolean
#   preserve_primary_ip  boolean
#   preserve_additional_ip  boolean
#   ip_notes             text
#  );

###########################################################################
### DATA CHECKING and preparation te enter the data in the database.   ###
### Unless there is an error, the script will redirect to the          ###
### display_computer.php3 when it is done processing the cancellation. ###
##########################################################################
global $SCHEDULE_HOUR;
global $COMMAND_QUEUE_CANCELLATION;
global $COMMAND_UNQUEUE_CANCELLATION;
global $COMMAND_OVERRIDE;
global $COMMAND_CONFIRM_CANCELLATION;
global $COMMAND_CANCEL_NOW;
global $MOVING_QUESTION;
global $MAX_DAYS_IN_ADVANCE;
global $db;

$computer_number = $computer->computer_number;

extract($form);

if($already_queued and
    ($_POST['command'] != $COMMAND_OVERRIDE and $_POST['command'] != $COMMAND_CANCEL_NOW))
{
    # If not overriding, the user may mistakenly be
    # mistakenly marking the server as cancelled again.
    return("Computer #$computer->customer_number-$computer->computer_number is already marked as cancelled. You can modify the current data or unmark the computer as cancelled, but you must first reload the cancellation form.");
}

###
### Data Checking
###

if (empty($reason_number) or $reason_number == "false") {
    return("No option from the \"reason for canceling\" menu is selected. Select a Category AND Type.");
}

$now_sec = time();
$now_date = getdate($now_sec);
$today_sec = mktime(0, 0, 0,
    $now_date['mon'], $now_date['mday'], $now_date['year']);

if ($_POST['command'] == $COMMAND_CONFIRM_CANCELLATION)
{
    if($already_queued)
        $sec_due_offline = $already_queued_data['sec_due_offline'];
    else
        $sec_due_offline = $now_sec;
}
else if(isset($month))
{
    $sec_due_offline = mktime($SCHEDULE_HOUR, 0, 0, $month, $day, $year);
}

if ( !empty($sec_due_offline) and
        $sec_due_offline < $today_sec
        and $_POST['command'] != $COMMAND_CANCEL_NOW
        and !$already_queued)
{
    # Past is invalid
    return("The due-offline date cannot be before today.");
} elseif( !empty($sec_due_offline) and
          $sec_due_offline > ($today_sec + (3600 * 24 * $MAX_DAYS_IN_ADVANCE))) {
    # $MAX_DAYS_IN_ADVANCE days is the farthest date in the future
    return("You cannot schedule a cancellation "
        . "more than $MAX_DAYS_IN_ADVANCE days in advance.");
}

if ($future_hosting_method == 'Rackspace Hosting')
{
    if (strlen($reason_info) < 20)
        $reason_info = 'Continuing to host with Rackspace.';
    $chose_competitor = 'f';
    $future_consideration = 't';
}

if ($continue_hosting == 'f')
{
    if (strlen($reason_info) < 20)
        $reason_info = 'Not continuing to host.';
    $future_hosting_method = 'None';
    $chose_competitor = 'f';
}

if (strlen($reason_info) < 20)
{
    return("Please enter more info on how we could prevent the customer from leaving.");
}

if ($continue_hosting != "t" and $continue_hosting != "f")
{
    return("Invalid answer to question regarding continued hosting.");
}

if ($continue_hosting == "f" and $chose_competitor != "t") {
    $chose_competitor = "f";
}

if ($chose_competitor != "t" and $chose_competitor != "f")
{
    return("Invalid answer to \"$MOVING_QUESTION\" ($chose_competitor)");
}

if ($future_consideration != "t" and $future_consideration != "f")
{
    return("Invalid answer to question regarding future consideration of Rackspace for hosting.");
}

if ($preserve_primary_ip != "t" and $preserve_primary_ip != "f")
{
    return("Invalid answer to question regarding preservation of primary IPs in the current configuration.");
}

if ($preserve_additional_ip != "t" and $preserve_additional_ip != "f")
{
    return("Invalid answer to question regarding preservation of additional IPs in the current configuration.");
}

if ( empty($notification_checkbox) and $_POST['command'] != $COMMAND_CANCEL_NOW)
{
    return('You must check the box "I have notified the customer"');
}

###
### End of Data Checking
###

$insert_data = array(
    "computer_number" => $computer_number,
    "reason_number" => $reason_number,
    "reason_info" => $reason_info,
    "chose_competitor" => $chose_competitor,
    "competitor_benefits" => $competitor_benefits,
    "sec_last_mod" => $now_sec,
    "continue_hosting" => $continue_hosting,
    "future_hosting_method" => $future_hosting_method,
    "competitor" => $competitor,
    "future_consideration" => $future_consideration,
    "preserve_primary_ip" => $preserve_primary_ip,
    "preserve_additional_ip" => $preserve_additional_ip,
    "ip_notes" => $ip_notes
    );
if( !empty($sec_due_offline) ) {
        $insert_data["sec_due_offline"] = $sec_due_offline;
}

if ($_POST['command'] == $COMMAND_CANCEL_NOW)
{
    if($already_queued)
        $insert_data['sec_due_offline'] = $already_queued_data['sec_due_offline'];
    else
        $insert_data['sec_due_offline'] = $now_sec;
}

$is_online = $db->TestExist("select computer_number
    from server
    where computer_number=$computer_number
        and status_number >= 12
    ");

$reason_category = $db->GetVal("select reason_category
    from offline_reasons
    where reason_number = '$reason_number'");


### No more data checking.
### Final insert into the database.

$db->BeginTransaction();
if ($already_queued)
{
### Modify Cancellation Queue data
    $db->Update("queue_cancel_server", $insert_data,
        "computer_number=$computer_number
            and oid = $already_queued_data[oid]::oid"); # escape=true
    $cancel_data = $insert_data;
    $cancel_data['reason_category'] = $reason_category;
    LogCancellation($computer,
        "Updated computer's cancellation queue data.",
        $cancel_data);
    if ($insert_data['sec_due_offline'] != $already_queued_data['sec_due_offline'])
    {
        $subject = "Cancellation Re-Scheduled - $computer_number";
        $body = "The following device has been RE-scheduled "
             . "for cancellation:\n"
             . "\nComputer: $computer->customer_number-$computer_number"
             . "\nNEW Offline due date: "
             . strftime("%b %d, %Y", $insert_data['sec_due_offline'])
             . "\nOLD Offline due date: "
             . strftime("%b %d, %Y", $already_queued_data['sec_due_offline'])
             . "\nReason category: " . getReasonCategory($reason_number);
        $computer->GenTicket2(
            QUEUE_ACCOUNTING,
            TICKET_CATEGORY_ACCOUNTING_CANCELLATIONS,
            TICKET_SEVERITY_MODERATE,
            $subject,
            $body,
            1,
            1
        );
        ## If it is a PrevenTier device create a ticket for Backbone Engg team
        if ($computer->OS() == "PrevenTier")
        {
            $computer->GenTicket2AfterCommit(
                QUEUE_NETWORKING,
                TICKET_CATEGORY_PREVENTIER,
                TICKET_SEVERITY_MODERATE,
                $subject,
                $body,
                1,
                1
            );
        }
    }
}
else
{
### Insert Cancellation Queue data for the first time
    $insert_data["sec_created"] = $now_sec;
    $db->Insert("queue_cancel_server", $insert_data); #escape=true
    $cancel_data = $insert_data;
    $cancel_data['reason_category'] = $reason_category;
    LogCancellation(
        $computer, "Computer queued for cancellation.",
        $cancel_data);

### Send a confirmation message to the customer
    SendConfirmation($computer, $cancel_data);
### Send notification to the CRM (Customer Retention) dept.
    $cancel_data = $insert_data;
    $cancel_data['reason_category'] = $reason_category;
    $computer->sendScheduledForCancelNotice();

    $computer->GenTicket2(
        QUEUE_ACCOUNTING,
        TICKET_CATEGORY_ACCOUNTING_CANCELLATIONS,
        TICKET_SEVERITY_MODERATE,
        "Cancellation Scheduled - $computer_number",
        "The following device has been scheduled for cancellation:\n"
        . CancellationInfoString($cancel_data),
        1,
        1
    );
## This was added due to Jira prov-296 as part of the Schedule for Cancellation Process
    if($computer->isSharedStorage() ){
	    $computer->GenTicket2(
	    	QUEUE_ENG_MGD_STORAGE_NAS,
	    	TICKET_CATEGORY_ENGG_MANAGED_STORAGE_OTHER,
	    	TICKET_SEVERITY_MODERATE,
	    	"UNAS Device Schedule for Cancellation: #$account_number-$computer_number",
	    	"UNAS Device Scheduled for Cancellation",
	    	1,
	    	1
	    );
    }


    ## If it is a PrevenTier device create a ticket for Backbone Engg team
    if ($computer->OS() == "PrevenTier")
    {
        $computer->GenTicket2AfterCommit(
            QUEUE_NETWORKING,
            TICKET_CATEGORY_PREVENTIER,
            TICKET_SEVERITY_MODERATE,
            "Cancellation Scheduled - $computer_number",
            "The following device has been scheduled for cancellation:\n"
            . CancellationInfoString($cancel_data),
            1,
            1
        );
    }

    // send a ticket to Billing
    // see 031210-0583
    // TODO:
    // only generate this ticket if the account is going to go
    // offline, i.e. no more servers.

    $computer->customer->LoadComputers();
    $found_live_computer = false;
    $last_offline_date = 0;
    foreach( $computer->customer->computer_list as $cust_computer ) {
        $status = $cust_computer->getData('status_number');
        if ( $status > 1 && $status != STATUS_DECOMMISSION ) {
            // this one's live - but has it been scheduled to be cancelled?
            $sec_due_offline = $cust_computer->getSecDueOffline();
            if ( $sec_due_offline > $last_offline_date ) {
                $last_offline_date = $sec_due_offline;
            }
            if ( empty( $sec_due_offline)  ) {
                $found_live_computer = true;
            }
        }
    }

    if ( !$found_live_computer ) {
        $date_array = getdate($last_offline_date);
        $offline_date_string = sprintf("%s %s, %s", $date_array['month'], $date_array['mday'], $date_array['year']);
        $customer_number= $computer->customer->account_number;
        $computer->GenTicket2(
            QUEUE_BILLING,
            TICKET_CATEGORY_BILLING_CANCELLATIONS,
            TICKET_SEVERITY_MODERATE,
            "Customer $customer_number scheduled for cancellation",
            "All of this account's servers have been cancelled and the account is scheduled for cancellation on $offline_date_string. Please make sure there are no outstanding billing issues for this account.",
            1,
            1
            );
    }
}

if($_POST['command']==$COMMAND_CANCEL_NOW)
{
### Downgrade the server and add offline date to offline_servers table
    $db->SubmitQuery("update queue_cancel_server
        set completed='t'
        where computer_number = $computer_number");
        
   	$rpc_result = changeServerStatusViaXMLRPC( $computer->computer_number, -1,  CancellationInfoString($cancel_data), "", "", "", $reason_number );
      	if (!empty($rpc_result)) {
         	trigger_error($rpc_result);
     	}
}

setScheduledCancellationAttribute($account_number, true);

$db->CommitTransaction();
}

function setScheduledCancellationAttribute($account_number, $cancellation) {
    global $db;
    $cancellationStr = 'false';
    if ($cancellation) {
        $cancellationStr = 'true';
    }
    $acctId = $db->getVal("SELECT \"ID\" FROM \"ACCT_Account\" WHERE \"AccountNumber\" = $account_number");

    $acctAttribQuery = $db->SubmitQuery("SELECT \"ACCT_Attribute_CacheID\" FROM \"ACCT_Attribute_Cache\" WHERE \"ACCT_AccountID\" = $acctId");

    if ($acctAttribQuery->numRows() > 0) {
        $db->SubmitQuery("UPDATE \"ACCT_Attribute_Cache\" SET servers_scheduled_cancellation = $cancellationStr where \"ACCT_AccountID\"=$acctId");
    }
    else {
        $db->SubmitQuery("INSERT INTO \"ACCT_Attribute_Cache\" (\"ACCT_AccountID\", servers_scheduled_cancellation) VALUES ($acctId, $cancellationStr)");
    }
}

function handleUncancellation(
    $form, $computer, $already_queued, $already_queued_data, $account_number) {
global $db;

if(!$already_queued) {
    return("Computer #$computer->computer_number is not in the cancellation queue; therefore, it cannot be removed from the queue");
}
$db->Delete("queue_cancel_server",
    "computer_number=$computer->computer_number");
$computer->Log("Computer removed from cancellation queue.");

$subject = "Cancellation VOIDED";
$body = "The following device has been unscheduled for cancellation:\n"
. "\nComputer: $computer->customer_number-$computer->computer_number";
$computer->GenTicket2(
    QUEUE_ACCOUNTING,
    TICKET_CATEGORY_ACCOUNTING_CANCELLATION_VOIDED,
    TICKET_SEVERITY_MODERATE,
    $subject,
    $body,
    1,
    1
);
## If it is a PrevenTier device create a ticket for Backbone Engg team
if ($computer->OS() == "PrevenTier")
{
    $computer->GenTicket2AfterCommit(
        QUEUE_NETWORKING,
        TICKET_CATEGORY_PREVENTIER,
        TICKET_SEVERITY_MODERATE,
        $subject,
        $body,
        1,
        1
    );
}
setScheduledCancellationAttribute($account_number, false);
}

function changeServerStatusViaXMLRPC( $server_num, $new_status, $reason="", $ticket_num="", $migr_days="", $new_migr_server = "", $offline_reason="" ){
    $format = new xmlrpcmsg('changeComputerStatus', array(new xmlrpcval($server_num, "int"),
                                                          new xmlrpcval($new_status, "int"),
                                                          new xmlrpcval($reason, "string"),
                                                          new xmlrpcval($ticket_num, "string"),
                                                          new xmlrpcval($migr_days, "string"),
                                                          new xmlrpcval($new_migr_server, "string"),
                                                          new xmlrpcval($offline_reason, "string")));
                                                          
        
    $session_id = COREAUTH_GetSessionkey();
    $client=new rs_xmlrpc_client("/xmlrpc/Computer/::session_id::$session_id",
                                 XMLRPC_PYTHON_HOST, XMLRPC_PYTHON_PORT);
    $client->setSSLVerifyPeer(0);
    $request=$client->send($format, XMLRPC_PYTHON_TIMEOUT, XMLRPC_PYTHON_SERVER_METHOD);
        
    if (!$request->faultCode()) {
        $value=$request->value();
        $result = $value->scalarval();
        return $result;
    }
    else {
        error_log("Error trying to change server status. Error=\"".$request->faultString() . '"');
        trigger_error($request->faultString(), E_USER_WARNING);
        return $request->faultString();
    }
}

?>
