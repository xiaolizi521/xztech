<?
    require_once("CORE_app.php");
//Handle fixing all the port upgrade people
$table = array();
$table[] = "server_parts";
$table[] = "queue_server_parts";

for ($x = 0; $x < count($table); $x++) {
    $computers = $db->SubmitQuery("select * from ".$table[$x]." where product_sku = 100163;");

    for ($i = 0;$i < $computers->numRows(); $i++) {
        //Figure out if they have private net 
        $computer_number = $computers->getResult($i, "computer_number");
        if (!$db->TestExist("select * from ".$table[$x]." where product_sku = 100184 and computer_number = $computer_number;")) {
            //They don't have private net - alter the special nic
            //Add in the port upgrade
            print("<p>Found $computer_number");
            $db->SubmitQuery("INSERT INTO ".$table[$x]." 
                            VALUES (".$computers->getResult($i, "sec_created").","
                            .$computers->getResult($i, "sec_last_mod").",$computer_number"
                            .",101031 , 55,'hardware','port_upgrade','t');");

        }
    }
}
?>
