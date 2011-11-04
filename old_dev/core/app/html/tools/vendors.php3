<?php require_once("CORE_app.php"); ?>
<?set_back_link("vendors.php3");?>
<HTML>
<TITLE>Vendor Profiles</TITLE>
<?require_once("tools_body.php");?>

<?back_link();?>
<TABLE CELLSPACING=0 CELLPADDING=0 VALIGN="TOP" BORDER=0 WIDTH=540>
<TR>
	<TD BGCOLOR="#000000" ALIGN="LEFT" VALIGN="TOP" COLSPAN=6 HEIGHT=17>
	<IMG SRC="assets/images/c-tl.jpg" WIDTH=10 HEIGHT=10 BORDER=0 ALT="" ALIGN="TOP"></TD>
</TR>
<TR>
	<TD BGCOLOR="#000000" ALIGN="LEFT" VALIGN="TOP" COLSPAN=6 HEIGHT=17>
	<FONT COLOR="#FFFFFF" SIZE="+2"><CENTER>Profiles</CENTER></FONT></TD>
</TR>
<TR>
		<TD HEIGHT=30 COLSPAN=2><B>ADD A VENDOR</B>&nbsp;&nbsp;&nbsp;</TD>
		<TD ALIGN=RIGHT><A HREF="add_vendor.php3"><IMG SRC="assets/images/arrow.jpg" WIDTH=25 HEIGHT=25 BORDER=0 ALT="->" ALIGN="ABSMIDDLE"></a></TD>
</TR>
<TR>
	<TD BGCOLOR="#000000" ALIGN="LEFT" VALIGN="TOP" COLSPAN=6 HEIGHT=17>
	<FONT COLOR="#FFFFFF" >&nbsp;</CENTER></FONT></TD>
</TR>
<TR>
	<TD COLSPAN=5 BGCOLOR="#C0C0C0" ALIGN="LEFT" VALIGN="TOP"><FONT COLOR="#000000">Name</FONT></TD>
	<TD ALIGN="CENTER" BGCOLOR="#C0C0C0"><FONT COLOR="#000000"><B>Detail</B></FONT></TD>
</TR>
<?php
			$select_query= "
                SELECT DISTINCT ON (vendor_number) 
                    vendor_number, name 
                FROM vendor_profile  
                ORDER BY vendor_number, name;
                ";
			$db->BeginTransaction();
		$db->SubmitQuery("DECLARE mycursor cursor for ".$select_query);
		if (isset($start) && $start>0)
		{
			$db->SubmitQuery("MOVE forward $start in mycursor;");
		}
		else
		{
			$start=0;
		}
		if (!isset($max_authors))
			$max_authors=20;
		$result=$db->SubmitQuery("FETCH forward $max_authors in mycursor;");
		$db->SubmitQuery("CLOSE mycursor;");

			$db->BeginTransaction();
		
		if (!$result)
		{
			print("Error:Unable to retrieve vendor Information");
		}
		else
		{
			if ($result->numRows()<1)
			{
				print("Sorry, no vendors are registered.");
			}
			else
			{
				$num=$result->numRows();
				$i=0;
				while($i<$num)
				{
					print("<TR>\n<TD COLSPAN=5><FONT FACE=\"\"><B>".$result->getResult($i,"name")."</B></FONT></TD>");
					
					
					print("&nbsp;</TD><TD ALIGN=\"CENTER\"><A HREF=\"display_vendor.php3?vendor_number=".$result->getResult($i,"vendor_number")."\"> <IMG SRC=\"assets/images/arrow.jpg\" WIDTH=25 HEIGHT=25 BORDER=0 ALT=\"\"></A> \n</TR>\n");
					$i++;
				}
					print("<TR><TD COLSPAN=6 BGCOLOR=\"#000000\"><FONT SIZE=\"-3\">&nbsp;</FONT></TD></TR><TR><TD COLSPAN=6 ALIGN=\"CENTER\" VALIGN=\"MIDDLE\">");
				if ($start>0)
				{
					$pre_start=$start - $max_authors;
					print("<B>Previous ".$max_authors."</B><A HREF=\"vendors.php3?start=".$pre_start."&max_authors=".$max_authors."\"><IMG SRC=\"assets/images/arrow-l.jpg\" WIDTH=25 HEIGHT=25 BORDER=0 ALT=\"\"></A>&nbsp;&nbsp;");
				}

				$start=$start+$max_authors;
				
				if ($num==$max_authors)
				{
					print("&nbsp;&nbsp;<A HREF=\"vendors.php3?start=".$start."&max_authors=".$max_authors."\"><IMG SRC=\"assets/images/arrow.jpg\" WIDTH=25 HEIGHT=25 BORDER=0 ALT=\"\"></A><B>Next ".$max_authors."</B></TD>");
				}
				print("</TD></TR>\n");

			}
		}
        $result->freeResult();
		$db->CloseConnection();
?>
<TR>
	<TD BGCOLOR="#000000" ALIGN="LEFT" VALIGN="BOTTOM" COLSPAN=6 HEIGHT=17><IMG SRC="assets/images/c-bl.jpg" WIDTH=10 HEIGHT=10 BORDER=0 ALT="" ALIGN="BOTTOM"></TD>
</TR>
</TABLE><BR CLEAR="ALL"><BR>
<?back_link();?>
</BODY>
</HTML>
