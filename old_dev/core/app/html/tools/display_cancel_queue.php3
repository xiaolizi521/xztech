<?  
    require_once("CORE_app.php"); 
    require_once("act/ActFactory.php");
    
    // Enable Progress Bar
    include("TimeRegister.php");
    $page_timer = new PageTimeRegister();
    $page_timer->start(); 
    $loadtime = number_format((0+$page_timer->average_duration),0);
    set_title('Cancellation Queue ------ (Avg Load: '.$loadtime.' secs)','#003399');
?>
<?  require_once("act/ActFactory.php"); ?>
<?
if ( !empty($datacenter_number) ) {
    $dc_where_clause = " and datacenter_number = " . $datacenter_number;
    $dc_param = "&datacenter_number=" . $datacenter_number;
} else {
    $datacenter_number = "";
    $dc_where_clause = "";
    $dc_param = "";
}

function getDataCenterListBox() {
    global $GLOBAL_db;
    global $datacenter_number;

    $query = "select datacenter_number, datacenter_abbr from datacenter where \"Active\" = 't' and datacenter_number > 0 order by datacenter_abbr ASC";
    $result = $GLOBAL_db->SubmitQuery( $query );
    $form = "<FORM action='display_cancel_queue.php3' method=GET>\n";
    $form = $form . '<P style="border:thin solid black;margin-left:auto;margin-right:auto;display:table">';
    $form = $form . "Datacenter:&nbsp;";
    $form = $form . "<SELECT name='datacenter_number'>\n";
    $form = $form . "<OPTION value=''>Any</OPTION>\n";
    for ( $i = 0; $i < $result->numRows(); $i++ ) {
        $dc = $result->fetchArray($i);
        $option = "<OPTION value=" . $dc['datacenter_number'];
        if ( $dc['datacenter_number'] == $datacenter_number ) {
            $option = $option . " selected ";
        }
        $option = $option . ">" . $dc['datacenter_abbr'] . "</OPTION>\n";
        $form = $form . $option;
    }
    $form = $form . "</SELECT>\n";
    $form = $form . "<INPUT TYPE=submit value='Sort' class='form_button'>";
    $form = $form . "</P>\n</FORM>\n";
    return $form;
}
       
function DisplayQueueList($db, $servers, $default_color = "#FFFFFF")
{
    global $index;
    global $COMMAND_BILLING_UPDATED;
    global $COMMAND_BILLING_NOT_UPDATED;
    global $show_offline;
    global $COLSPAN;
    $i_account = ActFactory::getIAccount();
    $num = $servers->numRows();
    for ($i = 0; $i < $num; $i++)
    {
        if ($i%2!=0) {
           $color = "class=even";
        }
        else {
           $color = "class=odd";
        }
        $a = $db->FetchArray($servers, $i);
        print("\n<TR $color>");
        print("<td class=counter>" . ($i + $index + 1) . "</td>");
        print("<TD>$a[customer_number]</td>");
        print("<td><a href=display_computer.php3?customer_number=$a[customer_number]&computer_number=$a[computer_number]>");
        print("$a[computer_number]</a></TD>");
        print("\n<TD><TT>" . strftime("%m/%d/%Y", $a['sec_due_offline']));
        print("&nbsp;\n</TD><TD><TT>" . $a['server_location'] . "</TT>");
        
        $acfr_account = $i_account->getAccountByAccountNumber($db, $a['customer_number']); 
        print("&nbsp;\n</TD><TD nowrap>" . $acfr_account->getSupportTeamName());
        print("&nbsp;\n</TD><TD><FONT SIZE=-1>" . $a['reason_category']);
        print("&nbsp;\n</TD><TD>");
        $accountExecutive = $acfr_account->getAccountExecutive();
        if($accountExecutive ){
            print($accountExecutive->getFullName());
        }else{
            print("");
        }
        print("&nbsp;\n</TD><TD>$a[os]");
        if(in_dept("AR"))
        {
            print("\n</TD><TD><FONT SIZE=-1>");
            if($a['billing_updated'] == 't')
            {
                print "<a href=display_cancel_queue.php3?";
                print "computer_number=$a[computer_number]";
                print "&show_offline=$show_offline";
                print "&index=$index";
                print "&command=$COMMAND_BILLING_NOT_UPDATED>";
                print "<FONT COLOR=black>";
                print "Billing Updated</FONT></a>&nbsp;&nbsp;";
            }
            else
            {
                print "<a href=display_cancel_queue.php3?";
                print "computer_number=$a[computer_number]";
                print "&show_offline=$show_offline";
                print "&index=$index";
                print "&command=$COMMAND_BILLING_UPDATED>";
                print "<FONT COLOR=red>";
                print "Not Updated</FONT></a>&nbsp;&nbsp;";
            }
        }
        print("\n</TD><TD><a href=display_cancel_queue.php3?computer_number=" . $a['computer_number'] . " class=text_button>");
        print("VIEW</a></TD></TR>");
    }
}

$COMMAND_BILLING_UPDATED = "UPDATE_BILL";
$COMMAND_BILLING_NOT_UPDATED = "UN_UPDATE_BILL";
$COLSPAN = 11;

if (!isset($command))
{
    $command = '';
}
if (!isset($customer_number))
{
    $customer_number = '';
}
if (!isset($computer_number))
{
    $computer_number = '';
}

if (empty($show_offline)) {
    $show_offline = 'false';
}

$MAX_ROWS = 200;
if (empty($index)) {
    $index = 0;
}
$limit = "LIMIT $MAX_ROWS OFFSET $index";

$list_query_template = <<< SQL
    SELECT 
        t1.customer_number, 
        t1.computer_number,
        server_location(t1.computer_number), 
        t2.sec_due_offline, 
        t2.support_incidents, 
        t3.reason_category,
        t2.billing_updated,
        product_os.os
    FROM 
        %s t1 
        JOIN queue_cancel_server t2 USING (computer_number) 
        JOIN offline_reasons t3 USING (reason_number)
        JOIN server_parts USING (computer_number)
        JOIN product_os USING (product_sku)
    %s
    ORDER BY 
        sec_due_offline DESC, 
        customer_number, 
        computer_number
    $limit
SQL;


if($command == $COMMAND_BILLING_UPDATED)
{
    $db->SubmitQuery("update queue_cancel_server
        set billing_updated = 't' 
        where computer_number = $computer_number");
    ForceReload("display_cancel_queue.php3?show_offline=$show_offline" . $dc_param);
}
else if($command == $COMMAND_BILLING_NOT_UPDATED)
{
    $db->SubmitQuery("update queue_cancel_server
        set billing_updated = 'f' 
        where computer_number = $computer_number");
    ForceReload("display_cancel_queue.php3?show_offline=$show_offline" . $dc_param);
}

### START HTML
?>
<HTML id="mainbody">
<head>
<script>
    var avgLoadTime = <?print $loadtime;?>;
</script>
<TITLE>CORE: Cancellation Queue ---- (Avg Load: <?=$loadtime?> secs)</TITLE> 
     <LINK HREF="/css/core_ui.css" REL="stylesheet">
     <LINK HREF="/css/core2_basic.css" REL="stylesheet">
<?require_once("tools_body.php");?> 
<?include("wait_window_layer.php")?> 
<?
    print("<title>Cancellation Queue");
    if (strlen($computer_number) > 0)
        print(": $computer_number");
    print("</title>");
?>
<?require_once("tools_body.php");?>
<TABLE BORDER="0"
       CELLSPACING="0"
       CELLPADDING="2"
       CLASS="titlebaroutline">
<TR>
   <TD>
	<TABLE WIDTH="100%"
	       BORDER="0"
	       CELLSPACING="0"
	       CELLPADDING="0"
          BGCOLOR="FFFFFF">
    <TR>       
        <TD> 
         		<TABLE BORDER="0"
         		       CELLSPACING="2"
         		       CELLPADDING="2">
         		<TR>
         			<TD BGCOLOR="#003399" 
                      CLASS="hd3rev"> Cancellation Queue: <?=strftime("%m/%d/%Y")?>  </TD>
         		</TR>
                <TR> <TD> <?=getDataCenterListBox()?> </TD> </TR>
               <TR>
                  <TD> 
<!-- Begin Outlined Table Content ------------------------------------------ -->
<SCRIPT LANGUAGE="JavaScript"
        SRC="/script/wait_window.js"> 
</SCRIPT>
<TABLE class=datatable>

<?
if ($computer_number != "")
{
### Display info for one server
    $entry_query = "
        SELECT 
            DISTINCT ON (sec_created)
            t2.sec_created, 
            t2.sec_last_mod,
            sec_due_offline, 
            t1.customer_number, 
            t1.computer_number,
            reason_category, 
            reason_info, 
            chose_competitor,
            competitor_benefits, 
            support_incidents,
            continue_hosting,
            future_hosting_method,
            competitor,
            future_consideration,
            preserve_primary_ip,
            preserve_additional_ip,
            ip_notes
        FROM 
            server t1, 
            queue_cancel_server t2, 
            offline_reasons t3
        WHERE 
            t1.computer_number = t2.computer_number
            and t1.computer_number = $computer_number
            and t2.reason_number = t3.reason_number
        ORDER BY sec_created DESC
        ";

    $result = $db->SubmitQuery($entry_query);
    if ($db->NumRows($result) < 0)
    {
        DisplayError("Computer #$computer_number not in cancellation queue");
    }
    print("<style> TH {text-align: left} </style>\n");
    $i_account = ActFactory::getIAccount();
    $i_contact = ActFactory::getIContact();
    for($i = 0; $i < $db->NumRows($result); $i++)
    {
        $a = $db->FetchArray($result, $i);
        print("\n<TR><TD ALIGN=CENTER COLSPAN=2><B><U>");
        print("<a href=display_computer.php3?customer_number=$a[customer_number]&computer_number=$a[computer_number]>");
        print("$a[customer_number]-$a[computer_number]</a></B></U>");
        
        $i_account = ActFactory::getIAccount();
        $account = $i_account->getAccountByAccountNumber($db, $a["customer_number"]);
        $primaryContact = $account->getPrimaryContact();
        
        print("\n<TR><TH>Customer Name:<TD>"
            . $primaryContact->individual->getFullName()."</TR>\n");            
        print("\n<TR><TH>Company Name:<TD>"
            . $primaryContact->individual->companyName . "</TD></TR>");
        print("\n<TR><TH>Offline Due Date:</th> <TD><b>");
        print(strftime("%b %d, %Y", $a['sec_due_offline']));
        $sec_offline = $db->GetVal("select sec_offline
            from offline_servers
            where computer_number = $a[computer_number]");
        if ($sec_offline != "")
        {
            print("\n<TR><TH>Actual Offline Date: <TD>");
            print(strftime("%b %d, %Y", $sec_offline));
        }
        print("\n<TR><TH>Reason Category: <TD>$a[reason_category]");
        if ($a['support_incidents'] != '')
        {
            print("\n<TR><TH>Support Incidents (old field): 
                <TD>$a[support_incidents]");
        }
        print("\n<TR><TD COLSPAN=2><B>
            How could we have prevented you from leaving us?</B>");
        print("\n<BR><BLOCKQUOTE><TT>$a[reason_info]<br><br>");
        print("\n<TR><TH>Are you going to continue hosting?\n");
        if ($a['continue_hosting'] == 't')
            print("<TD> Yes");
        else if ($a['continue_hosting'] == 'f')
            print("<TD> No");
        else
            print("<TD>Unknown");
        print("\n<TR><TH>If yes, how?
            <TD>$a[future_hosting_method]\n");
        print("\n<TR><TH>Switched to competitor: ");
        if ($a['chose_competitor'] == 't')
            print("<TD> Yes");
        else
            print("<TD> No");
        print("\n<TR><TD COLSPAN=2><B>Competitor Benefits:</B>");
        print("<BR><BLOCKQUOTE><TT>");
        print("$a[competitor_benefits]");
        print("<TR><TH>Would you consider us if you have future hosting 
            needs?");
        if ($a['future_consideration'] == 't')
            print("<TD> Yes");
        else if ($a['future_consideration'] == 'f')
            print("<TD> No");
        else
            print("<TD>Unknown");

        print("<TR><TH>Do primary IPs need to be preserved in current customer configuration?");
        if ($a['preserve_primary_ip'] == 't')
            print("<TD> Yes </TD>");
        else if ($a['preserve_primary_ip'] == 'f')
            print("<TD> No </TD>");
        else
            print("<TD>Unknown");
        print("</TH></TR>");

        print("<TR><TH>Do additional IPs need to be preserved in current customer configuration?");
        if ($a['preserve_additional_ip'] == 't')
            print("<TD> Yes </TD>");
        else if ($a['preserve_additional_ip'] == 'f')
            print("<TD> No </TD>");
        else
            print("<TD>Unknown</TD>");
        print("</TH></TR>");

        print("\n<TR><TD COLSPAN=2><B>
            IP preservation notes:</B>");
        print("\n<BR><BLOCKQUOTE><TT>$a[ip_notes]<br><br>");

    }
}
else
{
    ### Display list of all queued servers
    ?>

   <TR>
      <TH> &nbsp; </TH>
      <th> Cust# </th>
      <TH> Server# </TH>
      <TH> Due Offline </TH>
      <TH><font size=-1> Switch[Port] </TH>
      <TH> Support </TH>
      <TH> Reason Category </TH>
      <TH> Account Mgr </TH>
      <TH> Server OS </TH>
       <?
       if (in_dept("AR"))
       {
           print("<TH> Billing Status </TH>\n");
       }
       ?>
      <TH colspan=2> &nbsp; </TH>
    </TR>

    <?

    $show_next_link = false;
    $now_sec = time();
    if($show_offline == 'false') {
        # offline server link
        print("\n<TR><th>&nbsp;</th><Td COLSPAN=$COLSPAN>");
        print("<A HREF=display_cancel_queue.php3?show_offline=true" . $dc_param . " class=text_button>");
        print("Show Offline Servers</a></td>");
        # Display online servers
        
        #TODO: Finish sorting all cases by datacenter
        $where_clause = "where (t2.completed is null or t2.completed = 'f')
                            and t2.sec_due_offline < $now_sec
                            ";
        $online_and_due_query = sprintf($list_query_template, 
            'server', 
            $where_clause . $dc_where_clause
            );
        $servers = $db->SubmitQuery($online_and_due_query);
        if ($servers->numRows() == $MAX_ROWS) {
            $show_next_link = true;
        }
        if ($servers->numRows() > 0)
            DisplayQueueList($db, $servers, "#e6e6e6");
        else
        {
            print("<TR><TH COLSPAN=$COLSPAN>");
            print("<FONT COLOR=red>No servers due to be taken offline today");
            print("</FONT>");
            print("</TH></TR>");
        }
        $online_not_due_query = sprintf($list_query_template, 
            'server', 
            "where (t2.completed is null or t2.completed = 'f') 
                and t2.sec_due_offline > $now_sec" . $dc_where_clause);
        $servers = $db->SubmitQuery($online_not_due_query);
        if ($servers->numRows() == $MAX_ROWS) {
            $show_next_link = true;
        }
        if ($servers->numRows() > 0)
        {
            print("\n<TR><th class=subhead1 COLSPAN=$COLSPAN>");
            print("DUE OFFLINE IN THE FUTURE</th></TR>\n");
            DisplayQueueList($db, $servers, "#a0a0a0");
        }
    }
    else {
        # HIDE OFFLINE LINK
        print("\n<TR><th>&nbsp;</th><Td COLSPAN=$COLSPAN>");
        print("<A HREF=display_cancel_queue.php3 class=text_button>");
        print("Show Online Servers</A></Td></TR>\n");
        # Show cancel queue info for offline servers
        print("\n<TR><th class=subhead1 COLSPAN=$COLSPAN> OFFLINE </th></TR>\n");
### The queue_cancel_server_history table lists servers that have
### gone offline in the past.
### We must search server since the servers could have
### been put back online.
        # OFFLINE
        $offline_query = sprintf($list_query_template, 
            'server', 
            "where t2.completed = 't' and status_number < 12" 
            . $dc_where_clause);
        $servers = $db->SubmitQuery($offline_query);
        if ($servers->numRows() == $MAX_ROWS) {
            $show_next_link = true;
        }
        if ($servers->numRows() < 1)
        {
            print("<TR><TH COLSPAN=8>None after index $index\n");
        }
        else
        {
            DisplayQueueList($db, $servers, "#cccccc");
        }

        # PREVIOUSLY OFFLINE
        print("\n<TR><th class=subhead1 COLSPAN=$COLSPAN>PREVIOUSLY OFFLINE BUT REINSTATED</th></tr>\n");
        $offline_query = sprintf($list_query_template, 
            'server', 
            "where t2.completed = 't' and status_number >= 12" 
            . $dc_where_clause);
        $servers = $db->SubmitQuery($offline_query);
        if ($servers->numRows() == $MAX_ROWS) {
            $show_next_link = true;
        }
        if ($servers->numRows() < 1)
        {
            print("<TR><TH COLSPAN=$COLSPAN>None after index $index\n");
        }
        else
        {
            DisplayQueueList($db, $servers, "#cccccc");
        }
    }
    if ($index > 0) {
        $prev_index = $index - $MAX_ROWS;
    }
    else {
        $prev_index = 0;
    }
    if ($show_next_link or $index > 0) {
        $next_index = $index + $MAX_ROWS;
        print "<TR><TH COLSPAN=3>"
            . "<A HREF=display_cancel_queue.php3?"
            . "index=$prev_index&show_offline=$show_offline"
            . $dc_param . " class=text_button>"
            . "&lt;&lt;&lt; Previous $MAX_ROWS</A></TH>\n"
            ;
        print "<TH COLSPAN=2>$index to " . ($index + $MAX_ROWS) . "</TH>\n";
        
        print "<TH COLSPAN=3>"
            . "<A HREF=display_cancel_queue.php3?"
            . "index=$next_index&show_offline=$show_offline"
            . $dc_param . " class=text_button>"
            . "Next $MAX_ROWS &gt;&gt;&gt;</A></TH>\n"
            ;
        print "</TR>";
    }
}
?>
</TABLE>
<!-- End Outlined Table Content -------------------------------------------- -->
                  </TD>
               </TR>
         		</TABLE>
        </TD>
    </TR>
    </TABLE></TD>
</TR>
</TABLE>
<SCRIPT language="javascript">
<!-- 
	//Hides wait window when page loads
	ap_showWaitMessage('waitDiv', 0);
//--> 
</SCRIPT>
<?= page_stop() ?>
</HTML>
<? $page_timer->stop();?>
