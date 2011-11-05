-- Product Selection (How Products are Organized) $Date: 02/07/11 19:17:25-00:00 $
/*
"Field Name" "SKU" "Status"
*/
select 
    field_name as "Field Name",
    product_sku as "SKU",
    active_status as "Status"
from product_selection
order by field_name, product_sku, active_status
;