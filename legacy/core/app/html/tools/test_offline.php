<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN">

<html>
<head>
	<title>Untitled</title>
</head>
<?require_once("tools_body.php");?>

<FORM ACTION="offline_reason.php">
<INPUT TYPE="hidden" NAME="COMMAND" VALUE="SHOW_VALUE">

<SCRIPT LANGUAGE="JavaScript1.2">
<!--
if (document.images) 
{
	
	source = new Object();
	source.g100 = new Array
	source.g100[0] = new Array
		source.g100[0][0] = "In-House";
		source.g100[0][1] = "50";
		source.g100[1][0] = "No Traffic";
		source.g100[1][1] = "52";		
		source.g100[2][0] = "Out of Business";
		source.g100[2][1] = "51";	
}
function updateMenus(type) 
{
 if (document.images) 
 {
  	sel = type.selectedIndex;
  	if (sel == 1) 
  	{
   		sourc = source.g100[i];
  	} 
  		else if (sel == 2) 
  		{
   			sourc = source.g200;
  		} 
  		else if (sel == 3) 
  		{
   			sourc = source.g300;
 		} 
  		else if (sel == 4) 
  		{
   			sourc = source.g400;
  		} 
  		else if (sel == 5) 
  		{
   			sourc = source.g500;
  		} 
		else if (sel == 6) 
  		{
   			sourc = source.g600;
  		} 
  	else 
  	{
   		sourc = new Array();
  	}
  type.form.source.length = sourc.length;
  for(i=0;i<sourc.length;i++)

  type.form.source.options[i].text = sourc[i][0];
  type.form.source.options[i].value = sourc[i][1];
 } 
 else 
 {
  	alert("Your browser does not allow this script to modify the "
  	+"type and source selection lists. Get a newer browser.");
 }
}
// -->
</SCRIPT>

	<B>Category:</B>
	<SELECT name="type" size="1" onChange="updateMenus(this)">
		<OPTION>Please select...
		<OPTION>Market Trend
		<OPTION>Service Failure
		<OPTION>Competition
		<OPTION>Non-payment
		<OPTION>AUP
		<OPTION>Migration
	</SELECT>
	<B>Type:</B>

	<SELECT name="source" size="6">
		<OPTION>&nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp;
	</SELECT>


<BR><BR>
<INPUT TYPE="submit" VALUE="VIEW">

</FORM>



</body>
</html>
