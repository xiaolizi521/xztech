<?php
require_once("CORE_app.php");
if( !in_dept("PRODUCT_MANAGEMENT") ) {
    trigger_error("You are not authorized to access this page",FATAL);
    exit;
}
    // Enable Progress Bar
    include("TimeRegister.php");
    $page_timer = new PageTimeRegister();
    $page_timer->start(); 
    $loadtime = number_format((0+$page_timer->average_duration),0);
    set_title('Product List ------ (Avg Load: '.$loadtime.' secs)','#003399');
?>
<html id="mainbody">
<head>
  <script>
    var avgLoadTime = <?print $loadtime;?>;
  </script>
  <TITLE>CORE: Product List ---- (Avg Load: <?=$loadtime?> secs)</TITLE> 
  <LINK HREF="/css/core_ui.css" REL="stylesheet">
  <LINK HREF="/css/core2_basic.css" REL="stylesheet">
<?require_once("tools_body.php");?> 
<?include("wait_window_layer.php")?>

  <form method="get" action="products.php3">
    <fieldset>
      <legend>Filter</legend>
      <p>
        To limit the SKUs you would like displayed, select from the below
        criteria. Not filling out any of this will result in a complete
        list.
      </p>
      <p>
        <font color="darkred">Note that the Show Active Products Only link
        is not entirely accurate. If you check it, some active products may
        not be displayed.</font>
      </p>
      <table class="datatable">
        <tr>
          <th>Status</th>
          <?
          if (!empty($activeonly)) {
            $checked = "checked";
          } else {
            $checked = "";
          } ?>
          <td><input type="checkbox" name="activeonly" value="1" id="activecb" <? echo $checked ?>><label for="activecb">Show Active Products Only</label></td>
        </tr>
        <tr>
          <th>Product</th>
          <td>
            <select name="product_limit">
            <option value="">-- Do Not Filter --</option>
            <?
              // Get list of available product_name's
              $result = $db->SubmitQuery('
                select
                  distinct product_name as product_name
                from
                  product_table
              ');
              $num = $result->numRows();
              for( $i=0; $i<$num; $i++ ) {
                $name = $result->getResult($i, 'product_name');
                $selected = "";
                if (!empty($product_limit)) {
                  if ($product_limit == $name) {
                    $selected = "selected";
                  }
                }
                echo "<option value=\"$name\" $selected>$name</option>";
              }
              $result->freeResult();
            ?>
            </select>
          </td>
        </tr>
      </table>
      <input type="submit" name="submit" value="Show Products">
      <input type="hidden" name="go" value="1">
    </fieldset>
  </form>

<? if (!empty($go)) { ?>
  <? include("products_listing.php") ?>
<? } ?>

<?= page_stop() ?>
</HTML>
<? $page_timer->stop();?>
<?php
// Local Variables:
// c-basic-offset: 4
// indent-tabs-mode: nil
// End:
?>
