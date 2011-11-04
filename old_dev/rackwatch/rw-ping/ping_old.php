<html>

<head>
<title>Results</title>
</head>

<body bgcolor="#FFFFFF">

<font size="+1">Rackwatch Ping Utility</font>

<br>

<form action="ping.php" method="POST">
<table border="0">
<tr>
<? 
if ($host){
	print "<td><input name=\"host\" type=\"text\" value=\"$host\" ";
	print "size=\"40\" maxlength=\"200\"></td>\n";
}else{
	print "<td><input name=\"host\" type=\"text\" size=\"40\" ";
	print "maxlength=\"200\"></td>\n";
}
?>
	<td><input type="submit" value="Ping"></td>
	<td><input type="reset" value="Reset"></td>
</tr>
</table>
</form>

<?

if ($host){

        $incr = 0; unset($invalid_host);
        while ($incr < strlen($host)) {
                $match = preg_match('/[.0-9a-zA-Z-]/',$host[$incr]);
                if (!($match)) {
                        $invalid_host = $host;
                        break;
                }
                $incr++;
        }
        print "<hr>\n\n";
        print "<pre>\n";
        if ($invalid_host) {
                print "You entered an invalid target: ($invalid_host)  Please use only IP Addresses or host names.\n";
        } else {
                system("/bin/ping -c 5 $host");
        }
        print "</pre>\n\n";
        print "<b>Complete</b>\n";
}

?>


</body>

</html>
