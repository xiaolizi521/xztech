<?php

header("Cache-Control: no-cache");
set_time_limit( ini_get('max_execution_time') );

require( "code/config.php" );
require( "code/XMLGenerator.php" );

// FreshBooks API
$fb = new FreshBooksAPI();
$db = new db();
$rtn = chr(10);

$to = 'dtholl@swbell.net,dtholl@gmail.com';
$subject = 'NOTICE: Prepaid Time Expiring';
$fromname = 'admin@iacprofessionals.com';
$fromemail = 'admin@iacprofessionals.com';
sendmailPHP($to, "sendmailPHP", 'Test Email', $fromname, $fromemail );

?>