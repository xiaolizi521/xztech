<?php

include "classes/class.callimage.php";

$image = new CallImage($_GET['user']);

unset($image);

?>