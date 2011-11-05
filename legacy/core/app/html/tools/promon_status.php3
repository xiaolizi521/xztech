<?
	$admin_userid="system";
	$password="KLNsd823";
	require_once("CORE_app.php");
	if ($customer_number != "" 
       && $computer_number != "" 
       &&$customer_number > 0 
       && $computer_number > 0) {?>

<HTML>
<TITLE>CORE: Migration List</TITLE>
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
          BGCOLOR="#FFFFFF">
    <TR>       
        <TD> 
         		<TABLE BORDER="0"
         		       CELLSPACING="2"
         		       CELLPADDING="2">
         		<TR>
         			<TD BGCOLOR="#003399" CLASS="hd3rev"> Last Log Entry -
                  #<?print($customer_number);?>-<?print($computer_number);?> </TD>
         		</TR>
               <TR>
                  <TD> <?=print_last_entry($conn,"computer_log","comments","computer_number=$computer_number and customer_number=$customer_number");?> </TD>
               </TR>
         		</TABLE>
        </TD>
    </TR>
    </TABLE></TD>
</TR>
</TABLE>
</BODY>
</HTML>
<? } ?>

