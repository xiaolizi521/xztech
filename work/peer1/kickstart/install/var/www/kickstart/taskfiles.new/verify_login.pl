#!/usr/bin/perl -w
use Data::Dumper;

###############
## Functions ##
###############
sub getMacs() {
	# we should use the route command on linux to figure this out
	my $macs = `/sbin/ifconfig -a 2> /dev/null | grep HWaddr | awk '{print \$5}'`;
	chomp($macs);
	$macs =~ s/\n/,/g;
	return $macs;
}

sub lookup() {
	# Prepare curl params
	my $macs = getMacs();
	my $url = "https://dcc.$dc/widgets/devices/mac";

	my $data = sprintf('{"inputs": {"name": "", "cachable": "f", "parameter": [{"name": "mac address", "value": "%s" }] } }',$macs);

	my $opts  = $curl_opts;
	   $opts .= join(" ", @headers)." ";
	   $opts .= "-X POST -d '$data' ";

	# Query DCC for MAC address on server
	my $cmd = "curl $opts $url 2> /dev/null";
	my $result = `$cmd`;
	chomp($result);
	if( $result eq '' ) {
		print STDERR "[xx] Failed to lookup device information.\n";
		exit(1);
	}

	# Parse output from DCC
	
	if ( $result =~/=>/g ){

	
		my %data = eval($result);

		my $parameters = $data{outputs}{parameter}; 
		while( (my $key, my $value) = each( %$parameters ) ) {
			$device_info{$value->{name}} = $value->{value};
			if( $value->{name} eq 'service.deviceid' ) {
				$device_id = $value->{value};
			}
		}
	 } else {

		print "[xx] $result\n\n";

	 }

	my $location  = $device_info{'location.building'} .' - ';
	   $location .= $device_info{'location.floor'}    .' - ';
	   $location .= $device_info{'location.row'}      .'-';
	   $location .= $device_info{'location.cabinet'}  .'/';
	   $location .= $device_info{'location.slot'};

	# Email body to us and output for user
	my $body = <<EOF;
Client		: $device_info{'client.name'} ($device_info{'client.clientid'})
Location	: $location
Service		: $device_info{'service.nickname'} ($device_info{'service.serviceid'})
Service Status	: $device_info{'service.status'}
Device		: $device_info{'service.name'} ($device_info{'service.deviceid'})
Device Status	: $device_info{'device.status'}
EOF
	# Show the user what we know
	print STDERR $body;

	# Prepare to send a ticket and e-mail
	$client_id	= $device_info{'client.clientid'};
	$service_id	= $device_info{'service.serviceid'};
	$service_status	= lc($device_info{'service.status'});
	$datacenter	= $device_info{'service.datacenter'};
	$ticket_body	= $body;


	# Non-Online states, no need to check
	if( $service_status eq 'provision'	||
	    $service_status eq 'deprovision'	||
	    $service_status eq 'reclaim'	||
	    $service_status eq 'offline'	||
    	    ($service_status eq '' && $taskfile eq 'reclaim') )
	{
		print STDERR "[ii] Service status is '" . uc($service_status) . "' in Ocean, " .
			"skipping additional checks.\n";
		post_status('verify_done');
		exit(0);
	}

	# Online states
	if( $service_status eq 'active' && $service_id eq '' ) {
		print STDERR "                 !! WARNING !! WARNING !! WARNING !!                 \n";
		print STDERR "   DEVICE NOT LINKED TO A SERVICE BUT IS IN ACTIVE STATUS IN OCEAN   \n";
	}

	my @emails = ('pa-dev@peer1.com');
	if( lc($dc) =~ /lax1/ ) {
		# LAX1 = ladcops@peer1.com
		push(@emails, 'ladcops@peer1.com');
	} elsif( lc($dc) =~ /ldn1/ ) {
		# LDN1 = ukdcops@peer1.com
		push(@emails, 'ukdcops@peer1.com');
	} elsif( lc($dc) =~ /iad2/ ) {
		# IAD2 = iad-staff@peer1.com, ryoungman@peer1.com
		push(@emails, 'iad-staff@peer1.com');
		push(@emails, 'ryoungman@peer1.com');
	} elsif( lc($dc) =~ /sat5/ ) {
		# SAT5 = sat-dcops-staff@peer1.com, rblevins@peer1.com, bpearson@peer1.com
		push(@emails, 'sat-dcops-staff@peer1.com');
		push(@emails, 'rblevins@peer1.com');
		push(@emails, 'bpearson@peer1.com');
	} elsif( lc($dc) =~ /tor/ ) {
		# TOR = jvalladares@peer1.com
		push(@emails, 'jvalladares@peer1.com');
	}

	# Create ticket in appropriate DC queue and send e-mails
	createTicket($client_id, $service_id, $ticket_body, $datacenter);
	foreach my $email (@emails) {
		print STDERR "[ii] Emailing $email\n";
		createEmail($email, $ticket_body);
	}
}

sub createEmail {
	my ($to, $body) = @_;
	my $subject = "Running $taskfile, need confirmation";
	my $email_json = sprintf(
'{
"inputs":
	{
	"name": "Email",
	"cachable": "f",
	"parameter":
	[
		{ "name": "recipient", "value": "%s" },
		{ "name": "from", "value": "pa-dev@peer1.com" },
		{ "name": "subject", "value": "%s" },
		{ "name": "body", "value": "%s" }
	]
	}
}', $to, $subject, $body);

	# Replace actual newlines with a newline char
	my $newline = '\n';
	$email_json =~ s/\n/$newline/g;

	open(EMAIL, '>/tmp/email.txt');
	print EMAIL $email_json;
	close(EMAIL);

	# Prepare curl
	my $opts  = $curl_opts;
	   $opts .= "-X POST ";
	   $opts .= join(" ", @headers)." ";
	   $opts .= "-d @/tmp/email.txt";
	my $url   = "https://dcc.$dc/widgets/communications/email";

	my $cmd = "curl $opts $url 2> /dev/null";
	my $result = `$cmd`;

	chomp($result);
	return $result;
}

sub createTicket {
	my ($clientid, $serviceid, $content, $datacenter) = @_;
	my $subject = "Running $taskfile, need confirmation";
	my $ticket_json = sprintf(
'{
"inputs":
	{
	"name": "Create DCO Ticket",
	"cachable": "f",
	"parameter":
	[
		{ "name": "client id", "value": "%s" },
		{ "name": "service id", "value": "%s" },
		{ "name": "dc", "value": "%s" },
		{ "name": "subject", "value": "%s" },
		{ "name": "message", "value": "%s" }
	]
	}
}', $clientid, $serviceid, $datacenter, $subject, $content);

	# Replace actual newlines with a newline char
	my $newline = '\n';
	$ticket_json =~ s/\n/$newline/g;

	open(TICKET, '>/tmp/ticket.txt');
	print TICKET $ticket_json;
	close(TICKET);

	if( $clientid eq '' || $serviceid eq '' ) {
		return;
	}

	# Prepare curl
	my $opts  = $curl_opts;
	   $opts .= "-X POST ";
	   $opts .= join(" ", @headers)." ";
	   $opts .= "-d @/tmp/ticket.txt";
	my $url   = "https://dcc.$dc/widgets/workflows/run?workflow_id=1006";

	my $cmd = "curl $opts $url 2> /dev/null";
	my $result = `$cmd`;

	chomp($result);
	return $result;
}

sub preverify() {
	$SIG{INT}  = "preverify";
	$SIG{TSTP} = "preverify";

	my @sayings = (
		"If only it was that easy...",
		"The cow says mooo",
		"Wrong move, try again",
		"What are you doing, Dave?");
	my $saying = $sayings[int(rand(@sayings))];

	system("clear");
	print STDERR "\n";
	print STDERR "[!!] $saying\n";

	verify();
}

sub verify {
	my $access_approved = 0;
	post_status('verify_action');
	while( !$access_approved ) {
		print "AUTHORIZATION REQUIRED TO CONTINUE:\n";
		print "Corp username: ";
		$username = <STDIN>;
		chomp($username);

		print "Corp password: ";
		system "stty -echo";
		$password = <STDIN>;
		chomp($password);

		system "stty echo";
		my $url = "https://dcc.$dc/datacenter/devices/$device_id/events";
		my @headers = ( "-H 'Accept: text/xml'" );
		my $opts  = "-iku '$username:$password' ";
		   $opts .= "-X POST -s --globoff ";
		   $opts .= join(" ", @headers)." ";
		   $opts .= "-d '<event>Running task $taskfile</event>' ";
	
		my @output = `curl $opts $url 2> /dev/null`;
		$http_status = shift( @output );

		if( $http_status =~ /HTTP\/1\.1 200 OK/ ) {
			print STDERR "[ii] Access Granted, continuing on.\n";
			$access_approved = 1;
			post_status('verify_done');
			exit(0);
		} else {
			print STDERR "[xx] Access failed, please try again\n\n";

			sleep(1);
			system("clear");
		}
	}
}

sub post_status {
	my( $status ) = @_;
	# make curl PUT
	my $macs = getMacs();
	my @mac_list = split(/,/, $macs);
	foreach( @mac_list ) {
		my $url = "http://ks1.$dc/cgi-bin/register.cgi";
		my $opts  = "-X POST -s --globoff ";
		   $opts .= "-d 'macaddr=$_&status=$status'";
		my $cmd = "curl $opts $url 2> /dev/null";
		#print "$cmd\n";
		my @output = `$cmd`;
		#print @output;
	}
}

####################
## Initialization ##
####################

# MAC Address
$macs = &getMacs;
if( $macs eq '' ) {
	print STDERR "[xx] Could not find MAC address, is eth0 up?\n";
	$macs = "                 ";
}

$banner = <<END;
#####################################################################
##                                                                 ##
##                             WARNING                             ##
##          This system is about to run a destructive task         ##
##                                                                 ##
##        YOUR AUTHORIZATION IS REQUIRED FOR THIS OPERATION!       ##
##                                                                 ##
##                                                                 ##
#####################################################################
END

# Init
our ($dc, $macs, $banner, $taskfile, $device_id, $service_id, $client_id, $ticket_body, $datacenter);

# Get the Kickstart server we are dealing with
# option domain-name "kslan.dmy0.dev.peer1.com";
$dc = `grep 'kslan.' /etc/resolv.conf | cut -d . -f 2-`;
chomp($dc);
$dc =~ s/\";//;

post_status('verifying');

# The taskfile that executed this verify script
$taskfile = lc($ARGV[0]);

if ( !defined($taskfile) or $taskfile eq '' ) {
	$taskfile = "none";
}

# Global curl options
our $curl_opts = "--globoff -s --connect-timeout 30 -ku 'inewton:xxxxPb2Auxxxx' ";
our @headers = ('-H', '\'Accept: application/perl\'','-H', '\'Content-Type: application/json\'' );

our %device_info = ();
$device_info{'client.clientid'}		= "";
$device_info{'client.name'}		= "";
$device_info{'service.serviceid'}	= "";
$device_info{'service.nickname'}	= "";
$device_info{'service.status'}		= "";
$device_info{'service.name'}		= "";
$device_info{'service.deviceid'}	= "";
$device_info{'service.datacenter'}	= "";
$device_info{'device.status'}		= "";
$device_info{'location.building'}	= "";
$device_info{'location.floor'}		= "";
$device_info{'location.row'}		= "";
$device_info{'location.cabinet'}	= "";
$device_info{'location.slot'}		= "";


# Do not allow anyone to bypass the script
$SIG{INT}  = "preverify";
$SIG{TSTP} = "preverify";

##########
## Main ##
##########
# Make the user aware verify is being called
#$taskfile ne "None" and print "$banner";
print "$banner" if ($taskfile ne "none" and $taskfile ne "audit");
print "============================ ACTION =================================\n";
print "[ii] $taskfile\n";
print "[ii] Mac Addresses: $macs\n";
print "===================== SERVER INFORMATION ============================\n";
print STDERR "[xx] No task detected, are we not in a taskfile?\n" if ($taskfile eq "none");
if($taskfile eq "audit") {
	print STDERR "[ii] Audit task detected, skipping checks.\n";
	post_status('verify_done'); 
	exit(0);
}
lookup();
print "============================ END ====================================\n\n\n";
verify();
