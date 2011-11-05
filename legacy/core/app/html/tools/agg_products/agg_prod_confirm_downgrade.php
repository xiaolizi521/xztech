<?require_once("CORE_app.php"); ?>
<?set_back_link("");?>
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
<? print("<TITLE>Downgrade Aggregate Product $agg_product_number </TITLE>");
?>
<?require_once("tools_body.php");?>
<BR>

<TABLE CELLSPACING=0 CELLPADDING=0 VALIGN="TOP" BORDER=0 WIDTH=540>
<!-- end spacer -->

<TR>
	<TD BGCOLOR="#000000" ALIGN="LEFT" VALIGN="TOP" COLSPAN=3 HEIGHT=17>
	<IMG SRC="/tools/assets/images/c-tl.jpg" WIDTH=10 HEIGHT=10 BORDER=0 ALT="" ALIGN="TOP"></TD>
</TR>
<TR>
	<TD BGCOLOR="#000000" ALIGN="LEFT" VALIGN="TOP" COLSPAN=3 HEIGHT=17>
	<FONT COLOR="#FFFFFF" SIZE="+2" ><CENTER>Downgrade Product</CENTER></FONT></TD>
</TR>
<!-- spacer -->
<TR>
<?
	$status=$db->GetVal("select status from ".$agg_prod->getStatusTable()." where status_number=$new_status   AND agg_class='".get_class($agg_prod)."';");
	$criteria=$db->GetVal("select criteria from ".$agg_prod->getStatusTable()." where status_number=$new_status   AND agg_class='".get_class($agg_prod)."';");
?>
<TD><p>Please confirm your request to downgrade the product to  </TD><TH><Strong><?print($status);?><BR><?print($criteria);?></STRONG></TD>
		<TD ALIGN=RIGHT><A HREF="<?print($agg_prod->downgradeUrl($new_status));?>"><IMG SRC="/tools/assets/images/arrow.jpg" WIDTH=25 HEIGHT=25 BORDER=0 ALT="->" ALIGN="ABSMIDDLE"></a></TD>

<TR>
	<TD>&nbsp;</TD>
</TR>
<TR><TD COLSPAN=8>
<?$agg_prod->display();?>
</TD></TR>
<TR>
	<TD>&nbsp;</TD>
</TR>
</TABLE>
</TD></TR>
</TABLE>

<TABLE WIDTH="540" BORDER="0" CELLSPACING="0" CELLPADDING="0" VALIGN="TOP">
<TR>
	<TD BGCOLOR="#000000" ALIGN="LEFT" VALIGN="BOTTOM" COLSPAN=3 HEIGHT=17><IMG SRC="/tools/assets/images/c-bl.jpg" WIDTH=10 HEIGHT=10 BORDER=0 ALT="" ALIGN="BOTTOM"></TD>
</TR>

</TABLE><BR CLEAR="ALL">
<BR>
</TABLE>
<?= page_stop() ?>
</HTML>
