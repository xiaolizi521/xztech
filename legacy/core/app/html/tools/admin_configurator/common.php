<?php
/*
Set up the following variables:
 $computer -- A RackComputer object
 $ConfigOpt
 $RackCart
 $Configurator

Defaulted variables:
 $server_type
 $first_load

*/

require_once("CORE_app.php");
//$print_sql = 1;
//phpinfo();

// Defaults
if( empty($server_type) ) {
	$server_type="Linux";
}
if( empty( $first_load ) ) {
        $first_load = "";
}
if( empty( $server_name ) ) {
        $server_name="";
}
if( empty($pricing_mode) ) {
    $pricing_mode = "";
}
if( empty($back_to_cart) ) {
    $back_to_cart = 1;
}

// Setup variables
if( empty($ViewCart_x) ) {
    // They aren't viewing the cart.
    $is_done = 0;
    if( empty($Update) ) {
        // They weren't updating, so we don't write prices.
        $ConfigOpt->setWritePriceMode( false );
    }
} else {
    $is_done = 1;
}
$computer= new RackComputer;
$computer->Init($customer_number,$computer_number,$db);
if( empty( $old_final_monthly ) ) {
    $old_final_monthly = $computer->getData("final_monthly");
}
if( empty( $old_final_setup ) ) {
    $old_final_setup = $computer->getData("final_setup");
}

$ConfigOpt->setMode( ADMIN );
$ConfigOpt->setPricingMode( $pricing_mode );
$ConfigOpt->setDataCenterNumber( $computer->getData("datacenter_number") );

$RackCart = new RackCart( $db,
                          $customer_number,
                          $ConfigOpt,
                          $computer );
$Configurator = new RackConfigurator( $server_type,
                                      $db,
                                      $ConfigOpt,
                                      $product_page,
                                      $first_load,
                                      $back_to_cart );
$Configurator->SetupCart( $customer_number,
                          $computer_number,
                          $product_name,
                          $server_name );
if( $is_done ) {
    // Go back to the network_map (aka the "Cart")
    ForceReload("network_map.php?customer_number=$customer_number&computer_number=$computer_number");
    exit();
}


?>
