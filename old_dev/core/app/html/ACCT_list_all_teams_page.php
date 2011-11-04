<?php
require_once("CORE_app.php");
require_once("team_display.php");

?>
<html>
<HEAD>
    <TITLE> CORE: View All Teams </TITLE>
    <LINK href="/css/core_ui.css" rel="stylesheet">
<SCRIPT LANGUAGE="JavaScript" SRC="/script/popup.js" TYPE="text/javascript"></SCRIPT>
</HEAD>
<BODY MARGINWIDTH="10"
      MARGINHEIGHT="10"
      LEFTMARGIN="10"
      TOPMARGIN="10"
      BGCOLOR="#FFFFFF">
<?php
if( in_dept("CORE") ) {
    $query = '
SELECT "ID"
FROM "ACCT_Team"';
} else {
    $query = '
SELECT "ACCT_TeamID"
FROM "ACCT_xref_Team_Contact_TeamMemberRole"
GROUP BY "ACCT_TeamID"';
}

$result = $GLOBAL_db->SubmitQuery($query);
$num_items = $result->numRows();
for( $i=0; $i<$num_items ; $i++ ) {
    $team_id = $result->getCell($i,0);
    DisplayTeam($team_id,true);
    echo "\n<br>\n";
}
?>
</body>
</html>