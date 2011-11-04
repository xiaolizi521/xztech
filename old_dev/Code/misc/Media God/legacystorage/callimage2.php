<?php
include 'callimage.class.php';

$foo = new CallImage();

?>

<html>

<head>
</head>
<body>
<?php echo $foo->user;?>'s image looks like:<br />
<img src="<?php $foo->displayImage(); ?>">
</body>
</html>