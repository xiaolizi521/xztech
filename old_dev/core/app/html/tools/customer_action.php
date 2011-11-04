<?php
require_once("CORE_app.php");

	if (!isset($full_log))
		$full_log=false;
	$customer= new RackCustomer;
	$customer->Init($customer_number,$db);
	if (!empty($command)&&isset($command))
	{

			if ($command=="ADD_COMPUTER")
			{
				$computer_number=create_computer($customer_number,"","","","",$datacenter_number);
                JSForceReload("/ACCT_main_workspace_page.php?computer_number=$computer_number");
			}
			elseif ($command=="ADD_AGG_PROD")
			{
				$new_agg_prod = new $agg_class($db,$customer_number,"",ADMIN);
				$new_agg_prod->save();
				ForceReload("agg_products/".$new_agg_prod->createUrl());
			}
			if ($command=="QUICK_COMMENT")
			{
				$customer->Log($quick_comment);
				ForceReload("/py/account/view.pt?account_number=$customer_number");
			}
	}
?>
