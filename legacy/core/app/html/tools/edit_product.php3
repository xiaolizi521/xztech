<?php require_once("CORE_app.php"); ?>
<?
    if (!empty($command) && $command=="EDIT_PRODUCT") {
        for ($i = 0; $i <= $indexmax; $i++) {
            $datacenter_number = $HTTP_POST_VARS["datacenter_number_$i"];
            $product_sku = $HTTP_POST_VARS["product_sku_$i"];
            $product_name = $HTTP_POST_VARS["product_name_$i"];
            $product_description = $HTTP_POST_VARS["product_description_$i"];
            $product_price = $HTTP_POST_VARS["product_price_$i"];
            $product_setup_fee = $HTTP_POST_VARS["product_setup_fee_$i"];
            
            $datacenter = $db->GetVal("
                SELECT name
                FROM datacenter
                WHERE datacenter_number = $datacenter_number
            ");
            $old_info=$db->SubmitQuery("
                SELECT
                    * 
                FROM
                    product_table 
                WHERE
                    product_sku=$product_sku
                    AND datacenter_number = $datacenter_number
            ");
            $ConfigOpt->setDataCenterNumber($old_info->getResult(0,"datacenter_number"));

            //Now build up the full list
            
            $stamp = time();
            if( !empty($product_setup_fee) ) {
                $setup_fee_value = "'$product_setup_fee'";
            } elseif( $product_setup_fee == "" ) {
                $setup_fee_value = "NULL"; # calculate when configuring server
            } else {
                $setup_fee_value = "product_setup_fee"; # no change
            }

            $db->SubmitQuery("
                UPDATE sku
                SET sec_last_mod = $stamp,
                    product_name = '$product_name',
                    product_description = '$product_description'
                WHERE product_sku = $product_sku
            ");

            $db->SubmitQuery("
                UPDATE xref_sku_datacenter
                SET product_setup_fee = $setup_fee_value,
                    product_price = '$product_price'
                WHERE product_sku = $product_sku
                    AND datacenter_number = $datacenter_number
            ");

            $old_info->freeResult();
        }
        $db->CloseConnection();
        $flags = "go=1";
        if (!empty($activeonly)) {
            $flags .= "&activeonly=1";
        } 
        if (!empty($product_limit)) {
            $flags .= "&product_limit=$product_limit";
        }
        $flags = str_replace(" ", "+", $flags);
        JSForceReload("/tools/products.php3",$flags,"workspace");
    } else { ?>
        <html id="mainbody">
            <head
                <title>CORE: Edit Products</title>
        <?require_once("tools_body.php");?>
        <FORM ACTION="edit_product.php3" METHOD=POST>
        <?
        $index = -1;
        if (empty($product)) {
            $flags = "go=1";
            if (!empty($activeonly)) {
                $flags .= "&activeonly=1";
            } 
            if (!empty($product_limit)) {
                $flags .= "&product_limit=$product_limit";
            }
            $flags = str_replace(" ", "+", $flags);
            JSForceReload("/tools/products.php3",$flags,"workspace");
        } else {
            foreach ($product as $product_info) {
                $vals = split("[ ]", $product_info, 2);
                $product_sku = $vals[0];
                $datacenter_number = $vals[1];
                $datacenter = $db->GetVal("
                    SELECT name
                    FROM datacenter
                    WHERE datacenter_number = $datacenter_number
                ");
                $index = $index + 1;
                ?>
                <INPUT TYPE=HIDDEN NAME="command" value="EDIT_PRODUCT">
                <TABLE BORDER="0" CELLSPACING="0" CELLPADDING="2" CLASS="titlebaroutline">
                    <TR>
                        <TD>
                           <TABLE WIDTH="100%"
                                  BORDER="0"
                                  CELLSPACING="0"
                                  CELLPADDING="0"
                                  BGCOLOR="#FFFFFF">
                               <TR>       
                                  <TD>
                                      <TABLE BORDER="0"
                                             CELLSPACING="2"
                                             CELLPADDING="2">
                                         <TR>
                                             <TD BGCOLOR="#003399" CLASS="hd3rev"> Edit Product: Sku #<?=$product_sku?> </TD>
                                         </TR>
                                         <TR>
                                             <TD>

<!-- Begin Outlined Table Content ------------------------------------------ -->
<?
  if ($product_sku) {
    $product = "$product_sku $datacenter_number";
  }
?>

    <? $data=$db->SubmitQuery("
           SELECT
              * 
           FROM
              product_table 
           WHERE
              product_sku = $product_sku
              AND datacenter_number = $datacenter_number
       "); 
    ?>
    <INPUT TYPE=HIDDEN NAME="datacenter_number_<?=$index?>" value="<?=$datacenter_number?>">
    <INPUT TYPE=HIDDEN NAME="product_sku_<?=$index?>" value="<?print $product_sku;?>">
    <INPUT TYPE=HIDDEN NAME="product_name_<?=$index?>" value="<?print($data->getHTResult(0,'product_name'));?>">
    <TABLE BORDER="0" CELLSPACING="1" CELLPADDING="2">
        <TR>
            <TH ALIGN=LEFT COLSPAN="2"> Product Description (All Datacenters): </TH>
        </TR>
        <TR>
            <TD COLSPAN="2">
                <INPUT TYPE=TEXT NAME="product_description_<?=$index?>" size=52
                       value="<?print $data->getResult(0,"product_description") ;?>">
            </TD>
        </TR>
        <TR>
            <TH ALIGN=LEFT> Price (<?=$datacenter?>): </TH>
            <TD>
                <?=$ConfigOpt->getCurrencyHTML()?>
                <INPUT TYPE=TEXT 
                       NAME="product_price_<?=$index?>" 
                       SIZE=8
                       VALUE="<? 
	                   $value=GetMoneyAsInt($data->getResult(0,"product_price"));
                           print $value;?>">
            </TD>
        </TR>
        <TR>
            <TH ALIGN=LEFT> Setup Fee Price (<?=$datacenter?>): </TH>
            <TD>
                <?=$ConfigOpt->getCurrencyHTML()?>
                <INPUT TYPE=TEXT NAME="product_setup_fee_<?=$index?>" SIZE=8
                       VALUE="<? 
                           $value = $data->getResult(0,"product_setup_fee");
                           if (empty($value) and $value == "") {
                                print $value;
                           } else {
                                print GetMoneyAsInt($value);
                           }
                       ?>"> If left blank it will be calculated by the standard formula. 
            </TD>
        </TR>
        <TR>
          <TD>
            <A HREF="/product/<?=$product_sku ?>" class='text_button'> Manage with Skutopia </A>
          </TD>
        </TR>
    </TABLE>
    <?$data->freeResult(); ?>

<!-- End Outlined Table Content -------------------------------------------- -->
                                            </TD>
                                        </TR>
                                    </TABLE>
                                </TD>
                            </TR>
                        </TABLE>
                    </TD>
                </TR>
            </TABLE><br>
        <? } ?>
    <? } ?>
<? } ?>
<input type="hidden" name="indexmax" value="<?=$index?>">
<INPUT TYPE="image"
       SRC="/images/button_command_save_off.jpg"
       BORDER="0">
<? if (!empty($activeonly)) { ?>
  <input type="hidden" name="activeonly" value="1">
<? } ?>
<? if (!empty($product_limit)) { ?>
  <input type="hidden" name="product_limit" value="<?=$product_limit?>">
<? } ?>
</FORM>
<?=page_stop()?>
</HTML>
<?php
// Local Variables:
// c-basic-offset: 4
// indent-tabs-mode: nil
// End:
?>

