<? require_once("CORE_app.php"); ?>
<?
	$customer_pass=$db->SubmitQuery("
		SELECT password 
		from customer_auth 
		where customer_number=$customer_number;");
	$pass_exists = $db->NumRows($customer_pass);
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN">
<HTML id="mainbody">
<HEAD>
	<TITLE>CORE:  Create/Change MyRackspace Password </TITLE>
    <LINK HREF="/css/core_ui.css" REL="stylesheet">
<?require_once("tools_body.php");?>

<p>Beginning with MyRackspace 4.0, Rackers use a modified version of their Rackspace UID and SSO PWD to login into customer accounts on MyRackspace.
The modified UID is formed by prepending the string <strong>'racker_'</strong> to their normal UID.
For example, if your UID is <strong>'bsmith'</strong>, you would enter <strong>'racker_bsmith'</strong> in the user name field on the MyRackspace login page and your SSO PWD in the password field.</p>

<p><a href="https://my.rackspace.com/portal/">Click here to continue to MyRackspace login.</a></p>
<?= page_stop() ?>
</HTML>

