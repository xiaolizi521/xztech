<?
require_once("CORE_app.php");
require_once("CoreSmarty.php");
$monitor_result = $db->submitquery("
        select
            sla_promise, 
            server_xref_custom_monitor.description as server_desc,
            notes,
            name,
            custom_monitor.description
        from
            server_xref_custom_monitor
        join
            custom_monitor
        using
            (custom_monitor_id)
        where
            server_xref_custom_monitor_id = $id;");
if ( $monitor_result->numRows() == 1 ) {
    $sla_promise = $monitor_result->getResult( 0, 'sla_promise' );
    $server_desc = $monitor_result->getResult( 0, 'server_desc' );
    $notes = $monitor_result->getResult( 0, 'notes' );
    $name = $monitor_result->getResult( 0, 'name' );
    $description = $monitor_result->getResult( 0, 'description' );
    $smarty = new CoreSmarty;
    $smarty->assign('id', $id );
    if ($sla_promise == 't') {
        $smarty->assign('sla_promise', "Yes" );
    } else {
        $smarty->assign('sla_promise', "No" );
    }
    $smarty->assign('server_desc', $server_desc );
    $smarty->assign('notes', $notes );
    $smarty->assign('name', $name );
    $smarty->assign('description', $description );
    $smarty->display('server_monitor_view.tpl');

}


?>
