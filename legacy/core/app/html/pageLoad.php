<?php
if( empty($numberOfLoads) ) {
    $numberOfLoads = 4;
}
?>
<html>
<head>
	<title>CORE: Page Timer </title>
    <link href="/css/core_ui.css" rel="stylesheet">
<script language=javascript>

var time1 = new Date()
var startTime = time1.getTime()
var numberOfLoads = <?=$numberOfLoads?>;

<?php
if( !empty($url) ) {
    echo "/*url*/\n";
    $cookieString = $url;
} elseif( !empty($HTTP_COOKIE_VARS["file"]) ) {
    echo "/*cookie*/\n";
    $cookieString = $HTTP_COOKIE_VARS["file"];
} else {
    echo "/*empty*/\n";
    $cookieString = '';
}
?>
var cookieString = "<?=$cookieString?>";

function setURL( file ) {
    document.cookie = "file=" + escape( file );
    window.location.href = "pageLoad.php";
}
function resetFile() {
    document.cookie = "file=" + escape("");
    window.location.href = "pageLoad.php";
}
function pageDone() {
    var time2 = new Date();
    var endTime = time2.getTime();
    var results = document.getElementById("results");
    var duration = (endTime-startTime)/1000;
    var mean = duration / numberOfLoads;
	var date = new Date();
	var d  = date.getDate();
	var day = (d < 10) ? '0' + d : d;
	var m = date.getMonth() + 1;
	var month = (m < 10) ? '0' + m : m;
	var yy = date.getYear();
	var year = (yy < 1000) ? yy + 1900 : yy;
	var hour = date.getHours();
	var min = date.getMinutes();
	var seconds = date.getSeconds();
    results.innerHTML = "Date:            " + day + "/" + month + "/" + year + "\nTime:            " + hour+ ":" + min + ":" + seconds + "\nURL:             " + cookieString + "\nNumber Of Loads: " + numberOfLoads + "\nMean Seconds:    " + mean + "\nTotal Seconds:   " + duration + "\n";
}
</script>

<meta content="MSHTML 5.00.3314.2100" name=generator></head>
<body <?php if(!empty($cookieString)){ echo "onload=pageDone()"; }?>>

<center>
<table>
<tr>
	<th> URL </th>
	<td>
		<form action="pageLoad.php" onsubmit="setURL(getElementById('url1').value);">
		<input type="text" id="url1" name="url" value="<?=$cookieString?>" maxlength="500" size="60">
	 	<input type=submit value="Time Page">
		<input onclick=resetFile() type=button value="Reset"></form>	
	</td>
</tr>
<tr>
	<th>URL</th><td>
		<form action="pageLoad.php" onsubmit="setURL(getElementById('url2').value);">
		<select id="url2" name="url">
		<option value="/py/splash.pt">Splash</option>
		<!-- <option value="/py/ticket/queue.esp?open=q1">Queue (ESPy)</option> -->
		<option value="/py/ticket/queue.pt?open=q1">Queue (PSE)</option>
		<option value="/py/account/view.pt?account_number=11">Account #11</option>
		<option value="/py/account/tree.pt?account_number=11">Account Tree #11</option>
		<option value="/py/account/view.pt?account_number=545">Account #545</option>
		<option value="/py/account/tree.pt?account_number=545">Account Tree #545</option>
		<option value="/py/ticket/view.pt?ref_no=040316-1028">Ticket</option>
		<option value="/tools/DAT_display_computer.php3?computer_number=2673">Computer</option>
		</select>
	 	<input type=submit value="Time Page">
		<input onclick=resetFile() type=button value="Reset"></form></td>
</tr>
</table>
<div></div>
</center>

<?php
if( !empty($cookieString) ) {
?>
<pre id="results" style="margin: auto; width: 90ex; border: solid thin black; background: #EEE">
Please wait while profiling....
</pre>
<?php
for( $i=0; $i<$numberOfLoads; $i++ ) {
?>
<center>
<script language=javascript>
if (cookieString != "") {
document.write ("<iframe id=\"inline<?=$i?>\" width=640 height=128 src=\"" + cookieString + "\" ></iframe><br>")
}
</script>
</center>
<?php } } ?>
</body></html>
