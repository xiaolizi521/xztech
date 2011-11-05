<?php
require_once('CORE_app.php');
require_once("class.mailer.php");
require_once("class.parser.php");
require_once("act/ActFactory.php");

/** Needed to generate pdfs */
require("contract_control/ContractPDF.php");
/** Needed to generate pdfs */
require("contract_control/ContractNewDevicePDF.php");


global $GLOBAL_db;
$db = $GLOBAL_db;
if ( !$mod_request_id ) {
    die( print "You need to provide a Modification Request ID");
}
#$mod_request_id = 2081;
$renewal_sql = 'select "ID","Term","Notes","BillingContactID","PreviousTerm", "AccountManagerID"  
                    from "CNTR_ContractRenewal" 
                    where "CNTR_ContractModRequestID"='.$mod_request_id;
$result = $db->SubmitQuery( $renewal_sql );
if ( $result->numRows() == 1 ) {
    $renewal = $result->fetchArray( 0 ); 
    $renewal_id = $renewal['ID'];
    $term = $renewal['Term'];
    $notes = $renewal['Notes'];
    $notes = iconv( 'UTF-8', 'latin1//TRANSLIT', $notes );
    $bc_id = $renewal['BillingContactID'];
    #$bc_id = 73874;
    $ind_id = $db->getVal( 'select "crm_individual_id" from "CONT_Contact" where "ID"='.$bc_id );
    $acct_id = $db->getVal( 'select "ACCT_AccountID" from "CNTR_ContractModRequest" where "ID"='.$mod_request_id );
    $crm_company_id = $db->getVal( 'select "crm_company_id" from "ACCT_Account" where "ID"='.$acct_id );
    #$crm_company_id = 339134;
    $i_contact = ActFactory::getIContact();
    $conts = $i_contact->getExternalContacts( $db, $crm_company_id );
    foreach( $conts as $cont ) {
        if ( (int)$cont->individual->primaryId == (int)$ind_id ) {
            $bc =& $cont;
            break;
        } 
    }
    $previous_term = $renewal['PreviousTerm'];
    $am_id = $renewal['AccountManagerID'];
    $result->freeResult();
    $mod_request_sql = 'select * from "CNTR_ContractModRequest" where "ID"='.$mod_request_id;
    $result = $db->SubmitQuery( $mod_request_sql );
    $mod_request = $result->fetchArray( 0 ); 
    $revision_id = $mod_request['Revision'];
    $created = $mod_request['Created'];
    $status_id = $mod_request['CNTR_val_ContractModStatusID'];
    $contract_type_id = $mod_request['CNTR_val_ContractTypeID'];
    $account_id = $mod_request['ACCT_AccountID'];
    $i_account  = ActFactory::getIAccount();
    $account =  $i_account->getAccountByAccountId($db, $account_id);
    $date_effective = $mod_request['DateEffective'];
    $sales_person_id = $mod_request['SalesPersonID'];
    $sales_person = '';
    if ( !$sales_person_id ) {
        die( print "Sales Person is a required field" );
    } 
    $sales_person =& new CONT_Contact();
    $sales_person->loadID( $sales_person_id );
    $additional_sales_person_id = $mod_request['AdditionalSalesPersonID'];
    $additional_sales_person = '';
    if ( $additional_sales_person_id ) {
        $additional_sales_person = new CONT_Contact();
        $additional_sales_person->loadID(  $additional_sales_person_id );
    }
    $months_retroactive = $mod_request['MonthsRetroactive'];
    $split_billing = $mod_request['SplitBilling'];
    $date_received = $mod_request['DateReceived'];
    $creator_id = $mod_request['CreatorID'];
    $creator = '';
    if ( $creator_id )  {
        $creator = new CONT_Contact();
        $creator->loadID( $creator_id );
    }
    $private_note = $mod_request['PrivateNote'];
    $is_prepay = $mod_request['IsPrepay'];
    $result->freeResult();
    $customer_name = $bc->individual->getFullName(); 
    $telephone = $bc->individual->getPrimaryPhoneNumber( $db );
    $email = $bc->individual->getPrimaryEmailAddress( $db );
    $address = $bc->individual->getPrimaryAddress();
    $street = $address->address1."\n".$address->address2."\n".$address->address3;
    $city = $address->city;
    $state = $address->regionCode;
    $zipCode = $address->postCode;
    $country = $address->countryCode;
    $company_name = $account->account_name;

        $contract = &new ContractPDF($revision_id,"Contract Extension and Price Modification Form\nFor Account #".$account->account_number);
        $contract->setFont('','B',8);
        $contract->setAuthor($sales_person->getName() );
        $contract->addBlock("<B>Business Development Consultant</B>: ".$sales_person->getName());
        $ae = $account->getAccountExecutive();
        $contract->addBlock("<B>AccountManager</B>: ".$ae->formatted_name);
        $contract->addBlock("<B>Date Effective</B>: $date_effective");
        $contract->Ln(.2);
        $contract->addBlock("In consideration of Rackspace agreeing to the New Monthly Fees set forth below, Customer hereby agrees to extend the <B>initial $previous_term contract</B> (as defined in the Master Services Agreement and Service Order Form(s) thereto between Rackspace and Customer) for a period of <B>$term</B> from the date this form is approved by Rackspace.  Price change will take effect on the next invoice after this form is approved by Rackspace.  The New Monthly Fees set forth below are not subject to modification within <B>$term</B> of the date this form is approved by Rackspace.");
        $contract->Ln(.2);
        $table = array();
        $table[] = array("Customer #: ".$account->account_number,"Billing Address");

        $table[] = array("Customer Name: $company_name","Address: $street");
        $table[] = array("Contact Name: $customer_name","City: $city");

        $table[] = array("Telephone Number: $telephone","State: $state");
        $table[] = array("Email Address: $email","Zip Code: $zipCode");
        $table[] = array("","Country: $country");

        $contract->setFont('','B',10);
        $contract->addBlock("Billing Information");
        $contract->setFont('','',8);
        $contract->addColumnText($table);

        $contract->Ln(.2);
        $contract->SetFont('','B',8);
        $contract->addBlock("**Except for the extension of the Initial Term and the price changes set forth in the \"New Monthly Fee\" section Upgrades below, all of the terms and conditions of the Master Services Agreement and Service Order Form(s) thereto by and between Rackspace and Customer are unmodified and shall continue in full force and effect, and Rackspace and Customer hereby ratify, reaffirm and confirm such terms and conditions, as modified hereby.  Customer is encouraged to review the Rackspace Acceptable Use Policy posted at http://www.rackspace.com/aboutus/acceptable_use.php as of the effective date of the Renewal Term.**");
            
        $contract->Ln(.2); 
        $contract->setFont('','B',10);
        $contract->addBlock("Additional Notes");
        $contract->SetFont('','',8);
        $contract->addBlock($notes);
        $contract->Ln(.2);
        $contract->setFont('','B',10);
        $contract->addBlock("Servers Being Renewed");
        $contract->Ln(.2);
        $contract->setFont('','B',8);
        $headers = array('Server #','Current Monthly','New Monthly Fee',"New Contract End Date","Notes");
        $data = array(); 
        $totalMonthly = 0;
        $renewal_comps_sql = 'select * from "CNTR_ContractRenewalComputers" where "CNTR_ContractRenewalID"='.$renewal_id;
        $result = $db->SubmitQuery( $renewal_comps_sql );
        for( $i=0; $i < $result->numRows(); $i++ ) {
            $comp = $result->fetchArray( $i );
            $data[] = array( $comp['ComputerID'], 
                             $comp['CurrentMonthly'],
                             $comp['NewMonthly'],
                             $comp['ContractEndDate'],
                             $comp['Notes']
                      );
            $totalMonthly = $totalMonthly + $comp["NewMonthly"];
        }
        $result->freeResult();

        if ($is_prepay != 'f') {
                if ($term != "Coterminous"
                        && $term != "Month to Month") {
                    $termMonths = split(" ", $term);
                    $totalPrepay = $termMonths[0] * $totalMonthly;
                    $contract->addBlock("Total Prepay Amount: $" . $totalPrepay);
                }
        }

        $contract->SetFont('','B',8);
        $contract->addTable($headers,
                            $data,
                            array(.75,1.25,1.25,1,3.5),
                            RGBColor::Black(),
                            RGBColor::White(),
                            NULL,
                            NULL,
                            'C',
                            8);
        $contract->Ln(.3);

        $table = array();
        $table[] = array("Customer Approval:","Rackspace Approval");
        $table[] = array("Customer Name: $customer_name",$creator->getTitle().": ".$creator->getName());
        $table[] = array("Customer's Signature:","Manager's Approval:");
        $table[] = array("","");
        $table[] = array("______________________________","______________________________");
        $table[] = array("","");
        $table[] = array("Date:_________________________","Date:_________________________");
        $testPDF = new PDF();
        $testPDF->setXY(0,0);
        $testPDF->setFont('','B',10);
        $testPDF->addBlock("Approval Information");
        $testPDF->setFont('','',8);
        $testPDF->addColumnText($table);
        if ($testPDF->getY()+$contract->getY() > 10) {
            $contract->addPage(false);
        }
        $contract->setFont('','B',10);
        $contract->addBlock("Approval Information");
        $contract->setFont('','',10);
        $contract->addColumnText($table);


        $contract->Output();

}
?>
