# $Id: Database.pm,v 1.3 2002/03/02 21:57:18 jenni Exp $
package LibDB;

use Mysql;

sub init {
	shift @_;
	($dbhost, $dbname, $dbuser, $dbpass) = @_;
	my $dbh = Mysql->connect($dbhost, $dbname, $dbuser, $dbpass);
    $dbh->{'mysql_auto_reconnect'} = 1;
	return $dbh;
}

1;
