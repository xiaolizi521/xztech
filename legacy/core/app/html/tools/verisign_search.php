<?
    require_once("CORE_app.php"); 
    // Enable Progress Bar
    include("TimeRegister.php");
    $page_timer = new PageTimeRegister();
    $page_timer->start(); 
    $loadtime = number_format((0+$page_timer->average_duration),0);
    set_title('Verisign Search ------ (Avg Load: '.$loadtime.' secs)','#003399');
?>
<html id="mainbody">
<head>
<script>
    var avgLoadTime = <?print $loadtime;?>;
</script>
<TITLE>CORE: Verisign Search ---- (Avg Load: <?=$loadtime?> secs)</TITLE> 
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
         			<td bgcolor="#003399" class="hd3rev"> Verisign Search </td>
         		</tr>
               <tr>
                  <td>
<TABLE class=datatable>
<?include("form_wrap_begin.php")?>
<FORM ACTION="verisign_search.php" METHOD="POST">
<INPUT TYPE=HIDDEN NAME="command" VALUE="SEARCH">
<?
	if(!isset($domain_name))
		$domain_name="";

    // do some basic sanity checks to make sure
    // we've got a legal regex.
    $domain_name_is_valid = true;
    if (ereg("[[:space:]]", $domain_name) )
    {
        $domain_name_is_valid = false;
    }
    if (ereg("[\&\@\!\?\$\#\%\^\(\)\+\/\\,\_]", $domain_name) )
    {
        $domain_name_is_valid = false;
    }

    if ( !$domain_name_is_valid ) {
        print("<b>Invalid domain name ($domain_name).");
    }

?>
<TR>
    <TH nowrap> Domain Name Contains:</TH>
    <TD><INPUT TYPE=TEXT SIZE=30 NAME="domain_name" VALUE="<?print($domain_name);?>"></TD>
    <Td><input type="image"
               src="/images/button_command_search_off.jpg"
               border="0"></Td>
</TR>
</TABLE>
</FORM>
<?include("form_wrap_end.php");?>

<?if (isset($command) &&
        $command=="SEARCH" &&
        $domain_name_is_valid):?>
<TABLE class=datatable>
<tr>
    <td colspan=4>  </td>
</tr>
<TR>   
    <td class=counter> &nbsp; </td>
	<Th> Aggregate Product #</Th>
	<th> Domain Name </th>
	<th> Details </Th>
</TR>
<?
	//looking for a variety of customer numbers and computer numbers 
	$agg_products=$db->SubmitQuery("
        SELECT DISTINCT ON (agg_product_number) 
            agg_product_number, value as domain_name 
        FROM agg_product_configuration 
        WHERE label = 'domain_name' 
            AND value ~* '$domain_name';
        ");
	$num=$agg_products->numRows();
    for ($i=0;$i<$num;$i++)	{
        $ctr = $i+1;
        if ($i%2!=0)
            $bgcolor="class=odd";
        else
	        $bgcolor="class=even";
	    //Must be a site submission
        print "<TR $bgcolor>";
        print "<td class=counter> $ctr </td>";
	    print("<TD>".$agg_products->getResult($i,"agg_product_number")."</TD><TD>".$agg_products->getResult($i,"domain_name")."</td>");
	    print("<TD ALIGN=\"CENTER\"><A HREF=\"agg_products/agg_prod.php?agg_product_number=".$agg_products->getResult($i,"agg_product_number")."\"> <IMG SRC=\"/images/button_arrow_right_off.jpg\" WIDTH=20 HEIGHT=20 BORDER=0 ALT=\"View\"></A> \n</TR>\n");
	}
	$agg_products->freeResult();
?>
</TABLE>
<?endif;?>
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
<?= page_stop() ?>
</HTML>
<? $page_timer->stop();?>
