<?require_once("CORE_app.php"); ?>
<?	
	if (!isset($full_log))
		$full_log=false;
	if (isset($agg_product_number))
		$agg_prod=BuildAggProd($db,$agg_product_number,ADMIN);	
	else
		trigger_error("Unable to display an Aggregate Product that does not exist.",E_USER_ERROR);
        $customer_number = $agg_prod->getData("customer_number");
        $account_number = $customer_number;
	if (!empty($command)&&isset($command))
	{
		if ($command=="QUICK_COMMENT")
		{
			$agg_prod->log($quick_comment,"QUICK_COMMENT");
			ForceReload($agg_prod->displayUrl());
		}
		//This is where the action stuff used to live
	}
?>
<HTML id="mainbody">
<head>
<? print("<TITLE>Delete Aggregate Product $agg_product_number </TITLE>"); ?>
    <LINK HREF="/css/core_ui.css" REL="stylesheet">
<?require_once("tools_body.php");?>

<TABLE BORDER="1"
       CELLSPACING="0"
       CELLPADDING="0">
<TR>
	<TD>
		<TABLE BORDER="0"
		       CELLSPACING="2"
		       CELLPADDING="2">
		<TR>
			<TD BGCOLOR="#003399" CLASS="hd3rev" COLSPAN="2"> Delete Product: 
				#<?print($agg_product_number);?> </TD>
		</TR>
		<TR>
			<TD><p>Please confirm the deletion of this product. 
				This action cannont be undone. You may be better of downgrading 
				it to inactive.<BR><BR>
				
			<B>Are you sure you want it deleted?</B>
			<A HREF="<?print($agg_prod->deleteUrl());?>">
			<IMG SRC="/images/button_command_small_yes_off.jpg" 
				 WIDTH="50" 
				 HEIGHT="20" 
				 BORDER="0" 
				 ALT="Yes, delete." 
				 ALIGN="ABSMIDDLE"></A></TD>
		
		<TR>
			<TD> <?$agg_prod->display();?> </TD>
		</TR>
				</TABLE>
			</TD>
		</TR>
</TABLE>
<?= page_stop() ?>
</HTML>
