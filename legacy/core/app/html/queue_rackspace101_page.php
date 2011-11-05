<?php
require_once("CORE_app.php"); 
require_once("menus.php"); 

    // Enable Progress Bar
    include("TimeRegister.php");
    $page_timer = new PageTimeRegister();
    $page_timer->start();
require_once("queue_rackspace101_logic.php");
    $loadtime = number_format((0+$page_timer->average_duration),0);
    set_title('Rackspace 101 Queue ------ (Avg Load: '.$loadtime.' secs)','#003399');
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN">
<HTML id="mainbody">
<HEAD>
<script>
    var avgLoadTime = <?print $loadtime;?>;
</script>
    <TITLE>
        CORE: Rackspace 101 Queue ---- (Avg Load: <?=$loadtime?> secs)
    </TITLE>
     <LINK HREF="/css/core_ui.css" REL="stylesheet">
     <LINK HREF="/css/core2_basic.css" REL="stylesheet">
    <SCRIPT LANGUAGE="JavaScript" SRC="/script/date-picker.js"></SCRIPT>
    <?= menu_headers() ?>
</HEAD>
<?= page_start() ?>
<?include("wait_window_layer.php")?>
<!-- Begin Reports -->
<BR>
<TABLE BORDER="1"
       CELLSPACING="0"
       CELLPADDING="2"> 
<TR>
	<TD>
	<TABLE BORDER="0"
	       CELLSPACING="2"
	       CELLPADDING="2"
	       ALIGN="left">
	<TR>
		<TD BGCOLOR="#003399" 
			CLASS="hd3rev"> Rackspace 101 Queue </TD>
	</TR>
	<TR>
		<TD>
          <?include("form_wrap_begin.php"); ?>
          <form action="<?=$PHP_SELF ?>" name="main_form" method=post>
            <input type='hidden' name='team_limit' value='<?=$team_limit ?>'>
          <table border='0' width=100%>
            <tr>
              <td nowrap>
                Records per Team:
              </td>
              <td>
                <input type='text' size='4'
                          name='count_limit' value='<?=$count_limit ?>'>
                (Entering 0 will unlimit)
              </td>
            </tr>
            <tr>
              <td>
                Start Date:
              </td>
              <td>
                <input type="text" name="date_limit"
                   size="15" value="<?=$date_limit ?>">
          <a href="javascript:show_calendar('main_form.date_limit');"
          onmouseover="window.status='Pick a date';return true;"
          onmouseout="window.status='';return true;"><img 
          src="/images/show-calendar.gif" width="24" height="22"
          border="0" valign="middle"></a>
              </td>
            </tr>
            <tr>
              <td></td>
              <td>
                <input type="submit" value=" Submit ">
                <input type="button" value="View All Dates"
                       onClick="window.document.location='<?=$PHP_SELF ?>?count_limit=<?=$count_limit ?>&team_limit=<?=$team_limit ?>&date_limit='"
                >
                       
              </td>
            </tr>
          </table>
          </form>
          <?include("form_wrap_end.php"); ?>
<SCRIPT LANGUAGE="JavaScript"
        SRC="/script/wait_window.js"> 
</SCRIPT> 
        </TD>
	</TR>
    <tr>
        <td> 
        <table class=datatable>
            <tr>
                <td class=counter> Legend </td>
            </tr>
            <tr class=oddred>
                <td align=center> Over 7 days old </td>   
            </tr> 
            <tr class=evenred>
                <td align=center> Over 7 days old </td>   
            </tr>                    
        </table> </td>
    </tr>
	<TR>
		<TD>
<?php
PrintQueue();

?>
        </TD>
	</TR>
	</TABLE></TD>
</TR>
</TABLE>
<!-- End Reports -->
<SCRIPT language="javascript">
<!-- 
	//Hides wait window when page loads
	ap_showWaitMessage('waitDiv', 0);
//--> 
</SCRIPT>
<?= page_stop() ?>
</HTML>
<? $page_timer->stop();?>
<?php

// Local Variables:
// c-basic-offset: 4
// indent-tabs-mode: nil
// End:
?>
