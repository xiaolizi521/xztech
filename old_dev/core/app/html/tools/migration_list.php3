<? require_once("CORE_app.php"); 
    // Enable Progress Bar
    include("TimeRegister.php");
    $page_timer = new PageTimeRegister();
    $page_timer->start(); 
    $loadtime = number_format((0+$page_timer->average_duration),0);
    set_title('Migration List ------ (Avg Load: '.$loadtime.' secs)','#003399');
?>
<html id="mainbody">
<head>
<script>
    var avgLoadTime = <?print $loadtime;?>;
</script>
<TITLE>CORE: Migration List ---- (Avg Load: <?=$loadtime?> secs)</TITLE> 
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
          BGCOLOR="FFFFFF">
    <TR>       
        <TD> 
         		<TABLE BORDER="0"
         		       CELLSPACING="2"
         		       CELLPADDING="2">
         		<TR>
         			<TD BGCOLOR="#003399" CLASS="hd3rev"> Migration List </TD>
         		</TR>
               <TR>
                  <TD> 
                  <TABLE class=datatable>
                  <TR>
                    <td class=counter> &nbsp; </td>
                  	<Th> Action </th>
                  	<Th> Status </Th>
                  	<Th> &nbsp; </Th>
                  </TR>
                  <TR>
                  	
                  <?
                  	
                  	# All servers that have gone online except suspended servers
                  	# which are now in its own list.
                  	$computers=$db->SubmitQuery("
                          SELECT DISTINCT ON (computer_number) 
                              customer_number,computer_number,
                  			status_number,sec_created 
                          FROM server_status_all 
                  		WHERE status_number=15 
                  		ORDER BY computer_number, status_number, sec_created DESC ;
                          ");
                  
                  	if($computers->NumRows() > 0)
                  	{
                  		DisplayProcessComputerList($computers);
                  		$computers->FreeResult();
                  
                  	}
                  	else	
                  	{
                  		print("<TD COLSPAN=\"3\" ALIGN=\"CENTER\"><BR><B><FONT COLOR=\"#FF0000\">There are no servers in process at this time.</FONT>.</B><BR><BR></TD>");
                  	}
                  	
                  ?>
                  </TR>
                  </TABLE>                  
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
