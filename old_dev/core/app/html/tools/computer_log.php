<?php
require_once("CORE_app.php");

?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<html>
<head>
<title>CORE: Computer Log: <?=$customer_number?></title>
<link href="/css/core2_basic.css" rel="stylesheet">
</head>
<body>

<table class="blueman" style="width: auto; margin: auto">
<tr>
  <th  class="blueman">Computer Log: <?=$computer_number?></th></tr>
<tr  class="odd">
  <td colspan="1"  class="blueman">
<!-- Begin Blueman Table -->

<?display_log_reverse($conn,"computer_log","comments","customer_number=$customer_number and computer_number=$computer_number");?>


<!-- End Blueman Table -->
</td></tr></table>

</body>
</html>