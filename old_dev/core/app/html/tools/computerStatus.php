<?php
require_once("CORE_app.php");
require_once("act/ActFactory.php");
require_once("class.parser.php");

$max_status=0; // lazy assignment later, within getUpgradeRestriction.
$min_status=0;

define("MANAGED_FINAL_CONFIGURATION_OTHER", 5);
define("INTENSIVE_FINAL_CONFIGURATION_SEGMENT_CONFIGURATION", 4824);
define("CONFIGURE_HYPERVISOR", 6784);

// Return nothing if upgrades are allowed or the reason for the restriction.
function  getUpgradeRestriction(&$computer) {
	global $db;
	global $max_status;
	global $min_status;
	$reason = '';

	if (!$max_status) {
		$max_status=$db->GetVal("select max(status_number) from status_options");
		$min_status=$db->GetVal("select min(status_number) from status_options");
	}

	$current_status = $computer->getData("status_rank");
	if ($current_status == STATUS_NO_LONGER_ACTIVE and in_dept("ACCOUNT_EXECUTIVE")) {
		return '';
	}
	if ( $current_status == STATUS_SEGMENT_CONFIGURATION and $computer->account->isMerged() ) {
		if ( !$computer->GetSiteId() ) {
			return 'Cannot Upgrade to Online/Complete until a Site Id is set.';
		}

	}
	$rep_name=$computer->GetRepName();
	if ( $current_status == STATUS_ORDER_SUBMITTED and empty($rep_name) ) {
		return 'Cannot upgrade until a Sales Rep is set.';
	}

	$illegal_skus = $computer->getInvalidParts();
	if (count($illegal_skus)) {
		if( in_dept("CORE") ) {
			$reason = '<!-- ALLOW WITH CAVEAT -->\n<tr><td colspan="2"><p style="color: red; text-align: center">Server has ' . $illegal_skus . ' invalid parts. Non-CORE users cannot upgrade.</p></td></tr>\n';
		} else {
			$reason = 'Cannot upgrade servers with invalid parts';
		}
	}

	if ($current_status >= STATUS_ORDER_SUBMITTED) {
	  if (!$computer->isConfigured()) {
            $reason = 'This server is not configured yet.';
	  }
	}

	if ($current_status == STATUS_FINAL_CONFIGURATION) {
		if ($computer->getAccountNumber() == ONE_HOUR_CUSTOMER_NUMBER) {
			// Account #1 is a special account for prebuilt servers.
			// If they have not been assigned a computer prebuilt computer
			// Don't let them upgrade until they do
			$reason = 'Cannot upgrade One-Hour Reserve computer to Online/Complete';
		} elseif (($computer->getSwitch() == "" or $computer->getData("port_number") == "") and $computer->isNetworked() and !$computer->isPix501() and !$computer->isVirtualMachine() and !$computer->isSharedStorage()) {
			// A port can be 0, so you must compare with ""
			// Must have a complete location before going online/complete
			$reason = 'Incomplete switch location. Cannot upgrade to Online/Complete.';
		} elseif ($current_status >= $max_status) {
			$reason = 'Server is at maximum status.';
		}
	}

	$is_build_tech = $db->getVal("SELECT 1 FROM build_tech WHERE userid='" . GetRackSessionUserid() . "' AND computer_number = " . $computer->computer_number . " AND customer_number = " . $computer->customer_number);
	if (!$is_build_tech and !in_dept("PERM_IP_ADMIN|PROFESSIONAL_SERVICES|ACCOUNT_EXECUTIVE") and $current_status >= STATUS_RECEIVED_CONTRACT and $current_status < STATUS_ONLINE and $current_status != STATUS_SEGMENT_CONFIGURATION and !in_dept("SET_ONLINE_COMPLETE")) {
		$dont_let_them_upgrade = 1;
		$reason='No permissions to upgrade the server.';
		if (in_dept("PRODUCTION")) {
			$reason = "Non-Build Tech's cannot upgrade servers";
		}
	}

	if ($current_status == STATUS_SUSPENDED_AUP and !in_dept('AUP')) {
		// Only AUP can bring systems to Reactivation from Suspended - AUP
		$reason = "Only AUP staff can upgrade an AUP Suspended Server";
	} else if ($computer->needsLicense()) {
		// This applies to any current_status.
		$reason = "You must <tt>Edit Parts/Platform</tt> -&gt; <tt>Hardware</tt> to add a valid license for this computer";
	} else if (($current_status == STATUS_ORDER_SUBMITTED || $current_status == STATUS_SENT_CONTRACT) and $computer->HasInactiveParts() and !in_dept("CORE|PRODUCT_MANAGEMENT")) {
		$reason = "This server cannot be upgraded because it has the following parts which are no longer available:<ul>";
		$inactive_parts = $computer->GetInactiveParts();
		foreach ($inactive_parts as $inactive_part) {
			$sku = $inactive_part["product_sku"];
			$name = $inactive_part["name"];
			$reason .= "<li>$sku ($name)</li>";
		}
		$reason .= "</ul>";
	}

	// CORE-4101 Jay Farrimond 4/11/07
	// SAN and DAS devices must have corresponding servers before they can be
	// upgraded in the contract process to RECEIVED CONTRACT
	if( ( $current_status == STATUS_SENT_CONTRACT ) &&
	    ( $computer->isManagedStorage() || $computer->isDAS() || $computer->isDNAS() || $computer->isSharedStorage() ) &&
	    !$computer->isBehindNetworkDevice() ) {
	  $reason = "SAN, DAS, DNAS and UNAS devices must have corresponding servers";
	}

	// IPSPACE - Added check for assigned switch before sending to assembly.
	if ($current_status == STATUS_RECEIVED_CONTRACT ) {
		if (($computer->getSwitch() == "" or $computer->getData("port_number") == "") and $computer->isNetworked() and !$computer->isPix501() and !$computer->isVirtualMachine() and !$computer->isSharedStorage()) {
			// A port can be 0, so you must compare with ""
			// Must have a complete location before going online/complete
			$reason = 'Incomplete switch location. Cannot upgrade to "Sent to Assembly".';
		}
	}
	// Virtual Machine Wait suspension -  no availiable options
	if (($current_status == 
	     STATUS_WAIT_SUSPENDED_VIRTUAL_MACHINE) && 
	    $computer->isVirtualMachine()){
	    $reason = 'Please try to Suspend VM with "Turn VM Off" button.';   
	}
	// Virtual Machine Wait can't go to Received Contract w/out a Hypervisor -  no availiable options
	if (($current_status == STATUS_SENT_CONTRACT) &&
	    $computer->isVirtualMachine() &&
	    $computer->getHypervisorNumber() === 0){
	    $reason = 'VM must be assigned a Hypervisor before it can proceed.';   
	}
	// Virtual Machine Wait can't go to Ready for QC w/out a UUID -  no availiable options
	if ($current_status == STATUS_SENT_TO_ASSEMBLY &&
	    $computer->isVirtualMachine()){

	    $uuid = $computer->getUuid();
	    if (is_null($uuid) || $uuid == ''){
	        $reason = 'VM must be assigned a Uuid before it can proceed.';
	    }
	}
	    
	    
	

	return $reason;
}

function upgradeComputerStatus(&$computer, $new_status, $reason, $ticket_num, $auto_assign_ip, $migr_new_server='', $migr_days='') {
	global $db;

	// Code from display_computer.php3 case "UPGRADE_STATUS":

        #################################################################
        if ( $new_status == STATUS_COMPROMISED_SYSTEM_LEVEL ) {
                $ticket_number = putServerIntoCompromisedSystemLevel( $reason, &$computer, $ticket_num );
                $reason = "ticket #: $ticket_number";
        } elseif ( $new_status == STATUS_COMPROMISED_APP_LEVEL ) {
                $ticket_number  = putServerIntoCompromisedAppLevel( $reason, &$computer, $ticket_num );
                $reason = "ticket #: $ticket_number";
        }
        if($new_status == STATUS_SEGMENT_CONFIGURATION ) {
            if ($computer->account->segment_id == INTENSIVE_SEGMENT || $computer->account->segment_id == MANAGED_SEGMENT) {
                if ($computer->account->segment_id == INTENSIVE_SEGMENT) {
                    $ticket_queue = QUEUE_INTENSIVE;
                    $ticket_category = INTENSIVE_FINAL_CONFIGURATION_SEGMENT_CONFIGURATION;
                } else {
                    $ticket_queue = QUEUE_MANAGED;
                    $ticket_category = MANAGED_FINAL_CONFIGURATION_OTHER;
                }
                $segment_configuration_subject = "Device Segment Configuration";
                $segment_configuration_body = "DCOPS/Networking/Managed Storage is finished with this device.  Please perform customer configurations and QC.  Server/Net Device/Managed Storage needs to be marked \"online complete\" when ready to be turned over to the customer.";
		

		if ($computer->isHyperVisor()){
		    // the computer is a Hypervisor- we still kick off a ticket but 
		    // with different parameters
		    $ticket_queue = QUEUE_VIRT_INFRASTRUCTURE;
		    $ticket_category = CONFIGURE_HYPERVISOR;
		    $segment_configuration_subject = "Device Segment Config. " . 
		        $computer->customer_number . " - " .
		        $computer->computer_number;

		    $parser = new core_parser;
                    $parser->setAccount($computer->account);
                    $parser->setComputer($computer);

		    $segment_configuration_body = $parser->ParseMessageLabel('seg_config_HV');
		}

               #need to create ticket here
               # this ticket is required for real servers and not for firewalls,loadbalancers etc.
               if ( $computer->isRealServer() ){            
                 $computer->GenTicket2(
                    $ticket_queue,
                    $ticket_category,
                    TICKET_SEVERITY_MODERATE,
                    $segment_configuration_subject,
                    $segment_configuration_body,
                    1, // is_private
                    1 // is_internal
                );
                }
            }
        }
        #################################################################
        ## CORE-7046: EGL: Detect the change in status from 'Sent Contract' to
        ##		'Contract Received'. When that is found, call a Python method that will
        ##		send the appropriate notifications, and auto-assign the team to that
        ##		account if it doesn't already have a team.
        #################################################################
        $old_status = $computer->getData("status_rank") ;
        $computer->UpgradeStatus($new_status,$reason, $auto_assign_ip, $migr_new_server, $migr_days);
        $db->CommitTransaction();
        if ( $old_status == STATUS_SENT_CONTRACT && $new_status == STATUS_RECEIVED_CONTRACT) {
            // Need to notify the Python side of things for auto-assignment of account team, etc.
            $computer->_sendContractReceivedNotice($old_status, $new_status) ;
        }
}

function downgradeComputerStatus(&$computer, $new_status, $reason, $ticket_num, $auto_assign_ip) {

    // Code from display_computer.php3 case "DOWNGRADE_STATUS":

        if ( $computer->getData("status_rank") == STATUS_COMPROMISED_APP_LEVEL  && $new_status == STATUS_COMPROMISED_SYSTEM_LEVEL ) {
            $ticket_number = putServerIntoCompromisedSystemLevel( $reason, &$computer, $ticket_num );
            $reason = "ticket #: $ticket_number";
        }
        elseif ( $computer->getData("status_rank") == STATUS_COMPROMISED_SYSTEM_LEVEL || $computer->getData("status_rank") == STATUS_COMPROMISED_APP_LEVEL ) {
            $body = $reason;
            $ticket_queue = $computer->account->getSupportSegmentQueue();
            if ( $computer->getData("status_rank") == STATUS_COMPROMISED_SYSTEM_LEVEL ) {
                $subject = "Server Coming Out Of Compromised System Level";
                $ticket_category = 56;
            }
            else {
                $subject = "Server Coming Out Of Compromised at App Level";
                $ticket_category = 56;
            }
            if ( !empty( $ticket_num )) {
                addTicketMessage( $ticket_num,
                                    $body,
                                    GetRackSessionContactID(),  #source_contact
                                    1, # is_private_message
                                    0  # send_message_text
                                    );
                $reason = "ticket #: $ticket_num";
            }
            else {
                $ticket_number = $computer->GenTicket2(
                    $ticket_queue,
                    $ticket_category,
                    TICKET_SEVERITY_MODERATE,
                    $subject,
                    $body,
                    1, // is private
                    0, // is internal
                    TICKET_PRIORITY_HIGHEST,
                    GetRackSessionContactID(GetRackSessionEmployeeNumber())
                );
                $reason = "ticket #: $ticket_number";
            }
        }
        if ($computer->getData("status_rank") == -1) {
		// This stayed in display_computer.php3: DisplayError("Server cannot be downgraded below Offline/No longer Active");
		    return -1;
        } else if ( (!empty($new_status) and ($new_status == -1)) or ($computer->getData("status_rank") == 1)) {
		// Don't DowngradeStatus until comments have been made.
		// This stayed in display_computer.php3: ForceReload(
		//                                           "cancel_computer.php3?"
		//                                           . "command=CONFIRM_CANCELLATION"
		//                                           . "&computer_number=$computer_number" );
		    return -2;
        } else {
            if( empty($new_status) ) {
                $new_status = $computer->DowngradeStatus();
            } else {
                $new_status = $computer->DowngradeStatus($new_status);
            }
        }
	return 0;
}

function updateAttributeCache($account_number, $old_status, $new_status) {
    global $db;

    $suspendedBilling = 'false';
    if ($new_status == 31) { // Suspended for billing
        $suspendedBilling = 'true';
    }

    $acctId = $db->getVal("SELECT \"ID\" FROM \"ACCT_Account\" WHERE \"AccountNumber\"=$account_number");

    $acctAttribQuery = $db->SubmitQuery("SELECT \"ACCT_Attribute_CacheID\" FROM \"ACCT_Attribute_Cache\" WHERE \"ACCT_AccountID\" = $acctId");

    if ($acctAttribQuery->numRows() > 0) {
        $db->SubmitQuery("UPDATE \"ACCT_Attribute_Cache\" SET servers_suspended_billing=$suspendedBilling WHERE \"ACCT_AccountID\"=$acctId");
    }
    else {
        $db->SubmitQuery("INSERT INTO \"ACCT_Attribute_Cache\" (\"ACCT_AccountID\", servers_suspended_billing) VALUES ($acctId, $suspendedBilling)");
    }
}

function needsRekick(&$computer) {

    global $db;

    $q1 = '
    SELECT
    "Start"
    FROM
    server_status_log
    WHERE
    computer_number = ' . $computer->computer_number . ' and
    status_number = '. STATUS_COMPROMISED_SYSTEM_LEVEL . '
    order by "Start"
    desc
    limit 1
    ';

    $date_compromised = $db->getVal( $q1 );

    if ( !$date_compromised ) {

        return false;

    } else {

        $q2 = '
        SELECT
        "Start"
        FROM
        server_status_log
        WHERE
        computer_number = ' . $computer->computer_number . ' and
        status_number = '. STATUS_REKICK . '
        order by "Start"
        desc
        limit 1
        ';

        $date_rekicked = $db->getVal( $q2 );

        if ( !$date_rekicked || ( $date_rekicked < $date_compromised ) ) {

            return true;

        }

    }

}

?>
