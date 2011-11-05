<?php

require_once("CORE_app.php");
require_once("helpers.php");
require_once("act/ActFactory.php");
require_once("tools/phone_input_helper.php");

checkDataOrExit( array( 'external_contact_primary_id' => "Contact ID" ) );
checkDataOrExit( array( 'individual_id' => "Individual ID" ) );
checkDataOrExit( array( 'phone_type' => "Phone Type" ) );
checkDataOrExit( array( 'phone_number' => "Phone Number" ) );

$contact = ActFactory::getIContact();
$contact = $contact->getExternalContact( $GLOBAL_db, $external_contact_primary_id);
if ( !isset( $orig_phone_type ) )  {
    $orig_phone_type = $phone_type;
}
$hidden_tags  = '<input type="hidden" name="external_contact_primary_id"' .
                ' value="'. $external_contact_primary_id . '">';
$hidden_tags  .= '<input type="hidden" name="individual_id"' .
                ' value="'. $individual_id . '">';
$hidden_tags  .= '<input type="hidden" name="phone_type"' .
                ' value="'. $phone_type. '">';
$hidden_tags  .= '<input type="hidden" name="phone_number"' .
                ' value="'. $phone_number. '">';
$hidden_tags  .= '<input type="hidden" name="orig_phone_type"' .
                ' value="'. $orig_phone_type. '">';
$hidden_tags .= '<input type="hidden" name="submitted" value="yes">';
if ( !isset( $country ) ) {
    $country = '';
}
# To do:  Have a widget that allows ContactPhoneTypes to be toggled
# on and off.
$iAccount = ActFactory::getIAccount();
$phone_types = $iAccount->getLookupValues('individual.phonetype');

$phone_mask = ACFR_Phone::getPhoneMask($country);
$phone_mask = str_replace("/x", "x", $phone_mask);
$phone_mask = str_replace("9", "#", $phone_mask);
$phone_mask = str_replace("/9", "9", $phone_mask);

if ( !empty( $new_phone_number ) ) {
    $concatenated_phone_number = "";
    while(list($key,$val) = @each($new_phone_number)) {
        $concatenated_phone_number .= $val;
    }
    
 // edit phone in onyx here
    $primary_flag = '';
    $contact = ActFactory::getIContact();
    $result = $contact->updateExternalContactPhone( $external_contact_primary_id,
                         $individual_id, $concatenated_phone_number, $country, $phone_type, $primary_flag, $orig_phone_type );    
    print '<script language="javascript">window.opener.document.location = window.opener.document.location; window.close();</script>';  
    exit();
}
?>
<HTML>
<HEAD>
    <TITLE>
        CORE: Editing Phone Number for
        <?=$SESSION_first_name?> <?=$SESSION_last_name?>
    </TITLE>
	<LINK HREF="/css/core_popup.css" REL="stylesheet">
    <? print(getJumpingJavascript()); ?>
</HEAD>
<BODY MARGINWIDTH="0" 
      MARGINHEIGHT="0" 
	  LEFTMARGIN=0 
	  TOPMARGIN=0 
	  BGCOLOR="#FFFFFF">
<TABLE BORDER="0" 
       CELLSPACING="0" 
	   CELLPADDING="4" 
	   ALIGN="left">
<TR>
	<TD>
<!-- Begin Left Content Area ----------------------------------------------  -->
<!-- Begin Add Phone ------------------------------------------------------- -->
	<TABLE BORDER="0" 
	       CELLSPACING="0" 
		   CELLPADDING="0" 
		   ALIGN="left">
	<TR>
		<TD><IMG src="/images/tbl-left-top.jpg" 
		         WIDTH="20" 
				 HEIGHT="25" 
				 BORDER="0" 
				 ALT=""></TD>
		<TD BGCOLOR="#003399" 
		    CLASS="hd3rev">Editing Phone Number for
                    <?=$SESSION_first_name?> <?=$SESSION_last_name?> </TD>
		<TD><IMG src="/images/tbl-right-top.jpg" 
		         WIDTH="10" 
				 HEIGHT="25" 
				 BORDER="0" 
				 ALT=""></TD>
	</TR>
	<TR>
		<TD BACKGROUND="/images/tbl-left-tile.jpg">&nbsp;</TD>
		<TD>
                    <FORM name="frmNumber" action="EXT_CONT_edit_phone_page.php">
                    <?=$hidden_tags ?>
			<TABLE>
			<TR>
				<TD ALIGN="right">
				Phone:</TD><TD> <? $markup = getPhoneInputMarkup("new_phone_number", "data", $phone_mask, $phone_number, false);
                        print($markup); ?> 
				</TD>               
			</TR>
            <TR>
                <TD ALIGN="right">
                                    Country: </TD><TD>
					<?= $country; ?>
                </TD>
            </TR>
            <TR>
                <TD ALIGN="right">
                                    Type:</TD><TD>
                        <SELECT NAME="phone_type" CLASS="data">
                        <OPTION VALUE=""> --select-- </OPTION>
                        <? foreach ($phone_types as $pt) {
                            $valid_type = true;
                            foreach($contact->individual->phones as $part) {
                                if ($pt->desc == $part->phone_type) {
                                    $valid_type = false;
                                }
                            }
                            if ( $pt->parameter_id == $phone_type ) {
                                $selected = 'selected';
                                $valid_type = true;
                            }
                            else {
                                $selected = '';
                            }
                            if ($valid_type) {?>
                               <option <?=$selected ?> value="<?=$pt->parameter_id?>"><?=$pt->desc?></option>
                        <? } } ?>
                        </SELECT>
                </TD>
            </TR>
                        <TR>
				<TD ALIGN="right" COLSPAN=2> <INPUT TYPE="image" 
				                          NAME="CONTINUE" VALUE="saved"
										  SRC="/images/button_command_save_off.jpg" 
										  HSPACE="2" 
										  VSPACE="2" 
										  BORDER="0"> </TD>
			</TR>
			</TABLE>
			</FORM>
		</TD>
	</TR>
	<TR ALIGN="left" 
	    VALIGN="top" 
		BGCOLOR="#003399">
		<TD ALIGN="left" 
		    VALIGN="bottom"><IMG src="/images/tbl-left-bottom.jpg" 
			                     WIDTH="20" 
								 HEIGHT="8" 
								 BORDER="0" 
								 ALT="" 
								 ALIGN="TOP"></TD>
		<TD BGCOLOR="#003399" 
		    class="smhd">&nbsp;</TD>
		<TD HEIGHT="8" 
		    ALIGN="left" 
			VALIGN="bottom"><IMG src="/images/tbl-right-bottom.jpg" 
			                     WIDTH="10" 
								 HEIGHT="8" 
								 BORDER="0" 
								 ALT="" 
								 ALIGN="TOP"></TD>
	</TR>
	</TABLE>
<!-- End Add Phone --------------------------------------------------------- -->
<!-- End Left Content Area ------------------------------------------------  -->
	</TD>
	<TD VALIGN="top">
<!-- Begin Right Content Area ---------------------------------------------  -->
				
<!-- End Right Content Area -----------------------------------------------  -->
	</TD>
</TR>
</TABLE>
</BODY>
</HTML>
