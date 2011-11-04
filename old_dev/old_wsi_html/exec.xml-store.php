#!/usr/bin/php
<?php
    
    /*
    **
    ** Name: XML Storage Execution
    **
    ** Purpose: This calls and processes the Whatpulse User XML file
    ** 
    ** INPUT: Unprocessed (freshly downloaded) Whatpulse XML Users File
    **
    ** Output: XML 1.1 DOM Formatted Compliant XML file with Unicode Characters and Proper Escapes
    **
    ** Usage: Call from command line. This script is not meant to produce HTML friendly output.
    **
    ** No paramaters are required.
    **
    **
    */
    
    // Import all required variables and classes.
    require_once("conf/xmlvars.php");
    require_once("includes/php/classes/req/class.xml-store.php");
    
    // Set te locale. This is in case of any possible issues.
    setlocale(LC_ALL, 'en_US.UTF8');
    
    // We want all errors to return. This is a cron job. 
    error_reporting(E_ALL);

    // Unfortunately, the file gets rather large. We need to increase the memory limit for a PHP process.
    ini_set("memory_limit", "1024M");
    
    // Begin the timer. We want to know how long the script is taking. 120+sec requires more optimizing.
    $stimer = explode( ' ', microtime() );
    $stimer = $stimer[1] + $stimer[0];
    
    // This object is the primary bulk of processing. Reference the appropriate class file.
    // We need an XML parser, a DB object, and a DOMDocument.
    $userObj = new xmlFileObject;
    $xml = new xmlParser($userObj);
    $xmlDoc = new DOMDocument();
    
    // We want properly indented output.
    $xmlDoc->formatOutput = true;
    
    // Input (the entire) file into $data.    
    $data = file_get_contents('sigs.xml.new');

    // Unfortunately, the data isn't unicode (unlike what its "doctype" says.)
    $data = utf8_encode($data);
    
    // This is required. There are many high-ascii and unicode non-standard characters.
    // This replaces them. Refer to the array in the file xmlvars to modify or add if needed.    
    $data = str_replace($xmlsearch, $xmlreplace, $data);
        
    // And now the fun begins. Parse the data.
    $xml->parse($data);
    
    // For loop to process the data and begin the whatpulse 
    for($x=0; $x<$userObj->indexSize(); $x++):
    
        // We now have the XML as a meta-database object. We want the first `row`.
        $userArray = $userObj->returnAssocSingle();

            // Now we process the row.
            foreach($userArray as $key => $value):

                // Switch the row "key" (a-la column name)
                switch($key):
                    
                    // For the root, we create the root.
                    case 'whatpulse':
                    
                        $root = $xmlDoc->createElement("whatpulse");
                        $xmlDoc->appendChild($root);
                        break;
                    
                    // For each user we have potentially two root objects.
                    // User, the main user object.
                    // Team, an optional object that acts as a child root to the user object.    
                    case 'user':
                    case 'team':

                        if (!$bool[$key]):
                            
                            ${$key} = $xmlDoc->createElement($key);
                            $bool[$key] = TRUE;
                        
                        // We have to protect against an infinite user.
                        elseif ($key === 'user' && $bool['user']):
                            
                            $root->appendChild(${$key});
                            unset(${$key});
                            ${$key} = $xmlDoc->createElement($key);

                        endif;
                        break;
                        
                    // This is a hack.
                    // For the final element in($team || $user)...
                    // We need to close the element properly.
                    case 'miles':
                    
                        if($bool['team']):
                            
                            ${$key} = $xmlDoc->createElement($key);
                            ${$key}->appendChild($xmlDoc->createTextNode($value));
                            $team->appendChild(${$key});
                            
                            $user->appendChild($team);
                            
                            unset($team);
                            
                            $bool['team'] = FALSE;

                        else:
                        
                            ${$key} = $xmlDoc->createElement($key);
                            ${$key}->appendChild($xmlDoc->createTextNode($value));
                            $user->appendChild(${$key});

                        endif;

                        break;
                    
                    // Default is to append the field and its data.
                    
                    default:

                        ${$key} = $xmlDoc->createElement($key);
                        ${$key}->appendChild($xmlDoc->createTextNode($value));
                        $user->appendChild(${$key});
                        break;

                endswitch;

            endforeach;

        endfor;
        
    $xmlDoc->save("newPulseXML.xml");
    
    fclose($fp);
    
    echo "The conversion of the unintuitive Whatpulse XML file has completed.\n";
    
    $etimer = explode( ' ' , microtime() );
    $etimer = $etimer[1] + $etimer[0];
    
    printf( "Script took <b>%f</b> seconds to run.", $etimer-$stimer);
    
?>