select computer_number ,
	case 
		when product_sku = 100116 then 'Linux'
		when product_sku = 100493 then 'FreeBSD'
		when product_sku = 100480 then 'Sun'
		when product_sku = 100141 then 'NT'
		when product_sku = 100528 then 'WIN2K'
		when product_sku = 100106 then 'RAQ/RAQ2'
		when product_sku = 100166 then 'RAQ3'
		when product_sku = 100117 then 'Colocation'
		else 'Unknown'
	END as os
	from server_parts where 
	product_sku in (100116 ,100493 ,100480 ,100141 ,100106 ,100166 ,100117 ,100528);
