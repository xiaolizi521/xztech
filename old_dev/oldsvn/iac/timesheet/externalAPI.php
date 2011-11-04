<?

include( "code/db.php" );
include( "code/getid3/getid3.php" );

$root_path = "/home/iac/public_html";

function createNewProject( $clientID, $name, $description, $status, $limit_hours = 0.00 )
{
	$db = new db();
	
	$array = array( "client_id" => $clientID,
			   "name" => $name,
			   "description" => $description,
			   "status" => $status,
			 	"limit_hours" => $limit_hours );
	
	$db->add( "project", $array );
	
	// Get last ID
	$db->query( "SHOW TABLE STATUS LIKE 'project'" );
	$row = mysql_fetch_assoc( $db->result['ref'] );
	$id = $row['Auto_increment'] - 1;
	return $id;
}

function updateProject( $projectId, $name = "", $description = "", $status = "", $limit_hours = "" )
{
	
}

function addHoursToPrepaid( $projectId, $description, $limit_hours )
{
	$db = new db();
	
	$db->query( "SELECT * FROM project WHERE id = $projectId" );
	
	if( $db->result['rows'] > 0 )
	{
		$row = mysql_fetch_assoc( $db->result['ref'] );
		
		$hours = $row['limit_hours'];
		$hours += $limit_hours;
		
		$tempDescription = $row['description'] . "\n\n" . $description;
		
		$array = array( "limit_hours" => $hours,
						"description" => $tempDescription );
		
		$db->update( "project", $array, array( "id" => $projectId ) );
		
		// need to reset low hours alert if flagged
		$db->query( "UPDATE project_alerts SET alert_value1 = 0 WHERE project_id = $projectId AND alert_name = 'LOW_HOURS'" );
	}
}

function addFile( $project_id, $file, $description )
{
	global $root_path;
	
	$db = new db();
	$tmpname = $file['tmp_name'];
	$name = $file['name'];
	$mime = $file['type'];
	$size = $file['size'];
	$extension = end(explode(".", $name));
	$basename = stripslashes( str_replace( ".$extension", "", $name ) );
		
	if( file_exists( "$root_path/timesheet/uploads/".$name ) )
	{
		$i = 1;
		while( file_exists( "$root_path/timesheet/uploads/".$basename."-".$i.".".$extension ) )
			$i++;

		$basename .= "-".$i;
	}
	
	if( !copy( "$root_path/temporary_uploads/".$tmpname, "$root_path/timesheet/uploads/$basename.$extension" ) )
		echo "<strong>Error moving uploaded file. Contact us immediately for help.</strong>";
	
	@unlink( "$root_path/temporary_uploads/".$tmpname );
	
	chmod( "$root_path/timesheet/uploads/$basename.$extension", 0666 );
	
	$audio = new getID3();
	$array = $audio->analyze( realpath( "$root_path/timesheet/uploads/$basename.$extension" ) );
	$data = array( "date" => time(),
						"name" => $post['name'],
						"filename" => $basename.".".$extension,
						"extension" => $extension,
						"mime" => $mime,
						"description" => "Audio to transcribe",
						"size" => $size,
						"original_filename" => $name,
						"audio_length" => round( $array['playtime_seconds'] ),
						"audio_artist" => ( !empty( $array['comments_html']['artist'] ) ? implode( '<BR>', $array['comments_html']['artist']) : '&nbsp;' ),
						"audio_title" => ( !empty($array['comments_html']['title']) ? implode('<BR>', $array['comments_html']['title'])  : '&nbsp;'),
						"audio_bitrate" => ( !empty($array['audio']['bitrate'] ) ? round($array['audio']['bitrate'] / 1000 ) : "" ),
						"project_id" => $project_id,
						"uploaded_by" => 0,
						"uploaded_by_name" => "IAC Website" );
	
	$db->add( "files", $data );
}

?>