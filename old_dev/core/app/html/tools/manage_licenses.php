<? 
include("CORE.php");
include("license.phlib");

############################
# Contents:                #
# 1. Function definitions. #
# 2. Constants defined.    #
# 3. Form processed.       #
# 4. Form displayed.       #
############################

$COMMAND_ADD_LICENSE_GROUP = 'Add License Group';
$COMMAND_EDIT_LICENSE_GROUP = 'Edit License Group';
$COMMAND_DISPLAY_KEYS = 'Display Keys in Group';
$COMMAND_DISPLAY_LICENSE_USAGE = 'Display License Usage';

if (isset($command))
{
	if ($command == $COMMAND_ADD_LICENSE_GROUP)
	{
		ForceReload('edit_license_group.php');	
		exit();
	}	

	// all other commands require a license_index
	if (!isset($license_index) || $license_index == '')
	{
		DisplayError("You must select a license group.");
		exit();
	}

	if ($command == $COMMAND_EDIT_LICENSE_GROUP)
	{
		ForceReload("edit_license_group.php?"
			. "license_index=$license_index");	
	}
	else if ($command == $COMMAND_DISPLAY_KEYS)
	{
		ForceReload("display_license_keys.php?"
			. "license_index=$license_index");	
	}
	else if ($command == $COMMAND_DISPLAY_LICENSE_USAGE)
	{
		ForceReload("display_license_usage.php?"
			. "license_index=$license_index");	
	}
	exit();
}
?>
<html id="mainbody">
<head>
<?
$title = "Manage Licenses";
print("<title>$title</title>");
require_once("tools_body.php");
include("form_wrap_begin.php")
?>
<form action=manage_licenses.php>
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
         			<TD BGCOLOR="#003399" CLASS="hd3rev"> Manage Licenses </TD>
         		</TR>
               <TR>
                  <TD>
                  <TABLE HEIGHT="17"
                         BORDER="0"
                         CELLSPACING="1"
                         CELLPADDING="2"
                         VALIGN="TOP">
                  <TR>
                     <TD COLSPAN=2>
                     <input type=submit name=command 
                     	value="<?=$COMMAND_ADD_LICENSE_GROUP?>"></TD>
                  </TR>
                  <TR>
                     <TD>
                     <input type=submit name=command 
                     	value="<?=$COMMAND_EDIT_LICENSE_GROUP?>">
                     <br>
                     <input type=submit name=command 
                     	value="<?=$COMMAND_DISPLAY_KEYS?>">
                     <br>
                     <input type=submit name=command 
                     	value="<?=$COMMAND_DISPLAY_LICENSE_USAGE?>"></TD>
                     <TD VALIGN=TOP>
                     <SELECT name=license_index>
                     <OPTION value="">-- SELECT LICENSE GROUP --
                     <?
                     
                     $license_list = getLicenseGroups();
                     foreach($license_list as $row) {
                     	print('<option value="'
                     		. $row['license_index'] . '">"'
                     		. $row['license_name'] . "\"</option>\n");
                     }
                     ?>
                     </SELECT></TD>
                  </TR>
                  </TABLE>                  
                  
                  </TD>
               </TR>
         		</TABLE>
        </TD>
    </TR>
    </TABLE></TD>
</TR>
</TABLE>
<?include("form_wrap_end.php");?>
</FORM>
<?
loadLicenseTree();
?>

<?= page_stop() ?>
</html>
