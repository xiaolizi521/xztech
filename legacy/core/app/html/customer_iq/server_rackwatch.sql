-- Server Rackwatch List (Not Account Based!) $Date: 02/07/11 19:33:21-00:00 $
SELECT
    DISTINCT ON (computer_number)
    computer_number as "Server Number",
    ip_address as "Primary IP",
    datacenter_abbr || ':' || text(switch_number)  as "Switch",
    port_number as "Port #",
    name as "Datacenter"
FROM
    server
    join datacenter using (datacenter_number)
    left join ip_assignment_list using (computer_number)
    left join (
        SELECT DISTINCT ON (computer_number) 
               computer_number, product_description
        FROM sku JOIN server_parts USING (product_sku)
        WHERE product_description ~* 'RackWatch'
    ) as RackWatch using (computer_number)
WHERE
  -- Limits
      (server.status_number >= 4 OR server.status_number = -1)
  AND (ip_assignment_list.primary_ip = 't')
ORDER BY
  "Server Number"
;

