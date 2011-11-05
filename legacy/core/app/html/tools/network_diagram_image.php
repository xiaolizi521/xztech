<?php
/*
 * Created on October 05, 2005
 * Created by ben
 * 
 * The page actually responds to an HTTP GET request for the network diagram itself (separate page so that it can send
 * no cache headers to the browser
 */ 
header("Content-type: image/png");

// tell the browser not to cache this page
header ("Expires: ".gmdate("D, d M Y H:i:s", time())." GMT");
header ("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
header ("Cache-Control: no-cache, must-revalidate");
header ("Pragma: no-cache");

include('CORE_app.php');
require_once('network_diagram.php');

$filepath = tempnam("/tmp/", "graph-computer-" . $_GET['sd'] . '--');

// Code to remove dependency between this item and the page from where 
// it is rendered.  This makes this item connection session independent.
// Currently -- redo the draw every time.  
// NOTE: This double draw wil be fixed in the refactor to python code
//
$sd = $_GET['sd']; // Seed Device
$bcolor = ($_GET['color'] == 1); // should we color it or not
    
$diagram = new network_diagram($sd, $db, $bcolor);
$diagram->graph->useDigraph();
$diagram->graph->outputPNG( $filepath );

// Grab image from file and display to page
$im = imagecreatefrompng($filepath);
unlink($filepath);
imagepng($im);
imagedestroy($im);

?>
