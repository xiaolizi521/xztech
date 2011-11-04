<?php
require_once("CORE_app.php");
require_once('helpers.php');
$iAccount = ActFactory::getIAccount();

$regions = $iAccount->getLookupValues("region-$country_code");
print '<OPTION VALUE="">'."\n";
print "--select--\n";
print "</OPTION>\n";
foreach ($regions as $region) {
    print '<option value="'.$region->parameter_id.'">'.$region->desc.'</option>'."\n";
}
print "</SELECT>\n";

?>
