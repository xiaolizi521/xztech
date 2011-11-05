#!/usr/bin/php


<?php

    /* This is the GVO IP Grabbing Script.
     * It will go through and grab all IPs across all subnets, as well as hostnames.
     * Written by Adam Hubscher.
     */
    
    $file = fopen("ipaddresses.csv", "a+");

    error_reporting(0);

    $ipranges = array (
        '12.204.164.' => '24',
        '12.68.140.' => '24',
        '12.68.137.' => '24',
        '12.68.139.' => '25',
        '12.68.141.' => '25',
        '12.68.136.' => '27' );
    
    $cidr = array ( 24 => 255, 25 => 128, 27 => 32 );
    
    $currport = 2;

    $key = "/root/.ssh/id_rsa";
    $pubkey = "/root/scripts/utilities/publickey";

    $sinfo = array();

    foreach ( $ipranges as $ip => $range ) {

        $start = $range == 27 ? 226 : 2;

        for ($x = $start; $x <= $cidr[$range]-1; $x++) {

            print("At IP: ".$ip.$x."\n");

            $continue = FALSE;

            if(!$connection = ssh2_connect($ip . $x, 2, array('hostkey'=>'ssh-rsa'))){

                if(!$connection = ssh2_connect($ip . $x, 22, array('hostkey' => 'ssh-rsa'))):
                    
                    //this host failed to connect period.

                    $sifno[$ip . $x][0] = "Connection Failed on Both Ports.";

                    $continue = TRUE;
                
                else:

                    $sinfo[$ip . $x]["port"] = "22";

                endif;

            }

            else {

                $sinfo[$ip . $x]["port"] = "2";

            }

            if (ssh2_auth_pubkey_file($connection,'root', "/root/scripts/utilities/publickey", "/root/.ssh/id_rsa", '')) {

                $sinfo[$ip . $x]["keyed"] = TRUE;

                print("Successfully Connected to " . $ip . $x . "\n");
        
            } 
            
            else {

                $sinfo[$ip . $x]["keyed"] = FALSE;

                $continue = TRUE;

                print("Could not authenticate to " . $ip . $x . "\n");
                
            }

            if($continue) { continue; }

            $shell = ssh2_shell($connection, "bash");
            
            $cmd = "echo '[start]';hostname; ifconfig | grep inet | grep -v 127.0.0.1 | awk '{print $2}' | sed 's/addr://';echo '[end]'";
            
            $output = user_exec($shell,$cmd);
            
            fclose($shell);

            $output = array_filter($output);
            $y = 0;

            foreach($output as $key => $value) {
                
                $value = trim($value);
                if(!is_null($value) && $value != "" && $value) {

                    if(preg_match('/\b([0-9]{1,3})\.([0-9]{1,3})\.([0-9]{1,3})\.([0-9]{1,3})\b/', $value)){
                            
                        $sinfo[$ip . $x][$y] = $value;
                    }

                    else {

                        $sinfo[$ip . $x]["hostname"] = $value;
                   
                    }

                    $y++;
                }
                    
            }
            
            // End IP For
        }

        unset($output);
       $sinfo = array(); 
    foreach($sinfo as $primip => $arr) {

        $ip = array();
        $temp = array();

        foreach($arr as $key => $value) {

            if($key == "hostname" || $key == "port" || $key == "keyed") {

                $temp[$key] = $value;

            }

            else {

               $ip[] = $value;
            }
        }

        if(!$temp["keyed"]) {

            $foocsv[$primip]["port"] = $temp["port"];
            $foocsv[$primip]["keyed"] = FALSE;

            continue;

        }

        $foocsv[$ip[0]]["hostname"] = $temp["hostname"];

        $foocsv[$ip[0]]["port"] = $temp["port"];

        $foocsv[$ip[0]]["keyed"] = $temp["keyed"];

        $foocsv[$ip[0]]["count"] = count($ip) - 1;

        for($x = 1; $x < count($ip); $x++) {

            $foocsv[$ip[0]][$x] = $ip[$x];
        }

    }

    unset($sinfo);

    foreach ( $foocsv as $primip => $arr ) {

        $tmp = $primip;

        if(!$arr["keyed"]) {

            $tmp .= ", NO";

            continue;
        }

        else {

            $tmp .= ", YES";
        }

        $tmp .= ", " . $arr["hostname"];

        if(is_int($arr["count"]) && $arr["count"] != "" && !is_null($arr["count"])) {

        for($z = 1; $z <= $arr["count"]; $z++) {

            $tmp .= ", " . $arr[$z];
        }
        }

        print($tmp . "\n");

        fwrite($file, $tmp);

    }

    
        // End Foreach
    }

    function user_exec($shell,$cmd) {
        
        fwrite($shell,$cmd . "\n");
        $output = "";
        $start = false;
        $start_time = time();
        $max_time = 2; //time in seconds
        
        while(((time()-$start_time) < $max_time)) {
            
            $line = fgets($shell);
            
            if(!strstr($line,$cmd)) {
                
                if(preg_match('/\[start\]/',$line)) {
                    
                    $start = true;
                }
                
                elseif(preg_match('/\[end\]/',$line)) {
                    
                    return $output;
                }
                
                elseif($start){
                    
                    $output[] = $line;
                }
            }
        }
    }

?>
