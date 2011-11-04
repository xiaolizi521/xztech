<?

function createButton($name, $url) {
    return "<A href=\"$url\" class=\"text_button\"> $name </A>\n";
}

if ( isset($datacenter_number) ) {
    // SQL for getting lsit of switches
    // for dropdown
    $switch_sql = "
	SELECT DISTINCT
	        d.datacenter_abbr,
		s.\"Number\" AS switch_number,
		z.datacenter_number
	FROM
	       \"NTWK_Switch\" s
	JOIN  \"NTWK_Zone\" z USING (\"NTWK_ZoneID\")
	JOIN  datacenter d USING (datacenter_number)
	WHERE
	        z.datacenter_number = $datacenter_number
	ORDER BY
	        z.datacenter_number, 
		s.\"Number\" ASC;";

        $datacenter_abbr = $db->getVal("
            SELECT
                datacenter_abbr
            FROM
                datacenter
            WHERE
                datacenter_number = $datacenter_number;");
} else {
    // SQL for getting lsit of switches
    // for dropdown
    $switch_sql = "
	SELECT DISTINCT
	        d.datacenter_abbr,
		s.\"Number\" AS switch_number,
		z.datacenter_number
	FROM
	       \"NTWK_Switch\" s
	JOIN  \"NTWK_Zone\" z USING (\"NTWK_ZoneID\")
	JOIN  datacenter d USING (datacenter_number)
	ORDER BY
	        z.datacenter_number, 
		s.\"Number\" ASC;";
}

// Create a button for each datacenter
$result = $db->SubmitQuery("
    SELECT
        datacenter_number,
        datacenter_abbr
    FROM
        datacenter 
    WHERE
        datacenter_number > 0 and \"Active\" = 't'
    ORDER BY
        datacenter_abbr;" );
$buttons = createButton(
    "Show All", 
    "switch_port.php3");
for( $i=0; $i<$result->numRows(); $i++ ) {
    $id = $result->getCell($i,0);
    $name = $result->getCell($i,1);
    $buttons .= createButton(
        "$name", 
        "switch_port.php3?datacenter_number=$id");
}

// Create switch selectbox, or message explaining
// Why it is not there
$switch_form = '';
$switch_number_list=$db->SubmitQuery($switch_sql); 
$ctr=0;
$num=$db->NumRows($switch_number_list);
if ($num > 0) {
    $have_switches = 1;
    $switch_form .= "<TR>\n<TD valign=\"top\" width=\"50%\">\n";
    $switch_form .= "<form action=switch_port.php3>";
    
    $switch_form .= "<p align=\"center\">";
    
    if (isset($datacenter_number)) {
        $switch_form .= "<input type=hidden name=datacenter_number ";
        $switch_form .= "value=$datacenter_number>";
        $switch_form .= "<b> $datacenter_abbr</b>:";
    }
    
    $switch_form .= "<select name=switch_select>";
    
    for ($i=0;$i<$num;$i++)
    {
        $switch = $value = '';
        if (!isset($datacenter_number)) {
            $datacenter_abbr = 
                $db->GetResult($switch_number_list,$i,0);
            $switch .= $datacenter_abbr . ":";
        }
        $switch_number = $db->GetResult($switch_number_list,$i,1);
        $switch .= $switch_number;
        $value = $datacenter_abbr . ":" . $switch_number;
        $switch_form .= "<option value=$value";
        if (isset($switch_select) and $switch_select == $value) {
            $switch_form .= " selected";
        }
        $switch_form .= ">$switch</option>\n";
    }
    $switch_form .= "</select>\n";
    $switch_form .= "</p>\n</td>\n";
    $switch_form .= "<TD align=\"center\" width=\"50%\">\n";
    $switch_form .= "<input type=submit value=\"Select Switch\" ";
    $switch_form .= "class=\"form_button\">\n</p>\n</td>\n</tr>\n</form>";
} else {
    $have_switches = 0;
    $switch_form .= "<TR><TD align=\"center\" colspan=2>\n";
    $switch_form .= "<FONT COLOR=#FF0000>";
    $switch_form .= "NO SWITCHES FOR $datacenter_abbr";
    $switch_form .= "</FONT>";
    $switch_form .= "</TD></TR>";
}

$switch_table = '';

if ($have_switches) {

    $switch_header =  "<TR>\n";
    $switch_header .= "\t<TH> Switch # </TH>\n";
    $switch_header .= "\t<TH> Port # </TH>\n";
    $switch_header .= "\t<TH> Cust#-Server# </TH>\n";
    $switch_header .= "\t<TH> &nbsp; </TH>\n";
    $switch_header .= "</TR>\n";
    
    $switch_table .= $switch_header;
    
    /*
     *  Fill variables necessary for pulling switch data
     *  examples:
     *  $switch_number:      SAT1:A1
     *  $datacenter_abbr:    SAT1
     *  $short:              A1
     *  $datacenter_number:  1
     */
    if ( isset($switch_select) ) {
        $switch_number = $switch_select;
        $pieces = explode(':', $switch_select);
        $datacenter_abbr = $pieces[0];
        $short = $pieces[1];
        $datacenter_number = $db->GetVal("
            SELECT
                datacenter_number
            FROM
                datacenter
            WHERE
                datacenter_abbr = '$datacenter_abbr'");
    } else if (isset($datacenter_number)) {
        # choose first switch based on datacenter
        $short = $db->GetVal("
	    SELECT DISTINCT
                s.\"Number\" AS switch_number
            FROM
               \"NTWK_Switch\" s
            JOIN  \"NTWK_Zone\" z USING (\"NTWK_ZoneID\")
            JOIN  datacenter d USING (datacenter_number)
            WHERE
              z.datacenter_number = $datacenter_number
            ORDER BY
                s.\"Number\" ASC
            LIMIT 1;");
        $switch_number = $datacenter_abbr . ":" . $short;
        // Datacenter number / abbr already set
    } else {
        $switch_number = "SAT1:A1";
        $short = "A1";
        $datacenter_abbr = "SAT1";
        $datacenter_number = 1;
    }
    $num_ports_total= $db->GetVal("select model.\"NumberPorts\" 
                                   from 
                                        \"NTWK_Switch\" switch, 
                                        \"NTWK_SwitchModel\" model,
                                        \"NTWK_Zone\" zone
                                   where switch.\"NTWK_SwitchModelID\" = model.\"NTWK_SwitchModelID\" 
                                         and switch.\"NTWK_ZoneID\" = zone.\"NTWK_ZoneID\"
                                         and zone.datacenter_number = '$datacenter_number'
                                         and switch.\"Number\" ='$short';");
    
    // fill the table with rows
    for ($y=1;$y<=$num_ports_total;$y++) {
        $switch_row = '';

        $port_sql = "
            SELECT
                switch_number,
                smp.\"Number\" as port_number,
                customer_number,
                xref.computer_number 
            FROM
                \"NTWK_Switch\" switch,
                \"NTWK_xref_Port_Computer_InterfaceType\" xref,
                \"NTWK_SwitchModelPort\" smp,
                \"NTWK_Port\" port,
                server
            WHERE
                xref.\"NTWK_PortID\" = port.\"NTWK_PortID\"
                AND xref.\"computer_number\" = server.computer_number
                AND smp.\"NTWK_SwitchModelPortID\" = port.\"NTWK_SwitchModelPortID\"
                AND datacenter_number = $datacenter_number
                AND switch_number='$short' 
                AND port_number=$y  
            ORDER BY
                switch_number,
                port_number
            ASC;";
        $port_sql = "
            select
                switch.\"Number\" as switch_number,
                smp.\"Number\" as port_number,
                server.customer_number,
                server.computer_number
            from
                \"NTWK_Port\" port,
                \"NTWK_Switch\" switch,
                \"NTWK_SwitchModelPort\" smp,
                \"NTWK_xref_Port_Computer_InterfaceType\" xref,
                \"NTWK_Zone\" zone,
                server
            where
                port.\"NTWK_SwitchID\" = switch.\"NTWK_SwitchID\"
                and port.\"NTWK_SwitchModelPortID\" = smp.\"NTWK_SwitchModelPortID\"
                and port.\"NTWK_PortID\" = xref.\"NTWK_PortID\"
                and switch.\"NTWK_ZoneID\" = zone.\"NTWK_ZoneID\"
                and xref.computer_number = server.computer_number
                and switch.\"Number\" = '$short'
                and zone.datacenter_number = '$datacenter_number'
                and smp.\"Number\" = '$y'
            order by
                switch.\"Number\",
                smp.\"Number\"
                ASC;
        ";

        $switch_assignments=$db->SubmitQuery($port_sql);
        
        if (($ctr%2)==0) {
            $color="class=even";
        } else {
            $color="class=odd";
        }
        
        $switch_row .= "<TR $color >\n\t<TD ALIGN=CENTER> $switch_number </TD>\n";
        $switch_row .= "\t<TD ALIGN=CENTER> $y </TD>\n";
        
        if (!$switch_assignments || $db->NumRows($switch_assignments)<=0)
        {
            $switch_row .= "\t<TD ALIGN=CENTER><FONT COLOR=#FF0000>";
            $switch_row .= "FREE PORT </FONT></TD>\n";
            $switch_row .= "\t<TD> &nbsp; </TD>\n</TR>\n";
        } else {
            if ($db->NumRows($switch_assignments)>1) {
                $message = "Switch :$switch_number Port:$y\n";
                
                for ($k=0;$k<$db->NumRows($switch_assignments);$k++) {
                    $message .= $db->GetResult(
                        $switch_assignments,
                        $k,
                        "customer_number");
                    $message .= "-";
                    $message .= $db->GetResult(
                        $switch_assignments,
                        $k,
                        "computer_number");
                    $message .= "\n";
                }
                
                // send mail if dup is found
                $subject = "Duplicate Switch/Port Assignments";
                $from = GetRackEmailAddr("noreply");
                mail(
                    'assembly_email',
                    $subject,
                    $message,
                    "From:".$from."\nReply-To:".$from );
            }
            $switch_row .= "<TD ALIGN=\"CENTER\">";
            $switch_row .= $db->GetResult(
                $switch_assignments,
                0,
                "customer_number" );
            $switch_row .= "-";
            $switch_row .= $db->GetResult(
                $switch_assignments,
                0,
                "computer_number");

            //if ($db->GetResult($switch_assignments,0,"computer_number")!="") {
            //    $switch_rows .= "<BR>"
            //    $switch_rows .= determine_os(
            //        $db->GetResult(
            //            $switch_assignments,
            //            0,
            //            "computer_number") ) );
            //}

            $switch_row .= "</TD>";
            $switch_row .= "<TD ALIGN=\"CENTER\">";
            
            // ARO - Changing this URL so we get the side navigation back when they click.
            // $url =  "/tools/DAT_display_computer.php3?computer_number=";
            $url =  "/ACCT_main_workspace_page.php?computer_number=";
            $url .= $db->GetResult(
                $switch_assignments,
                0,
                "computer_number");
            
            $switch_row .= "<A HREF=\"$url\">";
            $switch_row .= "<IMG SRC=\"/images/button_arrow_off.jpg\" ";
            $switch_row .= "WIDTH=20 HEIGHT=20 BORDER=0 ALT=\"->\"></A>\n";
            $switch_row .= "</TR>\n";
        }

        $switch_table .= $switch_row;
        $db->FreeResult($switch_assignments);
        $ctr++;
    }
}
$db->FreeResult($switch_number_list);

?>
