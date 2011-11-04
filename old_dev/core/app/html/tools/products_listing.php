<form method="post" action="edit_product.php3">
<input type="submit" name="submit" value="edit">
<TABLE class="blueman" style="width: 100%; table-layout: fixed">
  <TR>
    <TH class="blueman"> Products </TH>
  </TR>
  <tr>
    <td>
      <!-- Begin Outlined Table Content ------------------------------------------ -->
      <SCRIPT LANGUAGE="JavaScript" SRC="/script/wait_window.js"></SCRIPT>
      <TABLE class="datatable" style="width: 100%; table-layout: fixed">
        <TR>
          <td class="counter" style="width: 3em"> &nbsp; </td>
          <TH> Name </TH>
          <TH style="width: 10em"> Datacenter </TH>
          <TH style="width: 8ex"> SKU </TH>
          <TH> Description </TH>
          <TH align="right" style="width: 12ex"> Price </TH>
          <TH align="right" style="width: 12ex"> Setup Fee </TH>
        </TR>
        <?php
          $result = $db->SubmitQuery('
            SELECT
              product_sku
            FROM
              product_selection
            GROUP BY product_sku
            ORDER BY product_sku
          ');
          $visible = array();
          for( $i=0; $i<$result->numRows(); $i++ ) {
            $visible[$result->getCell($i,0)] = true;
          }
          $result->freeResult();

          $where_clause = "";
          $from_clause = "";
          if (!empty($activeonly)) {
              $from_clause .= " join product_selection using (product_sku)\n";
              $where_clause .= " where product_selection.active_status = 'active'\n";
          }

          if (!empty($product_limit)) {
              $product_limit = addslashes($product_limit);
              if (!$where_clause) {
                  $where_clause .= " where ";
              } else {
                  $where_clause .= " and ";
              }
              $where_clause .= " product_name = '$product_limit'\n";
          }

          $result = $db->SubmitQuery("
            SELECT
              distinct
              product_sku as sku,
              product_name as name,
              datacenter_number,
              datacenter.name as datacenter,
              product_price as price,
              product_setup_fee as setup_fee,
              datacenter_number as dc,
              product_sku,
              product_description
            FROM
              product_table
              join datacenter using (datacenter_number)
            $from_clause
            $where_clause
            ORDER BY
              product_name ASC,
              product_sku ASC,
              datacenter_number ASC,
              product_description ASC
          ");

          $num =$result->numRows();
          for( $i=0; $i<$num; $i++ ) {
            $ctr = $i +1;
            $sku = $result->getResult($i,'product_sku');
            $name = $result->getResult($i,'name');
            $datacenter = $result->getResult($i,'datacenter');
            $datacenter_number = $result->getResult($i,'datacenter_number');
            $desc = $result->getResult($i,'product_description');
            $price = $result->getResult($i,'price');
            $setup_fee = $result->getResult($i,'setup_fee');
            $dc = $result->getResult($i,'dc');

            if ( empty($visible[$sku]) ) {
              $name = $name . " <b>(non-selectable)</b>";
              $invisible = true;
            } else {
              $invisible = false;
            }
    
            switch($dc) {
              case 1: $sign = "$"; break;
              case 2: $sign = "&pound;"; break;
              case 3: $sign = "$"; break;
              case 4: $sign = "$"; break;
              default: $sign = "";
            }

            $price = $sign . $price;

            if ($i%2!=0) {
              if( $invisible ) {
                $color='class=oddred';
              } else {
                $color='class=odd';
              }
            } else {
              if( $invisible ) {
                $color='class=evenred';
              } else {
                $color='class=even';
              }
            }

            echo "<TR $color>";
            echo "<td class=counter>$ctr<input type=checkbox name=\"product[]\" value=\"$sku $datacenter_number\"></td>\n";
            echo "\t<td nowrap align=\"left\" style=\"overflow:hidden\">";
            echo $name;
            echo "</td>\n";
            echo "<TD nowrap style=\"overflow:hidden; text-align: right\"> $datacenter</TD>\n";
            echo "<TD> $sku </TD>\n";
            echo "<TD style=\"overflow:hidden\"> $desc </TD>\n";    
            echo "\t<td align=\"right\">";
            echo $price;
            echo "</td>\n";
            echo "<TD>$setup_fee</TD>\n";
            echo "</tr>\n";
          }
        ?>		
      </TABLE>
<!-- End Outlined Table Content -------------------------------------------- -->
    </TD>
  </TR>
</TABLE>
<? if (!empty($activeonly)) { ?>
  <input type="hidden" name="activeonly" value="1">
<? } ?>
<? if (!empty($product_limit)) { ?>
  <input type="hidden" name="product_limit" value="<?=$product_limit?>">
<? } ?>
<input type="submit" name="submit" value="edit">
</form>
