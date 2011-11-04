-- Server Processors (Not Account Based!) $Date: 02/08/08 15:35:36-00:00 $
SELECT
  DISTINCT ON (sp.computer_number)
  sp.computer_number as "Server Number",
  pt.product_description as "Processor"
FROM
  server,
  server_parts sp,
  product_table pt
WHERE
  -- Joins
      pt.product_sku = sp.product_sku
      AND server.datacenter_number = pt.datacenter_number 
      AND server.computer_number = sp.computer_number
  -- Limits
      AND pt.product_name ~* 'processor'
      AND pt.product_name <> 'Processor Fan'
 ;
