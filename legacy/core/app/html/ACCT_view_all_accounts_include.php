<?PHP
require_once("ACCT_view_all_accounts_include_logic.php");

?>

<TABLE BORDER="1"
       CELLSPACING="0"
       CELLPADDING="0">
<TR>
	<TD>	   
		<TABLE BORDER="0"
		       CELLSPACING="2"
		       CELLPADDING="2">
	    <TR>
	        <TD BGCOLOR="#003399"
	            CLASS="hd3rev"
                COLSPAN="8"> Associated Accounts: <?=$name ?>
            </TD>
	    </TR>
	    	<?php print($account_rows); ?>
	</TABLE></TD>
</TR>
</TABLE>
<p style="float: left">
    <?  if ($offset != 0) { ?>
        <a href="ACCT_view_associated_accounts_page.php?contact_id=<?= $contact_id ?>&offset=<?= $offset - $page_size ?>">Previous</a>
    <? } ?>
</p>
<p style="float: right">
    <?  if (sizeof($accounts) == $page_size) { ?>
        <a href="ACCT_view_associated_accounts_page.php?contact_id=<?= $contact_id ?>&offset=<?= $offset + $page_size ?>">Next</a>
    <? } ?>
</p>
<p style="text-align: center">
    Page #<?= ($offset / $page_size) + 1 ?>
</p>
