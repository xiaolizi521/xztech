<?
require_once("CORE_app.php");
require_once("CoreSmarty.php");
require_once("menus.php");

print '<html id="mainbody">';
print "<head>\n";
print menu_headers();
print "<title>Custom Monitors</title>\n";
print "</head>\n";

print page_start();

$monitors = array();
$monitors_result = $db->submitquery("
             select 
                custom_monitor_id, 
                name,
                points,
                description
             from custom_monitor;");
if( !isset( $selected_id ) ) {
    $selected_id = -1;
}
for( $i=0; $i < $monitors_result->numRows(); $i++ ) {
    $id = $monitors_result->getResult( $i, 'custom_monitor_id' );
    $monitor = $monitors_result->getResult( $i, 'name' );
    $points = $monitors_result->getResult( $i, 'points' );
    $description = $monitors_result->getResult( $i, 'description' );
    $monitors[] = array(
                        'id' => $id,
                        'name' => $monitor,
                        'points' => $points,
                        'description' => $description,
                        'selected' => ($id == $selected_id) ? true : false 
                       );
}
$smarty = new CoreSmarty;
$smarty->assign('monitors', $monitors);
$smarty->display('custom_monitors.tpl');

print page_stop();
print "</html>";
?>

