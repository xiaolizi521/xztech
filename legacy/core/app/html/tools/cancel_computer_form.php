<?
function printCancellationForm(
        $form, $computer, $already_queued, $already_queued_data, $error) {
    global $SCHEDULE_HOUR;
    global $COMMAND_QUEUE_CANCELLATION;
    global $COMMAND_UNQUEUE_CANCELLATION;
    global $COMMAND_OVERRIDE;
    global $COMMAND_CONFIRM_CANCELLATION;
    global $COMMAND_CANCEL_NOW;
    global $MOVING_QUESTION;
    global $MAX_DAYS_IN_ADVANCE;
    global $db;

    if ($already_queued) {
        $default = $already_queued_data;
    }
    else {
        $default['sec_due_offline'] = '';
        $default['reason_number'] = '';
        $default['reason_info'] = '';
        $default['support_incidents'] = '';
        $default['chose_competitor'] = '';
        $default['competitor'] = '';
        $default['competitor_benefits'] = '';
        $default['continue_hosting'] = '';
        $default['future_hosting_method'] = '';
        $default['future_consideration'] = '';
        $default['oid'] = '';
        $default['reason_number'] = '';
	$default['preserve_primary_ip'] = '';
	$default['preserve_additional_ip'] = '';
	$default['ip_notes'] = '';
        foreach($default as $name => $value) {
            if (!empty($form[$name])) {
                $default[$name] = $form[$name];
            }
        }

        if (!empty($form['month'])) {
            $default['sec_due_offline'] = 
                mktime($SCHEDULE_HOUR, 0, 0, 
                    $form['month'], $form['day'], $form['year']);
        }
    }
    $command = $form['command'];

    ?>
<script language="JavaScript" src="/script/action_functions.js"></script>
<LINK HREF="/ui/rui/resources/css/rack-all.css" REL="stylesheet">
<body onload='testfordowngradeactions(<? print $computer->computer_number; ?>,-1);'>
<FORM ACTION="cancel_computer.php3" METHOD='post' name="theform">
<?
# hidden
    print("\n<input name=computer_number type=hidden");
    print(" value=\"$computer->computer_number\">");
?>
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
         			<TD BGCOLOR="#003399" CLASS="hd3rev"> 
                  Cancellation Form:
                  #<?printf("%s-%s",
                        $computer->customer_number, 
                        $computer->computer_number);?> 
                  </TD>
         		</TR>
               <TR>
                  <TD>

<!-- Begin Outlined Table Content ------------------------------------------ -->

<TABLE BORDER="0"
       CELLSPACING="2"
       CELLPADDING="2"
       VALIGN="TOP">
    <? if(!empty($error)): ?>
        <TR>
         <TD COLSPAN=2 BGCOLOR=yellow>
         <big><b>
         <FONT COLOR=red>
         Error: <?= $error ?>
         </FONT>
         </b></big>
         </TD>
        </TR>
    <? endif; ?>
    <? if($command == $COMMAND_CONFIRM_CANCELLATION): ?>
        <TR>
         <TD COLSPAN=2>
         <FONT COLOR=red>
         This information should have been entered by the support specialist
         when the computer was scheduled for cancellation.
         Make any changes necessary, and then click 
        "Mark Server Offline/No Longer Active"</TD>
    <? else: ?>
        <TR>
         <TD COLSPAN=2>
         <FONT COLOR=red>
         This form schedules PERMANENT cancellations of servers.</i>
         <br> Do not use this to suspend servers. </TD>
    <? endif; ?>
</TR>
<TR>
<TD COLSPAN=2 class="rack-panel-emergency">


</td>
</tr>
<?
# customer-computer number
    print("\n<tr><TH ALIGN=LEFT>Customer-Computer #: </TH>");
    print("<TD><b> $computer->customer_number");
    print("-$computer->computer_number </TD></tr>");

# customer name
    $primaryContact = $computer->account->getPrimaryContact();
    print("\n<tr><TH ALIGN=LEFT>Customer Name: </TH>");
    print("<TD>" . $primaryContact->individual->getFullName() ."</TD></TR>");

# company
    print("\n<TR><TH ALIGN=LEFT> Company: </TH>");
    print("<td>" . $primaryContact->primaryCompanyName."</TD></TR>");
?>
</TABLE>
<TABLE BORDER="0"
       CELLSPACING="2"
       CELLPADDING="2"
       VALIGN="TOP">
<TR VALIGN="top">
	<TH COLSPAN=2 ALIGN=LEFT> Reason for cancelling service: <TH>
</TR>
<TR>
	<TD COLSPAN=2>
<?    
    //If this is a cancellation edit, display offline reason
    //otherwise, allow user to set offline reason
    
    if(!empty($default['reason_number']))
    {
        //Determine current values and pass to Javascript menu
        $reason_number = $default['reason_number'];
        $reason_category = $db->GetVal("
            select 
                 reason_category
            from 
                offline_reasons
            where 
                reason_number = '$reason_number'");
        
        $selected_group = getReasonCategoryGroup($reason_number);
        $selected_type = $reason_category;
        include("offline_reason.phinc");
    }
    else
    {    
        include("offline_reason.phinc"); 
    }
?></TD>
</TR>
<tr>
	<TH colspan=2 ALIGN=LEFT> How could we have prevented this cancellation?
        <br>(Specify with detail including incidents; 20 character minimum):</TH>
</TR>
<TR>
   <TD COLSPAN=2><TEXTAREA COLS="45"
	              ROWS="6"
	              NAME="reason_info"><?print($default['reason_info']);?></TEXTAREA></TD>
</TR>
</TABLE>
<TABLE BORDER="0"
       CELLSPACING="2"
       CELLPADDING="2"
       VALIGN="TOP">
<tr>
	<TH ALIGN=LEFT> Are you going to continue hosting? </TH>
	<td>
<select name=continue_hosting onClick="disableUselessFields()">
    <option> -- SELECT --
<?
    $select_true = '';
    $select_false = '';
    if(isset($default['continue_hosting'])) {
        if($default['continue_hosting'] == 't')
        {
            $select_true = " SELECTED ";
        }
        else if($default['continue_hosting'] == 'f')
        {
            $select_false = " SELECTED ";
        }
    }
    printf("<option %s value=f>No\n", $select_false);
    printf("<option %s value=t>Yes\n", $select_true);
?>
</select></TD>
</TR>
<TR>
	<TH ALIGN=LEFT>If yes, how:</TH>
	<td><select name=future_hosting_method onClick="disableUselessFields()">
    <? // Change the name, but never change the value
       // Values should be moved into their own table in the database.
    ?>
    <option><?
        if (isset($default['future_hosting_method'])) {
            print($default['future_hosting_method']);
        }
        else {
            print('-- SELECT --');
        }
        ?>
    <option value="Rackspace Hosting">Rackspace Hosting
    <option value="In-House/Self">In House/Self
    <option value="Virtual Hoster">Virtual Hoster
    <option value="Colocation">Colocation
    <option value="Other Managed Hoster">Other Managed Hoster
    </select></TD>
</TR>
<tr>
	<TH ALIGN=LEFT> <? print($MOVING_QUESTION); ?> </TH>
	<td>
<select name=chose_competitor onClick="disableUselessFields()">
    <option> -- SELECT --
<?
    $select_true = '';
    $select_false = '';
    if($default['chose_competitor'] == 't')
    {
        $select_true = " SELECTED ";
    }
    else if($default['chose_competitor'] == 'f')
    {
        $select_false = " SELECTED ";
    }
    printf("<option %s value=f>No\n", $select_false);
    printf("<option %s value=t>Yes\n", $select_true);
?>
</select></TD>
</TR>
<tr>
	<TH ALIGN=LEFT> Who? </TH>
	<td><input size=20 name=competitor
               value="<?print(@$default['competitor']);?>"></TD>
</TR>		
</TABLE>
<TABLE BORDER="0"
       CELLSPACING="2"
       CELLPADDING="2"
       VALIGN="TOP">	  
<tr>
	<th align=left> If yes, why did you choose that company? </th>
</tr>
<tr>
   <td> <textarea name=competitor_benefits cols=45 rows=6 wrap=virtual><? print($default['competitor_benefits']); ?></textarea></TD>
</TR>
</TABLE>
<TABLE BORDER="0"
       CELLSPACING="2"
       CELLPADDING="2"
       VALIGN="TOP">
<tr>
	<TH ALIGN=LEFT> Would you consider us <BR> if you have future hosting needs?</TH>
	<td>
	<select name=future_consideration onClick="disableUselessFields()">
	    <option> -- SELECT --
	<?
	    $select_true = '';
	    $select_false = '';
	    if(isset($default['future_consideration'])) {
	        if($default['future_consideration'] == 't')
	        {
	            $select_true = " SELECTED ";
	        }
	        else if($default['future_consideration'] == 'f')
	        {
	            $select_false = " SELECTED ";
	        }
	    }
	    printf("<option %s value=f>No\n", $select_false);
	    printf("<option %s value=t>Yes\n", $select_true);
	?>
	</select></TD>
</TR>
<tr>
	<TH ALIGN=LEFT> When would you like us <BR> to take the server offline? </TH>
  <? if($command == $COMMAND_CONFIRM_CANCELLATION
        or $command == $COMMAND_CANCEL_NOW): ?>
    <td><FONT COLOR=RED>
            <? 
            print(strftime("%B %d, %Y", $default['sec_due_offline'])); 
            print("<br><i>(Today is " . strftime("%B %d, %Y", time()) . ")");
            ?></TD>
  <? else: ?>
    <td>
    <select name=month>
        <option value="">-SELECT-
    <?
        if ($default['sec_due_offline'] != 0)
        {
            $month = strftime("%m", $default['sec_due_offline']);
            $day = strftime("%d", $default['sec_due_offline']);
            $year = strftime("%Y", $default['sec_due_offline']);
        }
        else
        {
            $month = '';
            $day = '';
            $year = '';
        }
        for($i = 1; $i <= 12; $i++)
        {
            $d[3] = $i;
            print("\n<option ");
            if($i == $month)
                print(" SELECTED ");
            print("value=$i>");
            print(strftime("%b", mktime(0, 0, 0, $i, 1, 0, 0)));
        }
    ?>
    </select><select name=day>
    <?
        for($i = 1; $i <= 31; $i++)
        {
            print("<option ");
            if($i == $day)
                print(" SELECTED ");
            print(" value=$i>$i\n");
        }
    ?>
    </select><select name=year>
    <?
        $date_array = getdate(time());
        $current_year = $date_array['year'];
        $next_year = $current_year + 1;
        print("<option>$current_year");
        $next_year_sec = mktime(0,0,0, 1, 1, $next_year, 0);
        if (($next_year_sec - time()) < ($MAX_DAYS_IN_ADVANCE * 3600 * 24))
        {
            print("<option> $next_year");
        }
        if ($already_queued)
        {
            print("<option SELECTED> $year");
        }
    ?>
    </select>
  <?endif;?></TD>
</TR>
<tr>
	<TH ALIGN=LEFT> Do primary IPs need to be preserved in current customer configuration?</TH>
	<td>
	<select name=preserve_primary_ip onClick="disableUselessFields()">
	    <option> -- SELECT --
	<?
	    $select_true = '';
	    $select_false = '';
	    if(isset($default['preserve_primary_ip'])) {
	        if($default['preserve_primary_ip'] == 't')
	        {
	            $select_true = " SELECTED ";
	        }
	        else if($default['preserve_primary_ip'] == 'f')
	        {
	            $select_false = " SELECTED ";
	        }
	    }
	    printf("<option %s value=f>No\n", $select_false);
	    printf("<option %s value=t>Yes\n", $select_true);
	?>
	</select></TD>
</TR>
<TR>
	<TH ALIGN=LEFT> Do additional IPs need to be preserved in current customer configuration?</TH>
	<td>
	<select name=preserve_additional_ip onClick="disableUselessFields()">
	    <option> -- SELECT --
	<?
	    $select_true = '';
	    $select_false = '';
	    if(isset($default['preserve_additional_ip'])) {
	        if($default['preserve_additional_ip'] == 't')
	        {
	            $select_true = " SELECTED ";
	        }
	        else if($default['preserve_additional_ip'] == 'f')
	        {
	            $select_false = " SELECTED ";
	        }
	    }
	    printf("<option %s value=f>No\n", $select_false);
	    printf("<option %s value=t>Yes\n", $select_true);
	?>
	</select></TD>
</TR>
<tr>
	<TH ALIGN=LEFT>IP preservation notes:</TH>
</TR>
<TR>
   <TD><TEXTAREA COLS="45"
	              ROWS="6"
	              NAME="ip_notes"><?print($default['ip_notes']);?></TEXTAREA></TD>
</TR>
<TR>
	<td colspan=2 ALIGN=CENTER>
		<TABLE BORDER="1"
       CELLSPACING="0"
       CELLPADDING="4">
		<tr>
			<td><font color=red>
			<DIV ALIGN="center">
         Be sure to notify the customer that they will be billed for the next month,<BR>
			although they can have their server taken down on any specified day.
         </DIV>
			</font>
			<p>
			From contract:<br>
			<blockquote>
			This Agreement shall automatically renew, in full, for a one (1) month
			period <BR> unless Customer or Rackspace notifies the other party in writing thirty
			(30)days <BR> in advance of the termination of the current period.
			</blockquote>
			<center>
			<input type=checkbox name=notification_checkbox>
			<b>I have notified the customer.</b></TD>
		</TR>
		</table></TD>
</TR>
<tr>
	<td align=center colspan=2><b>
    <? if ($command == $COMMAND_CONFIRM_CANCELLATION
            or $command == $COMMAND_CANCEL_NOW): ?>
        <input type=submit name=command 
            value="<?print("$COMMAND_CANCEL_NOW");?>">
    <? elseif(!$already_queued): ?>
        <input type=submit name=command 
            value="<?print("$COMMAND_QUEUE_CANCELLATION");?>">
    <? endif; ?>
	<input type=reset value="Reset Form Values"></TD>
</TR>
<?
    if ($already_queued 
        and (empty($command) or $command != $COMMAND_CONFIRM_CANCELLATION))
    {
        print "\n<TR>\n";
        print "<td align=center colspan=2>";
        print "<hr noshade><font color=red><b>";
        print ">>> Server Already Queued for Cancellation <<<";
        print "<br><input type=submit name=command 
               value=\"$COMMAND_OVERRIDE\">";
        print "&nbsp; &nbsp;<input type=submit name=command 
            value=\"$COMMAND_UNQUEUE_CANCELLATION\">";
		print("</TD>\n</TR>\n");
    }
?>
</table>
</FORM>
<div id = "downgrade_info" style="display: none; background-color:#FFF4F4;border:3px solid #EC0909;"></div>
</body>
<?
}
?>
