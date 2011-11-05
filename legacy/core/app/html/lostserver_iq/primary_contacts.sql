-- Primary Contacts $Date: 02/01/16 23:40:09-00:00 $
SELECT
    xcna.customer_number as "Account Number",
    person."FirstName" || ' ' || person."LastName" as "Primary Contact",
    org."Name" as "Primary Contact's Organization"
FROM 
    (select customer_number, "ACCT_AccountID"
     from
        queue_cancel_server join
        server using (computer_number) join
        "xref_customer_number_Account" using (customer_number)
     where
       --date_where
     group by customer_number, "ACCT_AccountID"
    ) as xcna,
    "ACCT_xref_Account_Contact_AccountRole" xaca,
    "CONT_Contact" cont,
    "CONT_Person" person,
    "CONT_Org" org
WHERE 
      xaca."ACCT_val_AccountRoleID" = 1
  AND xcna."ACCT_AccountID" = xaca."ACCT_AccountID"
  AND xaca."CONT_ContactID" = cont."ID"
  AND cont."CONT_OrgID" = org."ID"
  AND cont."CONT_PersonID" = person."ID"
;

