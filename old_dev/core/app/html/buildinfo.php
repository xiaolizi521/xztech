<?php
/* Print before we define DB passwords */
/* just adding a comment */
//foreach ( $GLOBALS as $key=>$value ) 
//{
//	print "$key: $value<br>";
//}
require_once("localconfig.php");

print("<HTML><BODY>");
print("<H2>Globals</H2>");
$safeKeys  = array(	'rack_test_system', 
					'HTTP_HOST', 
					'HTTP_USER_AGENT', 
					'HTTP_COOKIE',
					'SERVER_SIGNATURE',
					'SERVER_SOFTWARE',
					'SERVER_NAME',
					'SERVER_PORT',
					);
foreach ( $safeKeys as $key ) 
{
	print "$key: $GLOBALS[$key]<br/>";
}

if($GLOBALS["rack_test_system"] == 1)
{
	print("<HR/>");
	print("<H2>Values Only Displayed on a Test System</H2>");
	foreach ( $GLOBALS as $key=>$value ) 
	{
		print "$key: $value<br/>";
	}

}

print("</HTML></BODY>");


?>
