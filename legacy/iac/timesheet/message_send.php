<?

session_start();
	
$p_level = $_SESSION['p_level'];

include( "header_functions.php" );

// Check to see if anything was posted
$post = $_POST;
$project_id = $post['project_id'];
$task_id = 0;
$filename = end( explode( "|", $post['file_id'] ) );
$file_id = explode( "|", $post['file_id'] );
$file_id = $file_id[0];
$recipients = $post['recipients'];


	
//$db->debug = true;


//if( sizeof( $post ) < 1 || !is_uploaded_file( $_FILES['file']['tmp_name'] ) )
//	header( "file_new.php?error" );

$data = array( "date" => time(),
					"project_id" => $project_id,
					"task_id" => $task_id,
					"file_id" => $file_id,
					"filename" => $filename,
					"message" => $post['message'],
					"priority" => $post['priority'],
					"sent_by" => $employeeArray['id'],
					"sent_by_name" => $employeeArray['name'] );

$db->add( "messages", $data );

$db->query("SELECT LAST_INSERT_ID() AS id");  
$row = mysql_fetch_assoc( $db->result['ref'] );
$messageId = $row['id'];
if ( count($recipients) > 0 ){
	foreach ($recipients as $recipient){
		$msgData = array ( 	"message_id" => $messageId,
							"employee_id" => $recipient,
							"status" => 1);	
		$db->add( "message_recipients", $msgData );
	}
}


header( "Location: messages.php?success" );
	
?>
