<html>
	<head>
		<title>Project Management System</title>
		<link href="style/style.css" rel="stylesheet" type="text/css" />
		<link href="style/lightbox.css" rel="stylesheet" type="text/css" />
		<script type="text/javascript" src="scripts/prototype.js"></script>
		<script type="text/javascript" src="scripts/lightbox.js"></script>
		<script type="text/javascript">
		function popUp(URL) {
		day = new Date();
		id = day.getTime();
		eval("page" + id + " = window.open(URL, '" + id + "', 'toolbar=0,scrollbars=0,location=0,statusbar=0,menubar=0,resizable=1,width=220,height=370');");
		}
		</script>
	</head>
	<body<?= $body ?>>
		<div id="header">
			<div class="center">
				<a href="home.php"><img src="images/headerLogo.jpg" alt="Project Manager"></a>
			</div>
		</div>
		<div id="navigation">
			<? include( "navigation.php" ); ?>
		</div>
		<div class="center">
			<div id="content">
