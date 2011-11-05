<?php
/******************************************************************
 * API Tester
 * Date: June 27, 2007
 * Version: 1.0
 * Limitation: Item name and description should not contain commas.
******************************************************************/

function sendRequest($url,$key,$xml){
	$ch = curl_init();    // initialize curl handle
	curl_setopt($ch, CURLOPT_URL, $url); // set url to post to
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); // return into a variable
	curl_setopt($ch, CURLOPT_TIMEOUT, 4); // times out after 4s
	curl_setopt($ch, CURLOPT_POSTFIELDS, $xml); // add POST fields
	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE); // turn off verification of SSL for testing
	curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE); // turn off verification of SSL for testing
	curl_setopt($ch, CURLOPT_USERPWD, $key);
	curl_setopt($ch, CURLOPT_USERAGENT, "API Test Tool");
	$result = curl_exec($ch); // run the whole process
	curl_close ($ch);
	if (strlen($result) < 2) $result = "Could not execute curl.";
	preg_match_all ("/<(.*?)>(.*?)\</", $result, $outarr,PREG_SET_ORDER);
	$n = 0;
	while (isset($outarr[$n])){
		$retarr[$outarr[$n][1]] = strip_tags($outarr[$n][0]);
		$n++;
	}
	return $result;
}

if (isset($HTTP_POST_VARS['submit'])) {
	$url = $_POST['xmlurl'];
	$key = $HTTP_POST_VARS['key'];
	$xml = stripslashes($HTTP_POST_VARS['xml']);
	$result = sendRequest($url,$key,$xml);
}

if (!$url) $url = "https://yourcompanyname.freshbooks.com/api/xml-in";
$defaultText = "<?xml version=\"1.0\" encoding=\"UTF-8\"?><request method=\"item.get\"><item_id>1</item_id></request>";
?>

<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
	<head>
		<title>API Tester</title>
		<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
	</head>
	<body>
	<style type="text/css">
		h2{font-family: Arial,Helvetica,sans-serif;font-size: 16px;color: #000000;}
	</style>
	<FORM NAME="form" METHOD=POST ACTION="<?php echo($PHP_SELF); ?>">
		<input type=hidden name='submit'>
		<h2>XML URL:&nbsp;<input size="50" name="xmlurl" value="<? echo stripslashes($url); ?>" ></h2>
		<h2>Authentication Token:&nbsp;<input size="35" name="key" value="<?= stripslashes($key); ?>"></h2>
		<h2>XML Input:</h2>
		<textarea cols="70" rows="15" name="xml" ><? if ($xml == "") echo $defaultText;	else echo stripslashes($xml);?></textarea>
		<br/><br/>
		<input name='sendxml' type='submit' value='Submit' alt='Submit' align='bottom'><br/>
		<h2>Response:</h2>
		<pre style="background-color:#eee;padding:10px 12px"><code><?
			if ($result) {
				$viewxmlresp = 1;
				echo htmlspecialchars($result);
			}?>
		</code></pre>
	</form>
</body>
</html>