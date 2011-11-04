<?php

require_once("CORE_app.php");
require_once("helpers.php");

$chkdict = array( 'computer_number' =>
                  "A Computer Number (Something's gone wrong!)" );

checkDataOrExit( $chkdict );
$computer=new RackComputer;
$computer->Init('',$computer_number,$GLOBAL_db);
if( $computer->IsComputerGood() ) {
    $computer->toggle_non_durable_passwords();
}
Header("Location:".$_SERVER['HTTP_REFERER']);
?>
