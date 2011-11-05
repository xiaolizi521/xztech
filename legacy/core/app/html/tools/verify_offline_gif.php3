<?
	Header("Content-type: image/gif");
	Header("Expires: " . strftime("%a, %d %b %Y %T", time() + 300));
	# Had to pass address in PATH_INFO (e.g. "ping_gif.php3/www.rackspace.com")
	# so that netscape would not assume all the pictures were the same
	$address = substr($PATH_INFO, 1);
	if ($address == "")
	{
		$picture = "broken.gif";
	}
	else
	{
		# php will normally look for ping in /usr/bin
		# only certain versions of ping support "-w" which is the wait limit
		$s = exec(EscapeShellCmd("ping -q -w 1 -c 1 $address"), $a, $result);
		# zero is ping success; therefore,
		# the suspended computer is online which is WRONG
		if (strlen($s) < 10) # ping failed to run
		{
			$picture = "broken.gif";
		}
		else if ($result == 0) 
		{
			$picture = "alert.red.gif";
		}
		else
		{
			$picture = "ball.gray.gif";
		}
	}
	$fp = fopen($picture, "rb");
	$read_length = 1024;
	while (1)
	{
		$s = fread($fp, $read_length);
		print($s);
		if (strlen($s) < $read_length)
			break;
	}
?>
