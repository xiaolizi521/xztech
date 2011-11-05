<?

// Other functions
function status( $s )
{
	switch( $s )
	{
		case("in-progress"):
			return( "<span class=\"color_blue\">In Progress</span>" );
			break;
		case("do-not-start"):
			return( "<span class=\"color_gray\">On Hold</span>" );
			break;
		case("not-started"):
			return( "<span class=\"color_yellow\">Begin Work</span>" );
			break;
		case("completed"):
			return( "<span class=\"color_green\">Completed</span>" );
			break;
	}
}

function approved( $s )
{
	switch( $s )
	{
		case( 1 ):
			return( "<span class=\"color_green\">Approved</span>" );
			break;
		case( 0 ):
			return( "<span class=\"color_red\">Not Approved</span>" );
			break;
	}
}

	
$status = array( 	"in-progress" => "In Progress",
						"do-not-start" => "On Hold",
						"not-started" => "Ready to Begin",
						"completed" => "Completed" );


?>