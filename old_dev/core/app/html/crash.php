<?php

define('NO_AUTH',true);
require_once("CORE_app.php");

?>
<HTML>
  <HEAD>
    <TITLE>Crash CORE</TITLE>
  </HEAD>
<BODY>
<h1>This page deliberately causes a crash</h1>
<p>
It's main purpose is to test the bug system.
</p>

<?php

$GLOBAL_db->SubmitQuery('
  SELECT \'<b>\'fish foo bar </p>
    FROM "GoddessKnowsWhat"
  ;');

?>
</BODY>
</HTML>