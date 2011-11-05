<?php

require_once("ACCT_view_all_contacts_include_logic.php");

/* Having all this code here goes against rules we have seperating the
 * templates and code, but if this is included multiple times, it must
 * be in here to be re-run each time, while the functions should only 
 * be loaded once.
 */

if( in_dept("CORE") ) {
    $show_cid = true;
} else {
    $show_cid = false;
}

if( empty( $account_number ) ) {
        @$account_number += 0;
        print '<html><head><title>Enter Contact Number</title></head><body>';
        print "\n";
        print '<form action="' . $GLOBALS['REQUEST_URI'] . '">';
        print 'Please enter an account number<input name="account_number"';
        print 'value="' . $account_number . '">';
        print "<br>\n";
        print "<input type='submit' name='Submit'>\n";
        print "</form>\n";
        print '</body></html>';
        print "\n";
        exit;
}

$account_id = $onyx_account->account_id;

?>

<!-- Begin Rackspace Contacts ------------------------ -->
<TABLE BORDER="1"
       CELLSPACING="0"
       CELLPADDING="0"
       ALIGN="left">
<TR>
	<TD>
		<TABLE BORDER="0"
		       CELLSPACING="2"
		       CELLPADDING="2"
		       ALIGN="left">
		    <TR>
		        <TD BGCOLOR="#003399"
		            CLASS="hd3rev">Rackspace Contacts 
					&nbsp; &nbsp;
					<A HREF="javascript:makePopUpWin('/ACCT_add_rackspace_contact/step1_page.php?account_id=<?=$account_id ?>',500,700,'',3)">
					<IMG SRC="/images/button_table_add.gif" 
					 						   WIDTH="35" 
											   HEIGHT="16" 
											   BORDER="0"
											   ALIGN="texttop"
											   ALT="Add a contact"></A>
			    </TD>
		    </TR>
		    <TR>
		        <TD>
                  <table border="0"
                         cellspacing="0"
                         cellpadding="2">
                    <tr>
                      <th> Role </th>
                      <th WIDTH="100%"> Name </th>
                      <th> &nbsp; </th>
                      <th> &nbsp; </th>
                      <th> &nbsp; </th>
                    </tr>
                      <?php PrintRackers(); ?>
                  </table>
		        </TD>
		    </TR>
		</TABLE></TD>
</TR>
</TABLE>
<!-- End Rackspace Contacts ------------------------ -->

