<?php
require_once("CORE_app.php");
require_once("verify_data.phlib");

MakeNotEmpty($customer_number);
MakeNotEmpty($computer_number);
MakeNotEmpty($agg_product_number);
$customer_number = ereg_replace("[^0-9]*","",$customer_number);
$computer_number = ereg_replace("[^0-9]*","",$computer_number);
$agg_product_number = ereg_replace("[^0-9]*","",$agg_product_number);

$customer_number = trim($customer_number);
$computer_number = trim($computer_number);
$agg_product_number = trim($agg_product_number);
if ($customer_number < 1) {
	$customer_number = 0;
}
if ($computer_number < 1 ) {
	$computer_number = 0;
}
if ($agg_product_number < 1) {
	$agg_product_number = 0;
}

# Set optional form values to an empty string to avoid unset var warnings.
$field_names = array('command', 'customer_number', 
    'computer_number', 'agg_product_number', 'error');
foreach($field_names as $name) {
    eval("\$is_empty = empty($$name);");
    if ($is_empty) {
        eval("$$name = '';");
    }
}
	if ($command=="FIND_COMPUTER"||($command=="FIND_AGG_PROD"&&strlen($agg_product_number)<6 && VerifyNumber($agg_product_number)))
	{
		if ($command=="FIND_AGG_PROD"&&strlen($agg_product_number)<6&& VerifyNumber($agg_product_number))
		{
			$computer_number=$agg_product_number;
			$agg_product_number="";
			$command="FIND_COMPUTER";
		}
		if (strlen($computer_number)==6 && VerifyNumber($computer_number))
		{
			$agg_product_number=$computer_number;
			$command="FIND_AGG_PROD";
		}
		else
		{
		
				// don't pass on customer / computer numbers with anything
				// except for digits in them
				if ( !empty($customer_number) && 
							 ereg("[^(0-9)]", $customer_number ) )
				{
					$customer_number = "";
				}
				if ( !empty($computer_number) && 
							 ereg("[^(0-9)]", $computer_number) )
				{
					$computer_number = "";
				}
                $customer_number = GetCustomerNumberByComputerNumber($db, $computer_number);
				if( !empty($customer_number) && $customer_number > 0 ) {
                                        JSForceReload("/ACCT_main_workspace_page.php","content_page=" 
                                                . urlencode("/tools/DAT_display_computer.php3") 
                                                . "&args=" . urlencode("account_number=$customer_number&customer_number=$customer_number&computer_number=$computer_number"),"workspace");

				} else {
					$error="Unable to find any computers that match that computer number";
                }
		}
	}
	if ($command=="FIND_CUSTOMER")
	{
		if ($customer_number > 0 && (TestRealAccount($db,$customer_number)||TestQueueAccount($db,$customer_number)))
		{
                                        JSForceReload("/ACCT_main_workspace_page.php","content_page=" 
                                                . urlencode("/py/account/view.pt") 
                                                . "&args=" . urlencode("account_number=$customer_number&customer_number=$customer_number"),"workspace");

		}
		else
			$error="Unable to find any customers that match that customer number";
	}
	if ($command=="FIND_AGG_PROD")
	{
		if ($db->TestExist("select agg_product_number from customer_agg_products where agg_product_number=$agg_product_number;"))
		{
                    $customer_number = $db->GetVal("SELECT customer_number 
                                                    FROM  customer_agg_products
                                                    WHERE agg_product_number = $agg_product_number;");
                                        JSForceReload("/ACCT_main_workspace_page.php","content_page=" 
                                                . urlencode("/tools/agg_products/agg_prod.php") 
                                                . "&args=" . urlencode("account_number=$customer_number&customer_number=$customer_number&agg_product_number=$agg_product_number"),"workspace");

		}
		else
			$error="Unable to find any aggregate products that match that customer number";
	}
?>

<HTML id="mainbody">
<HEAD>
	<TITLE>CORE: QUICK FIND </TITLE>
<LINK HREF="/css/core_ui.css" REL="stylesheet">
<?require_once("tools_body.php");?>

<?include("form_wrap_begin.php")?>
<TABLE BORDER="1"
       CELLSPACING="0"
       CELLPADDING="0"
       VALIGN="TOP">
<TR>
	<TD><TABLE BORDER="0"
	           CELLSPACING="2"
	           CELLPADDING="2">
		<TR>
			<TD COLSPAN="2"
			    ALIGN="left"
			    BGCOLOR="#003399"
			    CLASS="hd3rev"> Quick Find </TD>
		</TR>
		<TR>
			<TD COLSPAN=2><i> <?print($error);?> </i></TD>
		</TR>
		<TR>
			<FORM ACTION="quick_find.php3" METHOD=POST>
			<INPUT TYPE=HIDDEN NAME="command" VALUE="FIND_CUSTOMER">
			<TD VALIGN=TOP BGCOLOR="#CCCCCC" CLASS="label"> Account #: </TD>
			<TD><INPUT TYPE="text"
			                     NAME="customer_number"
			                     VALUE="<?print($customer_number);?>"
			                     SIZE="10">
			    <INPUT TYPE="image"
			           SRC="/images/button_command_search_off.jpg"
			           ALIGN="absmiddle"
			           BORDER="0"></TD>
			</FORM>
		</TR>
		<TR>
			<FORM ACTION="quick_find.php3" METHOD=POST>
			<INPUT TYPE=HIDDEN NAME="command" VALUE="FIND_COMPUTER">
			<TD VALIGN=TOP BGCOLOR="#CCCCCC" CLASS="label"> Computer #:</TD>
			<TD><INPUT TYPE="text"
			           NAME="computer_number"
			           VALUE="<?print($computer_number);?>"
			           SIZE="10">
			   <INPUT TYPE="image"
			           SRC="/images/button_command_search_off.jpg"
			           ALIGN="absmiddle"
			           BORDER="0"></TD>
			</FORM>
		</TR>
		<TR>
			<FORM ACTION="quick_find.php3" METHOD=POST>
			<INPUT TYPE=HIDDEN NAME="command" VALUE="FIND_AGG_PROD">
			<TD VALIGN=TOP BGCOLOR="#CCCCCC" CLASS="label"> Aggregate Product #: </TD>
			<TD><INPUT TYPE="text"
			           NAME="agg_product_number"
			           VALUE="<?print($agg_product_number);?>"
			           SIZE="10">
			    <INPUT TYPE="image"
			           SRC="/images/button_command_search_off.jpg"
			           ALIGN="absmiddle"
			           BORDER="0"></TD>
			</FORM>
		</TR>
		</TABLE></TD>
	</TR>
</TABLE>

<?include("form_wrap_begin.php")?>
<?$db->CloseConnection();?>
<?= page_stop() ?>
</HTML>
