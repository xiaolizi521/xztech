-- Server Info (Not Account Based!) $Date: 04/04/20 13:05:57-05:00 $
SELECT
  server.computer_number as "Server Number",
  server.customer_number as "Account Number",
  CASE WHEN datacenter.datacenter_number = 2 THEN float8(server.final_monthly) * POUNDS_TO_DOLLARS
       ELSE server.final_monthly
  END as "Monthly Fee",
  CASE WHEN datacenter.datacenter_number = 2 THEN float8(server.final_setup) * POUNDS_TO_DOLLARS
       ELSE server.final_setup
  END as "Setup Fee",
  server.contract_term as "Contract Term",
  so.status as "Server Status",
  CASE WHEN ss.sec_finished_order IS NOT NULL 
            AND ss.sec_finished_order > 1 
       THEN text(date(ss.sec_finished_order::abstime))
       ELSE 'Not Online' 
   END as "Online Date",

  (SELECT "FirstName" || ' ' || "LastName"
   FROM "xref_employee_number_Contact" xenc,
        "CONT_Contact" cont,
        "CONT_Person" person
   WHERE 
     -- Joins
         ra.rep_number = xenc.employee_number
     AND xenc."CONT_ContactID" = cont."ID"
     AND cont."CONT_PersonID" = person."ID"
  ) as "Sales Rep.", 

  (SELECT os 
   FROM computer_os
   WHERE computer_os.computer_number = server.computer_number
   limit 1 -- Remove this once the duplicate custom monitoring is fixed
  ) as "OS",
  datacenter.name as "Datacenter"
FROM
  server,
  datacenter,
  rep_assignment ra,
  status_options so,
  sales_speed ss
WHERE
  -- Joins
      server.computer_number = ss.computer_number
  AND server.status_number = so.status_number
  AND server.datacenter_number = datacenter.datacenter_number
  AND server.computer_number = ra.computer_number
  AND server.customer_number = ra.customer_number
  -- Limits
  AND (server.status_number >= 4 OR server.status_number = -1)
;
