<?

/* Include the database include file */
require_once( 'includes/database.php' );

if ( !isset( $_GET['story'] ) )
{
	$result = $database->query( "SELECT * FROM `news` ORDER BY `id` DESC LIMIT 0, 10" );
	
	if ( $result->num_rows )
	{
		while ( $story = $result->fetch_assoc() )
		{
			echo '<b>' . $story['headline'] . '</b><p />';
			echo $story['news'];
			echo '<p />';
			echo 'Posted by ' . $story['poster'] . ' on ' . date( "l, F j, Y, g:i a", $story['id'] );
			echo '<a href="news.php?story=' . $story['id'] . '">';
			echo 'Comments (' . $story['comments'] . ')';
			echo '</a><p />';
		}
	}
}
else
{
	$query 	= sprintf( "SELECT * FROM `news` WHERE `id`=%s",
						$_GET['story'] );
	$result = $database->query( $query );
	
	if ( $result->num_rows )
	{
		$story = $result->fetch_assoc();
		
		echo '<b>' . $story['headline'] . '</b><p />';
		echo $story['news'];
		echo '<p />';
		echo 'Posted by ' . $story['poster'] . ' on ' . date( "l, F j, Y, g:i a", $story['id'] );
		echo '<p />';
		echo '<hr />';
		
		$query 	= sprintf( "SELECT * FROM `news_comments` WHERE `newsid`=%s",
						$_GET['story'] );
		$result = $database->query( $query );
		
		if ( $result->num_rows )
		{
			while ( $comment = $result->fetch_assoc() )
			{
				echo '<i>';
				echo $comment['comment'] . '<br /></i>';
				echo 'Posted by ';
				echo $comment['poster'] . ' on ' . date( "l, F j, Y, g:i a", $comment['id'] );
				echo '<p />';
			}
		}	
	}
}

?>