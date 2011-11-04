<?
require_once("CORE_app.php"); 
require_once("class.parser.php");
require_once("class.mailer.php");

############################
# Contents:                #
# 1. Function definitions. #
# 2. Constants defined.    #
# 3. Form displayed.       #
# 4. Form processed.       #
############################

if (!empty($_REQUEST['command'])) {
    #print str($HTTP_POST_VARS);
    #exit();
} else {
    $_REQUEST['command'] = '';
}
    function ComputerCancellationHeader()
    {
        global $customer_number, $computer_number, $computer;
        global $db;
        print("<HTML id=\"mainbody\">
            <title> CORE: Cancellation Form $customer_number-$computer_number
            </title>");
        print("<LINK HREF=\"/css/core_ui.css\" REL=\"stylesheet\">");
        print("<HEAD>");
        ?>
            <SCRIPT>
                function disableUselessFields() {
                    var continue_hosting = document.getElementsByName('continue_hosting');
                    var future_hosting_method = document.getElementsByName('future_hosting_method');
                    var preserve_primary_ip = document.getElementsByName('preserve_primary_ip');
                    var preserve_additional_ip = document.getElementsByName('preserve_additional_ip');

                    if (continue_hosting.length > 0 && future_hosting_method.length > 0
                     && preserve_primary_ip.length > 0 && preserve_additional_ip.length > 0) {
                        continue_hosting = (continue_hosting[0].value == 'f');
                        future_hosting_method = (future_hosting_method[0].value.toLowerCase().indexOf('rackspace') >= 0);
                        preserve_primary_ip = (preserve_primary_ip[0].value == 'f');
                        preserve_additional_ip = (preserve_additional_ip[0].value == 'f');

                        document.theform.future_hosting_method.disabled = continue_hosting;
                        document.theform.chose_competitor.disabled = continue_hosting || future_hosting_method;
                        document.theform.competitor.disabled = continue_hosting || future_hosting_method;
                        document.theform.competitor_benefits.disabled = continue_hosting || future_hosting_method;
                        document.theform.future_consideration.disabled = future_hosting_method;
                        document.theform.ip_notes.disabled = preserve_primary_ip && preserve_additional_ip;
                    }
                }
            </SCRIPT>
        <?
        print("</HEAD>");
    }

    // dead code?
    //resolves Text value from Type field submitted for offline reason to actual reason number
    function getReasonValue($group, $source)
    {
        global $db;
        $query="
                SELECT 
                    reason_number
                FROM
                    offline_reasons
                    JOIN offline_reason_groups USING (category_group)
                WHERE category_group_name = '$group'
                    AND reason_category = '$source'
                ";
        $reason_number = $db->GetVal($query);
        return($reason_number);
    }

    // dead code?
    function getReasonCategoryGroup($reason_number) {
        global $db;
        $query="
                SELECT 
                    category_group
                FROM
                    offline_reasons
                WHERE reason_number = $reason_number
                ";
        $category_group = $db->GetVal($query);
        return($category_group);
    }

$SCHEDULE_HOUR = 8; # hour when servers show up in queue
$COMMAND_QUEUE_CANCELLATION = "Add to Cancel Queue";
$COMMAND_UNQUEUE_CANCELLATION = "Remove from Cancel Queue";
$COMMAND_OVERRIDE = "Save Changes to Schedule";
$COMMAND_CONFIRM_CANCELLATION = "CONFIRM_CANCELLATION";
$COMMAND_CANCEL_NOW = "Mark Server Offline/No Longer Active";
$MOVING_QUESTION = 'Are you switching to another company?';
$MAX_DAYS_IN_ADVANCE = 150; # limit cancellation scheduling
    
function setupCancellation(
        $form, &$computer, &$already_queued, &$already_queued_data) {

    global $SCHEDULE_HOUR;
    global $COMMAND_QUEUE_CANCELLATION;
    global $COMMAND_UNQUEUE_CANCELLATION;
    global $COMMAND_OVERRIDE;
    global $COMMAND_CONFIRM_CANCELLATION;
    global $COMMAND_CANCEL_NOW;
    global $MOVING_QUESTION;
    global $MAX_DAYS_IN_ADVANCE;
    global $db;

    extract($form);

    if(!in_dept("SUPPORT|ACCOUNT_EXECUTIVE|AR|PRODUCTION|AUP"))
    {
        return("You do not have access to the cancellation form.");
    }

    if(!in_dept("PRODUCTION")
        and ($_REQUEST['command'] == $COMMAND_CANCEL_NOW
            or $_REQUEST['command'] == $COMMAND_CONFIRM_CANCELLATION))
    {
        return("Only the production department can mark a server offline. Please schedule a cancellation instead.");
    }

    if ($computer_number=="")
    {
        return("Unable to display the cancellation form because you are missing the computer number");
    }

    # If a server is brought back online, after it has been queued
    # for cancellation, it is no longer considered ALREADY QUEUED.
    # Offline servers are still considered ALREADY QUEUED so that
    # the info in the queue is accessible to be edited.
    $result = $db->SubmitQuery("select t1.oid,t1.* 
        from queue_cancel_server t1, server_status_all t2
        where t1.computer_number = $computer_number
            and t1.computer_number = t2.computer_number
            and (t1.completed = 'f' 
                or t1.completed is null
                or t2.status_number = -1)
        order by sec_created DESC limit 1");
    if ($db->NumRows($result) < 1)
    {
        $already_queued = false;
        $already_queued_data['sec_due_offline'] = '';
        $already_queued_data['reason_number'] = '';
        $already_queued_data['reason_info'] = '';
        $already_queued_data['support_incidents'] = '';
        $already_queued_data['chose_competitor'] = '';
        $already_queued_data['competitor'] = '';
        $already_queued_data['competitor_benefits'] = '';
        $already_queued_data['oid'] = '';
        $already_queued_data['reason_number'] = '';
        $already_queued_data['preserve_primary_ip'] = '';
        $already_queued_data['preserve_additional_ip'] = '';
    }
    else
    {
        $already_queued = true;
        $already_queued_data = $db->FetchArray($result, 0);
    }

    $computer=new RackComputer;
    $customer_number = $db->GetVal("select customer_number
        from server
        where computer_number=$computer_number");
    $computer->Init($customer_number,$computer_number,$db);
    if (!$computer->IsComputerGood())
    {
        return("Unable to load any information about computer number $computer_number.\n<br>This computer may no longer exist.  If you continue to have problems contact the database administrator");
    }

    if (!$computer->customer->IsCustomerGood())
    {
        return("Unable to load any information about customer number $customer_number This customer may no longer exist.  If you continue to have problems contact the database administrator");
    }
}

$computer = false;
$already_queued = false;
$already_queued_data = false;
$fatal_error = setupCancellation(
            $_REQUEST, $computer, $already_queued, $already_queued_data);

if (!empty($fatal_error)) {
    ComputerCancellationHeader();
    print "Error: $fatal_error";
    exit();
}

$error = false;
if($_REQUEST['command'] == $COMMAND_QUEUE_CANCELLATION
        or $_REQUEST['command'] == $COMMAND_OVERRIDE
        or $_REQUEST['command'] == $COMMAND_CANCEL_NOW) { 
    require('cancel_computer_handler.php');
    $error = handleCancellation(
        $_REQUEST, $computer, $already_queued, $already_queued_data, $customer_number);
    if (empty($error)) {
        ForceReload("DAT_display_computer.php3?"
            . "computer_number=$computer_number&"
            . "customer_number=$computer->customer_number");
        print("<b>Computer #$computer->computer_number added 
            to cancellation queue.");
        exit();
    }
}
else if($_REQUEST['command'] == $COMMAND_UNQUEUE_CANCELLATION) {
    require('cancel_computer_handler.php');
    $error = handleUncancellation(
        $_REQUEST, $computer, $already_queued, $already_queued_data, $customer_number);
    if (empty($error)) {
        ForceReload("DAT_display_computer.php3?"
            . "computer_number=$computer_number&"
            . "customer_number=$computer->customer_number");
        print("<b>Computer #$computer->computer_number removed from
            cancellation queue.");
        exit();
    }
}
require('cancel_computer_form.php');
ComputerCancellationHeader();
printCancellationForm(
    $_REQUEST, $computer, $already_queued, $already_queued_data, $error);
?>
</HTML>
<?php
// Local Variables:
// c-basic-offset: 4
// indent-tabs-mode: nil
// End:
?>
