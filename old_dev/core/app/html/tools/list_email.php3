<? require_once("CORE_app.php");?>

<TITLE>List of Email Addresses</TITLE>
<?require_once("tools_body.php");?>

<BODY BGCOLOR=#FFFFFF>

<?
	$pgemails=$db->SubmitQuery("
        SELECT DISTINCT email 
        FROM no_spam_list 
        ORDER BY email ASC;
        ");
	$numbl=$pgemails->numRows();
	for ($i=0;$i<$numbl;$i++)
	{
	 //	pg_exec($conn,"delete from email_list where email~*'".$pgemails->getResult($i,0)."';");
	}
	$pgemails->freeResult();
//	$db->SubmitQuery("delete from email_list where email~*',';");
//	$db->SubmitQuery("delete from email_list where email='';");
	$pgemails=$db->SubmitQuery("
        SELECT DISTINCT email 
        FROM email_list 
        ORDER BY email ASC;
        ");
	$num=$pgemails->numRows();
	$ctr=0;
	for ($i=0;$i<$num;$i++)
	{
		if ($ctr_on)
		{
			if ($ctr==14)
			{
					print("<br>\n<p>");
					$ctr=0;
			}
		}
		if ($ctr!=0 )
			print(",");

		print(" ".$pgemails->getResult($i,0)." ");
		$ctr++;
	}
?>
