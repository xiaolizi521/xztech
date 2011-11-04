<?
require_once("CORE_app.php");
$new_agg_prod = new $agg_class($db,$account_number,"",ADMIN);
$new_agg_prod->save();
ForceReload($new_agg_prod->createUrl());

            
?>
