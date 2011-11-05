<? require_once("CORE_app.php"); 
   require_once("act/ActFactory.php");
    // Enable Progress Bar
    include("TimeRegister.php");
    $page_timer = new PageTimeRegister();
    $page_timer->start(); 
    $loadtime = number_format((0+$page_timer->average_duration),0);
    set_title('Support Team Customer Load ------ (Avg Load: '.$loadtime.' secs)','#003399');
?>
<html id="mainbody">
<head>
<script>
    var avgLoadTime = <?print $loadtime;?>;
</script>
<TITLE>CORE: Support Team Customer Load ---- (Avg Load: <?=$loadtime?> secs)</TITLE> 
     <LINK HREF="/css/core_ui.css" REL="stylesheet">
     <LINK HREF="/css/core2_basic.css" REL="stylesheet">
<?require_once("tools_body.php");?> 
<?include("wait_window_layer.php")?> 
<SCRIPT LANGUAGE="JavaScript"
        SRC="/script/wait_window.js"> 
</SCRIPT>

<table border="0"
       cellspacing="0"
       cellpadding="2"
       class="titlebaroutline">
<tr>
   <td>
	<table width="100%"
	       border="0"
	       cellspacing="0"
	       cellpadding="0"
          bgcolor="#FFFFFF">
    <tr>       
        <td> 
         		<table border="0"
         		       cellspacing="2"
         		       cellpadding="2">
         		<tr>
         			<td bgcolor="#003399" class="hd3rev"> CORE: Support Team Customer Load </td>
         		</tr>
               <tr>
                  <td>
<TABLE class=datatable>
<TR>
	<TH>Team</TH>
	<TH>Accounts</TH>
    <th> % of Accounts</th> 
    <th> Graph % </th>
	<TH>Servers</TH>  
    <th> % of Servers</th>
    <th> Graph % </th>
</TR>
<?
$i_account = ActFactory::getIAccount();
$total = $i_account->getAccountServerTotals($db);

$teams = $i_account->getAccountServerCounts($db);

for ($i=0;$i<$teams->numRows();$i++) {
    if (($i%2) == 0) {
	    $color="class=even";
	} else {
	    $color="class=odd";
    }
    print("<TR $color>
            <Td bgcolor=#e6e6e6>" . $teams->getResult($i,"team_name") . "</Td>\n");
    print('<TD align="right">' . $teams->getCell($i, 'num_accounts') . "</td>
           <td align=right>" . number_format(($teams->getCell($i, 'num_accounts') /
                         $total->getCell(0, 'num_accounts'))*100,2) .
          "%</TD>\n");                                                             
    print("<TD valing=middle><img src='/images/339900.gif' height=10 width='".number_format((($teams->getCell($i, 'num_accounts')/$total->getCell(0, 'num_accounts')*100)),0)."' border=0></TD>");          
    print('<TD align=right>' . $teams->getCell($i, 'num_servers') . "</td>
         <td align=right>" . number_format(($teams->getCell($i, 'num_servers') /
                         $total->getCell(0, 'num_servers'))*100,2) .
          "%</TD>
            <TD valing=middle><img src='/images/339900.gif' height=10 width='".number_format((($teams->getCell($i, 'num_servers')/$total->getCell(0, 'num_servers')*100)),0)."' border=0></TD>                    
          \n</TR>\n");
}
?>
<tr>
    <td colspan=6>&nbsp;</td>
</tr>
<TR bgcolor=#e6e6e6>
	<TH>Total</TH>
	<TD ALIGN="right"><?=$total->getCell(0, 'num_accounts') ?></TD>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
	<TD ALIGN="right"><?=$total->getCell(0, 'num_servers') ?></TD>
    <td>&nbsp;</td> 
    <td>&nbsp;</td>    
</TR>
</TABLE>
</td>
               </tr>
         	</table>
        </td>
    </tr>
    </table></td>
</tr>
</table>
<SCRIPT language="javascript">
<!-- 
	//Hides wait window when page loads
	ap_showWaitMessage('waitDiv', 0);
//--> 
</SCRIPT>
<?=page_stop() ?>
</HTML>
<? $page_timer->stop();?>
