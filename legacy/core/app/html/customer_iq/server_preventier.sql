-- Server PrevenTier Info (Not Account Based!) $Date: 03/09/17 14:35:12-05:00 $

select 
	customer_number as "Account Number",
	server.computer_number as "PrevenTier Server Number",
    ( select
        status_options.status
      from
        status_options
      where
        status_options.status_number = server.status_number
    ) as "PrevenTier Server Status",
	"final_setup" as "PrevenTier Setup Cost",
	"final_monthly" as "PrevenTier Monthly Cost",
	-- License Bought
	( select
		product_description
	from
		server_parts as sp,
		sku
	where
		sp.product_label = 'preventier_license'
	   	and server.computer_number = sp.computer_number
		and sp.product_sku = sku.product_sku
	limit 1
	) as "License Name",
	-- License Bought Count
	( select
		item_quantity
	from
		server_parts as sp,
		sku
	where
		sp.product_label = 'preventier_license'
	   	and server.computer_number = sp.computer_number
		and sp.product_sku = sku.product_sku
	limit 1
	) as "License Count",
	-- # of Servers with $0 sku
	( select
		count(*)
	from
		server_parts as sp,
		server as s
	where
		server.customer_number = s.customer_number
		and sp.product_sku = 102151
	   	and s.computer_number = sp.computer_number
		and s.status_number >= 2 -- Sent Contract
	) as "Number of Servers"
from
	computer_os,
	server
where
-- Conditionals
	os = 'PrevenTier'
-- JOINS
	AND computer_os.computer_number = server.computer_number
