function logoutFunc () {
	alert('You do not have cookies enable, so you MUST Click "Cancel" at prompt to logout.');
	document.location = "<? echo($_SERVER['PHP_SELF'].'?logout=true'); ?>";	
}
