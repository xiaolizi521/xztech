<?php require_once("CORE_app.php"); ?>
<?
    if (isset($show_days)) {
            setcookie("real_show_days", $show_days, time()+(24*3600),"/");
    }
    else if (isset($real_show_days)) {
        $show_days = $real_show_days;
    }
    else {
        $show_days = 0;
    }
?>
<HTML id="mainbody">
<HEAD>
   <TITLE>CORE: Inactive Computers</TITLE>
<META NAME="ROBOTS" CONTENT="None">
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
         			<TD BGCOLOR="#003399" CLASS="hd3rev"> Inactive Computers </TD>
         		</TR>
               <TR>
                  <TD> 
<!-- Begin Outlined Table Content ------------------------------------------ -->
<TABLE BORDER="0"
       CELLSPACING="1"
       CELLPADDING="2"
       VALIGN="TOP">
<?
		$select_query="
            SELECT DISTINCT customer_number
            FROM offline_servers
            ORDER BY customer_number DESC
            ";
			$db->BeginTransaction();
		$db->SubmitQuery("DECLARE mycursor cursor for ".$select_query);
		if (isset($start) && $start > 0)
		{
			$db->SubmitQuery("MOVE forward $start in mycursor;");
		}
		else
		{
			$start=0;
		}
		if (!isset($max_customers)) {
			$max_customers=15;
        }
		$results=$db->SubmitQuery("FETCH forward $max_customers in mycursor;");
		$db->SubmitQuery("CLOSE mycursor;");

		$db->CommitTransaction();

		if ($results&&$db->NumRows($results))
		{
				if ($start>0)
				{
					$pre_start = $start - $max_customers;
					$prev_link = "<TR>";
               $prev_link .= "<TD>";
               $prev_link .= "<A HREF=\"no_longer.php3?start=".$pre_start."&max_customers=".$max_customers."\">";
               $prev_link .= "<IMG SRC=\"/images/button_arrow_left_off.jpg\" WIDTH=20 HEIGHT=20 BORDER=0 ALT=\"Previous\"></A></TD>";
	            $prev_link .= "<TD><A HREF=\"no_longer.php3?start=".$pre_start."&max_customers=".$max_customers."\">";               
               $prev_link .= "Previous $max_customers </A></TD> ";
   			   $prev_link .= "</TR>";
            }

				$start = $start + $max_customers;
				
				if ($db->NumRows($results)==$max_customers)
				{
					$next_link = "<TR>";
               $next_link .= "<TD>";
               $next_link .= "<A HREF=\"no_longer.php3?start=".$start."&max_customers=".$max_customers."\">";
               $next_link .= "<IMG SRC=\"/images/button_arrow_off.jpg\" WIDTH=20 HEIGHT=20 BORDER=0 ALT=\"Next\">";
               $next_link .= "</A></TD>";
               $next_link .= "<TD>";
               $next_link .= "<A HREF=\"no_longer.php3?start=".$start."&max_customers=".$max_customers."\">";               
               $next_link .= "Next ".$max_customers."</TD></TR>";
               }
         if (!empty($prev_link)) {
             print $prev_link;
         }
         print $next_link;
         print "<TR>
	               <TH COLSPAN=2> Name </TH>
	               <TH> Customer Rep </TH>
	               <TH> Computers </TH>
                 </TR>";
			print("<TR><TD COLSPAN=6 ALIGN=\"CENTER\" VALIGN=\"MIDDLE\">");
			DisplayCustomerList($results);
			print("</TD></TR>\n");
         print("<TR><TH COLSPAN=6 ALIGN=\"CENTER\" VALIGN=\"MIDDLE\"> &nbsp; </TH></TR>");
         if (!empty($prev_link)) {
             print $prev_link;
         }
         print $next_link;
		}
	$db->FreeResult($results);
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
<?= page_stop() ?>
</HTML>
