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
?>
<HTML id="mainbody">
<head>
	<? print("<TITLE>Upgrade Aggregate Product $agg_product_number </TITLE>");?>
    <LINK HREF="/css/core_ui.css" REL="stylesheet">	
<?require_once("tools_body.php");?>
<BR>
<TABLE BORDER="1"
       CELLSPACING="0"
       CELLPADDING="0">
<TR>
	<TD>
		<TABLE BORDER="0"
		       CELLSPACING="2"
		       CELLPADDING="2">
		<TR>
			<TD BGCOLOR="#003399" CLASS="hd3rev" COLSPAN="2"> Product Upgrade: 
				#<?print($agg_product_number);?> </TD>
		</TR>
		<TR>
		<?
			$status = $db->GetVal("
					select 
						status 
					from 
						".$agg_prod->getStatusTable()." 
					where 
						status_number=$new_status  
						AND agg_class='".get_class($agg_prod)."';");
			
			$criteria = $db->GetVal("
					select 
						criteria 
					from 
						".$agg_prod->getStatusTable()." 
					where 
						status_number=".$agg_prod->getData("status_number")."  
						AND agg_class='".get_class($agg_prod)."';");
		?>
			<TD COLSPAN="2"> Please confirm your request to upgrade the product 
			to the next stage.</TD>
			
		<TR>
			<TD BGCOLOR="#CCCCCC" CLASS="label"> Upgrade Status To: </TD>
			<TD> <B><?print($status);?></B> <BR> <?print($criteria);?> </TD>
		</TR>
		<TR>
		
		</TR>
			<TD ALIGN=RIGHT COLSPAN="2"><A HREF="<?print($agg_prod->upgradeUrl($new_status));?>">
			<IMG SRC="/images/button_command_continue_off.jpg" WIDTH="100" HEIGHT="24" BORDER="0" ALT="->" ALIGN="ABSMIDDLE"></a></TD>
		</TR>
		<TR>
			<TD COLSPAN="2"> <HR NOSHADE>
			<?$agg_prod->display();?> </TD>
		</TR>		
		</TABLE>
	</TD>
</TR>
</TABLE>
<?= page_stop() ?>
</HTML>
