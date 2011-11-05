<?
require_once("CORE_app.php");
$ConfigOpt->setMode(ADMIN);
$computer = new RackComputer($customer_number, $computer_number, $db);
$ConfigOpt->setDataCenterNumber($computer->getData("datacenter_number"));
if (empty($customer_number) || $customer_number=="") {
    $customer_number=DetermineCustomerNumber();
}
MakeNotEmpty($os);
MakeNotEmpty($back_to_cart);
MakeNotEmpty($server_name);
MakeNotEmpty($Continue_x);
MakeNotEmpty($ViewCart_x);
MakeNotEmpty($first_load);



FixServerTypeCase($server_type);
if (empty($server_type)) {
    $server_type = determine_os($computer_number, $db);
    if( empty($server_type) or (! $computer->isWindowsPlatform() ) ) {
        trigger_error('Invalid OS for Microsoft License', E_USER_ERROR);
    }
}

RestrictToDataCenter($db, $customer_number, "microsoft_os_license.php",
    "server_type=" . urlencode($os)
        . "&first_load=1&customer_number=$customer_number",
    $ConfigOpt->getDataCenterNumber());
$product_page="microsoft_os_license";
$Configurator = new RackConfigurator($server_type, $db, $ConfigOpt,
    $product_page, $first_load, $back_to_cart);

// Determine License
if ($REQUEST_METHOD == 'GET') {
    $HTTP_VARS = $HTTP_GET_VARS;
}
else {
    $HTTP_VARS = $HTTP_POST_VARS;
}
if (isset($command) and $command == 'CHOOSE_MS_OS_LICENSE') {
    // removed 'ms_os_sql_installed' from ms_os_list
    $ms_os_list = array('ms_os_virtual_hoster', 
        'ms_os_five_clients');
    foreach($ms_os_list as $name) {
        if (empty($HTTP_VARS['product_name'][$name]) 
                || $HTTP_VARS['product_name'][$name] == '') {
            $error_message = 'Please choose an answer for all of the following'
                . ' questions to continue.';
            break;
        }
    }
}

//$page_title,$page_label,$section_title MUST be set
$page_vars["page_title"] = "configurator";
$page_vars["page_label"] = "Microsoft License";
$page_vars["section_title"] = "price";
$page_vars["sub_section_title"] = "price_a_server";

$page_vars["configurator"] = true;

$Configurator->SetupCart($customer_number, $computer_number,
    $product_name, $server_name);
if( !empty($Continue_x) or !empty($ViewCart_x) and empty($error_message) ) {
    //They would like to view the current cart
    ForceReload("network_map.php?"
        . "customer_number=$customer_number&computer_number=$computer_number");
}
$RackCart=new RackCart($db,$customer_number,$ConfigOpt, $computer);

$page_title="Configure MS Licensing";
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<html>
<head>
<title><?=$page_title?></title>
<link HREF="/css/core2_basic.css" REL="stylesheet">
<link HREF="/css/configurator.css" REL="stylesheet">
</head>
<body>

<table class="blueman">
<tr><th class="blueman"><?=$page_title?></th></tr>
<tr><td>
<? 
    if (isset($error_message)) {
        print "<FONT COLOR=RED>\n";
        print($error_message);
        print "\n</FONT>\n";
    }
?>
<FORM ACTION=microsoft_os_license.php METHOD=POST>
<INPUT TYPE=HIDDEN NAME=command VALUE=CHOOSE_MS_OS_LICENSE>
<?$Configurator->FormStart();?>
<OL>
<LI>
Are you using this server for a business which is one of the following?
<ul>
<li>Virtual Hoster</li>
<li>ASP (Application Service Provider)</li>
<li>ISV (Independent Software Vendor)</li>
</ul>
<BR>
<? $Configurator->MicrosoftOS_VirtualHosterSelect(); ?>
</LI>
<LI>
Will more than 4 clients be required to login via Windows?
<BR>
<?$Configurator->MicrosoftOS_FiveClientsSelect();?>
</LI>
</OL>
</TD></TR>
<?
$config_computer = new ConfigComputer($customer_number, $computer_number, 
    $db, $ConfigOpt);
$microsoft_os_license = $config_computer->GetList('microsoft_os_license');
if(isset($microsoft_os_license->RackPartItemArray[0])):
?>
    <TR><TD>&nbsp;</TD></TR>
    <TR><TD><B>
    Current License:<BR>
    <?
    print($microsoft_os_license->RackPartItemArray[0]->part_description);
    print "&nbsp;&nbsp;";
    print $RackCart->GetCurrencyHTML();
    print($microsoft_os_license->RackPartItemArray[0]->part_price);
    ?>
    </B>
    <BR>
    <I>Click Update Price to see if the license will change.</I>
    </TD></TR>
    <tr><td>
        <input type="submit" name="Update" value="Update Price" 
               class="form_button">
    </td></tr>
<?
endif;
?>
<TR><TD><?include("include/microsoft_os_license_bottom.php");?>
</TD></TR>
</TABLE>

</html>
