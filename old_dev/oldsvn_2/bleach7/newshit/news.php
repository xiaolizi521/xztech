<html>
    <head>
        <title>Bleach7!</title>
        <style type="text/sss" title="currentStyle" media="screen">
            @import "style.css";
        </style>
    </head>
    <body>
        <div id="layout">
            <div id="i02" class="pos">
                <object type="application/x-shockwave-flash" data="images/topnav_buttons.swf" width="312" height="60" style="margin-top: 1px;">
                    <param name="movie" value="images/tponav_buttons.swf" />
                </object>
            </div>
        <div id="main_sec">
            <table cellpadding="0" cellspacing="0" id="MainTable">
                <tr>
                    <td class="MainTopLeft">&nbps;</td>
                    <td class="Main">
<?

/* Include the database include file */
require_once( 'includes/database.php' );

if ( !isset( $_GET['story'] ) )
{
	$start = ( isset( $_GET['start'] ) ) ? $_GET['start'] : 0;
	
	$query 	= sprintf( "SELECT * FROM `news` ORDER BY `id` DESC LIMIT %s, %s",
	 				$start, 
	 				( $start + 10 ) );
	
	$result = $database->query( $query );
	
	if ( $result->num_rows )
	{
		while ( $story = $result->fetch_assoc() )
		{
			echo '<table width="100%" cellpadding="3" cellspacin="0" border="0" class="main">'
                        echo '<tr>';
                        echo '<td class="secondary"><b>' . stripslashes( $story['headline'] ) . '</b></td>';
			echo '</tr>';
                        echo '<td>';
                        echo stripslashes( nl2br( $story['news'] ) );
                        echo '</td>';
                        echo '</tr>';
                        echo '<td align="right"><i>';
			echo 'Posted by ' . $story['poster'] . ' on ' . date( "l, F j, Y, g:i a", $story['id'] );
                        echo '</td>';
                        echo '</tr>';
                        echo '</table>';
			/*echo '<a href="news.php?story=' . $story['id'] . '">';
			echo '<br />Comments (' . $story['comments'] . ')';
			echo '</a><p />';*/
		}
		
		echo '<p />';
		echo '<hr />';
		
		$count = $database->query( "SELECT count(id) FROM `news`" );

                $count=$result->fetch_assoc();
		
		for ( $i = 0, $page = 1; $i < $count['count(id)']; $i += 10, $page++ )
			echo '<a href="news.php?start=' . $i . '">' . $page . '</a> ';
			
	}
}
else
{
	$start = ( isset( $_GET['start'] ) ) ? $_GET['start'] : 0;

	$query 	= sprintf( "SELECT * FROM `news` WHERE `id`=%s",
						$_GET['story'] );
						
	$result = $database->query( $query );
	
	if ( $result->num_rows )
	{
		$story = $result->fetch_assoc();
		
		echo '<b>' . stripslashes( $story['headline'] ) . '</b><p />';
		echo stripslashes( nl2br( $story['news'] ) );
		echo '<p />';
		echo 'Posted by ' . $story['poster'] . ' on ' . date( "l, F j, Y, g:i a", $story['id'] );
		echo '<p />';
		echo '<hr />';
		
		$query 	= sprintf( "SELECT * FROM `news_comments` WHERE `newsid`=%s ORDER BY `id` ASC LIMIT %s, %s",
						$_GET['story'],
						$start,
						( $start + 10 ) );
						
		$result = $database->query( $query );
		
		if ( $result->num_rows )
		{
			while ( $comment = $result->fetch_assoc() )
			{
				echo '<i>';
				echo stripslashes( nl2br( $comment['comment'] ) ) . '<br /></i>';
				echo 'Posted by ';
				echo $comment['poster'] . ' on ' . date( "l, F j, Y, g:i a", $comment['id'] );
				echo '<p />';
				echo '<hr />';
			}
			
			echo '<p />';
			echo '<hr />';
			
			$query = sprintf( "SELECT * FROM `news_comments` WHERE `newsid`=%s",
							$_GET['story'] );
							
			$count = $database->query( $query );
			
			for ( $i = 0, $page = 1; $i < $count->num_rows; $i += 10, $page++ )
				echo '<a href="news.php?story=' . $_GET['story'] . '&start=' . $i . '">' . $page . '</a> ';
				
		}
	}
}

?>
            </td>
            <td class="side_bar">
                <table celpadding="0" cellspacing="0" id="Side_Bar">
                    <tr>
                        <td class="side_bar_main">
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <div id="google">
                            </div>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
    <img src="images/Layout5_bottom.jpg" alt="" style="width: 750x;" >/
</div>
</body>
</html>
