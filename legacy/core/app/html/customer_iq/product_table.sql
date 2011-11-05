-- Product Table (RAW SKU information) $Date: 02/07/11 19:17:09-00:00 $
/*
"SKU"   "NAME"  "Description"   "Requirements"  "Conf Page" "Is ValAdd" "ValAdd Obj"    "Old Price" "Old Setup" "New Price" "New Setup"
*/
select 
    product_sku as "SKU",
    product_name as "Name",
    product_description as "Description",
    product_requirements as "Requirements",
    product_category as "Conf. Page",
    val_add as "Is ValAdd?",
    val_add_obj as "ValAdd Object",
    product_price as "Monthly Price",
case when product_setup_fee is null
     then 'NULL'
else text(product_setup_fee)
end as "Setup Fee"
from product_table
order by product_name, product_description, product_sku
;