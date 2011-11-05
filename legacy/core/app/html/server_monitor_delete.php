<?
require_once("CORE_app.php");
require_once("CoreSmarty.php");
$smarty = new CoreSmarty;
if ( isset( $action ) && $action == "DELETE_SERVER_MONITOR" ) {
    $result = $db->submitquery("
        delete from 
            server_xref_custom_monitor
        where
            server_xref_custom_monitor_id = $id;");
     $smarty->display('server_monitor_delete_success.tpl');
}
$monitor_result = $db->submitquery("
        select
            computer_number,
            description,
            notes
        from
            server_xref_custom_monitor
        where
            server_xref_custom_monitor_id = $id;");
if ( $monitor_result->numRows() == 1 ) {
    $comp_num = $monitor_result->getResult( 0, 'computer_number' );
    $description = $monitor_result->getResult( 0, 'description' );
    $notes = $monitor_result->getResult( 0, 'notes' );
    $smarty->assign('id', $id );
    $smarty->assign('computer_number',$comp_num);
    $smarty->assign('description', htmlentities(substr($description,0,100) ) );
    $smarty->assign('notes', htmlentities(substr($notes,0,100) ) );
    $smarty->display('server_monitor_delete.tpl');

}
?>
