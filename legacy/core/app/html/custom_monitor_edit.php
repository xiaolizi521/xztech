<?
require_once("CORE_app.php");
require_once("CoreSmarty.php");
if ( isset( $action ) && $action == "EDIT_MONITOR" 
     && isset( $id ) 
     && !empty( $id ) ) {
    if ( empty( $points ) ) {
        $points = 0;
    }
    $result = $db->submitquery("
        update
            custom_monitor 
        set
          points = '$points',
          name = '$name',
          description = '$description'
        where
          custom_monitor_id = '$id'
        ");
     header("location: /custom_monitors.php?id=$id");
}
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

    $smarty = new CoreSmarty;
    $smarty->assign('id', $id);
    $smarty->assign('points', $points);
    $smarty->assign('name', $name);
    $smarty->assign('description', $description);
    $smarty->display('custom_monitor_edit.tpl');
}
?>

