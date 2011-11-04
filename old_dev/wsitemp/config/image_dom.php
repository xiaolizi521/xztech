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
		
	}
}

// $XML now holds the string of formatted XML DOM standards compliant.
$XML = $doc->saveXML();

?>