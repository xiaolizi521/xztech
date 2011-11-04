<?PHP

require_once("CORE_app.php");
require_once("helpers.php");
require_once("act/ActFactory.php");

checkDataOrExit( array( 'external_contact_primary_id' => "External Contact Primary ID" ) );
checkDataOrExit( array( 'individual_id' => "Individual ID" ) );

$i_account = ActFactory::getIAccount();
$i_contact = ActFactory::getIContact();
$contact = $i_contact->getExternalContact( $GLOBAL_db, $external_contact_primary_id);
$email_type_options = "";
$selected_flag = "";
$emailTypeLookups = $i_account->getLookupValues('individual.user7');
foreach( $contact->individual->emailAddresses as $email ) {
    for($i=0; $i<count($emailTypeLookups); $i++) {
        if ( (int)$email->email_type_id == (int)$emailTypeLookups[$i]->parameter_id ) {
            array_splice( $emailTypeLookups, $i, 1 );
            break;
        }
    }
}
if ( count($emailTypeLookups) == 0 ) {
    print "<h2> Can't Add Any More Emails</h2>";
    exit;
}
for($i=0; $i<count($emailTypeLookups); $i++) {
        $email_type_options .= '<OPTION value="'.$emailTypeLookups[$i]->parameter_id.'"'.$selected_flag.'>'.$emailTypeLookups[$i]->desc.'</OPTION>';
}

makevar( 'first_name', $SESSION_first_name );
makevar( 'last_name', $SESSION_last_name );
makevar( 'email', '' );
        
$hidden_tags  = '<input type="hidden" name="external_contact_primary_id"' .
                ' value="'. $external_contact_primary_id . '">';
$hidden_tags .= '<input type="hidden" name="individual_id"' .
                ' value="'. $individual_id . '">';                
                
?>
<HTML>
<HEAD>
    <TITLE>
        CORE: Add Email Address
    </TITLE>
	<LINK HREF="/css/core2_basic.css" REL="stylesheet">
</HEAD>
<BODY MARGINWIDTH="0" 
      MARGINHEIGHT="0" 
	  LEFTMARGIN=0 
	  TOPMARGIN=0 
	  BGCOLOR="#FFFFFF">
	<!-- Begin Add Email ------------------------------------------------------ -->
	<TABLE class="blueman">
	<TR>
		<th class="blueman"> Add Email </th>
	</TR>
	<TR>
		<TD>
            <FORM ACTION="/EXT_CONT_add_email_handler.php">
            <?=$hidden_tags ?>
			<TABLE class="datatable">
			<TR>
				<th> Email Address: </th>
                <TD>
                   <INPUT TYPE="text" name="email_address"
				          CLASS="data"
				          SIZE="24"></TD>
			</TR>
			<TR>
				<TD ALIGN="right">
				Type:</TD><TD>
				<SELECT NAME="email_type_value">
                                <?=  $email_type_options ?>
				</SELECT>
				</TD>
			</TR>
			<TR>
				<TD ALIGN="right" COLSPAN=2> <INPUT TYPE="image" 
				                          NAME="CONTINUE" 
										  SRC="/images/button_command_save_off.jpg" 
										  HSPACE="2" 
										  VSPACE="2" 
										  BORDER="0"> </TD>
			</TR>
			</TABLE>
			</FORM>
		</TD>
	</TR>
	</TABLE>
</BODY>
</HTML>
