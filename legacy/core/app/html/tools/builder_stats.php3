<? require_once("CORE_app.php"); 
    // Enable Progress Bar
    include("TimeRegister.php");
    $page_timer = new PageTimeRegister();
    $page_timer->start();
    $loadtime = number_format((0+$page_timer->average_duration),0);
    set_title('Builder Stats ------ (Avg Load: '.$loadtime.' secs)','#003399');
?> 
<HTML id="mainbody">
<HEAD>
<script>
    var avgLoadTime = <?print $loadtime;?>;
</script>
<TITLE>CORE: Builder Stats ---- (Avg Load: <?=$loadtime?> secs)</TITLE>
     <LINK HREF="/css/core_ui.css" REL="stylesheet">
     <LINK HREF="/css/core2_basic.css" REL="stylesheet">
<?require_once("tools_body.php");?>
<?include("wait_window_layer.php")?>
<table border="0"
       cellspacing="0"
       cellpadding="0"
       align="left">
<tr bgcolor="#FFCC33">
    <td valign="top" width=10><img src="/images/note_corner.gif"
                          width="10"
                          height="10"
                          hspace="0"
                          vspace="0"
                          border="0"
                          valign="TOP"
                          alt=""></td>
     <td> NOTES: </td>
</tr>
<tr bgcolor="#FFF999">
    <td colspan=2> Below is the summary of how many servers each tech has built
    during the month. </td>
</tr>
</table>
<br clear="all">
<br>
<SCRIPT LANGUAGE="JavaScript"
        SRC="/script/wait_window.js"> 
</SCRIPT>
<TABLE BORDER="0"
       CELLSPACING="0"
       CELLPADDING="2"
       CLASS="titlebaroutline"
       WIDTH="80%">
<TR>
   <TD>
        <TABLE BORDER="0"
            CELLSPACING="2"
            CELLPADDING="2"
            WIDTH="100%"
            BGCOLOR="white">
        <TR>
            <TD BGCOLOR="#003399" CLASS="hd3rev"> Builder Stats </TD>
        </TR>
    <TR>
        <TD> 
    <?
    $tot_new_servers=0;
    $stamp=time();
    $curr_month=strftime("%m",$stamp);
    $curr_year=strftime("%Y",$stamp);

    if (!empty($year_month)) {
        $year = substr($year_month, 0, 4);
        $month = substr($year_month, 5, 7);
    }
    else {
        $month = $curr_month;
        $year = $curr_year;
    }

    if (checkdate($month,31,$year))
            $last_day=31;
    else if (checkdate($month,30,$year))
            $last_day=30;
    else if (checkdate($month,29,$year))
            $last_day=29;
    else if (checkdate($month,28,$year))
            $last_day=28;
    $begin_mark=mktime(0,0,0,$month,1,$year)-1; //Puts it at 23:59
    $begin_mark=strftime("%m/%d/%Y",$begin_mark);
    $end_mark="$month/$last_day/$year";

    $build_stats=array();
    $new_servers=$db->GetVal("
        select count(computer_number) 
        from sales_speed 
        where date(sec_finished_order::abstime)>'".$begin_mark."' 
            and date(sec_finished_order::abstime)<='$end_mark';");
    $new_servers_list=$db->SubmitQuery("
        select customer_number,
            computer_number 
        from sales_speed 
        where date(sec_finished_order::abstime)>'".$begin_mark."' 
            and date(sec_finished_order::abstime)<='$end_mark';");
    ?>

    <form>
    <select name='year_month'>
        <option value="">--select--</option>
        <?
        for ($y = $curr_year; $y > 1999;$y--)
        {
            if ($y == $curr_year) {
                $start = $curr_month;
            }
            else {
                $start=12;
            }

            for ($m = $start; $m > 0; $m--)
            {
                print " <option>$y/$m</option>\n";
            }
        }
        ?>
    </select>
    <input type=submit value="Change Month">


<TABLE BORDER="0"
       CELLSPACING="1"
       CELLPADDING="2"
       VALIGN="TOP">
<TR>
    <TH WIDTH="1%"> Date </TH>
    <TD><b><?= "$month/$year" ?></b></TD>
</TR>
<TR>
    <TH WIDTH="1%" style="white-space: pre"> # New Servers </TH>
    <TD> <?= $new_servers ?> </TD>
</TR>
<TR>
    <TD COLSPAN="2">
    <TABLE CELLPADDING=2 CELLSPACING=1 class=datatable>
    <tr>
    <th style="width: 1%">#</th>
    <th style="width: 1%"> Tech </th>
    <th style="width: 1%"> Number </th>
    <th style="width: 1%"> % of Servers </th>
    <th> Graph(%) </th>
    </tr>
<?
    $num=$db->NumRows($new_servers_list);
    $build_stats["Unknown"] = 0;

for ($i=0;$i<$num;$i++) {
    $customer_number=$db->GetResult($new_servers_list,$i,"customer_number");
    $computer_number=$db->GetResult($new_servers_list,$i,"computer_number");

    $builders=$db->SubmitQuery("
        SELECT userid 
        FROM build_tech 
        WHERE customer_number=$customer_number 
            AND computer_number=$computer_number
        ");
    $numbuild=$db->NumRows($builders);
    for ($j=0;$j<$numbuild;$j++)
    {
        $builder=$db->GetResult($builders,$j,"userid");
        if(!isset($build_stats[$builder])) {
            $build_stats[$builder] = 0.0;
        }
        $build_stats[$builder] = $build_stats[$builder] + 1;
    }
    $db->FreeResult($builders);
}

if (is_array($build_stats)) {
    reset($build_stats);        
    $row_ctr=0;
    ksort($build_stats);
    foreach($build_stats as $key => $val) {
        if (($row_ctr%2)==0) {
            $bgcolor="class=even";
        }
        else {
        $bgcolor="class=odd";
        }

        $percentage = $val * 100 / $num=$db->NumRows($new_servers_list);

        print "<TR $bgcolor>\n";
        print "<td class=counter> ".($row_ctr +1)." </td>";
        print "\t<TD> $key </TD>\n";
        print "\t<TD> ".number_format($val,2)." </TD>\n";
        print "\t<TD>" . number_format($percentage, 1) . "%</TD>\n";
        print "\t<TD valing=middle>
            <div style='border: 1px solid black'>
            <img src='/images/339900.gif' 
            height=10 width='$percentage%' border=0>
            </div></TD>\n";            
        print "</TR>\n";
        $row_ctr++;
    }
}

$db->FreeResult($new_servers_list);

?>
            </TABLE></TD></TR>
        </TABLE></TD></TR>
    </TABLE></TD></TR>
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
