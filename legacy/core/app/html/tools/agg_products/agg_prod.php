<?require_once("CORE_app.php"); ?>
<?	
	$dont_let_them_upgrade=false;
	if (!isset($full_log))
		$full_log=false;
		//$print_sql=true;
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
		else
		{
			if (!isset($command_info))
				$command_info="";
			$agg_prod->processor($command,$command_info);
		}
		//This is where the action stuff used to live
	}
$tree_url = "$py_app_prefix/account/tree.pt?" .
"account_number=$account_number&".
"agg_product_number=$agg_product_number&current=view&";

?>
<HTML id="mainbody">
<HEAD>
<? print("<TITLE>Modify Aggregate Product $agg_product_number </TITLE>");?>
    <LINK HREF="/css/core_ui.css" REL="stylesheet">
    <SCRIPT LANGUAGE="JavaScript1.2" TYPE="text/javascript">
    // Refreshes the tree
    try {
    top.frames["left"].document.location.href = 
    "<?=$tree_url?>" +
    top.frames["left"].cleanargs;
    }
    catch (e) {}
    </SCRIPT>
<?
$no_menu = true;
require_once("tools_body.php");
?>
<TABLE BORDER="1"
       CELLSPACING="0"
       CELLPADDING="0">
<TR>
	<TD>
		<TABLE BORDER="0"
		       CELLSPACING="2"
		       CELLPADDING="2">
		<TR>
			<TD BGCOLOR="#003399" CLASS="hd3rev" COLSPAN="3"> Product Tools: 
				#<?print($agg_product_number);?> </TD>
		</TR>
		<TR>
			<TD COLSPAN="3"> <?$agg_prod->customActions();?> </TD>
		</TR>
		<TR>
			<TD BGCOLOR="#CCCCCC" CLASS="label"> Quick Comment </TD>
			<TD>
				<FORM ACTION="<?print($PHP_SELF);?>" METHOD="POST">
				<INPUT TYPE=HIDDEN NAME="agg_product_number" VALUE="<?print($agg_product_number);?>">
				<INPUT TYPE=HIDDEN NAME="command" VALUE="QUICK_COMMENT">
				<TEXTAREA COLS=40 ROWS=2 WRAP=VIRTUAL NAME="quick_comment"></TEXTAREA>
			</TD>
			<TD ALIGN=RIGHT><INPUT TYPE="image"
			                       SRC="/images/button_arrow_off.jpg"
			                       ALT="Go"
			                       WIDTH="20"
			                       HEIGHT="20"
			                       BORDER="0"></FORM></TD>
		</TR>
		<TR>
			<TD BGCOLOR="#CCCCCC" CLASS="label"> Current Status: </TD>
			<TD COLSPAN="2"> <FONT COLOR=#FF0000> <?print($agg_prod->getData("status"));?> </FONT></TD>
		</TR>
		<?
			//Test to see if there are any higher levels
			$max_status=$agg_prod->getMaxStatus();
			$min_status=$agg_prod->getMinStatus();
		?>
		<?if ($dont_let_them_upgrade||$agg_prod->getData("status_number")>=$max_status):?>
		<?elseif ($agg_prod->getData("status_number")<12):?>
				<?
					$info=$agg_prod->getNextStatus();
					$next_status=$info["status"];
					$new_status=$info["status_number"];
				?>
					<TR>
						<TD BGCOLOR="#CCCCCC" CLASS="label"> Upgrade Status To: </TD>
						<TD> <FONT COLOR=#FF0000> <?print($next_status);?> </FONT> </TD>
						<TD ALIGN=RIGHT>
							<A HREF="<?print($agg_prod->confirmUpgradeUrl($new_status));?>">
							<IMG SRC="/images/button_arrow_off.jpg" 
							     WIDTH="20" 
								 HEIGHT="20" 
								 BORDER="0" 
								 ALT="Go" 
								 ALIGN="ABSMIDDLE"></A></TD>
					</TR>
			<?else:?>
			<?$urls=split("\?",$agg_prod->confirmUpgradeUrl(""));?>
			<FORM ACTION="<?print($urls[0]);?>" METHOD=POST>
				<TR>
					<TD>
					<INPUT TYPE="HIDDEN" Name="command" VALUE="UPGRADE_STATUS">
					<INPUT TYPE="HIDDEN" Name="agg_product_number" VALUE="<?print($agg_prod->key());?>">
					Upgrade Status To:</TD>
					<TD><SELECT name="new_status">
					<?
						$status_info=$db->SubmitQuery("select status_rank,status from ".$agg_prod->getStatusTable()." where status_rank>".$agg_prod->getData("status_number")." AND agg_class='".get_class($agg_prod)."' order by status_rank ASC;");
						$num=$db->NumRows($status_info);
						for ($i=0;$i<$num;$i++)
						{
							print("<OPTION VALUE=".$db->GetResult($status_info,$i,"status_rank").">".$db->GetResult($status_info,$i,"status")."\n");
						}
						$db->FreeResult($status_info);
					?>
					</SELECT></TD>
					<TD ALIGN=RIGHT><INPUT TYPE=IMAGE SRC="/images/button_arrow_off.jpg" WIDTH=20 HEIGHT=20 BORDER=0 ALT="->" ALIGN="ABSMIDDLE"></TD>
				</TR>
			</FORM>
		<?endif;?>
		<?if ($agg_prod->getData("status_number")>$min_status):?>
			<?if ($agg_prod->getData("status_number")<=12):?>
			
				<?
					$info=$agg_prod->getPrevStatus();
					$prev_status=$info["status"];
					$new_prev_status=$info["status_number"];
				?>
			<TR>
					<TD CLASS="label" BGCOLOR="#CCCCCC">Downgrade Status To: </TD>
					<TD> <FONT COLOR=#FF0000><?print($prev_status);?></FONT> </TD>
					<TD ALIGN=RIGHT>
					<A HREF="<?print($agg_prod->downgradeUrl($new_prev_status));?>">
					<IMG SRC="/images/button_arrow_off.jpg" 
					     WIDTH="20" 
						 HEIGHT="20" 
						 BORDER="0" 
						 ALT="Go" 
						 ALIGN="ABSMIDDLE"></A></TD>
			</TR>
			<?else:?>
			<?$urls=split("\?",$agg_prod->downgradeUrl(""));?>
			<FORM ACTION="<?print($urls[0]);?>" METHOD=POST>
			<INPUT TYPE="HIDDEN" Name="command" VALUE="DOWNGRADE_STATUS">
			<INPUT TYPE="HIDDEN" Name="agg_product_number" VALUE="<?print($agg_prod->key());?>">
			<TR>
					<TD CLASS="label" BGCOLOR="#CCCCCC"> Downgrade Status To: </TD>
                <TD><SELECT name="command_info[new_status]">
                    <?
                    $status_info = $db->SubmitQuery("
                        SELECT status_rank, status 
                        FROM ".$agg_prod->getStatusTable()." 
                        WHERE status_rank < "
                            . $agg_prod->getData("status_number")
                            . " and status_rank>=12  
                            AND agg_class='".get_class($agg_prod)."' 
                        ORDER BY status_rank ASC
                        ");
                    $num=$db->NumRows($status_info);
                    for ($i=0;$i<$num;$i++)
                    {
                        $option_status_rank = $db->GetResult($status_info, 
                            $i, "status_rank");
                        $option_status = $db->GetResult($status_info, 
                            $i, "status");
                        print("<OPTION VALUE=$option_status_rank>
                            $option_status</OPTION>\n");
                    }
                    $db->FreeResult($status_info);
					?>
					</SELECT></TD>
					<TD ALIGN=RIGHT><INPUT TYPE="image"
					                       SRC="/images/button_arrow_off.jpg"
					                       ALT="Go"
					                       WIDTH="20"
					                       HEIGHT="20"
					                       BORDER="0"></TD>
			</TR>
		</FORM>
			<?endif;?>
		<?endif;?>		
		<TR>
				<TD BGCOLOR="#CCCCCC" CLASS="label"> Delete Product </TD>
				<TD> &nbsp; </TD>
				<TD ALIGN=RIGHT>
				<A HREF="<?print($agg_prod->confirmDeleteUrl());?>">
				<IMG SRC="/images/button_arrow_off.jpg" 
				     WIDTH="20" 
					 HEIGHT="20" 
					 BORDER="0" 
					 ALT="X" 
					 ALIGN="ABSMIDDLE"></A></TD>
		</TR>		
		</TABLE>
	</TD>
</TR>
</TABLE>
<BR>
<BR CLEAR="all">
<TABLE BORDER="1"
       CELLSPACING="0"
       CELLPADDING="0">
<TR>
	<TD>
		<TABLE BORDER="0"
		       CELLSPACING="2"
		       CELLPADDING="2">
		<TR>
			<TD BGCOLOR="#003399" CLASS="hd3rev"> Product Details: </TD>
		</TR>
		<TR>
			<TD> <?$agg_prod->display();?> </TD>
		</TR>
		</TABLE>
	</TD>
</TR>
</TABLE>
<BR>
<BR CLEAR="all">

<TABLE BORDER="1"
       CELLSPACING="0"
       CELLPADDING="0">
<TR>
	<TD>
		<TABLE BORDER="0"
		       CELLSPACING="2"
		       CELLPADDING="2">
		<TR>
			<TD BGCOLOR="#003399" CLASS="hd3rev" COLSPAN="2"> Staff Comments: </TD>
		</TR>
		<?if ($full_log=="1"):?>
		<TR>
			<TD VALIGN=TOP><A HREF="<?print($REQUEST_URI);?>&full_log=0">
			<IMG SRC="/images/button_nav_next_tiny.gif" 
				 WIDTH="13" 
				 HEIGHT="13" 
				 BORDER="0" 
				 ALT="Show Full Log"></A></TD>
			<TD><? $agg_prod->displayReverseLog(); ?> </TD>
		</TR>
		<?else:?>
		<TR>
			<TD VALIGN=TOP><A HREF="<?print($REQUEST_URI);?>&full_log=1">
			<IMG SRC="/images/button_nav_next_tiny.gif" 
				 WIDTH="13" 
				 HEIGHT="13" 
				 BORDER="0" 
				 ALT="Hide Full Log"></A></TD>
			<TD> <?  $agg_prod->displayLastLog(); ?> </TD>
		</TR>
		<?endif;?>
		</TABLE>
	</TD>
</TR>
</TABLE>
<?= page_stop() ?>
</HTML>
