<?php
require_once 'XML/Parser.php';

class myParser extends XML_Parser
{
  function __construct()
  {
    parent::XML_Parser();
  }

 /**
  * handle start element
  *
  * @access private
  * @param  resource  xml parser resource
  * @param  string    name of the element
  * @param  array     attributes
  */
  function startHandler($xp, $name, $attribs)
  {
    echo "<strong>" . $name . "</strong>: ";
  }

 /**
  * handle start element
  *
  * @access private
  * @param  resource  xml parser resource
  * @param  string    name of the element
  */
  function endHandler($xp, $name)
  {
    printf('Ending Tag: ', $name);
  }

 /**
  * handle character data
  *
  * @access private
  * @param  resource  xml parser resource
  * @param  string    character data
  */
  function cdataHandler($xp, $cdata)
  {
    
  }
}
//start timer
$stimer = explode( ' ', microtime() );
$stimer = $stimer[1] + $stimer[0];
$p = new myParser();

$result = $p->setInputFile('sigs.xml');
$result = $p->parse();

$etimer = explode( ' ', microtime() );
$etimer = $etimer[1] + $etimer[0];
echo '<p style="margin:auto; text-align:center">';
printf( "Script timer: <b>%f</b> seconds.", ($etimer-$stimer) );
echo '</p>';

?>