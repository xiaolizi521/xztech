<?
require_once("CORE_app.php");
require_once("CoreSmarty.php");
if ( isset( $action ) && $action == "NEW_MONITOR" 
    && !empty( $name )
    && !empty( $description)
    ) {
    if ( empty( $points ) ) {
        $points = 0;
    }
    $result = $db->submitquery("
        Insert into custom_monitor (points, name, description) values
            ('$points','$name','$description') ");
     $id = $db->getVal("
        select 
            custom_monitor_id 
        from
            custom_monitor
        where
            points = '$points'
            and
            name = '$name'
            and
            description='$description';
        ");
     header("location: /custom_monitors.php?selected_id=$id");

}
$smarty = new CoreSmarty;
$smarty->display('custom_monitor_add.tpl');
?>
