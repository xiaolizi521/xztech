<? require_once("CORE_app.php"); 
    // Enable Progress Bar
    include("TimeRegister.php");
    $page_timer = new PageTimeRegister();
    $page_timer->start(); 
    $loadtime = number_format((0+$page_timer->average_duration),0);
    set_title('Daily Submission Stats ------ (Avg Load: '.$loadtime.' secs)','#003399');
?> 
<html id="mainbody">
<head>
<script>
    var avgLoadTime = <?print $loadtime;?>;
</script>
<TITLE>CORE: Daily Submission Stats ---- (Avg Load: <?=$loadtime?> secs)</TITLE> 
     <LINK HREF="/css/core_ui.css" REL="stylesheet">
     <LINK HREF="/css/core2_basic.css" REL="stylesheet">
<?require_once("tools_body.php");?> 
<?include("wait_window_layer.php")?> 
<SCRIPT LANGUAGE="JavaScript"
        SRC="/script/wait_window.js"> 
</SCRIPT>
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
          BGCOLOR="#FFFFFF">
    <TR>       
        <TD> 
         		<TABLE BORDER="0"
         		       CELLSPACING="2"
         		       CELLPADDING="2">
         		<TR>
         			<TD BGCOLOR="#003399" CLASS="hd3rev"> Daily Submission Stats </TD>
         		</TR>
               <TR>
                  <TD> 

<!-- Begin Outlined Table Content ------------------------------------------ -->

<TABLE class=datatable>
<?    
    print "
        <TR>
            <TH> Date </TH>
            <TH> # of Submissions </TH>
        </TR>
        ";
    $query = "
        SELECT DATE(t1.sec_created::abstime)
        INTO TEMP TABLE temp_sales_speed
        FROM sales_speed t1,
            server t2
        WHERE t1.computer_number = t2.computer_number
        ";
    $db->SubmitQuery($query);
    $submissions = $db->SubmitQuery("
        SELECT COUNT(date), date
        FROM temp_sales_speed 
        GROUP BY date
        ORDER BY date DESC
        ");

	$stamp=time();
	$curr_month=date("m",$stamp);
	$curr_year=date("Y",$stamp);
	$ctr=0;
    $row_num = 0;
	for ($year=$curr_year;$year>1998;$year--)
	#for ($year=$curr_year;$year>2000;$year--)
	{
		if ($year==$curr_year) {
            $start=$curr_month;
        }
		else {
            $start=12;
        }
		for ($month = $start; $month > 0; $month--) {
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
            $month_submission_count = 0;
			for ($day=$last_day;$day>0;$day--) {
                if ($ctr%2==0) {
                    print("<TR class=even>");
                }
                else {
                    print("<TR class=odd>");
                }

                $submission_count = 0;
                $current_ymd = sprintf("%04d%02d%02d", $year, $month, $day);
                while ($row_num < $submissions->numRows()) {
                    $row = $submissions->fetchArray($row_num);
                    $row_ymd = substr($row['date'], 0, 4)
                        . substr($row['date'], 5, 2)
                        . substr($row['date'], 8, 2);
                    if ($row_ymd == $current_ymd) {
                        $submission_count = $row['count'];
                        break;
                    }
                    else if ($row_ymd < $current_ymd) {
                        break;
                    }
                    $row_num++;
                }


                print("\t<td> $month/$day </td>");
                print("\t<td align=right> $submission_count </td>");
                print("</TR>\n");
                $month_submission_count += $submission_count;
                $ctr++;
            }

            print("<TR>\n\t<TH class=subhead1> $month/$year Total </TD>\n");
            print("\t<Td bgcolor=#99ccff align=right>$month_submission_count</td>");
            $ctr++;
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
<?=page_stop()?>
</HTML>
<? $page_timer->stop();?>
