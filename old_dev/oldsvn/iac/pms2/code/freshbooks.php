<?

/**
 * Provided by FreshBooksTools.com
 * A service of Becker Web Solutions, LLC (www.BeckerWebSolutions.com)
 * 
 * Description:	Use submitted form information to create a new client
 *				in FreshBooks
 *
 * Date:		August 19, 2007
 * Updated:		August 19, 2007
 * Version:		1.0
 * By:			Cory Becker
 *
 * License: 	This script may be modified from its original version. It may
 *				not be resold in any way; nor may it be used on any website
 *				other than for the website it was purchased for.
 *
 * Support:		For support, contact Becker Web Solutions, LLC:
 *				By Email:	Support@BeckerWebSolutions.com
 *				By Phone:	402-218-2110
 *				By Web:		www.BeckerWebSolutions.com
 *
 */
  
 class FreshBooksAPI
 { 
 	var $FreshBooksAPI_Key = "7b191f703c55f5a880c4c7cc6ea564a7";
 	var $FreshBooksAPI_URL = "https://IACProfessionals.freshbooks.com/api/2.1/xml-in";
 	
 	function post( $data )
 	{
 		// Following code used from apiTester.php from FreshBooks.com
 		$ch = curl_init();    // initialize curl handle
		curl_setopt($ch, CURLOPT_URL, $this->FreshBooksAPI_URL ); // set url to post to
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); // return into a variable
		curl_setopt($ch, CURLOPT_TIMEOUT, 4); // times out after 4s
		curl_setopt($ch, CURLOPT_POSTFIELDS, $data); // add POST fields
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE); // turn off verification of SSL for testing
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE); // turn off verification of SSL for testing
		curl_setopt($ch, CURLOPT_USERPWD, $this->FreshBooksAPI_Key );
		curl_setopt($ch, CURLOPT_USERAGENT, "FreshBooksTools Agent");
	 	//curl_setopt ($ch, CURLOPT_PROXY,"http://64.202.165.130:3128");		// Workaround for GoDaddy's Virtual Hosting
	
		$result = curl_exec($ch); // run the whole process
		curl_close ($ch);
	
		if (strlen($result) < 2) $result = "Could not execute curl.";
		preg_match_all ("/<(.*?)>(.*?)\</", $result, $outarr,PREG_SET_ORDER);
		$n = 0;
	
		while (isset($outarr[$n])){
			$retarr[$outarr[$n][1]] = strip_tags($outarr[$n][0]);
			$n++;
		}
		
		return $result;
 	}

	
	
	function clientCreate( $array = array() )
	{	
		/* Build XML Request */
		$xmlRequest = array();
		$xmlBody = array();
		
		$xmlBody['client'] = $array;
		
		$xmlRequest['request']['param1'] = "method:client.create";
		$xmlRequest['request']['value'] = $xmlBody;
		
		$xml = new XMLGenerator();
		
		/* Send XML Request */
		$xmlResult = new xmlarray( $this->post( $xml->BuildXMLDocument( $xmlRequest, true ) ) );
		return $xmlResult->createArray();
	}
	
	function clientUpdate( $clientId, $array = array() )
	{
		/* Build XML Request */
		$xmlRequest = array();
		$xmlBody = array();
		
		$xmlBody['client']['client_id'] = $clientId;
		
		$xmlRequest['request']['param1'] = "method:client.update";
		$xmlRequest['request']['value'] = $array;
		
		$xml = new XMLGenerator();
		
		/* Send XML Request */
		$xmlResult = new xmlarray( $this->post( $xml->BuildXMLDocument( $xmlRequest, true ) ) );
		return $xmlResult->createArray();
	}
	
	function clientGet( $clientId )
	{
		/* Build XML Request */
		$xmlRequest = array();
		$xmlBody = array();
		
		$xmlBody['client_id'] = $clientId;

		$xmlRequest['request']['param1'] = "method:client.get";
		$xmlRequest['request']['value'] = $xmlBody;
		
		$xml = new XMLGenerator();
		
		/* Send XML Request */
		$xmlResult = new xmlarray( $this->post( $xml->BuildXMLDocument( $xmlRequest, true ) ) );
		return $xmlResult->createArray();
	}
	
	function clientDelete( $clientId )
	{
		/* Build XML Request */
		$xmlRequest = array();
		$xmlBody = array();
		
		$xmlBody['client_id'] = $clientId;

		$xmlRequest['request']['param1'] = "method:client.delete";
		$xmlRequest['request']['value'] = $xmlBody;
		
		$xml = new XMLGenerator();
		
		/* Send XML Request */
		$xmlResult = new xmlarray( $this->post( $xml->BuildXMLDocument( $xmlRequest, true ) ) );
		return $xmlResult->createArray();
	}
	
	function clientList( $array = array() )
	{
		/* Build XML Request */
		$xmlRequest = array();
		$xmlBody = array();
		
		$xmlRequest['request']['param1'] = "method:client.list";
		$xmlRequest['request']['value'] = $array;
		
		$xml = new XMLGenerator();
		
		/* Send XML Request */
		$xmlResult = new xmlarray( $this->post( $xml->BuildXMLDocument( $xmlRequest, true ) ) );
		return $xmlResult->createArray();
	}
	
	function invoiceCreate( $clientId, $invoiceHeaderArray = array(), $lineItemsArray = array() )
	{
		/* Build XML Request */
		$xmlRequest = array();
		$xmlBody = array();
		$xmlBodyLines = array();
		
		$xmlBody['invoice'] = $invoiceHeaderArray;
		$xmlBody['invoice']['client_id'] = $clientId;
		
		for( $i = 0; $i < count( $lineItemsArray ); $i++ )
			$xmlBodyLines['line%'.$i] = $lineItemsArray[ $i ]; 	
		
		$xmlBody['invoice']['lines'] = $xmlBodyLines;
		$xmlBody['username'] = $array['username'];
		$xmlBody['page'] = $array['page'];
		$xmlBody['per_page'] = $array['per_page'];

		$xmlRequest['request']['param1'] = "method:invoice.create";
		$xmlRequest['request']['value'] = $xmlBody;
		
		$xml = new XMLGenerator();
		
		/* Send XML Request */
		$xmlResult = new xmlarray( $this->post( $xml->BuildXMLDocument( $xmlRequest, true ) ) );
		return $xmlResult->createArray();
	}
	
	function invoiceUpdate( $invoiceId, $invoiceHeaderArray = array(), $lineItemsArray = array() )
	{
		/* Build XML Request */
		$xmlRequest = array();
		$xmlBody = array();
		$xmlBodyLines = array();
		
		$xmlBody['invoice'] = $invoiceHeaderArray;
		$xmlBody['invoice']['invoice_id'] = $invoiceId;
		
		for( $i = 0; $i < count( $lineItemsArray ); $i++ )
			$xmlBodyLines['line%'.$i] = $lineItemsArray[ $i ]; 	
		
		$xmlBody['invoice']['lines'] = $xmlBodyLines;
		$xmlBody['username'] = $array['username'];
		$xmlBody['page'] = $array['page'];
		$xmlBody['per_page'] = $array['per_page'];

		$xmlRequest['request']['param1'] = "method:invoice.create";
		$xmlRequest['request']['value'] = $xmlBody;
		
		$xml = new XMLGenerator();
		
		/* Send XML Request */
		$xmlResult = new xmlarray( $this->post( $xml->BuildXMLDocument( $xmlRequest, true ) ) );
		return $xmlResult->createArray();
	}
	
	function invoiceGet( $invoiceId )
	{
		/* Build XML Request */
		$xmlRequest = array();
		$xmlBody = array();
		
		$xmlBody['invoice_id'] = $invoiceId;

		$xmlRequest['request']['param1'] = "method:invoice.get";
		$xmlRequest['request']['value'] = $xmlBody;
		
		$xml = new XMLGenerator();
		
		/* Send XML Request */
		$xmlResult = new xmlarray( $this->post( $xml->BuildXMLDocument( $xmlRequest, true ) ) );
		return $xmlResult->createArray();
	}
	
	function invoiceDelete( $invoiceId )
	{
		/* Build XML Request */
		$xmlRequest = array();
		$xmlBody = array();
		
		$xmlBody['invoice_id'] = $invoiceId;

		$xmlRequest['request']['param1'] = "method:invoice.delete";
		$xmlRequest['request']['value'] = $xmlBody;
		
		$xml = new XMLGenerator();
		
		/* Send XML Request */
		$xmlResult = new xmlarray( $this->post( $xml->BuildXMLDocument( $xmlRequest, true ) ) );
		return $xmlResult->createArray();
	}
	
	function invoiceList( $array = array() )
	{
		/* Build XML Request */
		$xmlRequest = array();
		$xmlBody = array();
		
		$xmlRequest['request']['param1'] = "method:invoice.list";
		$xmlRequest['request']['value'] = $array;
		
		$xml = new XMLGenerator();
		
		/* Send XML Request */
		$xmlResult = new xmlarray( $this->post( $xml->BuildXMLDocument( $xmlRequest, true ) ) );
		return $xmlResult->createArray();
	}
	
	function invoiceSendByEmail( $invoiceId )
	{
		/* Build XML Request */
		$xmlRequest = array();
		$xmlBody = array();
		
		$xmlBody['invoice_id'] = $invoiceId;

		$xmlRequest['request']['param1'] = "method:invoice.sendByEmail";
		$xmlRequest['request']['value'] = $xmlBody;
		
		$xml = new XMLGenerator();
		
		/* Send XML Request */
		$xmlResult = new xmlarray( $this->post( $xml->BuildXMLDocument( $xmlRequest, true ) ) );
		return $xmlResult->createArray();
	}
	
	function invoiceSendBySnailMail( $invoiceId )
	{
		/* Build XML Request */
		$xmlRequest = array();
		$xmlBody = array();
		
		$xmlBody['invoice_id'] = $invoiceId;

		$xmlRequest['request']['param1'] = "method:invoice.sendBySnailMail";
		$xmlRequest['request']['value'] = $xmlBody;
		
		$xml = new XMLGenerator();
		
		/* Send XML Request */
		$xmlResult = new xmlarray( $this->post( $xml->BuildXMLDocument( $xmlRequest, true ) ) );
		return $xmlResult->createArray();
	}
	
	function estimateCreate( $clientId, $estimateHeaderArray = array(), $lineItemsArray = array() )
	{
		/* Build XML Request */
		$xmlRequest = array();
		$xmlBody = array();
		$xmlBodyLines = array();
		
		$xmlBody['estimate'] = $estimateHeaderArray;
		$xmlBody['estimate']['client_id'] = $clientId;
		
		for( $i = 0; $i < count( $lineItemsArray ); $i++ )
			$xmlBodyLines['line%'.$i] = $lineItemsArray[ $i ]; 	
		
		$xmlBody['estimate']['lines'] = $xmlBodyLines;
		$xmlBody['username'] = $array['username'];
		$xmlBody['page'] = $array['page'];
		$xmlBody['per_page'] = $array['per_page'];

		$xmlRequest['request']['param1'] = "method:estimate.create";
		$xmlRequest['request']['value'] = $xmlBody;
		
		$xml = new XMLGenerator();
		
		/* Send XML Request */
		$xmlResult = new xmlarray( $this->post( $xml->BuildXMLDocument( $xmlRequest, true ) ) );
		return $xmlResult->createArray();
	}
	
	function estimateUpdate( $estimateId, $array = array() )
	{
		/* Build XML Request */
		$xmlRequest = array();
		$xmlBody = array();
		$xmlBodyLines = array();
		
		$xmlBody['estimate'] = $estimateHeaderArray;
		$xmlBody['estimate']['estimate_id'] = $estimateId;
		
		for( $i = 0; $i < count( $lineItemsArray ); $i++ )
			$xmlBodyLines['line%'.$i] = $lineItemsArray[ $i ]; 	
		
		$xmlBody['estimate']['lines'] = $xmlBodyLines;
		$xmlBody['username'] = $array['username'];
		$xmlBody['page'] = $array['page'];
		$xmlBody['per_page'] = $array['per_page'];

		$xmlRequest['request']['param1'] = "method:estimate.update";
		$xmlRequest['request']['value'] = $xmlBody;
		
		$xml = new XMLGenerator();
		
		/* Send XML Request */
		$xmlResult = new xmlarray( $this->post( $xml->BuildXMLDocument( $xmlRequest, true ) ) );
		return $xmlResult->createArray();
	}
	
	function estimateGet( $estimateId )
	{
		/* Build XML Request */
		$xmlRequest = array();
		$xmlBody = array();
		
		$xmlBody['estimate_id'] = $invoiceId;

		$xmlRequest['request']['param1'] = "method:estimate.get";
		$xmlRequest['request']['value'] = $xmlBody;
		
		$xml = new XMLGenerator();
		
		/* Send XML Request */
		$xmlResult = new xmlarray( $this->post( $xml->BuildXMLDocument( $xmlRequest, true ) ) );
		return $xmlResult->createArray();
	}
	
	function estimateDelete( $estimateId )
	{
		/* Build XML Request */
		$xmlRequest = array();
		$xmlBody = array();
		
		$xmlBody['estimate_id'] = $estimateId;

		$xmlRequest['request']['param1'] = "method:estimate.delete";
		$xmlRequest['request']['value'] = $xmlBody;
		
		$xml = new XMLGenerator();
		
		/* Send XML Request */
		$xmlResult = new xmlarray( $this->post( $xml->BuildXMLDocument( $xmlRequest, true ) ) );
		return $xmlResult->createArray();
	}
	
	function estimateList( $array = array() )
	{
		/* Build XML Request */
		$xmlRequest = array();
		$xmlBody = array();
		
		$xmlRequest['request']['param1'] = "method:estimate.list";
		$xmlRequest['request']['value'] = $array;
		
		$xml = new XMLGenerator();
		
		/* Send XML Request */
		$xmlResult = new xmlarray( $this->post( $xml->BuildXMLDocument( $xmlRequest, true ) ) );
		return $xmlResult->createArray();
	}
	
	function estimateSendByEmail( $estimateId )
	{
		/* Build XML Request */
		$xmlRequest = array();
		$xmlBody = array();
		
		$xmlBody['estimate_id'] = $estimateId;

		$xmlRequest['request']['param1'] = "method:estimate.sendByEmail";
		$xmlRequest['request']['value'] = $xmlBody;
		
		$xml = new XMLGenerator();
		
		/* Send XML Request */
		$xmlResult = new xmlarray( $this->post( $xml->BuildXMLDocument( $xmlRequest, true ) ) );
		return $xmlResult->createArray();
	}
	
	function recurringCreate( $clientId, $recurringHeaderArray = array(), $lineItemsArray = array() )
	{
		/* Build XML Request */
		$xmlRequest = array();
		$xmlBody = array();
		$xmlBodyLines = array();
		
		$xmlBody['recurring'] = $recurringHeaderArray;
		$xmlBody['recurring']['client_id'] = $clientId;
		
		for( $i = 0; $i < count( $lineItemsArray ); $i++ )
			$xmlBodyLines['line%'.$i] = $lineItemsArray[ $i ]; 	
		
		$xmlBody['recurring']['lines'] = $xmlBodyLines;
		$xmlBody['username'] = $array['username'];
		$xmlBody['page'] = $array['page'];
		$xmlBody['per_page'] = $array['per_page'];

		$xmlRequest['request']['param1'] = "method:recurring.create";
		$xmlRequest['request']['value'] = $xmlBody;
		
		$xml = new XMLGenerator();
		
		/* Send XML Request */
		$xmlResult = new xmlarray( $this->post( $xml->BuildXMLDocument( $xmlRequest, true ) ) );
		return $xmlResult->createArray();
	}
	
	function recurringUpdate( $recurringId, $recurringHeaderArray = array(), $lineItemsArray = array() )
	{
		/* Build XML Request */
		$xmlRequest = array();
		$xmlBody = array();
		$xmlBodyLines = array();
		
		$xmlBody['recurring'] = $estimateHeaderArray;
		$xmlBody['recurring']['recurring_id'] = $recurringId;
		
		for( $i = 0; $i < count( $lineItemsArray ); $i++ )
			$xmlBodyLines['line%'.$i] = $lineItemsArray[ $i ]; 	
		
		$xmlBody['recurring']['lines'] = $xmlBodyLines;
		$xmlBody['username'] = $array['username'];
		$xmlBody['page'] = $array['page'];
		$xmlBody['per_page'] = $array['per_page'];

		$xmlRequest['request']['param1'] = "method:recurring.update";
		$xmlRequest['request']['value'] = $xmlBody;
		
		$xml = new XMLGenerator();
		
		/* Send XML Request */
		$xmlResult = new xmlarray( $this->post( $xml->BuildXMLDocument( $xmlRequest, true ) ) );
		return $xmlResult->createArray();
	}
	
	function recurringGet( $recurringId )
	{
		/* Build XML Request */
		$xmlRequest = array();
		$xmlBody = array();
		
		$xmlBody['recurring_id'] = $recurringId;

		$xmlRequest['request']['param1'] = "method:recurring.get";
		$xmlRequest['request']['value'] = $xmlBody;
		
		$xml = new XMLGenerator();
		
		/* Send XML Request */
		$xmlResult = new xmlarray( $this->post( $xml->BuildXMLDocument( $xmlRequest, true ) ) );
		return $xmlResult->createArray();
	}
	
	function recurringDelete( $recurringId )
	{
		/* Build XML Request */
		$xmlRequest = array();
		$xmlBody = array();
		
		$xmlBody['recurring_id'] = $recurringId;

		$xmlRequest['request']['param1'] = "method:recurring.delete";
		$xmlRequest['request']['value'] = $xmlBody;
		
		$xml = new XMLGenerator();
		
		/* Send XML Request */
		$xmlResult = new xmlarray( $this->post( $xml->BuildXMLDocument( $xmlRequest, true ) ) );
		return $xmlResult->createArray();
	}
	
	function recurringList( $array = array() )
	{
		/* Build XML Request */
		$xmlRequest = array();
		$xmlBody = array();
		
		$xmlRequest['request']['param1'] = "method:recurring.list";
		$xmlRequest['request']['value'] = $array;
		
		$xml = new XMLGenerator();
		
		/* Send XML Request */
		$xmlResult = new xmlarray( $this->post( $xml->BuildXMLDocument( $xmlRequest, true ) ) );
		return $xmlResult->createArray();
	}
	
	function paymentCreate( $clientId, $array = array() )
	{
		/* Build XML Request */
		$xmlRequest = array();
		$xmlBody = array();
		
		$xmlBody['payment'] = $array;
		$xmlBody['payment']['client_id'] = $clientId;

		$xmlRequest['request']['param1'] = "method:payment.create";
		$xmlRequest['request']['value'] = $xmlBody;
		
		$xml = new XMLGenerator();
		
		/* Send XML Request */
		$xmlResult = new xmlarray( $this->post( $xml->BuildXMLDocument( $xmlRequest, true ) ) );
		return $xmlResult->createArray();
	}
	
	function paymentUpdate( $paymentId, $array = array() )
	{
		/* Build XML Request */
		$xmlRequest = array();
		$xmlBody = array();
		
		$xmlBody['payment'] = $array;
		$xmlBody['payment']['payment_id'] = $paymentId;

		$xmlRequest['request']['param1'] = "method:payment.update";
		$xmlRequest['request']['value'] = $xmlBody;
		
		$xml = new XMLGenerator();
		
		/* Send XML Request */
		$xmlResult = new xmlarray( $this->post( $xml->BuildXMLDocument( $xmlRequest, true ) ) );
		return $xmlResult->createArray();
	}
	
	function paymentGet( $paymentId )
	{
		/* Build XML Request */
		$xmlRequest = array();
		$xmlBody = array();
		
		$xmlBody['payment_id'] = $paymentId;

		$xmlRequest['request']['param1'] = "method:payment.get";
		$xmlRequest['request']['value'] = $xmlBody;
		
		$xml = new XMLGenerator();
		
		/* Send XML Request */
		$xmlResult = new xmlarray( $this->post( $xml->BuildXMLDocument( $xmlRequest, true ) ) );
		return $xmlResult->createArray();
	}
	
	function paymentList( $array = array() )
	{
		/* Build XML Request */
		$xmlRequest = array();
		$xmlBody = array();

		$xmlRequest['request']['param1'] = "method:payment.list";
		$xmlRequest['request']['value'] = $array;

		$xml = new XMLGenerator();

		/* Send XML Request */
		$xmlResult = new xmlarray( $this->post( $xml->BuildXMLDocument( $xmlRequest, true ) ) );
		return $xmlResult->createArray();
	}
 	
 }

?>