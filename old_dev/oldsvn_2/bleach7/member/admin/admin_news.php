<?php
	echo "<form method='post' name='form_news'>";
	if ( isset ( $action ) && !empty ( $action ) ) { //an action is set
		if ( $action == "add" ) { //add news item
			include ( "admin_news_add.php" );
		} elseif ( $action == "edit" ) { //editting news main page, display all the news items
			include ( "admin_news_edit.php" );
		} elseif ( $action == "delete" ) { //delete news main page, display all the news items
			include ( "admin_news_delete.php" );
		} elseif ( $action == "templates" && $user_info['type'] >= "90" ) { //templates main page, display all templates
			include ( "admin_news_template.php" );
		}
	} 
	echo "</form>";
?>