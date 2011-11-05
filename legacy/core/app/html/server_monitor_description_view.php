<?
require_once("CORE_app.php");
require_once("CoreSmarty.php");
$monitor_result = $db->submitquery("
        select
            description
        from
            server_xref_custom_monitor
        where
            server_xref_custom_monitor_id = $id;");
if ( $monitor_result->numRows() == 1 ) {
    $description = $monitor_result->getResult( 0, 'description' );
    $smarty = new CoreSmarty;
    $smarty->assign('description', $description );
    $smarty->display('server_monitor_description.tpl');
}

?>
