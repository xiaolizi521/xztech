<?php
/*
 * Created on Aug 24, 2005
 * Created by ben
 * 
 * This file defines the network_diagram class, which is used to dyanmically visualize the network
 * of an account
 */

require_once($CORE_ROOT . "/lib/GraphViz/GraphVizInclude.php");

define("NEUTRAL_COLOR", "#FFFFFF");    
define("THIS_DEVICE_COLOR", "#FFFF66");
define("CYCLE_COLOR", "#FF0000");
define("NO_IP_COLOR", "#C0C0C0");
define("INACTIVE_DEVICE_COLOR", "#006699");

define("MAX_DEVICES_TO_DISPLAY", 100);
 
class network_diagram {
    var $graph;
    var $isCircular;
    var $deviceNumber;
    var $thisDeviceColor;
    var $neutralColor;
    var $initializationErrorString = "";
    var $groupAssignments;
    var $groups;
    var $nodes;   
    var $accountNumber;
    
    //this constructor creates a network_diagram object, which creates a network diagram for all of the devices in
    // the account that the specified device is a member of.  The specified device will be colored with the specified
    // color to offset it visually
    function network_diagram($deviceNumber, $db, $colorThisDevice=true) {
        $this->deviceNumber = $deviceNumber;
        $this->db = $db;
        $this->colorThisDevice = $colorThisDevice;        
        $this->graph = &new Graph("Diagram");
        $this->isCircular = false;
        $this->groupAssignments = array();
        $this->groups = array();
        $this->nodes = array();       
        $this->accountNumber = -1;
        $this->hasTooManyDevices = 0;
        
        if($deviceNumber == "" or $deviceNumber == null or
           $db == "" or $db == null) {
            $this->initializationErrorString = "Diagram was not initialized properly. Be sure you are " .
                  "specifying a device number and database variable. Device was set as '" . $this->deviceNumber;
            return;
        }        
        
        $computer = new RackComputer($deviceNumber);
        $computer->Init("", $deviceNumber, $this->db);        
        $allDevicesInAccount = $computer->getOtherDevicesInAccount();
        $activeDevicesInAccount = $computer->getOtherDevicesInAccount(0);
        $allDevicesInAccount[] = $computer->computer_number;       
        if ($computer->getStatus() >= 0)
          $activeDevicesInAccount[] = $computer->computer_number;       
        
        // If there are more devices in the account than we can process in a reasonable amount of time
        if(count($activeDevicesInAccount) > MAX_DEVICES_TO_DISPLAY) {
            $this->hasTooManyDevices = count($activeDevicesInAccount);
            return;
        }        
                
        $this->accountNumber = $computer->getAccountNumber();
        
        $this->assignToGroups($activeDevicesInAccount);        
        $this->createDiagram($allDevicesInAccount, $activeDevicesInAccount);                                             
    }
    
    function printDiagram($printDiagnostic=false) { 
        
        $cmapname = tempnam('/tmp', "graph-" . $this->deviceNumber . '--');
        
        if($this->hasTooManyDevices) {
            print("<font size=\"+1\">We're sorry. The number of devices in this account exceeds the maximum number " .
                  "possible for generating a diagram of the network connections within the account. Note that it is " .
                  "still possible to use the organize device page to set the relationships between device without " .
                  "the visual aid of a diagram.</font><br />");
        }
        else if($this->initializationErrorString != "") {
            print("<font size=\"+2\" color=\"red\">Error. " . $this->initializationErrorString . "</font");
        }
        else {            
            $this->graph->useDigraph();
            $this->graph->outputCMAP(  $cmapname );
            $map_name = "my_image_map";                       
            
            if($this->isCircular) {
                print("<font size=\"+1\" color=\"" . CYCLE_COLOR . "\">A circular relationship exists between devices in " .
                        "this account. This is a misconfiguration that will cause errors. Please correct this problem " .
                        "immediately by correcting the organization of one or more devices in the account's " .
                        "network.</font><br />");
            }
            
            //print out the image to the browser, including no cache headers.  Note that I had to add a unique parameter 
            // here because firefox was still cacheing the image for the back-button event. Uniquifying means that that 
            // particular URL won't be cached.
            $color='0';
            if ( $this->colorThisDevice ) {
                $color = '1';
            }
            
            print("<img src='/tools/network_diagram_image.php?filename=" 
                . $filename . ".png&unique=" 
                . rand(10,10000) . "&sd=" 
                . $this->deviceNumber ."&color="  
                . $color . "' usemap='#$map_name'>");            
            $this->printImageMap( $cmapname, $map_name);
            unlink($cmapname);
            
            print("
            <table border=\"1\" width=\"285\">
              <tr>
                <td colspan=\"2\"><center><b>Diagram Color Legend:</b></center></td>
              </tr>
              <tr>
                <td width=\"20\" bgcolor=\"" . THIS_DEVICE_COLOR . "\"></td>
                <td>Network device currently being organized</td>
              </tr>
              <tr>
                <td width=\"20\" bgcolor=\"" . INACTIVE_DEVICE_COLOR . "\"></td>
                <td>Inactive device</td>
              </tr> 
              <tr>
                <td width=\"20\" bgcolor=\"" . NO_IP_COLOR . "\"></td>
                <td>Device with no Primary IP address assigned</td>
              </tr>
              <tr>
                <td width=\"20\" bgcolor=\"" . NEUTRAL_COLOR . "\"></td>
                <td>All other devices</td>
              </tr>
            </table>");
            
            if($printDiagnostic) {
                print("</center>");
                pre_print_r(htmlentities($this->graph->render()));
                print("<center>");
            }
        }
    }         

    function createDiagram($devicesInAccount, $activeDevicesInAccount) {
        //draw arrows from each parent to each group. make each group a plain text node with each row listing the device 
        // in that group
        foreach($activeDevicesInAccount as $currentDevice) {            
            $currentDeviceNode =& $this->createNode($currentDevice);
            
            $children = $this->db->submitQuery("SELECT computer_number
                                                FROM computer_behind_network_device
                                                WHERE device_number = " .$currentDevice . "
                                                ORDER by computer_number DESC;");
                                       
            //iterate over the list of devices behind the current device, connecting the current device to the group
            // that each child belongs to as we go                                           
            for ($i = 0; $i < $children->numRows(); $i++) {
                //grab the group that this child computer is in
                $computer_number = $children->getResult($i, 'computer_number');
                if (!in_array($computer_number, $activeDevicesInAccount) && in_array($computer_number, $devicesInAccount))
                    continue;

                $groupId = $this->getGroupId($computer_number);
                if($groupId == -1) {
                    $this->initializationErrorString = "There is a problem with the account setup. Computing device #" . 
                           $children->getResult($i, "computer_number") . " is configured to be behind computing device 
                           #$currentDevice.  These devices belong to different accounts.  The diagram cannot be 
                           displayed until this problem is corrected in the CORE database.";
                    return;
                }
                $groupNode =& $this->createGroupNode($groupId);
                
                //connect this parent device to that group of children devices                                
                //if it was just a node that was returned (group size == 1)
                if(HasMethod($groupNode,"isInCluster")) {
                    $this->graph->addNode($groupNode);
                    $currentDeviceNode->connectNode($groupNode);
                }
                else {
                    $this->graph->addCluster($groupNode);
                    //get the middle device in the cluster and just connect it, symbolizing a connection to the rest of 
                    // the nodes in that cluster. we are forced to do it this way due to a limitation of graphviz's
                    // dot algorithm, which won't let nodes connect to clusters
                    $midIndex = ceil($groupNode->getNumberOfNodes() / 2) - 1;
                    $currentDeviceNode->connectNode($groupNode->nodes[$midIndex]);
                }                                                                                                                                  
            }
            
            //finally, add this node to the graph 
            $this->graph->addNode($currentDeviceNode);            
        }
    }

    function assignToGroups($activeDevicesInAccount) {
        //iterate over all devices in account, grouping together devices that share the exact same parents, and 
        //assigning the devices to those groups
                
        foreach($activeDevicesInAccount as $currentDevice) {
            //get the parents of the current device
            $parents = $this->db->submitQuery("SELECT device_number
                                               FROM computer_behind_network_device
                                               WHERE computer_number = " . $currentDevice . "
                                               ORDER by device_number DESC;");
            $groupId = "";
            for ($i = 0; $i < $parents->numRows(); $i++) {
                $groupId .= $parents->getResult($i, "device_number");
            }
            $this->groupAssignments[$currentDevice] = $groupId;                       
        }
    }

    function getGroupId($deviceNumber) {
        if(array_key_exists($deviceNumber, $this->groupAssignments)) {
            return $this->groupAssignments[$deviceNumber];
        }
        else {
            return -1;
        }
    }

    function &createGroupNode($groupId) {
        //create a node for each member of the group, then create a cluster for the group, returning a reference to that
        // cluster                             
        
        //check whether we've already created this group node        
        if(!isset($this->groups[$groupId])) {                      
            //create a cluster for this group
            $cluster = &new Cluster($groupId);            
            
            //next, create a node for each device in the group
            $numInCluster = 0;
            foreach($this->groupAssignments as $currentDeviceNumber => $currentGroupId) {
                if($currentGroupId == $groupId) {
                    $node =& $this->createNode($currentDeviceNumber);
                    $cluster->addNode($node);
                    $numInCluster++;
                }
            }
            
            //only create a cluster if there are two or more devices to group together
            if($numInCluster > 1) {
                //now store the cluster
                $this->groups[$groupId] =& $cluster;
            }
            else {
                $this->groups[$groupId] =& $node;
            }            
        }        
        return($this->groups[$groupId]);
    }
    
    function &createNode($label) {       
        if (!isset($this->nodes[$label])) {        
            $node = &new CaptionedImageNode(); 
            $node->setCaption("#".$label);        
            $node->setImage(BASE_DIRECTORY.getIconForServer($label));                    
            $node->fontsize = 12;     //have to set fontsize to large value due to graphviz font problem on production servers
            $node->fontname = "arial";
            $node->colorConnections("gray", false);                    
            $node->setUrl("#$label");
            
            $computer = new RackComputer($label);
            $computer->Init("", $label, $this->db);  
            
            if($this->colorThisDevice and $label == $this->deviceNumber) {
                $node->setColor(THIS_DEVICE_COLOR);
            }        
            else if($computer->getStatus() < STATUS_SEGMENT_CONFIGURATION) {
                $node->setColor(INACTIVE_DEVICE_COLOR);
            }
            else if(!$computer->hasPrimaryIp()) {
                $node->setColor(NO_IP_COLOR);
            }
            else {
                $node->setColor(NEUTRAL_COLOR);
            }
            $this->nodes[$label] = &$node;            
        }
        return($this->nodes[$label]);
    }

    function printImageMap($filename="", $name="") {        
        if($filename == "") {
            return;
        }          
        $lines = @file($filename, true);
        if(empty($lines)) {
            return;
        }
        
        print("<map id='$name' name='$name'>");
        //parse to add onmouseover events
        $coordsSearch = "coords=";
        $computerNumSearch = "href=\"#";         
        foreach($lines as $line) {
            //figure out the computer number, which will be in the href quotes
            $computerNumStart = strpos($line, $computerNumSearch) + strlen($computerNumSearch);
            $computerNumEnd = strpos($line, '"', $computerNumStart);
            $computerNum = substr($line, $computerNumStart, $computerNumEnd - $computerNumStart);
            
            $computer = new RackComputer($computerNum);
            $computer->Init("", $computerNum, $this->db);
            
            //now create the javascript items
            //only allow devices with that are at least STATUS_ORDER_SUBMITTED to be organized          
            if($computer->getStatus() >= STATUS_ORDER_SUBMITTED) {                
                $onclickString = "onClick=\"node_click($computerNum, true); return false;\"";
            }
            else {
                $onclickString = "onClick=\"node_click($computerNum, false); return false;\"";
            }                             
            $onmouseoverString = "onmouseover=\"return escape('" .
                                 "<table border=\'1\'>" .
                                   "<tr>" .
                                     "<td>Computer Number:</td>" .
                                     "<td><img src=\'" . getIconForServer($computerNum) . "\' /> <b>$computerNum</b></td>" .
                                   "</tr><tr>" .
                                     "<td>Computer Name:</td>" .
                                     "<td>" . $computer->getData("server_name") . "</td>" .
                                   "</tr><tr>" .
                                     "<td>OS:</td>" .
                                     "<td>" . $computer->getData("os") . "</td>" .
                                   "</tr><tr>" .                                         
                                     "<td>IP:</td>";
            if($computer->getData("primary_ip") == "") {
                $onmouseoverString .= "<td>None Assigned</td>";
            }
            else {             
                $onmouseoverString .= "<td>" . $computer->getData("primary_ip") . "</td>";
            }
            $onmouseoverString .= "</tr>" .
                                  "<tr>" .
                                    "<td>Status</td>";         
            $serverStatus = $computer->getStatus();
            if($serverStatus == -1) { //having to put in this workaround due to really weird PHP problem on staging.
                $onmouseoverString .= "<td>Computer No Longer Active</td>";
            }                            
            else {
                $onmouseoverString .= "<td>" . $GLOBALS['SERVER_STATUS_LIST'][$serverStatus] . "</td>";
            }
            $onmouseoverString .= "</tr>" .
                                 "</table>')\"";
            
            //now modify the line to include the javascript items, and write it out
            $coordsStart = strpos($line, $coordsSearch);
            if($coordsStart) {
                print(substr($line, 0, $coordsStart)); 
                print($onclickString . " "); 
                print($onmouseoverString . " "); 
                print(substr($line, $coordsStart));
            }
        }        
        print("</map>");       
    }
}
?>
