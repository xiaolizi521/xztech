<?php
require_once("build_logic.php");
$title = "Build Query";
$notes = '
<p>
Use this tool to search for computers that have a specific set of parts.
</p>
<p>
Use <b>Add Part</b> to add a group of parts to your search.
You can further contral your search by adding a start and end date.
</p>
<p>
An empty date will be ignored.  The date format should be <b>mm/dd/yyyy</b>.
You can use the calendar button (<img src="/images/show-calendar.gif" width="24" height="22" border="0">) to help you select a date.
</p>
';

getReportDB(); // Check that we can connect to it.
if( empty($REPORTDB_IS_AVAILABLE) ) {
    $notes .= '<p style="font-weight: bolde">All data is current as of now.</p>';
} else {
    $notes .= '<p style="font-weight: bold">All data is current as of yesterday night.</p>';
}
require_once("header.php");
?>

<form name='calform' action='summary_page.php'>
<table border='0'>
  <tr>
    <td align='right'>
      Datacenters to Search:
    </td>
    <td>
      <?=$dc_select ?>
    </td>
  </tr>
  <tr>
    <td align='right'>
      Require SLA Type:
    </td>
    <td>
      <?=$sla_select ?>
    </td>
  </tr>
  <tr>
    <td align='right'>
    Start Date:
    </td>
    <td>
        <input type='text' name="startdate" size='10' value="<?=$startdate ?>">
        <a href="javascript:show_calendar('calform.startdate');" onmouseover="window.status='Pick start date';return true;" onmouseout="window.status='';return true;"><img src="/images/show-calendar.gif" width='24' height='22' border='0'></a>
    </td>
  </tr>
  <tr>
    <td align='right'>
    End Date:
    </td>
    <td>
        <input type='text' name="enddate" size='10' value="<?=$enddate ?>">
        <a href="javascript:show_calendar('calform.enddate');" onmouseover="window.status='Pick end date';return true;" onmouseout="window.status='';return true;"><img src="/images/show-calendar.gif" width='24' height='22' border='0'></a>
    </td>
  </tr>
  <tr>
    <td>
      <input type='button' onClick="makePopUpNamedWin('add_page.php',700,900,'',3,'part_add')" value="Add Part">
    </td>
  </tr>
  <tr>
    <td>
        <input
        type='<?php if( $first_time ) {echo "button";} else {echo "submit";} ?>'
        value='Summarize'>
    </td>
    <td>
        <input type='checkbox' name='dotime'>Show&nbsp;SQL
    </td>
  </tr>
    <tr>
      <td colspan='2'>
      <input type='button' onClick="window.document.location='clear_handler.php'"
      value="Clear Parts">
      </td>
    </tr>
</table>
</form>
<br>
<?php
if( empty( $SESSION_parts ) ) {
    $SESSION_parts = "";
}
PrintList($SESSION_parts); 
?>

<br>
<?php require_once("footer.php"); ?>
