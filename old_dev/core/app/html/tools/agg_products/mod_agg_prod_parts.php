<?php require_once("CORE_app.php"); ?>
<?
	if (isset($agg_product_number))
		$agg_prod=BuildAggProd($db,$agg_product_number,ADMIN);	
	else
		trigger_error("Unable to modify an Aggregate Product that does not exist.",E_USER_ERROR);
	
	if (isset($command)&&$command=="MOD_AGG_PROD_PARTS")
	{

		$agg_prod->updateParts($product_name);
		$agg_prod->saveParts();
		$agg_prod->save(); //Update pricing
		ForceReload($agg_prod->displayUrl());

	}
        $customer_number = $agg_prod->getData("customer_number");
        $account_number = $customer_number;

?>
<HTML id="mainbody">
<head>
   <TITLE> CORE: Add Aggregate Product </TITLE>
   <LINK HREF="/css/core_ui.css" REL="stylesheet">
</HEAD>
<?
$no_menu = true;
require_once("tools_body.php");
?>
<FORM ACTION="<?print($agg_prod->editPartsUrl());?>" METHOD="POST">
<INPUT TYPE=HIDDEN name=command value="MOD_AGG_PROD_PARTS">
<INPUT TYPE=HIDDEN name="agg_product_number" VALUE="<?print($agg_product_number);?>">

<TABLE BORDER="0"
       CELLSPACING="0"
       CELLPADDING="2"
       CLASS="titlebaroutline">
<TR>
   <TD>
	<TABLE WIDTH="100%"
	       BORDER="0"
	       CELLSPACING="0"
	       CELLPADDING="0"
          BGCOLOR="FFFFFF">
    <TR>       
        <TD> 
         		<TABLE BORDER="0"
         		       CELLSPACING="2"
         		       CELLPADDING="2">
         		<TR>
         			<TD BGCOLOR="#003399" CLASS="hd3rev"> Modify Aggregate Product </TD>
         		</TR>
               <TR>
                  <TD BGCOLOR="#CCCCCC" CLASS="hd4"> Product Info: </TD>
               </TR>
               <TR>
                  <TD>
                  <?
	                  $agg_prod->editPartsForm();
                  ?>
                  </TD>
               </TR>
               <TR>
                  <TD ALIGN="CENTER"> 
                  <INPUT TYPE="image"
                         SRC="/images/button_command_save_off.jpg"
                         BORDER="0"> </TD>
               </TR>
         		</TABLE>
        </TD>
    </TR>
    </TABLE></TD>
</tr>
</TR>
</TABLE>
</FORM>
<?= page_stop() ?>
</HTML>
