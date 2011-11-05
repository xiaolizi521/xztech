<?require_once("CORE_app.php");?>
<HEAD>
    <META HTTP-EQUIV="REFRESH" CONTENT="10;URL=/promon.php3">
    <META HTTP-EQUIV="PRAGMA" CONTENT="no-cache">
    <TITLE>PROMON - Critical view</TITLE>
</HEAD>
<?require_once("tools_body.php");?>
<?
	$page=get_page("promon.internal.rackspace.com","/promon/Critical.html");
	$page=split("Content-Type: text/html",$page);
	$page[1]=ereg_replace("display_computer","display_promon_computer",$page[1]);
	print($page[1]);
?>

	
