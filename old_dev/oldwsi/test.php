#!/usr/bin/php
<?php

print ("starting");
/* This script will be grabbing all IP addresses from all servers.
 * It will also write to the file what port SSH is running on
 * Whether it is Keyed or not
 */

ini_set("expect.timeout", -1);
ini_set("expect.loguser", "Off");

$ipranges = array ( '12.204.164.' => '24',
                    '12.68.140.' => '24',
                    '12.68.137.' => '24',
                    '12.68.139.' => '25',
                    '12.68.141.' => '25',
                    '12.68.136.' => '27' );

$cmd = "ifconfig | grep 'inet addr:' | grep -v '127.0.0.1' | cut -d: -f2 | awk '{ print $1 }'";

$cidr = array ( 24 => 254,
                25 => 128,
                27 => 32 );

//foreach ( $ipranges as $ip => $range):

        //for ($x = 0; $x <= $range; $x++):

                //$currip = $ip;
                //$currip .= $x;

                $currip = "europa.x-zen.cx";

                $stream = expect_popen("ssh triton@europa.x-zen.cx " . $cmd);

                    //fopen("expect://ssh triton@".$currip." ".$cmd, "r");
                $cases = array(array (0 => "password:", 1 => PASSWORD), array ("yes/no)?", YESNO));

                echo "here";

                while(true):
                switch (expect_expectl ($stream, $cases)) {

                        case PASSWORD:

                            fwrite($stream, chr(3));
                            echo "Failed to auth with server at IP: . " . $currip . "\n";
                            break 2;

                        case YESNO:

                            fwrite($stream, "yes\n");
                            break;

                        default:

                            die ("Experienced Error. Bye");
                            break;

                }
                endwhile;

//                while ($line = fgets($stream)) {

  //                      print $line;
    //            }

                fclose ($stream);

?>
