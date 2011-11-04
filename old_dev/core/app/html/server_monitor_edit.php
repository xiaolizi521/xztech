<?
require_once("CORE_app.php");
require_once("CoreSmarty.php");
if ( isset( $action ) && $action == "EDIT_SERVER_MONITOR" ) {
    $result = $db->submitquery("
        update
            server_xref_custom_monitor
        set
            description = '$description',
            notes = '$notes'
        where
            server_xref_custom_monitor_id = $id;");
    $smarty = new CoreSmarty;
    $smarty->display('server_monitor_edit_success.tpl');
}

$monitor_result = $db->submitquery("
        select
            sla_promise,
            description,
            notes
        from
            server_xref_custom_monitor
        where
            server_xref_custom_monitor_id = $id;");
if ( $monitor_result->numRows() == 1 ) {
    $sla_promise = $monitor_result->getResult( 0, 'sla_promise' );
    $description = $monitor_result->getResult( 0, 'description' );
    $notes = $monitor_result->getResult( 0, 'notes' );
    $smarty = new CoreSmarty;
    $smarty->assign('id', $id );
    $smarty->assign('description', $description );
    $smarty->assign('notes', $notes );
    $smarty->display('server_monitor_edit.tpl');

}
else
    die( "not found" );

?>
