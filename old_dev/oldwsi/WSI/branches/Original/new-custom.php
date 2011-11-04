<?
include "config.php";
include "designtop.php";
?>
<script>
function update_element(name) {
$(name + 'x').value = parseInt($(name + '_image').style.top) + 5;
}
</script>
<?

$query = "select * from whatpulse where `user` = '".$_SESSION['username']."'";
$data = mysql_fetch_assoc(mysql_query($query)) or die(mysql_error());
function create_element($data, $name, $database_name) {
	if (number_format($data[$database_name]) != "0") {
	$value = number_format($data[$database_name]);
	}
	else {
	$value = $data[$database_name];
	}
	$string = ucfirst($name) . ": " . $value ;
	$box = imagettfbbox($data['fontsize'],0,'fonts/' . $data['font'],$string);
	$image = imagecreatetruecolor($box[2]+10,$box[5]*-1+10);
	imagesavealpha($image, true);
	$trans_colour = imagecolorallocatealpha($image, 0, 0, 0, 127);
	imagefill($image, 0, 0, $trans_colour);
	$fontcolor = imagecolorallocate($image,$data['fontred'],$data['fontgreen'],$data['fontblue']);
	$shadowcolor = imagecolorallocate($image,$data['sred'], $data['sgreen'], $data['sblue']);

	if ($data['se']) {

		imagettftext($image,$data['fontsize'],0,1,10,$shadowcolor,'fonts/' . $data['font'],$string);
	}
	imagettftext($image,$data['fontsize'],0,0,9,$fontcolor,'fonts/' . $data['font'],$string);

	$path = "users/" . $data[$name] . "/" . strtolower($name) . ".png";
	imagepng($image,$path);
	echo "<img id='{$name}_image' src='$path' style=\"position:absolute;top:" . $data[$database_name . 'y'] . ";left:" . $data[$database_name . 'x'] . "\" />
	<script>new Draggable('{$name}_image', {onEnd: function (element, event) { update_element('" . strtolower($name) . "') } })</script>";
	
}


echo "<div style=\"position:absolute;background:url('$data[path]') no-repeat;width:$data[width];height:$data[height]\">&nbsp;";
create_element($data, "user", "user");
create_element($data, "keys", "tkc");
create_element($data, "clicks", "tmc");


echo "</div>";
function create_form_element($data, $name, $database_name) {
	echo "<input type='checkbox' name='{$database_name}e' id='usere' onclick=\"Element.toggle('{$name}_image')\" ";
	if ($data[$database_name . "e"]) {
		echo "checked";
	}
	echo " />
<label for='usere'>" . ucfirst($name) . "</label>
<input type='hidden' name='{$database_name}x' id='{$database_name}x' value=''>";
}
?>
<br><br><br>
<?
create_form_element($data, "user", "user");
create_form_element($data, "keys", "tkc");
create_form_element($data, "clicks", "tmc");
?>
 

<?
include "menu.php";
include "designbottom.php";
	?>