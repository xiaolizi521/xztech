<?
	session_start();
		
	$p_level = $_SESSION['p_level'];
	
	if( strlen( $p_level ) > 0 )
		include( "header_functions.php" );
	else
	{
		header( "Location: home.php" );
		exit();
	}
	
	// Include header TEMPLATE
	include( "header_template.php" );
	
	if ( isset( $_GET['id'] ) ){
		$db->query( "DELETE FROM resource_links WHERE id = ".$_GET['id'] );
	}
	
	if ( isset( $_POST['name'] ) ){
		$data = array( "name" => $_POST['name'],
						"url" => $_POST['link'],
						"employee_id" => $employeeArray['id'],
						"status" => 1 );

		$db->add( "resource_links", $data );
	}
	
	function directoryToArray($directory, $recursive) {
		$array_items = array();
		if ($handle = opendir($directory)) {
			while (false !== ($file = readdir($handle))) {
     			if ($file != "." && $file != "..") {
     				if (is_dir($directory. "/" . $file)) {
     					if($recursive) {
     						$array_items = array_merge($array_items, directoryToArray($directory. "/" . $file, $recursive));
     					}
    					$file = $directory . "/" . $file;
    					$array_items[] = preg_replace("/\/\//si", "/", $file);
    				} else {
    					$file = $directory . "/" . $file;
    					$array_items[] = preg_replace("/\/\//si", "/", $file);
    				}
    			}
    		}
    		closedir($handle);
    	}
    	return $array_items;
    }
	
    $db->query( "SELECT * FROM resource_links WHERE status = 1 ORDER BY name" );
?>

<div>
	<h1>Resources</h1>
	<? $resources = directoryToArray( "/home/iac/public_html/resources/", true ) ?>
	<? //$resources = directoryToArray( "C:/websites/iac/timesheet/resources/", true ) ?>
	
	<p><strong>IMPORTANT:</strong><br/>When prompted, enter <strong><u>iac</u></strong> for the username and <strong><u>2008</u></strong> for the password. You will only need to enter this once per session.</p>
	<table border="0" width="840px">
		<tr><th>Resource Files</th><th>Resource Links</th></tr>
		<tr>
		<td valign="top" width="50%">
	<div style="margin-top: 15px;">
		<ul>
			<? foreach( $resources as $resource ): ?>
				<? $temp = explode( "/home/iac/public_html/resources/", $resource ) ?>
				<? //$temp = explode( "C:/websites/iac/timesheet/resources/", $resource ) ?>
				<? $tempExt = explode( ".", $temp[1] ) ?>
				
				<? if( strlen( $tempExt[0] ) > 0 ): ?>
				<li>
					<a href="/resources/<?= $temp[1] ?>" target="_BLANK"><?= $temp[1] ?></a>
				</li>
				<? endif; ?>
				
			<? endforeach; ?>
		</ul>
	</div>
	</td>
	<td valign="top">
	<div style="margin-top: 15px;">
		<ul>
			<? while( $row = mysql_fetch_assoc( $db->result['ref'] ) ): ?>
				<li>
					<a target="_blank" href="<?= $row['url'] ?>"><?= $row['name'] ?></a>
					<? if ( $p_level == "super-manager" ) {
							echo '&nbsp;<a style="font-size:8pt;color:gray;" href="resources.php?id='.$row['id'].'">delete</a>';
						
					}?>
				</li>
			<? endwhile; ?>
		</ul>
		<br/>
		
		<ul><p><a href="resource_add.php" style="text-decoration: underline;color: #43539E;">Add Resource Link &raquo;</a></p></ul>
		
		
	</div>
	</td>
	</tr>
	</table>
</div>

<? include( "footer.php" ); ?>