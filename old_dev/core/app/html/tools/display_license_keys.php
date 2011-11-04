<? 
include("CORE.php");
include("license.phlib");
require_once("menus.php");

if(!isset($license_index))
{
    DisplayError("License index missing.");
}

function DisplayAssignTableKeys($title, $keys, 
    $show_recycle_link=false, $show_info_link=false)
{
    global $COMMAND_RECYCLE_STATIC_KEY;
    global $COMMAND_DYNAMIC_KEY_INFO;
    global $license_group;
    global $license_index;
    ?>
<html id="mainbody">
  <head>
    <?= menu_headers() ?>
  </head>
  <?= page_start() ?>
<TABLE BORDER="0"
       CELLSPACING="0"
       CELLPADDING="2"
       CLASS="titlebaroutline">
<TR>
   <TD>
	<TABLE WIDTH="100%"
	       BORDER="0"
	       CELLSPACING="0"
	       CELLPADDING="0"
          BGCOLOR="FFFFFF">
    <TR>       
        <TD> 
         		<TABLE BORDER="0"
         		       CELLSPACING="2"
         		       CELLPADDING="2">
         		<TR>
         			<TD BGCOLOR="#003399" CLASS="hd3rev"> <?print($title);?> </TD>
         		</TR>
               <TR>
                  <TD> 
<!-- Begin Table Content --------------------------------------------------  -->                  

    <TABLE BORDER="0"
       CELLSPACING="1"
       CELLPADDING="2">
    <TR>
      <TH> Key
      <TH> Computer
      <TH> Sku
      <TH colspan=3> Product Label (unique on computer)
    <?
    for ($i = 0; $i < $keys->numRows(); $i++)
    {
        $row = $keys->fetchArray($i);
        print("<TR bgcolor=#ddDDdd>"
            . "<TD width=1%><tt>" . $row['license_key']
            . "<TD width=1%><a href=display_computer.php3?" 
            . "computer_number=" . $row['computer_number']
            . ">" . $row['computer_number'] . "</a>"
            . "<TD width=1%>" . $row['product_sku']);
        print("<TD>" . $row['product_label'] . "</TD>\n");
        if ($show_info_link) {
            print("<TD width=1%><a href=edit_license_keys.php?"
                . "command=$COMMAND_DYNAMIC_KEY_INFO&"
                . "license_index=$license_index&"
                . "license_key=" . $row['license_key']
                . ">Info</a></TD>");
        }
        if ($show_recycle_link)
        {
            print("<TD width=1%><a href=edit_license_keys.php?"
                . "command=$COMMAND_RECYCLE_STATIC_KEY&"
                . "license_index=$license_index&"
                . "license_key=" . $row['license_key']
                . ">Recycle</a></TD>");
        }
        else
        {
            print("<TD width=1%>&nbsp;</TD>");
        }
        print("</TR>\n");
    }
    ?>
    </TABLE>
    <?
}

$license_group = new LicenseGroup($db, $license_index);

$title = "Display License Keys<br>\"" 
    . $license_group->get('license_name') . '"';
print("<title>$title</title>");
require_once("tools_body.php");
print CreateHeadlineString($title);

if ($license_group->get('key_based') == 'f')
{
    DisplayError("The license group \"" 
        . $license_group->get('license_name') . "\" does not use keys.");
}

$online_assigned_count = $license_group->getActiveKeyAssignmentCount();
$offline_assigned_count = $license_group->getInactiveKeyAssignmentCount();
if ($license_group->get('recyclable_key') == 't')
{
    $unassigned_count = $license_group->getUnassignedKeyCount();
}
else
{
    $unassigned_count = 0;
}
$assigned_count = $online_assigned_count + $offline_assigned_count;
$total_key_count = $assigned_count + $unassigned_count;
?>
<?include("form_wrap_begin.php")?>
<TABLE WIDTH=540 BORDER=0>
<?if(in_dept('INVENTORY')):?>
<TR><TH colspan=2 align=center>
<FORM ACTION=edit_license_keys.php>
<input type=hidden name=license_index value="<?print($license_index);?>">
<input type=submit name=command
    value="<?
        if($license_group->get('recyclable_key') == 't')
        {
            print($COMMAND_ADD_STATIC_KEY_FORM);
        }
        else
        {
            print($COMMAND_ASSIGN_DYNAMIC_KEY_FORM);
        }
        ?>">
</FORM>
</TH></TR>

<?endif;?>
<TR><TH align=left>Number of licenses purchased:</TH>
    <TD><?print($license_group->get('license_count'));?>

<?if ($license_group->get('recyclable_key') == 't'): ?>
<TR><TH align=left>Number of keys in pool:</TH>
    <TD><?print($total_key_count);?>
<?endif;?>

<TR><TH align=left>Number of keys assigned:</TH>
    <TD><?print($assigned_count);?>

</TABLE>
<?
if ($license_group->get('recyclable_key') == 't')
{
    ?>
    <TABLE WIDTH=540 BORDER=0>
    <TR bgcolor=black>
        <TH colspan=2><font color=white>Unassigned keys</TH></TR>
    <?
    $unassigned_keys = $license_group->getUnassignedKeys();
    if ($unassigned_keys->numRows() <= 0)
    {
        print("<TR><TH colspan=2>None</TH></TR>\n");
    }
    for ($i = 0; $i < $unassigned_keys->numRows(); $i++)
    {
        $row = $unassigned_keys->fetchArray($i);
        print("<TR bgcolor=#ddDDdd>");
        print("<TD><tt>" . $row['license_key'] . "</TD>\n");
        if (in_dept('INVENTORY'))
        {
            print("<TD width=1%><a href=edit_license_keys.php?"
                . "command=$COMMAND_ASSIGN_STATIC_KEY_FORM&"
                . "license_index=$license_index&"
                . "license_key=" . urlencode($row['license_key'])
                . ">Assign</a>\n");
        }
    }
}

if ($license_group->getRecycledKeyAssignmentCount())
{
    print("<TR><TH>None</TH></TR>\n");
}
?>
</TABLE>
<?
$license_type = $license_group->getLicenseType();
if ($license_type['id'] == 'DYNAMIC_KEY') {
    $show_info_link = true;
}
else {
    $show_info_link = false;
}


if ($license_group->getActiveKeyAssignmentCount())
{
    $online_assigned_keys = $license_group->getActiveKeyAssignments();
    DisplayAssignTableKeys('Assigned Keys', $online_assigned_keys, false,
        $show_info_link);
}
if ($license_group->getInactiveKeyAssignmentCount())
{
    $online_assigned_keys = $license_group->getUnusedKeyAssignments();
    if (in_dept('INVENTORY') || in_dept('CORE') )
    {
        DisplayAssignTableKeys('Unused Assigned Keys', $online_assigned_keys, 
            true, $show_info_link);
    }
    else
    { 
        DisplayAssignTableKeys('Unused Assigned Keys', $online_assigned_keys,
            false, $show_info_link);
    }
}
if ($license_group->getRecycledKeyAssignmentCount())
{
    $recycled_keys = $license_group->getRecycledKeyAssignments();
    DisplayAssignTableKeys('Recycled Keys', $recycled_keys,
        false, $show_info_link);
}

include("form_wrap_end.php");
loadLicenseTree($license_index, true);
?>
<!-- End Table Content ----------------------------------------------------  -->
                  </TD>
               </TR>
         		</TABLE>
        </TD>
    </TR>
    </TABLE></TD>
</TR>
</TABLE>
  <?= page_stop() ?>
</HTML>
