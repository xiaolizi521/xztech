<? 
include("CORE.php"); 
include("license.phlib");

############################
# Contents:                #
# 1. Function definitions. #
# 2. Constants defined.    #
# 3. Form displayed.       #
# 4. Form processed.       #
############################


$COMMAND_CREATE_NEW_LICENSE_GROUP = 'Create New License Group';
$COMMAND_EDIT_LICENSE_GROUP = 'Edit License Group';

$back_url = 'manage_licenses.php';
function DisplayHeader($title)
{
	print("<title>$title</title><body bgcolor=white>");
	print CreateHeadlineString($title);
}

if (getenv('REQUEST_METHOD') == 'GET')
{
	$form_data = $HTTP_GET_VARS;
}
else
{
	$form_data = $HTTP_POST_VARS;
}

if (isset($license_index))
{
	$license_group = new LicenseGroup($db, $license_index);
}
else
{
	$license_group = new LicenseGroup($db);
}

if (!isset($command))
{
	if (!isset($license_index))
	{
		$title = 'Add New License Group';
		$new_command = $COMMAND_CREATE_NEW_LICENSE_GROUP;
	}
	else
	{
		$title = 'Edit License Group';
		$new_command = $COMMAND_EDIT_LICENSE_GROUP;
	}
	DisplayHeader($title);
	DisplayForm($db, 'edit_license_group.php', $new_command, $license_group);
}
else if ($command == $COMMAND_CREATE_NEW_LICENSE_GROUP)
{
	$title = 'Adding New License Group';
	DisplayHeader($title);
	if(isset($license_index))
	{
		DisplayError("License number present / program error.");
	}
	$license_group->processForm($form_data);
	LateForceReload('manage_licenses.php', 1);
}
else if ($command == $COMMAND_EDIT_LICENSE_GROUP)
{
	$title = 'Editing License Group';
	DisplayHeader($title);
	if(!isset($license_index))
	{
		DisplayError("License index missing.");
	}
	$license_group->processForm($form_data);
	LateForceReload('manage_licenses.php', 1);
}

print CreateBottomLineString();
?>
