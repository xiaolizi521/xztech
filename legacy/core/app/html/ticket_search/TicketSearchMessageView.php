<?php
  /**
   * @author Sameer Chowdhury
   * @package ticket_search 
   * @subpackage View
 */
require_once('CORE_app.php');
require_once("class.mailer.php");
require_once("class.parser.php");
require_once("act/ActFactory.php");
require_once("menus.php");
require_once("CoreSmarty.php"); 
require_once("mysql.phlib");

$db = &new MySQLDatabase($GLOBALS['TICKETS_db_host'],$GLOBALS['TICKETS_db_port'],"ticket_search","ticket_search","ticket_search");
$core_db =& $GLOBALS['GLOBAL_db'];
$db->openConnection();
$data = array();
//Put Code Below

if ( !isset( $search_string ) ) {
    $search_string = '';
}
if ( $search_string ) {
    $data['search_string'] = htmlentities($search_string);
}
if ( !isset( $results_per_page ) ) {
    $results_per_page = 10;
}
if ( !isset( $next_start ) ) {
    $next_start = 0;
}
if ( !isset( $age ) ) {
    $age = 7;
}
if ( !isset( $limit_account_num ) ) {
    $limit_account_num = 0;
}
if ( !isset( $message_id ) ) {
    $message_id = 0;
}
if ( !isset( $ticket_id ) ) {
    $ticket_id = 0;
}
$data['age'] = $age;
$data['next_start'] = $next_start;
$data['results_per_page'] = $results_per_page;
$data['search_string'] = htmlentities($search_string);
$query = "select message from message where message_id=$message_id"; 
$message_search= $db->SubmitQuery($query);
$message = $message_search->getResult(0,"message");
$message = str_replace("\n","<BR>",$message);
$message_search->freeResult();
$ref_no_query = "select \"ReferenceNumber\" from \"TCKT_Ticket\" where \"TCKT_TicketID\"=$ticket_id";
$ref_no = $core_db->getVal( $ref_no_query );
$data['ref_no'] = $ref_no;
$data['message'] = $message;
$db->closeConnection();
//Put Code Above

$smarty = new CoreSmarty;
$smarty->assign( 'ticket', $data  );
$smarty->display('TicketSearchMessage.tpl');

// Local Variables:
// mode: php
// c-basic-offset: 4
// End:

?>
