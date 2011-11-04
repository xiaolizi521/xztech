<?php

require_once('CORE_app.php');
require_once('helpers.php');
require_once('act/ActFactory.php');

define("FAX_TYPE", 115);

if( in_dept("CORE") ) {
    $core_edit = true;
} else {
    $core_edit = false;
}

$readonly = false;

$contact_type = '';
$contact_roles = '';
$contact_first_name = '';
$contact_last_name = '';
$org_name = '';
$job_title = '';
$contact_street = '';
$contact_city = '';
$contact_state = '';
$contact_zip = '';
$country = '';
$phone_numbers = '';
$email_addresses = '';

$i_contact = ActFactory::getIContact();
$i_account = ActFactory::getIAccount();

$contact_type = $external_contact->getRoleName();
$contact_role_id = $external_contact->contactTypeId;
$contact_first_name = $external_contact->individual->firstName;
$contact_last_name = $external_contact->individual->lastName;
session_register("SESSION_last_name");
session_register("SESSION_first_name");
session_register("SESSION_external_internal_contact");
$SESSION_last_name = $external_contact->individual->lastName;
$SESSION_first_name = $external_contact->individual->firstName;
$SESSION_external_internal_contact="external";

session_register("SESSION_individual_id");
$SESSION_individual_id = $external_contact->individual->primaryId;

$org_id = $external_contact->companyId;
$org_name = $external_contact->primaryCompanyName;
$job_title = $external_contact->individual->titleDescription;
session_register("SESSION_job_title");
$SESSION_job_title=$external_contact->individual->titleDescription;

$contactPrimaryAddress = $external_contact->individual->getPrimaryAddress();
$contact_street = $contactPrimaryAddress->address1;
if( $contactPrimaryAddress->address2 ) {
    $contact_street .= "\n".$contactPrimaryAddress->address2;
}
if( $contactPrimaryAddress->address3 ) {
    $contact_street .= "\n".$contactPrimaryAddress->address3;
}
$contact_street = ereg_replace("\n","<br>\n",$contact_street);
$contact_city = $contactPrimaryAddress->city;
$contact_state = $contactPrimaryAddress->regionCode;
$contact_zip = $contactPrimaryAddress->postCode;
$contact_country = $contactPrimaryAddress->countryCode;

session_register("SESSION_street");
session_register("SESSION_city");
session_register("SESSION_state");
session_register("SESSION_zip");
session_register("SESSION_country_id");

$SESSION_street = $contact_street;
$SESSION_city = $contact_city;
$SESSION_state = $contact_state;
$SESSION_zip = $contact_zip;
$SESSION_country_id = $contact_country;

// Security
if(!$core_edit) {
    if(($contact_role_id == 5 or $contact_role_id == 6) and
       !in_dept("PERM_EMPLOYEE_HR|PERM_EMPLOYEE_EDIT")) {
        $userid = GetRackSessionUserid();
    }
}
// End Security

$phone_numbers = '';
function ap( $text ) {
        global $phone_numbers;
        $phone_numbers .= $text;
}

$edit_bmp = '<IMG SRC="/images/button_command_tiny_edit.gif" WIDTH="26" HEIGHT="13" BORDER="0" ALIGN="TEXTTOP" ALT="Edit">';
$del_bmp  = '<IMG SRC="/images/button_command_tiny_delete.gif" WIDTH="26" HEIGHT="13" BORDER="0" ALIGN="TEXTTOP" ALT="Delete">';

## Start Table
ap( '<TABLE class=datatable>' );

foreach($external_contact->individual->phones as $part) {
    $ptype = $part->phone_type; //business, office, fax, etc
    $ccode = $part->phone_country_code;
    $primary = $part->primary;
    $phone_number = $part->getPrintablePhoneNumber();

    ap( '<TR><th>' );
    ap( $ptype.': ' );
    ap( "</TD>\n<TD>" );
    if ((int)$primary == 0) {
        ap( $phone_number . '' );
    } else {
        ap( $phone_number . ' (Primary)' );
    }
    ap( "</th>\n<TD>" );
    if( !$readonly ) {
        ap( '<a href="javascript:makePopUpWin(\'/EXT_CONT_edit_phone_page.php' );
        ap( '?phone_type_id=' . $part->phone_type_id );
        ap( "&external_contact_primary_id=$external_contact_primary_id" );
        ap( "&individual_id=".$external_contact->individual->primaryId );
        ap( "&phone_type=$part->phone_type_id" );
        ap( "&phone_number=$part->phone_number" );
        ap( "&country=$part->phone_country_code" );
        ap( "', 180,360,'',4)\">$edit_bmp</a>" );
    } else {
        ap( '&nbsp;' );
    }
    ap( "</TD>\n<TD>" );

	$can_delete = true;  

	if( count($external_contact->individual->phones) == 1 ) {     
		$can_delete = false;
    }

	if( count($external_contact->individual->phones) == 2 ) {     
		if( $external_contact->individual->phones[0]->phone_type_id == FAX_TYPE or  $external_contact->individual->phones[1]->phone_type_id == FAX_TYPE) {
			if ( $part->phone_type_id != FAX_TYPE) {
				$can_delete = false;
			}
		}
    }

    if( !$readonly and !$part->primary and $can_delete) {
        ap( '<a href="javascript:makePopUpWin(\'/EXT_CONT_delete_phone_page.php' );
        ap( '?phone_type_id=' . $part->phone_type_id );
        ap( "&external_contact_primary_id=$external_contact_primary_id" );
        ap( "&individual_id=".$external_contact->individual->primaryId );
        ap( "&phone_type_id=$part->phone_type_id" );
        ap( "&phone_number=$part->phone_number" );
        ap( "&phone_type_name=$part->phone_type" );
        ap( "', 150,300,'',4)\">$del_bmp</a>" );
    } else {
        ap( '&nbsp;' );
    }

    ap( "</TD></TR>\n" );
}
## End Table
ap( '</TABLE>' );


$email_addresses = '';
function ae( $text ) {
        global $email_addresses;
        $email_addresses .= $text;
}

## Start Table
ae( '<table class=datatable>' );

foreach($external_contact->individual->emailAddresses as $part) {
    if(!empty($part->email_type)) {
        $email = $part->address;

        ae( '<TR><th>' );
        ae( $part->email_type . ': ' );
        ae( "</TD>\n<TD>" );
        ae( '<A HREF="mailto:'.$part->address.'">' );
        ae( htmlentities($part->address) . '</A>' );
        if ( $part->primary ) {
            ae( ' (Primary)' );
        }
        ae( "</th>\n<TD>" );
        if( !$readonly ) {
            ae( '<a href="javascript:makePopUpWin(\'/EXT_CONT_edit_email_page.php' );
            ae( '?email_type_id=' . $part->email_type_id );
            ae( "&individual_id=$SESSION_individual_id" );
            ae( "&email_type=$part->email_type" );
            ae( "&email_address=$part->address" );
            if ( $part->primary ) {
                ae( "&primary_flag=1" );
            }
            else {
                ae( "&primary_flag=0" );
            }
            ae( "', 250,450,'',4)\">$edit_bmp</a>" );
        } else {
            ae( "&nbsp;" );
        }
        ae( "</TD>\n<TD>" );
        if( !$readonly ) {
            ae( '<a href="javascript:makePopUpWin(\'/EXT_CONT_delete_email_page.php' );
            ae( '?email_type_id=' . $part->email_type_id );
            ae( "&email_type=$part->email_type" );
            ae( "&external_contact_primary_id=$external_contact_primary_id" );
            ae( "&individual_id=$SESSION_individual_id" );
            ae( "&email_address=$part->address" );
            ae( "', 250,400,'',4)\">$del_bmp</a>" );
        } else {
            ae( "&nbsp;" );
        }
        ae( "</TD></TR>\n" );
    }
}
## End Table
ae( '</TABLE>' );

# Get the secret
if( $external_contact->individual->secretQuestion != "") {
    $secret_exists = true;
} else {
    $secret_exists = false;
}
$secret_question = $external_contact->individual->secretQuestion;
$secret_answer = $external_contact->individual->secretAnswer;
$secret_questionURL = rawurlencode($external_contact->individual->secretQuestion);
$secret_answerURL = rawurlencode($external_contact->individual->secretAnswer);

?>
