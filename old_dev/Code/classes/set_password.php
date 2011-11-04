<?php

//error_reporting(E_ALL);
require_once 'HTML/QuickForm.php';
//require_once 'HTML/QuickForm/Renderer/Tableless.php';

$form = new HTML_QuickForm();

$form->addElement('header', null, 'Whatpulse Signatures Project User Form');
$form->addElement('text','username','Preferred Username:', array('size' => 25, 'maxlength' => 25));
$form->addElement('password','password','Preferred Password:',array('size'=> 25, 'maxlength' => 25));

$form->addElement('submit', null, 'Send');
//$form->addElement('reset', null, 'Reset');

$form->addRule('username', 'Username is required.', 'required', null, 'client');
$form->addRule('password', 'Password is required.', 'required', null, 'client');

if($form->validate()) {

		$formdata = $form->exportValues();
		
		$filename="/home/svn/pulse/.htpasswd";
		$fp = fopen($filename, 'r');
		$file_contents=fread($fp,filesize($filename));
		fclose($fp);
	
		$lines=explode("\n",$file_contents);

		foreach($lines as $line) {
			
				$bits = explode(":",trim($line));
				$users[$bits[0]] = trim($bits[1]);
			}
			
			if ($users[$formdata['username']] != '') {
			
				die ("user exists");
			
			}
			
			else {
			
				$username = $formdata['username'];
				$password = '{SHA}' . base64_encode(sha1($formdata['password'], TRUE));
				
				$newfile = $username.":".$password."\n";
				
				$fp = fopen($filename,'a');
				fwrite($fp,$newfile);
				fclose($fp);
				printf("%1 has been succesfully added to the password file.",$username);			
			}
	
	}
$form->display();

?>