<?php

require_once("CNTR_upgrade_all.php");
require_once("phpunit.php");

// test code for the CNTR_upgrade_all.php file

class UpgradeContractComputerTest extends TestCase {
    function test_upgradeContractComputer() {
        global $db;
        // this Computer is No Longer Active, so this should fail
        $Computer = new RackComputer(11, 3563, $db);
        $initial_status = $Computer->getData("status_number");
        upgradeContractComputer($Computer);
        $new_status = $Computer->getData("status_number");
        if ( $initial_status > LOWER_STATUS_BOUNDARY && 
             $initial_status < UPPER_STATUS_BOUNDARY ) {
            $this->assertEquals($initial_status + 1, $new_status);
        } else {
            $this->assertEquals($initial_status, $new_status);
        }
    }
}
$test = new UpgradeContractComputerTest("test_upgradeContractComputer"); 
$testRunner = new TestRunner();
$testRunner->run($test);

class GetContractComputersTest extends TestCase {

    function test_getContractComputers() {
        // Contract #2 contains two Computers.
        $Computers = getContractComputers(2);
        // the Computer number should be 3563
        foreach($Computers as $Computer) {
            $this->assertEquals( $Computer->computer_number, '3563' );
        }


    }
}

$test = new GetContractComputersTest("test_getContractComputers"); 
$testRunner = new TestRunner();
$testRunner->run($test);

class UpgradeContractComputersTest extends TestCase {
        function test_upgradeContractComputers() {
            // load a contract
            $Computers  = getContractComputers(32); // has multiple Computers
            // upgrade everything

            $num_comps = count($Computers);
            
            foreach ($Computers as $Computer) {
                // make sure that only Computers greater than No Longer Active 
                // and below Contract Received have been upgraded.
                $start_status = $Computer->getData("status_number");
                upgradeContractComputer($Computer);
                $end_status = $Computer->getData("status_number");
                if ( $start_status > LOWER_STATUS_BOUNDARY &&
                     $start_status < UPPER_STATUS_BOUNDARY) {
                    $this->assertEquals($start_status + 1, $end_status);
                } else {
                    $this->assertEquals($start_status, $end_status);
                }
            }
        }
}

$test = new UpgradeContractComputersTest("test_upgradeContractComputers"); 
$testRunner = new TestRunner();
$testRunner->run($test);

?>
