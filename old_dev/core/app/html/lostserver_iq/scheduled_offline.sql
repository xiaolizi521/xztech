-- Servers Scheduled to go Offline $Date: 02/01/16 21:26:12-00:00 $

SELECT
  distinct on ("Account Number","Server Number")
  customer_number as "Account Number",
  queue_cancel_server.computer_number as "Server Number",
  date(queue_cancel_server.sec_created) as "Scheduled on",
  date(queue_cancel_server.sec_due_offline) as "Due Offline"
FROM 
  queue_cancel_server,
  server
WHERE
  queue_cancel_server.computer_number = server.computer_number
  --AND date_where
ORDER BY "Account Number","Server Number","Scheduled on" DESC
