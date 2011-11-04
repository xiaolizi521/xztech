<?php

require_once("CORE_app.php");
require_once("act/ActFactory.php");

#define("LOWER_STATUS_BOUNDARY", 0); --  Now defined in Computer.phlib
#define("UPPER_STATUS_BOUNDARY", 3); --  Now defined in Computer.phlib

// we should have been passed a contract number
// use the contract number to load up all the Computers

function getContractComputers($contract) {
    global $db, $account_number;
    $Computer_list = $db->submitQuery(
        'SELECT
            computer_number
        FROM
            "CNTR_xref_Contract_Server"
        WHERE
            "CNTR_ContractID" = '.$contract);
    $Computers = array();
    for ( $i = 0; $i < $Computer_list->numRows(); $i++ ) {
        $number = $Computer_list->getResult($i, "computer_number");
        $computer = new RackComputer($account_number, $number, $db);
        $Computers[] = $computer;
    }
    $Computer_list->freeResult();
    return $Computers;
}

function contractHasNetDevice($Computers) {
    // return true if there's a firewall,
    // false otherwise
    foreach ( $Computers as $computer ) {
        if ($computer->isNetDevice() ) {
            return true;
        }
    }
    return false;
}

function contractHasMySQLLicense($Computers) {
    // return true if there is a mysql license
    // false otherwise
    foreach ( $Computers as $computer ) {
        if ($computer->hasMySQLLicense() ) {
            return true;
        }
    }
    return false;
}

// upgrade each Computer in the contract to its next status,
// but don't go above Contract Received (3) in any case.
// Also, don't upgrade No Longer Active Computers (-1).
function upgradeContractComputer($computer, $assign_ip) {
    $current_status = $computer->getData("status_number");
    if ( ( $current_status > LOWER_STATUS_BOUNDARY && $current_status < UPPER_STATUS_BOUNDARY )
         || $current_status == 9 ) { /* 9 is a Special case for Segment Configuration */
        $info = $computer->GetNextStatusRank();
        $new_status = $info["status_number"];
        $reason = 'Contract Upgrade';
        $computer->UpgradeStatus($new_status, $reason, $assign_ip);
        return $new_status;
    }
}

if ( empty($account_number) ) {
    trigger_error("No account number supplied.");
} 
if ( empty($contract) ) {
    trigger_error("No contract number supplied.");
} 

$url = "/py/computer/contractChangeMultipleStatus.pt?account_number=$account_number&contract_id=$contract";
if ($st == STATUS_RECEIVED_CONTRACT)
{
    $account = $comp->account;
    $account->LoadComputers();
    foreach ($account->computer_list as $comp)
    {
        if ($comp->isNetDevice() and
            $comp->data['status_number'] <= STATUS_ONLINE)
            {
                $firewall_return = urlencode($_SERVER['HTTP_REFERER']);
                ForceReload("/tools/organize_firewall.php?account_number=$account_number&contract=$contract&firewall_return=$firewall_return");
            }
    }
}


Header("Location: $url\n\n");
print "<html><head><meta http-equiv=\"refresh\" content=\"0;url=$url\" /></head><body><script type=\"text/javascript\">window.location.href='$url'</script></body></html>\n";

flush();
?>
