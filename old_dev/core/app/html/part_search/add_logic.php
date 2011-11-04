<?php

require_once("CORE_app.php");
require_once("common.php");

function PrintSelect() {
    global $report_db, $and_datacenter, $show_datacenter;
    $results = $report_db->SubmitQuery('
select *
from
(
SELECT
  product_sku as sku,
  product_name
  || \' '.$show_datacenter.' [#\' ||
  product_sku
  || \'] \' ||
  product_description as name,
  ( select 1
    from product_selection ps
    where ps.product_sku = pt.product_sku
    limit 1
  ) as in_prod_sel,
  ( select 2
    from product_os po
    where po.product_sku = pt.product_sku
    limit 1
  ) as in_os
FROM
  product_table pt
  join datacenter using (datacenter_number)
WHERE
  1 = 1
  '.$and_datacenter.'
ORDER BY
  in_os ASC,
  product_name ASC,
  datacenter_number ASC,
  product_description ASC
) as foo
where in_prod_sel is not null 
   or in_os is not null
' );

    $rows = $results->numRows();
    $start = 0;
    for( $i=0 ; $i<$rows ; $i++ ) {
        $sku = $results->getResult($i, 0);
        $name = $results->getResult($i, 1);
        $in_prod_sel = $results->getResult($i, 2);
        $in_os = $results->getResult($i, 3);
        if( $in_os and $start == 0 ) {
            print "<optgroup label=\"Product Line Parts\">\n";
            $start = 1;
        } elseif ( $in_prod_sel and $start == 1 ) {
            print "</optgroup>\n";
            print "<optgroup label=\"Available Parts\">\n";
            $start = 2;
        }
        print "<option value='$sku'>$name</option>\n";
    }
    print "</optgroup>\n";
}


// Local Variables:
// mode: php
// c-basic-offset: 4
// indent-tabs-mode: nil
// End:
?>
