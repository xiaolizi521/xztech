<?

// How is this used?  The other team uses this to kick start machines.  So they call this script with
// computer number and get usually configuration information for this script to use.
//

define(ONLINE, 12);
define(UNDER_REPAIR, 13);
define(REKICK, 51);

function canShowPasswordInfo( $computer ) {
  if( $computer->getData('status_number') < ONLINE
      || $computer->getData('status_number') == UNDER_REPAIR
      || $computer->getData('status_number') == REKICK
      || $computer->getData('status_number') == 62
      || $computer->getData('status_number') == 63
      || $computer->OS() == "Sun" )  {
    return 1;
  }
  return 0;
}

Header("content-type: text/plain");
if ($kickpass == 'K95141.3')
{
  define("NO_AUTH",1);
}
require("CORE.php");
if (!isset($computer_number)
    || $computer_number==""
    || ereg("[^(0-9)]", $computer_number))
{
  print "Error: invalid server number\n";
  exit();
}
else if (!isset($customer_number) || $customer_number=="")
{
  $customer_number=$db->GetVal("
			select customer_number
			from server
			where computer_number=$computer_number;");

}
else if (ereg("[^(0-9)]", $customer_number))
{
  print "Error: invalid server number\n";
  exit();
}
$computer= new RackComputer;
$computer->Init($customer_number,$computer_number,$db);

//Now get all the other info
if ($computer->IsComputerGood())
{
  print("status:".$computer->getData("status")."\n");
  print("status_number:".$computer->getData("status_number")."\n");
  print("customer_number:$customer_number\n");
  print("computer_number:$computer_number\n");
  print("ip:".$computer->getData("primary_ip")."\n");
  if ( canShowPasswordInfo( $computer ) ) {
    print("primary_userid:"
	  . $computer->getData("primary_userid")."\n");
    print("primary_userid_password:"
	  . $computer->getData("primary_userid_password")."\n");
    print("webmin_password:"
	  . $computer->getData("webmin_password")."\n");
    print("rack_password:"
	  . $computer->getData("rack_password")."\n");
    print("root_password:"
	  . $computer->getData("root_password")."\n");
  } else {
    echo "error: No Password Info\n";
  }
  print("webmin_port:".$computer->getData("webmin_port")."\n");
  print("datacenter:".$computer->getDataCenterAbbr()."\n");
  print("primary_dns:".$computer->getData("primary_dns")."\n");
  print("secondary_dns:".$computer->getData("secondary_dns")."\n");
  print("gateway:".$computer->getData("gateway")."\n");
  print("netmask:".$computer->getData("netmask")."\n");
  if ($computer->IsDualProc())
    $smp="1";
  else
    $smp="0";
  print("smp:$smp\n");
  $domain=$computer->getData("server_name");
  $domain_parts=explode(".",$domain);

  $default_subdomain = "$computer_number";
  if (count($domain_parts)==2)
    {
      //domain.com
      print("primary_domain:$domain\n");
      print("subdomain:$default_subdomain\n");
    }
  else if (count($domain_parts)>=3)
    {
      if (strlen($domain_parts[2])==2)
	      {
      	  //domain.co.uk
      	  print("primary_domain:$domain\n");
      	  print("subdomain:$default_subdomain\n");
      	}
      else
        {
          //www.domain.co.uk,www.domain.com, lon1.rackspace.co.uk, dfw1.core.rackspace.com, int.dfw1.core.rackspace.com, etc
          print("subdomain:$domain_parts[0]\n");
          // $primary_domain_name = "";
          print("primary_domain:");
          for ($i=1; $i<count($domain_parts); $i++)
            {
              // $primary_domain_name += $domain_parts[$i].".";
              if ($i != 1)
                {
                  print(".");
                }
              print($domain_parts[$i]);
            }
          print("\n");
        }
    }
    
  //Now handle the new stuff like number of ips :)
  // IPSPACE - DONE - Replace with proper call to new ip object.
  $ips=$computer->ip->getNonPrimaryIps();
  $num=count($ips);
  if ($num>0)
    {
      print ("extra_ips:$num\n");
      for ($i=0;$i<$num;$i++)
	{
	  print("extra_ip_$i:".$ips[$i]."\n");
	}
    }
  //Now find out about tape drive
  if ($computer->HasTapeDrive())
    {
      print ("has_tape_drive:1\n");
      //Now figure out backup schedule
      $backup_schedule=$computer->WhatIsBackupSchedule();
      if ($backup_schedule!="")
	print("backup_schedule:$backup_schedule\n");
    }
  else {
    print ("has_tape_drive:0\n");
  }

  $parts = $db->SubmitQuery("
            SELECT product_name, product_description, product_sku
            FROM server JOIN server_parts USING (computer_number)
                JOIN product_table USING (product_sku,datacenter_number)
            WHERE computer_number = $computer_number
            ORDER BY product_name, product_description
            ");
  print "skus:" . $parts->numRows() . "\n";
  for ($i = 0; $i < $parts->numRows(); $i++) {
    $row = $parts->fetchArray($i);
    print "skunumber_$i:$row[product_sku]\n";
    print "skuname_$i:$row[product_name]\n";
    print "skudesc_$i:$row[product_description]\n";
  }

  // show managed backup information for commvault
  //get the Managed Backup Agent
  $mbu_results = $db->SubmitQuery("
            SELECT p.product_description
            FROM sku p
                JOIN server_parts sp on (p.product_sku = sp.product_sku)
            WHERE sp.computer_number = " . $computer->computer_number . "
                AND p.product_description ilike 'Managed Backup Agent -%'
            ");
  $mbu_system =  preg_replace("/Managed Backup Agent - (.*)/i", "$1", $mbu_results->getCell(0,0));
  if ($mbu_system == "CommVault" || $mbu_system == "Legato") {
    $results = $db->SubmitQuery("
            SELECT s.computer_number,
                 co.os,
                 p.product_description
            FROM server s
                JOIN computer_os co on (co.computer_number = s.computer_number)
                JOIN server_parts sp on (s.computer_number = sp.computer_number)
                JOIN sku p on (sp.product_sku = p.product_sku)
            WHERE s.customer_number = $customer_number
                AND co.os ilike '%Managed%Backup%'
                AND s.status_number >= ".STATUS_SENT_CONTRACT."
                AND s.datacenter_number = ".$computer->GetDataCenterNumber()."
                AND s.computer_number IN (
                    SELECT s.computer_number
                    FROM server s
                        JOIN computer_os co on (co.computer_number = s.computer_number)
                        JOIN server_parts sp on (s.computer_number = sp.computer_number)
                        JOIN sku p on (sp.product_sku = p.product_sku)
                    WHERE s.customer_number = $customer_number
                        AND co.os ilike '%Managed%Backup%'
                        AND s.status_number >= ".STATUS_SENT_CONTRACT."
                        AND s.datacenter_number = ".$computer->GetDataCenterNumber()."
                        AND p.product_name = 'Backup Server Software'
                        AND p.product_description ilike '" . $mbu_system . "%'
                    )
                ");
    $num = $results->numRows();
    //print out the server links and retention data but get rid of the other stuff:
    if( !empty($num) ) {
      $mbu_cnt = 0;
      for( $i=0; $i<$num; $i++ ) {
	$cn = $results->getCell($i,0);
	$cos = $results->getCell($i,1);
	$cpart = $results->getCell($i,2);
	if ( ($cpart != "Managed Backup Storage Required") and (!preg_match("/.* Server/", $cpart))) {
	  print "mbu_retention_$mbu_cnt:$cpart\n";
	  $mbu_cnt++;
	}
      }
    }

  }

  //  IF this is a managed backup client with CommVault Agent, THEN
  //  display the bkhost ip IF it has one.
  //
  if( $computer->HasManagedBackupClientCommVault() ) {
    $clientsbkhost = $db->getVal("select ip from comm_bkhost,xref_server_bkhost where comm_bkhost.id = bkhost_id and computer_number = $computer_number;");

    $clientsbkhostname = $db->getVal("select comm_bkhost.name from comm_bkhost,xref_server_bkhost where comm_bkhost.id = bkhost_id and computer_number = $computer_number;");

    print "commcell_ip: $clientsbkhost\n";
    print "commcell_hostname: $clientsbkhostname\n";
  }


  // show the public zone
  //
  $zone = $computer->getPublicZoneID();
  if ( $zone ) {
    print "public_zone:$zone\n";
  }

  // if they've got a managed backup port,
  // get the zone that they're in

  // switch port == location

  $locations = $computer->getLocations();
  for ($i = 0; $i < count($locations); $i++) {
    $pnet_type = PNET_TYPE_MANAGED_BACKUP;
    if (strpos($locations[$i], "Managed Backup")) {
      print "locations:". $locations[$i] ."\n";
      $switch_number = explode("[", $locations[$i]);
      $number = $switch_number[0];

      $zone = $db->GetVal("
                    SELECT
                        \"NTWK_ZoneID\"
                    FROM
                        \"NTWK_Switch\"
                    WHERE
                        \"Number\" = '$number'
                    ;
                ");
      print "backup_net_zone:$zone\n";
      $ip = $computer->getPrivateNetIP($pnet_type);
      print "backup_net_ip:$ip\n";
      $backup_net_gateway = $computer->getPrivateNetManagedBackupGW($pnet_type);
      $backup_net_netmask = $computer->getPrivateNetMask($pnet_type);
      print "backup_net_gateway:$backup_net_gateway\n";
      print "backup_net_netmask:$backup_net_netmask\n";

      print "managed_backup_zone:$zone\n";
      print "managed_backup_ip:$ip\n";
      print "managed_backup_gateway:$backup_net_gateway\n";
      print "managed_backup_netmask:$backup_net_netmask\n";

    }
  }
  // private net, everyone on it -- routing rules, not contected to
  // live
  if ( $computer->HasPrivateNet() ) {
    $pnet_type = PNET_TYPE_PRIVATENET;
    $private_net_ip = $computer->getPrivateNetIP($pnet_type);
    print "private_net_ip:$private_net_ip\n";

# we only need the real zone for Managed Backup.
    $private_net_zone = 0;
    $private_net_gateway = $computer->getPrivateNetManagedBackupGW($pnet_type);
    $private_net_netmask = $computer->getPrivateNetMask($pnet_type);

    print "private_net_zone:$private_net_zone\n";
    print "private_net_gateway:$private_net_gateway\n";
    print "private_net_netmask:$private_net_netmask\n";
  }

  // dedicated swith of thier own
  //
  if ( $computer->HasLocalNet() ) {
    $pnet_type = PNET_TYPE_LOCALNET;
    $local_net_ip = $computer->getPrivateNetIP($pnet_type);
# we only need the real zone for Managed Backup.
    $local_net_zone = 0;
    $local_net_netmask = $computer->getPrivateNetMask($pnet_type);
    $local_net_gateway = $computer->getPrivateNetManagedBackupGW($pnet_type);
    print "local_net_ip:$local_net_ip\n";
    print "local_net_zone:$local_net_zone\n";
    print "local_net_netmask:$local_net_netmask\n";
    print "local_net_gateway:$local_net_gateway\n";
  }

  if ( $computer->isBehindNetworkDevice() ) {
    print "is_behind_net_device:t\n";
  } else {
    print "is_behind_net_device:f\n";
  }

  if ( $computer->hasVMNetVLANAssigned() > 0) {
    $vmnet_ip = '';
    $vmnet_kernal_ip = '';
    $vmnet_gateway = '';
    $vmnet_netmask = '';
    $vmnet_dns_primary = '';
    $vmnet_dns_secondary = '';
    $vmnet_customer_access = 'f';

    $vmnet_ip = $computer->getPrivateNetIP(PNET_TYPE_VMNET);
    $vmnet_gateway = $computer->getPrivateNetManagedBackupGW(PNET_TYPE_VMNET);
    $vmnet_netmask = $computer->getPrivateNetMask(PNET_TYPE_VMNET);
    
    if ($computer->HasPartNumber(103917)){
      // has the active sku
      $vmnet_customer_access = 't';
    }
    
    // get the VMNet specific info,  this function takes parameters by reference
    $computer->getVmNetPrivateNetInfo($vmnet_kernal_ip, 
				      $vmnet_dns_primary, 
				      $vmnet_dns_secondary, $cabinet_panel, $aggregation_panel);
    
    print "vmnet_ip:$vmnet_ip\n";
    print "vmnet_vmk_ip:$vmnet_kernal_ip\n";
    print "vmnet_gw:$vmnet_gateway\n";
    print "vmnet_nm:$vmnet_netmask\n";
    print "vmnet_dns_primary:$vmnet_dns_primary\n";
    print "vmnet_dns_secondary:$vmnet_dns_secondary\n";
    print "customer_access:$vmnet_customer_access\n"; 

  }

  if ($computer->isVirtualMachine()){

      $proc_type = '';
      $proc_quan = '';
      $memory = '';
      $harddrives = array();
      
      if ($computer->getVmKickstartInfo($processor_types, $processor_quantity, $memory, $harddrives) == 1){

	print "vm_virtual_processor_quantity:$processor_quantity\n";
	$i = 1;
	foreach ($processor_types as $proc_type){
	  print "vm_hypervisor_processor_type_$i:$proc_type\n";
	  $i++;
	}
	
	print "vm_memory:$memory\n";
	
	print "vm_hd_count:" . count($harddrives) . "\n";
	$i = 1;
	foreach ($harddrives as $hd_label => $hd_cap){
	  print "vm_hd_label_$i:$hd_label\n";
	  print "vm_hd_capacity_$i:$hd_cap\n";
	  $i++;
	}

      }else{
	// problem talking to the service...
      }
  }

}
else
{
  print("No Data:Unable to find this computer");
  print("customer_number:$customer_number\n");
  print("computer_number:$computer_number\n");
}
print "segment:".$computer->account->getSupportTeamName()."\n";

$osType = $computer->GetOsType();
if($osType == NULL or $osType == "") {
  print "kick:".$computer->OS()."\n";
}
else {
  print "kick:".$osType."\n";
}
print "non_durable_passwords:".$computer->uses_non_durable_passwords() . "\n";

// check to see if there is a cust-prov license OS attribute
$cust_prov_license_id = 2;
$lic_qry = "SELECT pa.value 
FROM server_parts AS sp, 
parts_attributes AS pa
WHERE 
sp.computer_number = $computer_number
AND sp.server_parts_id = pa.server_part
AND pa.sku_attribute = $cust_prov_license_id";

$the_cust_prov_license_key = $db->GetVal($lic_qry);

if ($the_cust_prov_license_key){
  print "Customer_Provided_License:" . $the_cust_prov_license_key . "\n";
}


$db->CloseConnection();
?>
