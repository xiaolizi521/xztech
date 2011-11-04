<?php

//Replace variables as you see fit

$doc = new DOMDocument();
$doc->formatOutput = true;

$root = $designXML->createElement("image");
$doc->appendChild($root);

/*
 *  $data_array is an array structured as follows:
 * $data[$field][$pos][$coord];
 */


// Create the FONT section
$font = $doc->createElement("font");
$fontid = $doc->createElement("id");

// Modify this to be the ID of the font in the database
$fontid->appendChild($doc->createTextNode("fontdbid"));
$font->appendChild($fontid);

// Create the shadow section
$shadow = $doc->createElement("shadow");
$shadowenabled = $doc->createElement("enabled");

// FALSE if shadow is not used. Enabled if shadow is used.
$shadowenabled->appendChild($doc->createTextNode(TRUE));
$shadow->appendChild($shadowenabled);

//Append to the root.
$root->appendChild($font);
$root->appendChild($shadow);

foreach ($data_array as $field) {
	
	// Create an element in the XML file for the field.
	${$field} = $doc->createElement($field);
	
	foreach ($data_array[$field] as $pos => $data) {
		
		// Create an element for the Position (x or y)
		${$pos} = $doc->createElement($pos);
		
		// Append the position coordinate (int)
		${$pos}->appendChild($doc->createTextNode($data));
		
		// Append the child "x or y" to the root "field"
		${$field}->appendChild(${$pos});
		
		// Append to the root.
		$root->appendChild(${$field});
		
	}
}

// $XML now holds the string of formatted XML DOM standards compliant.
$XML = $doc->saveXML();

?>