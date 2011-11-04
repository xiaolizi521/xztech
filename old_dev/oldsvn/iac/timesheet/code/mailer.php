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

include( "smtp.php" );

function sendmail( $to, $subject, $body, $fromname, $fromemail )
{
	global $employee_report_program;
	
	if( $employee_report_program == "smtp" )
		sendmailSMTP( $to, $subject, $body, $fromname, $fromemail );
	else
		sendmailPHP( $to, $subject, $body, $fromname, $fromemail );
}

function sendmailPHP( $to, $subject, $body, $fromname, $fromemail )
{

	// To send HTML mail, the Content-type header must be set
	$headers  = 'MIME-Version: 1.0' . "\r\n";
	$headers .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";

	// Additional headers
 	$headers .= 'From: '.$fromname.' <'.$fromemail.'>' . "\r\n";

	// Mail it
	mail($to, $subject, $body, $headers);

}

function sendmailSMTP( $to, $subject, $body, $fromname, $from )
{
	
	// Authenticate first using POP (iPowerWeb requires)

		$smtp=new smtp_class;

		$smtp->host_name="mail.iacprofessionals.com";       /* Change this variable to the address of the SMTP server to relay, like "smtp.myisp.com" */
		$smtp->host_port=25;                /* Change this variable to the port of the SMTP server to use, like 465 */
		$smtp->ssl=0;                       /* Change this variable if the SMTP server requires an secure connection using SSL */
		$smtp->localhost="localhost";       /* Your computer address */
		$smtp->direct_delivery=0;           /* Set to 1 to deliver directly to the recepient SMTP server */
		$smtp->timeout=10;                  /* Set to the number of seconds wait for a successful connection to the SMTP server */
		$smtp->data_timeout=0;              /* Set to the number seconds wait for sending or retrieving data from the SMTP server.
		                                       Set to 0 to use the same defined in the timeout variable */
		$smtp->debug=0;                     /* Set to 1 to output the communication with the SMTP server */
		$smtp->html_debug=1;                /* Set to 1 to format the debug output as HTML */
		$smtp->pop3_auth_host="mail.iacprofessionals.com";           /* Set to the POP3 authentication host if your SMTP server requires prior POP3 authentication */
		$smtp->user="timesheet@iacprofessionals.com";                     /* Set to the user name if the server requires authetication */
		$smtp->realm="";                    /* Set to the authetication realm, usually the authentication user e-mail domain */
		$smtp->password="t!m3sh33t";                 /* Set to the authetication password */
		$smtp->workstation="";              /* Workstation name for NTLM authentication */
		$smtp->authentication_mechanism=""; /* Specify a SASL authentication method like LOGIN, PLAIN, CRAM-MD5, NTLM, etc..
		                                       Leave it empty to make the class negotiate if necessary */

		$smtp->SendMessage( $from, 
								  array( $to ), 
								  array(	"From: $from",	"To: $to", "Subject: $subject", "Date: ".strftime("%a, %d %b %Y %H:%M:%S %Z"), "MIME-Version: 1.0", "Content-type: text/html; charset=iso-8859-1" ),
								  $body );
		
}
 
?>