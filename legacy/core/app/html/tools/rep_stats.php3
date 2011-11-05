<? require_once("CORE_app.php"); 
    // Enable Progress Bar
    include("TimeRegister.php");
    $page_timer = new PageTimeRegister();
    $page_timer->start();
    
?>
<HTML id="mainbody">
<HEAD>
<?
    $loadtime = number_format((0+$page_timer->average_duration),0);
?>    
<script>
    var avgLoadTime = <?print $loadtime;?>;
</script>
<?set_title('Sales Rep Stats ------ (Avg Load: '.$loadtime.' secs)','#003399');?>
<TITLE>CORE: Sales Rep Stats ---- (Avg Load: <?=$loadtime?> secs)</TITLE>
     <LINK HREF="/css/core_ui.css" REL="stylesheet">
     <LINK HREF="/css/core2_basic.css" REL="stylesheet">
<?require_once("tools_body.php");?>
<?include("wait_window_layer.php")?>
<TABLE BORDER="0"
       CELLSPACING="0"
       CELLPADDING="0"
       ALIGN="left">
<TR BGCOLOR="#FFCC33">
    <TD VALIGN="top" width=10><IMG SRC="/images/note_corner.gif"
                          WIDTH="10"
                          HEIGHT="10"
                          HSPACE="0"
                          VSPACE="0"
                          BORDER="0"
                          VALIGN="TOP"
                          ALT=""></td>
     <td> NOTES: </TD>
</TR>
<TR BGCOLOR="#FFF999">
    <TD colspan=2>
      <p>
      This report shows the number of contracts sent and received by
      each sales representative.
      </p>
      <p>
      <b>Note:</b> Occasionally, a sales rep may close more than 100% in a
      month.  This is because a contract was sent in the prior month, but
      closed in the current month.
      </p>
    </TD>
</TR>
</TABLE>
<BR CLEAR="all"><BR>
<SCRIPT LANGUAGE="JavaScript"
        SRC="/script/wait_window.js"> 
</SCRIPT>
<table border="0"
       cellspacing="0"
       cellpadding="2"
       class="titlebaroutline">
<tr>
    <td>

<TABLE BORDER="0"
       CELLSPACING="0"
       CELLPADDING="0"
       bgcolor=#ffffff>
<TR>
	<TD>
		<TABLE BORDER="0"
		       CELLSPACING="2"
		       CELLPADDING="2">
		<TR>
		    <TD COLSPAN="5"
		        ALIGN="left"
		        VALIGN="top"
		        BGCOLOR="#003399"
		        CLASS="hd3rev"> Contracts Sent and Received By Rep </TD>
		</TR>
		<TR BGCOLOR="#CCCCCC">
		    <TD CLASS="label"> Date </TD>
		    <TD CLASS="label"> Closed /Sent&nbsp;(%) </TD>
		    <TD CLASS="label"> Breakdown </TD>
		</TR>
		<?
		$tot_new_servers=0;
		$stamp=time();
		$curr_month=date("m",$stamp);
		$curr_year=date("Y",$stamp);
		$ctr=0;
		//Get list of all the sales reps
		$sales_reps = $db->SubmitQuery('
		    SELECT w1.employee_number, "CONT_ContactID" 
		    FROM "xref_employee_number_Contact" w1, 
		        employee_dept w2 
		    WHERE department = \'SALES\' 
		    AND w1.employee_number = w2.employee_number
            ');   
		$num = $sales_reps->numRows();
		$sales_rep["0"]="No Rep";
		for ($i = 0; $i < $num; $i++) {
		    $contact_id = $sales_reps->getResult($i,"CONT_ContactID");
		    $employee_number= $sales_reps->getResult($i,"employee_number");
		    $cont = new CONT_Contact;
		    $cont->loadID($contact_id);
		    $person = $cont->getPerson();
		    $sales_rep[$employee_number]= $person->getFirstName() 
		        . " " . $person->getLastName();
		}
		$sales_reps->freeResult();
		
		for ($year=$curr_year;$year>1998;$year--) {
		    if ($year==$curr_year) {
		            $start=$curr_month;
		    }
		    else {
		            $start=12;
		    }
		    for ($month=$start;$month>0;$month--) {
		        if (checkdate($month,31,$year)) {
		            $last_day=31;
		        }
		        else if (checkdate($month,30,$year)) {
		            $last_day=30;
		        }
		        else if (checkdate($month,29,$year)) {
		            $last_day=29;
		        }
		        else if (checkdate($month,28,$year)) {
		            $last_day=28;
		        }
		        $begin_mark = mktime(0,0,0,$month,1,$year) - 1; //Puts it at 23:59
		        $begin_mark=date("m/d/Y",$begin_mark); 
		        $end_mark="$month/$last_day/$year";
		        if ($ctr%2==0) {
		            print("<TR VALIGN=TOP bgcolor=#e6e6e6>");
		        }
		        else {
		            print("<TR VALIGN=TOP>");
		        }
		        unset($sales_stats);
		        unset($ordered_sales_stats);
		
		        //Find total to date
		        $pre_total=$db->GetVal("
		            SELECT COUNT(computer_number) 
		            FROM sales_speed w1
		            WHERE DATE(sec_contract_received::abstime)<='".$begin_mark."' 
		                AND sec_contract_received>0
		            ");
		
		        $new_servers_list=$db->SubmitQuery("
		            SELECT w1.customer_number, w1.computer_number, w2.rep_number 
		            FROM sales_speed w1, rep_assignment w2 
		            WHERE DATE(sec_contract_received::abstime) > '".$begin_mark."' 
		                AND DATE(sec_contract_received::abstime) <= '$end_mark' 
		                AND w1.computer_number = w2.computer_number 
		                AND w1.customer_number = w2.customer_number 
		            ORDER BY w2.rep_number ASC
		            ");
		        //$new_servers=$db->GetVal("select count(computer_number) from sales_speed where date(sec_contract_received)>'".$begin_mark."' and date(sec_contract_received)<='$end_mark';");
		        $new_servers=$new_servers_list->numRows();
		
		        //$ordered_servers=$db->GetVal("select count(computer_number) from sales_speed where date(sec_placed_order)>'".$begin_mark."' and date(sec_placed_order)<='$end_mark';");
		        $ordered_servers_list=$db->SubmitQuery("
		            SELECT w1.customer_number, w1.computer_number, w2.rep_number 
		            FROM sales_speed w1, rep_assignment w2 
		            WHERE DATE(sec_placed_order::abstime) > '".$begin_mark."' 
		                AND DATE(sec_placed_order::abstime) <= '$end_mark' 
		                AND w1.computer_number = w2.computer_number 
		                AND w1.customer_number = w2.customer_number 
		            ORDER BY w2.rep_number ASC
		            ");
		        $ordered_servers=$ordered_servers_list->numRows();
		
		        $change_lost_servers=$db->GetVal("
		            SELECT COUNT(computer_number) 
		            FROM offline_servers w1
		            WHERE DATE(sec_offline::abstime) > '".$begin_mark."' 
		                AND DATE(sec_offline::abstime) <= '$end_mark'
		            ");
		        $lost_servers=$db->GetVal("
		            SELECT COUNT(computer_number) 
		            FROM offline_servers w1
		            WHERE DATE(sec_offline::abstime) <= '$end_mark'
		            ");
		        $final_total=$pre_total+$new_servers-$lost_servers;
		        $tot_new_servers+=$new_servers;
		        //print("<TD><A HREF=\"monthly_.php3?month=$month&year=$year\"><IMG SRC=\"assets/images/arrow-sm-r.jpg\"></A>&nbsp;&nbsp;$month/$year</TD>");
		        print("<TD VALIGN=TOP> $month/$year </TD>\n");
		        print("<TD VALIGN=TOP> $new_servers / $ordered_servers ");
		        if ($ordered_servers>0) {
		            print("(" 
		                . number_format((($new_servers/$ordered_servers)*100),2)
		                . "%)");
		        }
		        print("</TD>\n");
		        print "<TD valign=top>";
                print "<TABLE class=datatable>\n";
				print "<TR>\n";
                print "<Th> # </Th>\n";
				print "<Th> Rep </Th>\n";
				print "<Th> Contracts Sent</Th>\n";
				print "<Th> Contracts Closed</Th>\n";
				print "<Th> %Completed </TD>\n";
                print "<Th> Graph </TD>\n";
				print "</TR>\n";
		        $sales_stats=array();
		        $ordered_sales_stats=array();
		        $num=$new_servers_list->numRows();
		        for ($i=0;$i<$num;$i++) {
		            $row = $new_servers_list->fetchArray($i);
		            $rep_number = $row['rep_number'];
		            
		            if ($rep_number == "") {
		                    $rep_number = 0;
		            }
		            if (!isset($sales_stats[$rep_number])) {
		                    $sales_stats[$rep_number] = 0;
		            }
		            $sales_stats[$rep_number]=$sales_stats[$rep_number]+1;
		        }
		        $num=$ordered_servers_list->numRows();
		        for ($i=0;$i<$num;$i++) {
		            $row = $ordered_servers_list->fetchArray($i);
		            $rep_number = $row["rep_number"];
		            
		            if ($rep_number=="") {
		                    $rep_number=0;
		            }
		            if (!isset($ordered_sales_stats[$rep_number])) {
		                    $ordered_sales_stats[$rep_number] = 0;
		            }
		            $ordered_sales_stats[$rep_number] = 
		                $ordered_sales_stats[$rep_number] + 1;
		        }
		        $db->FreeResult($ordered_servers_list);
		        if (is_array($sales_stats)) {
		            reset($sales_stats);
		        } else {
		            $sales_stats = array();
		        }
		        $tr=0;
                while (list($key,$val) = each($sales_stats)) {
                    $tr++;
    		        if ($tr%2==0) {
    		            print("<tr VALIGN=TOP class=even>\n");
    		        } else {
    		            print("<tr VALIGN=TOP class=odd>\n");
    		        }
                    print "<td class=counter>". $tr ."</td>";
		            if (!isset($sales_rep[$key])) {
		                    print "<TD VALIGN=TOP> Employee #$key </TD>\n";
		            }
		            else {
		                    print("<TD VALIGN=TOP> $sales_rep[$key] </TD>\n");
		            }
		            //Now figure out the close percentage    
		            if (isset($ordered_sales_stats[$key]) 
		                    &&  $ordered_sales_stats[$key]>0) {
		                $out=$ordered_sales_stats[$key];
		                print("<TD> $out </TD>");
                        echo "<TD>$val</TD>\n";
		                print("<TD> ".number_format((($val/$out)*100),2)."% </TD>");
                        print("<TD valing=middle><img src='/images/339900.gif' height=10 width='".number_format((($val/$out)*100),0)."' border=0></TD>");
		            } else {
                            echo "<TD> &nbsp;</td>\n";
                            echo "<TD>$val</TD>\n";
                            echo "<TD> &nbsp;</td>\n";
                    }
		            print("</TR>\n");
		        }
		        $db->FreeResult($new_servers_list);
		        unset($sales_stats);
		        unset($ordered_sales_stats);
		        print("</TABLE></TD>");
		        print("</TR>");
		        $ctr++;
		    }
		}
		?>
		<TR>
		    <TD BGCOLOR="#CCCCCC" CLASS="label"> Grand Totals: </TD>
		    <TD BGCOLOR="#CCCCCC" CLASS="label"> &nbsp; Sent / Received (%) </TD>
		    <TD BGCOLOR="#CCCCCC" CLASS="label"> &nbsp; Breakdown </TD>
		</TR>
		<TR>
		<?
		$sales_reps = $db->SubmitQuery('
		    SELECT w1.employee_number, "CONT_ContactID" 
		    FROM "xref_employee_number_Contact" w1, 
		        employee_dept w2 
		    WHERE department = \'SALES\' 
		    AND w1.employee_number = w2.employee_number
		    ');
		$num=$sales_reps->numRows();
		$sales_rep["0"]="No Rep";
		for ($i=0;$i<$num;$i++)
		{
		    $contact_id = $sales_reps->getResult($i,"CONT_ContactID");
		    $employee_number= $sales_reps->getResult($i,"employee_number");
		    $cont = new CONT_Contact;
		    $cont->loadID($contact_id);
		    $person = $cont->getPerson();
		    $sales_rep[$employee_number]= $person->getFirstName() 
		        . " " . $person->getLastName();
		}
		$sales_reps->freeResult();
		$tot_new_servers=$db->GetVal("
		    SELECT COUNT(computer_number) 
		    FROM sales_speed w1
		    WHERE sec_contract_received > 0
		    ");
		$tot_ordered_servers=$db->GetVal("
		    SELECT COUNT(computer_number) 
		    FROM sales_speed w1
		    WHERE sec_placed_order > 0
		    ");
		$new_servers_list=$db->SubmitQuery("
		    SELECT w1.customer_number, w1.computer_number, w2.rep_number 
		    FROM sales_speed w1, rep_assignment w2 
		    WHERE sec_contract_received > 0 
		        AND w1.computer_number = w2.computer_number 
		    ");
		$ordered_servers_list=$db->SubmitQuery("
		    SELECT w1.customer_number, w1.computer_number, w2.rep_number 
		    FROM sales_speed w1, rep_assignment w2 
		    WHERE sec_placed_order > 0 
		        AND w1.computer_number = w2.computer_number 
		    ");
		
		print("<TD ALIGN=CENTER VALIGN=TOP> &nbsp; </TD>");
		print("<TD ALIGN=CENTER VALIGN=TOP> $tot_ordered_servers / $tot_new_servers");
		if ($tot_ordered_servers>0) {
		    print(" (" 
		        . number_format((($tot_new_servers/$tot_ordered_servers)*100),2)
		        . "%)");
		}
		print("</TD>");
		print("<TD><TABLE class=datatable>\n");
		print "<TR>\n";
        print "<td bgcolor=#f0f0f0>#</td>";
		print "<Th> Rep </Th>\n";
		print "<Th> Total Sold </Th>\n";
        print "<Th> Graph </Th>\n";
		print "</TR>\n";		
		
		$sales_stats=array();
		$ordered_sales_stats=array();
		$num=$db->NumRows($new_servers_list);
		for ($i=0;$i<$num;$i++) {
		    $row = $new_servers_list->fetchArray($i);
		    $rep_number = $row['rep_number'];
		    
		    if ($rep_number == "") {
		        $rep_number = 0;
		    }
		    if (!isset($sales_stats[$rep_number])) {
		        $sales_stats[$rep_number] = 0;
		    }
		    $sales_stats[$rep_number]=$sales_stats[$rep_number]+1;
		}
		$num=$db->NumRows($ordered_servers_list);
		if (isset($sales_stats)&&is_array($sales_stats)) {
		    reset($sales_stats);
		}
		else {
		    $sales_stats = array();
		}
        $tr=0;
		while (list($key,$val) = each($sales_stats)) {
          $tr++;
          if ($tr%2==0) {
              print("<tr class=even>\n");
          } else {
              print("<tr class=odd>\n");
          }
          print "<td bgcolor=#f0f0f0>". $tr."</td>";       
          if (!isset($sales_rep[$key])) {
              print("<TD> Employee #$key </TD>\n <TD> $val");
          }
          else {
              print("<TD> $sales_rep[$key] </TD>\n <TD> $val ");
          }
          //Now figure out the close percentage    
          if (isset($ordered_sales_stats[$key])  
              && $ordered_sales_stats[$key]>0) {
              $out=$ordered_sales_stats[$key];
              print(" of $out");
              print(" (".number_format((($val/$out)*100),2)."%)");
          }
          print "</TD>\n";
          print "<TD valing=middle><img src='/images/339900.gif' height=10 width='".number_format($val/10,0)."' border=0></TD>\n";
          print "</TR>\n";
		}
		$new_servers_list->freeResult();
		unset($sales_stats);
		print("</TABLE></TD>");
		?>
		</TR>
		</TABLE>
	</TD>
</TR>
</TABLE>
</td>
</tr>
</table>
<SCRIPT language="javascript">
<!-- 
	//Hides wait window when page loads
	ap_showWaitMessage('waitDiv', 0);
//--> 
</SCRIPT>
<?= page_stop() ?>
</HTML>
<? $page_timer->stop();?>
