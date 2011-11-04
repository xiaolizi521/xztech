<?
/**
 * Author:   	Cory Becker
 * Date:   	 	September 21, 2007
 * Company:		Becker Web Solutions, LLC
 * Website:	 	www.beckerwebsolutions.com
 *
 * Description:
 *					Header
 */

// Include
include( "code/config.php" );

session_start();

$user = new user();
$check = $user->checkUser( $_SESSION['username'], $_SESSION['password'] );
if(!$check)
	header("Location: index.php" );

$employeeArray = $user->array;

$p_level = $employeeArray['type'];
$_SESSION['p_level'] = $p_level;

$verified = true;	// used to check that included page used the header

// Database
$db = new db();

function tsDate( $str, $timestamp )
{
	$offset_hours = 0;
	
	$offset = 3600 * $offset_hours;
	return date( $str, $timestamp + $offset );
}

function fileTypeImage( $mime )
{
	$base = explode( "/", $mime );
	switch( $base[0] )
	{
		case( "text" ):
			$image = "text-file.gif";
			break;
		case( "application" ):
			$image = "exe-file.gif";
			break;
		case( "image" ):
			$image = "photoshop-file.gif";
			break;
		case( "audio" ):
			$image = "mp3-music-file.gif";
			break;
		default:
			$image = "file.gif";
			break;
	}
	
	return $image;
}

function categoryStyle( $color )
{
	if( strlen( $color ) > 0 )
		return "style=\"background: #$color; border-bottom: 2px solid #FFF;\"";
}
?>