<?PHP
require_once("CORE_app.php");
require_once("helpers.php");
require_once("act/ActFactory.php");
require_once("act/ACFR_Phone.php");
require_once("tools/phone_input_helper.php");

checkDataOrExit( array( 'external_contact_primary_id' => "External Contact Primary ID" ) );
checkDataOrExit( array( 'individual_id' => "Individual ID" ) );

$i_account = ActFactory::getIAccount();
$i_contact = ActFactory::getIContact();
$external_contact = $i_contact->getExternalContact($GLOBAL_db, $external_contact_primary_id);

$primary_addr = $external_contact->individual->getPrimaryAddress();
$country_code = $primary_addr->countryCode;
makevar( 'first_name', $SESSION_first_name);
makevar( 'last_name', $SESSION_last_name);
makevar( 'number', '' );

$type_options = "";
$selected_flag = "";
$phoneTypeLookups = $i_account->getLookupValues('individual.phonetype');

for($i=0; $i<count($phoneTypeLookups); $i++) {
        $valid_type = true;
        foreach($external_contact->individual->phones as $part) {
            if ($phoneTypeLookups[$i]->desc == $part->phone_type) {
                $valid_type = false;
            }
        }
        if ($valid_type) {
            $type_options .= '<OPTION value="'.$phoneTypeLookups[$i]->parameter_id.'"'.$selected_flag.'>'.$phoneTypeLookups[$i]->desc.'</OPTION>';   
        }
}

$phone_mask = ACFR_Phone::getPhoneMask($country_code);
$phone_mask = str_replace("/x", "x", $phone_mask);
$phone_mask = str_replace("9", "#", $phone_mask);
$phone_mask = str_replace("/9", "9", $phone_mask);

$hidden_tags  = '<input type="hidden" name="contact_id"' .
                ' value="'. $external_contact_primary_id . '">';
# To do:  Have a widget that allows ContactPhoneTypes to be toggled
# on and off.

?>
<HTML>
<HEAD>
    <TITLE>
        CORE: Add Phone Number
    </TITLE>
	<LINK HREF="/css/core2_basic.css" REL="stylesheet">
    <? print(getJumpingJavascript()); ?>
</HEAD>
<BODY MARGINWIDTH="0" MARGINHEIGHT="0" LEFTMARGIN=0 TOPMARGIN=0 BGCOLOR="#FFFFFF">
	<TABLE class="blueman">
	<TR>
		<th class="blueman"> Add Phone Number</th>
	</TR>
	<TR>
		<TD>
            <FORM name="frmNumber" ACTION="EXT_CONT_add_phone_handler.php">
            <?=$hidden_tags ?>  
			<TABLE class="datatable">
			<TR>
				<th> Phone: </th>
                <TD> <? $markup = getPhoneInputMarkup("phone_number", "data", $phone_mask, "");
                        print($markup); ?> 
                </TD>
			</TR>
			<TR>
				<th> Country: </th>
                <TD><?= $country_code ?>
				</TD>
			</TR>
			<TR>
				<th> Type: </th>
                <TD>
                                    <select name="type_name"
				            CLASS="data">
                                      <?=$type_options ?>
                                    </select>
				</TD>
			</TR>

			<TR>
				<TD ALIGN="right" COLSPAN=2> <INPUT TYPE="image" NAME="CONTINUE" SRC="/images/button_command_save_off.jpg" HSPACE="2" VSPACE="2" BORDER="0"> </TD>
			</TR>
			</TABLE>

			</FORM>
		</TD>
	</TR>
	</TABLE>
<!-- End Add Email -------------------------------------------------------- -->
</BODY>
</HTML>
