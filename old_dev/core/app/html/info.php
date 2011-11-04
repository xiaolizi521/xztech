<?php
require_once("authentication.php");

require_once("CORE_app.php");
if( ! in_dept("CORE") ){
    ForceReload( "/" );
}
if( empty($notitle) ):
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<html>

<head>
  <title>CORE</title><script src="/script/MENU_workspace.js" type="text/javascript"></SCRIPT>

</head>
<body>
<?php
endif; //if( empty($notitle) )
?>
<link href="/css/core2_basic.css" type="text/css" rel="stylesheet">
<table class="blueman" onclick="" style="width: 100%">
<thead>
<tr>
  <th  class="blueman"> 
  PHP CORE Settings
  </th>
</tr>
</thead>

<tbody>
<tr  class="odd">
  <td colspan="1" style="padding: 0.5ex" class="blueman">
<pre><?php
print "DB: $CORE_db_login on $CORE_db_dbname@$CORE_db_host\n";
print "CORE_ROOT: ".CORE_ROOT."\n";
print "LOCALCONFIG_FILE: ". $LOCALCONFIG_FILE ."\n";
print "$session_key: " . $$session_key . "\n";
?></pre>
<?php
if( !empty( $show_more ) ) {
    require_once("authentication.php");
    print "<pre>";
    print "Userid: ".COREAUTH_GetUserid()."\n";
    print "EmpNum: ".COREAUTH_GetEmployeeNumber()."\n";
    print "GLOBALS: ";
    print_r( $GLOBALS );
    print "</pre>\n";
}
?>
</td></tr></tbody></table>
<?php
if( empty($notitle) ):
?>
</body>
</html>
<?php
endif; //if( empty($notitle) )
?>
