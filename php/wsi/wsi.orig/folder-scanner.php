<?php
function read_dir($dir) {
	$array = array();
	$d = dir($dir);
	while (false !== ($entry = $d->read())) {
		if($entry!='.' && $entry!='..') {
			$entry = $dir.'/'.$entry;
			if(is_dir($entry)) {
				$array[] = $entry;
				$array = array_merge($array, read_dir($entry));
			} else {
				$array[] = $entry;
			}
		}
	}
	$d->close();
	return $array;
}
$directory = read_dir("users/images");

foreach ($directory as $index => $name) {
	if (!is_dir("$name")) {
		echo "<img src='http://pulse.offbeat-zero.net/$name'><br>";
	}
}

?>