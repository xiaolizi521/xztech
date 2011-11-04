<?

session_start();
	
$p_level = $_SESSION['p_level'];

include( "header_functions.php" );

// Check to see if anything was posted
$post = $_POST;

$tmpname = $_FILES['file']['tmp_name'];
$name = $_FILES['file']['name'];
$mime = $_FILES['file']['type'];
$size = $_FILES['file']['size'];
$extension = end(explode(".", $name));
$basename = str_replace( ".$extension", "", $name );
$basename = preg_replace( "/[^a-zA-Z0-9s]/", " ", $basename );

if( sizeof( $post ) < 1 || !is_uploaded_file( $_FILES['file']['tmp_name'] ) )
{
	header( "file_new.php?error" );
	exit();
}

// Check if file exists in upload directory
$i = 1;
if( file_exists( "./uploads/".$name ) )
{
	while( file_exists( "./uploads/".$basename."-".$i.".".$extension ) )
		$i++;
	
	$basename .= "-".$i;
}

if( !move_uploaded_file( $tmpname, "./uploads/$basename.$extension" ) )
{
	header( "file_new.php?error" );
	exit();
}

// Chmod file so user can access it via web
chmod( "./uploads/$basename.$extension", 0666 );

// Check if audio file
$audio = new getID3();
$array = $audio->analyze( realpath( "./uploads/$basename.$extension" ) );

$data = array( "date" => time(),
					"name" => $post['name'],
					"filename" => $basename.".".$extension,
					"extension" => $extension,
					"mime" => $mime,
					"description" => $post['description'],
					"size" => $size,
					"original_filename" => $name,
					"audio_length" => round( $array['playtime_seconds'] ),
					"audio_artist" => ( !empty( $array['comments_html']['artist'] ) ? implode( '<BR>', $array['comments_html']['artist']) : '&nbsp;' ),
					"audio_title" => ( !empty($array['comments_html']['title']) ? implode('<BR>', $array['comments_html']['title'])  : '&nbsp;'),
					"audio_bitrate" => ( !empty($array['audio']['bitrate'] ) ? round($array['audio']['bitrate'] / 1000 ) : "" ),
					"project_id" => $post['project_id'],
					"uploaded_by" => $employeeArray['id'],
					"uploaded_by_name" => $employeeArray['name'] );

$db->add( "files", $data );

header( "Location: files.php?success" );

?>