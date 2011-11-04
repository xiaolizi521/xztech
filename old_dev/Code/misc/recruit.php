<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">

<head profile="http://gmpg.org/xfn/11">
	<title>The Mirage Guild Recruitment</title>
	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
	<link rel="stylesheet" type="text/css" media="screen" href="style/stylesheet.css"/>
</head>

<body class="wordpress k2" >

<div id="page">

		<div id="header">

		<h1>Mirage Guild [Alleria] - Recruitment Form</h1>
		<p class="description">Recruitment Form for our guild on Alleria</p>

		<ul class="menu">
						<li class="current_page_item"><a href="#" title="#">NA</a></li>

						<li class="current_page_item"><a href="#" title="#">NA</a></li>
			<li class="admintab"><a href="#">#</a></li>		</ul>

	</div>

		<hr />
<div class="content">
	
	<div id="primary">
		<div id="current-content">

			<div id="primarycontent" class="hfeed">
<?php
require_once "class.db.php";
require_once 'HTML/QuickForm.php';
require_once 'HTML/QuickForm/Renderer/Tableless.php';

$num = 0;

$form = new HTML_QuickForm();
$renderer =& new HTML_QuickForm_Renderer_Tableless();

$headertemplatenew = '<ul>';

$renderer->setHeaderTemplate($headertemplatenew);

$form->addElement('header', null, 'Mirage Guild Recruitment Application');

$form->addElement('text','name','Your Real Name:', array('size' => 25, 'maxlength' => 25));
$form->addElement('text','age','Your Age:',array('size'=>2,'maxlength'=>2));
$form->addElement('text','charname','Main Character Name:', array('size'=> 25, 'maxlength'=>25));
$form->addElement('text','class','Class:',array('size'=>25,'maxlength'=>25));
$form->addElement('text','spec','Class Spec:',array('size'=>25,'maxlength'=>25));
$form->addElement('text','level','Level:',array('size'=>2,'maxlength'=>2));
$form->addElement('text','email','Contact E-Mail:',array('size'=>25,'maxlength'=>50));
$form->addElement('text','othercontact','Other Contact (Specify Medium):',array('size'=>25,'maxlength'=>30));
$form->addElement('textarea','gear','Gear (Provide CT Profile Link or Details):');
$form->addElement('text','formerg','Former Guild:',array('size'=>25,'maxlength'=>25));
$form->addElement('textarea','leavereason','Reasons for Leaving:');

$obj_avail[] = &HTML_QuickForm::createElement('checkbox', 'Monday', null, 'Monday');
$obj_avail[] = &HTML_QuickForm::createElement('checkbox', 'Tuesday', null, 'Tuesday');
$obj_avail[] = &HTML_QuickForm::createElement('checkbox', 'Wednesday', null, 'Wednesday');
$obj_avail[] = &HTML_QuickForm::createElement('checkbox', 'Thursday', null, 'Thursday');
$obj_avail[] = &HTML_QuickForm::createElement('checkbox', 'Friday', null, 'Friday');
$obj_avail[] = &HTML_QuickForm::createElement('checkbox', 'Saturday', null, 'Saturday');
$obj_avail[] = &HTML_QuickForm::createElement('checkbox', 'Sunday', null, 'Sunday');
$form->addGroup($obj_avail, 'avail', 'Days Available For<br />Raid Hours: 7:30-10:30/11:30PM CST:', '<br />');

$obj_reps[] = &HTML_QuickForm::createElement('checkbox', 'ce', null, 'Cenarion Expedition');
$obj_reps[] = &HTML_QuickForm::createElement('checkbox', 'hh', null, 'Honor Hold');
$obj_reps[] = &HTML_QuickForm::createElement('checkbox', 'kot', null, 'Keepers of Time');
$obj_reps[] = &HTML_QuickForm::createElement('checkbox', 'lc', null, 'Lower City');
$obj_reps[] = &HTML_QuickForm::createElement('checkbox',"st", null, "Sha'Tar");
$form->addGroup($obj_reps, 'reps', 'Reputation revered or greater:', '<br />');

$obj_keys[] = &HTML_QuickForm::createElement('checkbox', 'arc', null, 'Arcatraz');
$obj_keys[] = &HTML_QuickForm::createElement('checkbox', 'kara', null, 'Karazhan');
$obj_keys[] = &HTML_QuickForm::createElement('checkbox', 'serp', null, 'Serpentshire Lair');
$obj_keys[] = &HTML_QuickForm::createElement('checkbox', 'shad', null, 'Shadow Labyrinth');
$obj_keys[] = &HTML_QuickForm::createElement('checkbox', 'shat', null, 'Shattered Halls');
$obj_keys[] = &HTML_QuickForm::createElement('checkbox', 'temp', null, 'Tempest Keep');
$form->addGroup($obj_keys, 'keys', 'Keys and Attunements:', '<br />');

$form->addElement('text','alchemy',"Alchemy Level: ",array('size'=>3,'maxlength'=>3));
$form->addElement('text','bs',"Blacksmithing Level: ",array('size'=>3,'maxlength'=>3));
$form->addElement('text','ench',"Enchanting Level: ",array('size'=>3,'maxlength'=>3));
$form->addElement('text','eng',"Engineering Level: ",array('size'=>3,'maxlength'=>3));
$form->addElement('text','leatw',"Leatherworking Level: ",array('size'=>3,'maxlength'=>3));
$form->addElement('text','tailor',"Tailoring Level: ",array('size'=>3,'maxlength'=>3));
$form->addElement('text','jewel',"Jewel Crafting Level: ",array('size'=>3,'maxlength'=>3));

$form->addElement('text','sponsor','Sponsor/Friend in Guild:',array('size'=>25,'maxlength'=>25));
$form->addElement('textarea','joinreason','Why would you like to join Mirage?');
$form->addElement('submit', null, 'Send');

// Define filters and validation rules

$form->addRule('charname', 'Please enter your character\'s name', 'required', null, 'client');
$form->addRule('charname','Letters and Numbers only please.','alphanumeric',null,'client');
$form->addRule('email','Must be vailed email address','emailonly',null,'client');
$form->addRule('class','Your class is required','required', null, 'client');
$form->addRule('class','Classes only have letters','lettersonly',null,'client');
$form->addRule('age','Your age is required','required',null,'client');
$form->addRule('level','Your level is required','required', null, 'client');
$form->addRule('reason',"We'd really like to know why you want to join.",'required',null,'client');
$form->addRule('reason','Letters and Numbers only please.','alphanumeric',null,'client');


if($form->validate()) {
	
	// Try opening a new DB connection     
	try {
		$var = new DB('localhost', 'mirageapps', 'mirageapps', 'apps');
	}

	// Catch any errors thrown if connection fails.

	catch(ConnectException $exception) {
		echo "Connection Error\n";
		var_dump($exception->getMessage());
	}

	// Catch any other errors that may have occured.
  
	catch(Exception $exception) {
 		echo "Other Script Error\n";
		var_dump($exception->getMessage());
	}
	$formdata = $form->exportValues();
	
	//print_r($formdata);
	
	$formcount = count($formdata);
	$x=1;
	//echo "COUNT: " . $formcount . "<br />";
	
	foreach($formdata as $key => $value) {
			
		//echo "X: " . $x . "<br />";
		
		if($key != "keys" && $key != "avail" && $key != "reps") {
			
			$column .= "`" . $key . "`";
			
			$values .=  '"'. $var->real_escape_string($value) . '"';
			
			if ($x < $formcount) { $column .= ","; $values .= ",";}
		}
		
		else {
			foreach($value as $key2 => $value2) {
				
				$valuecount = count($value);
				
				switch ($key) {
					
					case "keys":
						
						$column .= "`". $key2 ."`,";
						$values .= '"'. $var->real_escape_string($value2) . '",';
						break;
					
					case "avail":
						echo $key2 . "=>" . $value2. "<br />";
						if ($value2){
						$daysavail .= $key2;
							if($key2 != "Sunday") { $daysavail .= ","; }
						}
						break;
						
					case "reps":
						
						$column .= "`". $key2 ."`,";
						$values .= '"'. $var->real_escape_string($value2) . '",';
						break;
					
					default:
						break;
				}
			}

			if ($value = "avail" && !$num) {
				$num = 1;
				$column .= "`" . "avail" . "`,";
				$values .= '"' . $var->real_escape_string($daysavail) . '",';
			}
		}
		
	$x++;
	}
		
		$query = "INSERT INTO applications (".$column.") VALUES (".$values.")";
		
		try {
			$result = $var->query($query);
		}

		// Catch any errors thrown if query fails.

		catch(QueryException $exception) {
			echo "Query Error\n";
			var_dump($exception->getMessage());
		}

		// Catch any other errors that may have occured.

		catch (Exception $exception) {
 			echo "Other Script Error\n";
			var_dump($exception->getMessage());
		}
		echo "<p> You have been successfully entered into the database. We will be contacting you soon! Thanks for your entry!<br />- The Mirage Guild Officers</p>";
	footerHTML();
	exit;
}

// Output the form
//$form->display();

$form->accept($renderer);
echo $renderer->toHtml();
footerHTML();

function footerHTML() {
	
	echo <<<HTML
	
</div><!-- #primarycontent .hfeed -->

		</div> <!-- #current-content -->

		<div id="dynamic-content"></div>
	</div> <!-- #primary -->

	<hr />

<div class="secondary">
		<div class="sb-latest">

		<h2>Menu</h2>

		<ul>
				<li><a href='#' title='#'>Work in progress</a></li>
		</ul>
	</div>
		<div class="sb-links">
		<ul>
				<li id="linkcat-2" class="linkcat"><h2>Links</h2>
	<ul>
<li><a href="#">Work in Progress</a></li>

	</ul>
</li>
		</ul>
	</div>
</div>
<div class="clear"></div>

	
</div> <!-- .content -->

	<div class="clear"></div>

</div> <!-- Close Page -->

<hr />
<p id="footer">
	<small>
		Mirage is a <a href="http://www.worldofwarcraft.com" title="World of Warcraft">World of Warcraft</a> Guild on the Alleria 
Server.<br />
		All content Copyright © 2007 Adam Hubscher AKA OffbeatAdam. <br />
		World of Warcraft, The Burning Crusade, Alleria, and all related content is ©2004-2007 Blizzard Entertainment, Inc. All 
rights reserved.
	</small>
</p>

</body>
</html>
HTML;
}
	
?>
