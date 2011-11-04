<?

/**
 * Author:   	Cory Becker
 * Date:   	 	September 21, 2007
 * Company:		Becker Web Solutions, LLC
 * Website:	 	www.beckerwebsolutions.com
 *
 * Description:
 *					Configuration and includes
 */

include( "db.php" );
include( "db-extras.php" );
include( "user.php" );
include( "cache.php" );
include( "debug.php" );
include( "freshbooks.php" );
include( "xml.php" );
include( "status.php" );
include( "mailer.php" );
include( "getid3/getid3.php" );

// Base url without trailing slash
$base_url = "http://iac.x-zen.cx/pms2";

// Daily employee report
$employee_report_program = "smtp"; // "mail" for PHP mail() or "smtp" for SMTP
$employee_report = true;
$employee_report_email = "heather@iacprofessionals.com";

?>
