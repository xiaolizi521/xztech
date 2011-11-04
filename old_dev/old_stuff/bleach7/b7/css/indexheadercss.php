<?php include ('../member/settings.php'); ?>

.main { 
	text-decoration: none; 
	font-family: <?php echo $mainfontface ?>; 
	font-size: <?php echo $mainfontsize ?>; 
	color: <?php echo $mainfontcolor ?>;
}

.secondary { 
	text-decoration: none; 
	font-family: <?php echo $secondaryfontface ?>; 
	font-size: <?php echo $secondaryfontsize ?>; 
	color: <?php echo $secondaryfontcolor ?>;
}

.secondary a:link { text-decoration: none; 
	font-family: <?php echo $secondaryfontface ?>; 
	font-size: <?php echo $secondaryfontsize ?>; 
	color: <?php echo $secondaryfontcolor ?>;
}

.secondary a:active { text-decoration: none; 
	font-family: <?php echo $secondaryfontface ?>; 
	font-size: <?php echo $secondaryfontsize ?>; 
	color: <?php echo $secondaryfontcolor ?>;
}

.secondary a:visited { text-decoration: none;
	font-family: <?php echo $secondaryfontface ?>; 
	font-size: <?php echo $secondaryfontsize ?>; 
	color: <?php echo $secondaryfontcolor ?>;
}

.secondary a:hover { text-decoration: underline;
	font-family: <?php echo $secondaryfontface ?>; 
	font-size: <?php echo $secondaryfontsize ?>; 
	color: <?php echo $secondaryfontcolor ?>;
}

.textbox { backgroufnd-color: <?php echo $formbgcolor ?>;
	font-family: <?php echo $formfontface ?>;
	font-size: <?php echo $formfontsize ?>;
	color: <?php echo $formfontcolor ?>;
	border: 1px solid <?php echo $formbordercolor ?>;
}

.submit_button { background-color: <?php echo $formbgcolor ?>;
	font-family: <?php echo $formfontface ?>;
	font-size: <?php echo $formfontsize ?>;
	color: <?php echo $formfontcolor ?>;
	border: 1px solid <?php echo $formbordercolor ?>;
}