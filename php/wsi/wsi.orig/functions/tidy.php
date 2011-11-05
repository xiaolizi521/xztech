<? class tidy {
function post($post) {
	foreach($post as $key => $value) {
	$post[$key] = mysql_real_escape_string(htmlentities($value));
	}
	return $post;
}
}
?>