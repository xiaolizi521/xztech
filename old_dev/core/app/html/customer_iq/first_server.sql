-- First Server $Date: 04/08/23 18:20:56-05:00 $

SELECT 
customer_number as "Account Number",
date(min(sec_finished_order::abstime)) as "First Server Date",
to_char(date_part('day',now() - date(min(sec_finished_order::abstime)))/30.0, 'FM9990D99') as "Tenure (months)"

FROM
  sales_speed

WHERE sec_finished_order > 0

GROUP BY customer_number
ORDER BY customer_number, "First Server Date"
;
