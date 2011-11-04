<?
require_once("CORE_app.php");
require_once("CoreSmarty.php");
if ( isset( $action ) && $action == "DELETE_MONITOR" 
    && !empty( $id )) {
    $result = $db->submitquery("
        delete 
            from custom_monitor 
        where 
            custom_monitor_id = '$id'
        ");
    header("location: /custom_monitors.php?selected_id=$id");

}
$smarty = new CoreSmarty;

$num_dependent_nodes = $db->getVal("
        select 
            count(*)
        from
            \"server_xref_custom_monitor\"
        where
            custom_monitor_id = '$id'
        ");
if ( $num_dependent_nodes > 0 )  {
    $smarty->assign('error', "There are $num_dependent_nodes device monitors that use this custom monitor.  Please remove them before removing this custom monitor");
    $smarty->display('custom_monitor_delete.tpl');
}
else {
    $monitor_result = $db->submitquery("
            select 
                name,
                points,
                description
            from
                custom_monitor
            where
                custom_monitor_id = $id;");
    if ( $monitor_result->numRows() == 1 ) {
        $name = $monitor_result->getResult( 0, 'name' );
        $points = $monitor_result->getResult( 0, 'points' );
        $description = $monitor_result->getResult( 0, 'description' );

        $smarty->assign('id', $id);
        $smarty->assign('name', $name);
        $smarty->display('custom_monitor_delete.tpl');
    }
}
?>
