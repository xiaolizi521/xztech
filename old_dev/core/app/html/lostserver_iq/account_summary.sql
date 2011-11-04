-- Accounts Lost Summary $Date: 04/01/23 16:13:40-06:00 $

SELECT customer_number as "Account Number",
  count(computer_number) as "Servers to be Offlined",
  sum(
  CASE WHEN datacenter_number = 2 
       THEN null_to_float8(final_monthly) * 1.46
  ELSE null_to_float8(final_monthly)
  END ) as "Monthy Fee",
  sum(
  CASE WHEN datacenter_number = 2 
       THEN null_to_float8(final_setup) * 1.46
  ELSE null_to_float8(final_setup)
  END ) as "Setup Fee"
FROM
  (select distinct on (computer_number)
          customer_number,
          computer_number,
          status_number,
          final_monthly,
          final_setup,
          datacenter_number
   from
     queue_cancel_server join 
     server using (computer_number)
   WHERE  --date_where
  ) as queue_cancel_server
GROUP BY customer_number
ORDER BY customer_number
;
