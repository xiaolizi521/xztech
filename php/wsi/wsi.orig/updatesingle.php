<?php

include "regxmlparse.php";

//updatesingle.php

function updatesingle() {
	
	$xml = xml_parser_create('UTF-8');
	$rss = new wpulseXML;
	xml_set_object($xml, $rss);
	xml_set_element_handler($xml, 'start_element', 'end_element');
	xml_set_character_data_handler($xml, 'character_data');
	xml_parser_set_option($xml, XML_OPTION_CASE_FOLDING, false);
	
	
	$fp = fopen('sigs.xml.gz',r) or die('Cant open xml file');
	
	while ($data = utf8_encode(fread($fp, 4096))) {
		$data = str_replace("&sup2;",2,$data);
		$data = str_replace("&sup3;",3,$data);
		$data = str_replace("&iquest;","?",$data);
		$data = str_replace("&szlig;","B",$data);
		$data = str_replace("&sect;","S",$data);
		$data = str_replace("&eth;","o",$data);
		$data = str_replace("&ouml;","o",$data);
		$data = str_replace("&oslash;","o",$data);
		$data = str_replace("&Uuml;","U",$data);
		$data = str_replace("&deg;","o",$data);
		$data = str_replace("&aacute;","a",$data);
		$data = str_replace("&eacute;","e",$data);
		$data = str_replace("&egrave;","e",$data);
		$data = str_replace("&copy;","c",$data);
		$data = str_replace("&yen;","Y",$data);
		$data = str_replace("&ntilde;","n",$data);
		$data = str_replace("&auml;","a",$data);
		$data = str_replace("&uuml;","u",$data);
		$data = str_replace("&ETH;","D",$data);
		$data = str_replace("@","A",$data);
		$data = str_replace("&reg;","R",$data);
		$data = str_replace("&Auml;","A",$data);
		$data = str_replace("&acute;","",$data);
		$data = str_replace("&","and",$data);
		
		//$data = preg_replace('/&amp;/i','&',$data);
		
		//$data = preg_replace('/(<\W.*\W>.*<\/\W.*\W>)|(<\W.*\W>)/',htmlentities($1),$data);
		//$data = str_replace('/(<\W.*\W>.*<\/\W.*\W>)|(<\W.*\W>)/',htmlentities('$1'),$data);
		//echo "<br /><br /><p>".htmlentities($data)."</p><br /><br />";
		
		xml_parse($xml, $data) or die (sprintf("XML error: %s at line %d",xml_error_string(xml_get_error_code($xml)),xml_get_current_column_number($xml)));
	}
	fclose($fp);
	
	xml_parser_free($xml);
	echo "<div class='codeblock'>Success! You are now registered on WSI.</div>";
	$etimer = explode( ' ', microtime() );
	$etimer = $etimer[1] + $etimer[0];
	
	/*echo '<p style="margin:auto; text-align:center">';
	$time = $etimer-$stimer;
	printf( "Script timer: <b>%f</b> seconds.", ($etimer-$stimer) ); */

}