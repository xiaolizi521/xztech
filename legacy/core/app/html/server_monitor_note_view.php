<?
require_once("CORE_app.php");
require_once("CoreSmarty.php");
$monitor_result = $db->submitquery("
        select
            notes
        from
            server_xref_custom_monitor
        where
            server_xref_custom_monitor_id = $id;");
if ( $monitor_result->numRows() == 1 ) {
    $notes = $monitor_result->getResult( 0, 'notes' );
    $smarty = new CoreSmarty;
    $smarty->assign('notes', $notes );
    $smarty->display('server_monitor_note.tpl');
}

?>
