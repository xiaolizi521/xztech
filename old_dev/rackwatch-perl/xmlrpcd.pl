#!/usr/bin/perl

sub wget {
	
	
	my $object = "OpenNMS%3AName%3DXmlrpcd";
	my $location = "invoke?objectname=$object&operation=$_[0]";
	my $url = "http://manager:manager\@localhost:8181/$location";
	my $cmd = "wget --proxy=off -O /tmp/invoke.tmp \"$url\" 2>&1";
	
	open (TMP,"</tmp/invoke.tmp");
	
	my $temp = `$cmd`;
	
	my @tmp = <TMP>;

	my $return;
	
	if ($tmp[1] =~ m/\"success\"/i) {
	
		$return = "The operation to $_[0] xmlrpcd succeeded!\n";
	}

	else { 
	
		if ($_[0] == "stop") { 
	
			$return = "The operation to stop the xmlrpc daemon failed.\n";
			$return .= "This could be due to the daemon already having been stopped.\n";
			$return .= "Please watch for success on init or startup.\n";
		}
		
		else {
		
			$return = "The operation to $_[0] the xmlrpc daemon failed.\n";
		}
	
	}
	close (TMP);
	return $return;
}

sub sendpage {

	my($to,$body);
	
	($to,$body) = @_;
	
	print "Sending mail. \n";
	open(SENDMAIL, "|/usr/sbin/sendmail -t");
	
	print SENDMAIL "To: $to\n";
	print SENDMAIL "From: rwdev1@iad1.corp.rackspace.com\n";
	print SENDMAIL "Subject: CRITICAL! Poller XMLRPCD Malfunction.\n";
	print SENDMAIL "$body\n";
	
	close(SENDMAIL);
	print "Mail sent. \n";
	
}

while(true) {

	open FILE, "<", "/var/log/opennms/daemon/xmlrpcd.log" or die "Unable to open XML RPC Log File";

	print "Verifying Log File.\n";	
	@lines = <FILE>;
	
	for ($x = scalar(@lines); $x > (scalar(@lines) - 20); $x--) {
	
		if ($lines[$x] =~ m/removed from event queue/) {
				
				if(!$matched) {

					$matched = 1;
					
					$body = "We have found an error with the XML RPC Daemon.\n";
					$body .= "Primary Action will be taken.\n";
					$body .= "Executing XML RPC Daemon Restart Call...\n\n";
					
					$body .= &wget("stop");
					$body .= &wget("init");
					$body .= &wget("start");
					
					$body .= "\nWe have completed restarting the XML RPC daemon.\n\n";
					$body .= "Please verify that OpenNMS is speaking to CORE";
					$body .= "properly and that there are no further issues.\n\n";

				
					&sendpage("coresysadmin\@rackspace.com,page.adam.hubscher\@rackspace.com",$body);
					
			}
	
	
		}
	
	}
	
	print "Verified Log File.\n";
	if (!$matched) { print "No issues found. Sleeping for 60 seconds.\n"; }
	close FILE;
	
	$matched = 0;
	
	#sleeping for 60 seconds
	sleep 60;
}

