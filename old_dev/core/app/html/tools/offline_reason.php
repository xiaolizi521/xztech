<? require_once("CORE_app.php"); ?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN">

<html>
<head>
	<title>Untitled</title>
</head>
<?require_once("tools_body.php");?>

<FORM ACTION="offline_reason.php">
<INPUT TYPE="hidden" NAME="COMMAND" VALUE="SHOW_VALUE">
<% include("offline_reason.phinc"); %>
<BR><BR>
<INPUT TYPE="submit" VALUE="VIEW">

</FORM>

<?
	if($COMMAND == 'SHOW_VALUE')
	{
		if($source != '')
		{
			print("$type: $source (". getReasonValue($source).")");
		}
		else
		{
			print("Pick a source dummy.");
		}
	}
?>


</body>
</html>
