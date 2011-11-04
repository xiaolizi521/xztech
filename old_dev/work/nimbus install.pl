#!/usr/bin/perl -w 
# this is a new installer script just for the installation of Nimbus
# for Rackspace Intensive level monitored customrs.

use strict;

#vars
	my $MAIN_DATACENTER = undef;
	my $MAIN_IPADDRESS = undef;
	my $MAIN_CUSTOMERNUMBER = undef;
	my $MAIN_SERVERNUMBER = undef;
	my $MAIN_NIMBUS_PROG = "./nimldr";
	
# end vars

	print "Preparing to install nimbus please answer the following questions\n\n";
	$MAIN_DATACENTER = DATACENTER_VERIFY();
	$MAIN_IPADDRESS = VERIFY_IPADDRESS();
	$MAIN_CUSTOMERNUMBER = GET_ACCOUNT_NUMBER();
	$MAIN_SERVERNUMBER = GET_SERVER_NUMBER();
#ALIAS_IP($MAIN_IPADDRESS);
	NIMBUS($MAIN_DATACENTER, $MAIN_NIMBUS_PROG, $MAIN_IPADDRESS, $MAIN_CUSTOMERNUMBER, $MAIN_SERVERNUMBER);

	print "DATACENTER = $MAIN_DATACENTER\n\n";
	print "SERVERNUMBER = $MAIN_SERVERNUMBER\n\n";
	print "CUSTOMERNUMBER = $MAIN_CUSTOMERNUMBER\n\n";
	print "IPADDRESS = $MAIN_IPADDRESS\n\n";
	exit;


sub GET_ACCOUNT_NUMBER {

	# Print Menu and pass back option
	
	my $ACCOUNT_MENU_ANSWER = undef;
	my $ACCOUNT_MENU_LOOP = 0;
	my $ACCOUNT_MENU_RETURN = undef;
	
	while ( $ACCOUNT_MENU_LOOP == 0 ){
#		system "clear";
		print "\n Please select ACCOUNT\n";
		print "\t 1) CORE Production/Cert (2673)\n";
		print "\t 2) CORE Test (800326)\n";
		print "\t 3) CORE Dev\n (686347)";
		print "\t 4) Corporate Infrastructure Linux (616831)\n";
		print "So whats your answer:";
		chomp ( $ACCOUNT_MENU_ANSWER = <STDIN> );
		if ( $ACCOUNT_MENU_ANSWER < 1 or $ACCOUNT_MENU_ANSWER > 4) 
		{
			print "\nInvalid choice.... Please try again!!! :(\n";
		} else 
		{
			$ACCOUNT_MENU_LOOP = "1";
		}
	}
	#need to do if else on 1 - 4 to return DC
	if ( $ACCOUNT_MENU_ANSWER == "1") 
	{
		$ACCOUNT_MENU_RETURN = "2673";
	} elsif ( $ACCOUNT_MENU_ANSWER == "2")
	{ 
		$ACCOUNT_MENU_RETURN = "800326";
	} elsif ( $ACCOUNT_MENU_ANSWER == "3")
	{ 
		$ACCOUNT_MENU_RETURN = "686347";
	} elsif ( $ACCOUNT_MENU_ANSWER == "4")
	{ 
		$ACCOUNT_MENU_RETURN = "616831";
	}else
	{
		$ACCOUNT_MENU_RETURN = "-1";
	}

	return $ACCOUNT_MENU_RETURN;
}

sub GET_SERVER_NUMBER {

	my $SERVER_NUMBER_STDIN_LOOP =0;
	my $SERVER_NUMBER = undef;
	my $SERVER_NUMBER_STDIN_ANSWER = undef;
	
	while ( $SERVER_NUMBER_STDIN_LOOP == 0 ) {
	
		print "\n\t Please enter the CORE Server Number for this device:";
		chomp ( $SERVER_NUMBER = <STDIN> );
		print "\n\t Is this server number \"$SERVER_NUMBER\" correct? [y/n]:";
		$SERVER_NUMBER_ANSWER = READ_YES_NO();
		
		if ( $SERVER_NUMBER_ANSWER !~ /^n/i ) {
			
			$SERVER_NUMBER_STDIN_LOOP =1;
		}
	}
	
	return $SERVER_NUMBER;
}

sub VERIFY_IPADDRESS {

	#Function verifies the  IP address with the user
	my $VERIFY_IPADDRESS_PUB_IP = GET_IPADDRESS();
	my $VERIFY_IPADDRESS_LOOP = 0;
	my $VERIFY_IPADDRESS_ANSWER = "n";
	
	if ($VERIFY_IPADDRESS_PUB_IP != "-1" ) 
	{
#		system "clear";
		print "\n\n\n\t IP of server appears to be $VERIFY_IPADDRESS_PUB_IP is this correct[y/N]:";
		$VERIFY_IPADDRESS_ANSWER = READ_YES_NO();
		if ($VERIFY_IPADDRESS_ANSWER =~ /n/i )
		{
			$VERIFY_IPADDRESS_PUB_IP = IPADDRESS_STDIN();
		} 
	}else
	{
		$VERIFY_IPADDRESS_PUB_IP = IPADDRESS_STDIN();
	}
		


	return $VERIFY_IPADDRESS_PUB_IP;
} #END VERIFY_IPADDRESS

sub IPADDRESS_STDIN {

	my $IPADDRESS_STDIN_LOOP = 0;
	my $IPADDRESS_STDIN_PUB_IP = undef;
	my $IPADDRESS_STDIN_ANSWER = undef;
	while( $IPADDRESS_STDIN_LOOP == 0 ) {

		print "\n\t Please enter the IP of this server:";
		chomp( $IPADDRESS_STDIN_PUB_IP = <STDIN> );
		print "\n\t Is this ip \"$IPADDRESS_STDIN_PUB_IP\" correct? [y/n]:";
		$IPADDRESS_STDIN_ANSWER = READ_YES_NO();
		if ( $IPADDRESS_STDIN_ANSWER !~ /^n/i ) {
			$IPADDRESS_STDIN_LOOP = 1; 
		}
	}
	return $IPADDRESS_STDIN_PUB_IP;
		
} # END IPADDRESS_STDIN

sub GET_IPADDRESS {
	# this function will grab the ipaddress off of server that repliactes 
	# whatismyip.com
	
	my $GET_IPADDRESS_PUB_IP = undef;
	
	$GET_IPADDRESS_PUB_IP = `ifconfig | grep "inet addr:" | awk '{while(tot != 1){print $2; tot=1; }}' | sed s/addr://`

	return $GET_IPADDRESS_PUB_IP;


} # END GET_IPADDRESS

sub READ_FILE {

	#Function reads a file passed to it and then returns its contents
	#returns a -1 if the file is not found or cant be read

	my $READ_FILE_FILE_TO_READ = $_[0];
	my $READ_FILE_FILE_CONTENTS = undef;

	if ( -f $READ_FILE_FILE_TO_READ ) {
		open ( READ_FILE_FD, "<$READ_FILE_FILE_TO_READ" ) or return "-1" ;
		chomp ( $READ_FILE_FILE_CONTENTS = <READ_FILE_FD> );
		close ( READ_FILE_FD );
	}else {
		$READ_FILE_FILE_CONTENTS = "-1";
	}

	return $READ_FILE_FILE_CONTENTS;

} # end READ_FILE



sub READ_YES_NO {
	
	my $READ_YES_NO_LOOP = "0";
	my $READ_YES_NO_STDIN = undef;
	
	while ( $READ_YES_NO_LOOP == "0"){

		chomp ( $READ_YES_NO_STDIN = <STDIN> );

		if ( $READ_YES_NO_STDIN =~ /y/i ){
			return "y";
		} elsif ( $READ_YES_NO_STDIN =~ /n/i ) {
			return "n";
		} else {
			print "\tPlease enter [y/n]";
		}
		
	}
}
	
sub DATACENTER_MENU {
	# Print Menu and pass back option
	
	my $DATACENTER_MENU_ANSWER = undef;
	my $DATACENTER_MENU_LOOP = 0;
	my $DATACENTER_MENU_RETURN = undef;
	
	while ( $DATACENTER_MENU_LOOP == 0 ){
#		system "clear";
		print "\n Please select datacenter\n";
		print "\t 1) SAT\n";
		print "\t 2) IAD\n";
		print "\t 3) LON\n";
		print "\t 4) DFW\n";
		print "So whats your answer:";
		chomp ( $DATACENTER_MENU_ANSWER = <STDIN> );
		if ( $DATACENTER_MENU_ANSWER < 1 or $DATACENTER_MENU_ANSWER > 4) 
		{
			print "\nInvalid choice.... Please try again!!! :(\n";
		} else 
		{
			$DATACENTER_MENU_LOOP = "1";
		}
	}
	#need to do if else on 1 - 4 to return DC
	if ( $DATACENTER_MENU_ANSWER == "1") 
	{
		$DATACENTER_MENU_RETURN = "SAT";
	} elsif ( $DATACENTER_MENU_ANSWER == "2")
	{ 
		$DATACENTER_MENU_RETURN = "IAD";
	} elsif ( $DATACENTER_MENU_ANSWER == "3")
	{ 
		$DATACENTER_MENU_RETURN = "LON";
	} elsif ( $DATACENTER_MENU_ANSWER == "4")
	{ 
		$DATACENTER_MENU_RETURN = "DFW";
	}else
	{
		$DATACENTER_MENU_RETURN = "-1";
	}

	return $DATACENTER_MENU_RETURN;
	
} #end DATACENTER_MENU

sub DATACENTER_VERIFY {

	my $DATACENTER_VERIFY_ANSWER = undef;
	
	$DATACENTER_VERIFY_FILE_INPUT = DATACENTER_MENU();
		
	return $DATACENTER_VERIFY_FILE_INPUT;


} #END DATACENTER_VERIFY

sub NIMBUS {


		my $NIMBUS_DATACENTER = $_[0];
		my $NIMBUS_PROG = $_[1];
    	my $NIMBUS_IPADDR = $_[2];
		my $NIMBUS_CUSTOMER_NUMBER = $_[3];
		my $NIMBUS_SERVER_NUMBER = $_[4];



       #######################################################
       ##<NOTE> All Spelling mistakes are on purpose
       #######################################################



       #DFW
       #my $NIMBUS_DFWHUB01DOM="Enterprise";
       my $NIMBUS_DFWHUB01IP="127.0.0.1";
       my $NIMBUS_DFWHUB01NAME="DFWHUB01";
       my $NIMBUS_DFWHUB01ROBOT="dfwnimbushub01";
       #my $NIMBUS_DFWHUB02DOM = "Enterprise";
       #my $NIMBUS_DFWHUB02NAME = "DFWHUB02";
       #my $NIMBUS_DFWHUB02ROBOT = "dfwnimbushub02";
       #my $NIMBUS_DFWHUB02IP = "127.0.0.1";
       #my $NIMBUS_DFWHUB02PORT = "48002";
       #IAD
       #my $NIMBUS_IADHUB01DOM = "Enterprise";
       my $NIMBUS_IADHUB01IP="127.0.0.1";
       my $NIMBUS_IADHUB01NAME="IADHUB01";
       my $NIMBUS_IADHUB01ROBOT="iadnimbushub01";
       #my $NIMBUS_IADHUB02DOM = "Enterprise";
       #my $NIMBUS_IADHUB02NAME = "IADHUB02";
       #my $NIMBUS_IADHUB02ROBOT = "iadnimbushub02";
       #my $NIMBUS_IADHUB02IP = "127.0.0.1";
       #my $NIMBUS_IADHUB02PORT = "48002";
       #LON
       #my $NIMBUS_LONHUB01DOM = "Enterprise";
       my $NIMBUS_LONHUB01IP="127.0.0.1";
       my $NIMBUS_LONHUB01NAME="LONHUB01";
       my $NIMBUS_LONHUB01ROBOT="lonnimbushub01";
       #my $NIMBUS_LONHUB02DOM = "Enterprise";
       #my $NIMBUS_LONHUB02NAME = "LONHUB02";
       #my $NIMBUS_LONHUB02ROBOT = "lonnimbushub02";
       #my $NIMBUS_LONHUB02IP = "127.0.0.1";
       #my $NIMBUS_LONHUB02PORT = "48002";
       #SAT
       #my $NIMBUS_SATHUB01DOM = "Enterprise";
       my $NIMBUS_SATHUB01IP="10.1.15.44";
       my $NIMBUS_SATHUB01NAME="SAT1MN01";
       my $NIMBUS_SATHUB01ROBOT="satnimbushub01";
       #my $NIMBUS_SATHUB02DOM = "Enterprise";
       #my $NIMBUS_SATHUB02NAME = "SATHUB02";
       #my $NIMBUS_SATHUB02ROBOT = "satnimbushub02";
       #my $NIMBUS_SATHUB02IP = "127.0.0.1";
       #my $NIMBUS_SATHUB02PORT = "48002";

       my $NIMBUS_ROBOT_CONFIG = "/opt/nimbus/robot/robot.cfg";
       #my $NIMBUS_IPFILE="/boot/.rackspace/public_ip";
       my $NIMBUS_COMMAND = undef;
       my $NIMBUS_REQUESTCFG = "/opt/nimbus/request.cfg";
       my $NIMBUS_SNMPCONFIG = "/etc/snmp/snmpd.conf";

       #end vars

       #setup vars to write out configureation files.

               my $NIMBUS_HUBDOM = undef;
               my $NIMBUS_HUBNAME = undef;
               my $NIMBUS_HUBIP = undef;
               my $NIMBUS_HUBROBOT = undef;
               my $NIMBUS_SECDOM = undef;
               my $NIMBUS_SECHUBNAME = undef;
               my $NIMBUS_SECHUBIP = undef;
               my $NIMBUS_SECHUBROBOT = undef;
               my $NIMBUS_SECHUBPORT = undef;

       if ( $NIMBUS_DATACENTER =~ /DFW/i ){
               print "Setting Nimbus up for DFW.\n";
               $NIMBUS_HUBDOM = $NIMBUS_DFWHUB01DOM;
               $NIMBUS_HUBNAME = $NIMBUS_DFWHUB01NAME;
               $NIMBUS_HUBIP = $NIMBUS_DFWHUB01IP;
               $NIMBUS_HUBROBOT = $NIMBUS_DFWHUB01ROBOT;
               #$NIMBUS_SECDOM = $NIMBUS_DFWHUB02DOM;
               #$NIMBUS_SECHUBNAME = $NIMBUS_DFWHUB02NAME;
               #$NIMBUS_SECHUBIP = $NIMBUS_DFWHUB02IP;
               #$NIMBUS_SECHUBROBOT = $NIMBUS_DFWHUB02ROBOT;
               #$NIMBUS_SECHUBPORT = $NIMBUS_DFWHUB02PORT;


       }elsif ( $NIMBUS_DATACENTER =~ /IAD/i ) {
               print "Setting Nimbus up for IAD.\n";
               $NIMBUS_HUBDOM = $NIMBUS_IADHUB01DOM;
               $NIMBUS_HUBNAME = $NIMBUS_IADHUB01NAME;
               $NIMBUS_HUBIP = $NIMBUS_IADHUB01IP;
               $NIMBUS_HUBROBOT = $NIMBUS_IADHUB01ROBOT;
               #$NIMBUS_SECDOM = $NIMBUS_IADHUB02DOM;
               #$NIMBUS_SECHUBNAME = $NIMBUS_IADHUB02NAME;
               #$NIMBUS_SECHUBIP = $NIMBUS_IADHUB02IP;
               #$NIMBUS_SECHUBROBOT = $NIMBUS_IADHUB02ROBOT;
               #$NIMBUS_SECHUBPORT = $NIMBUS_IADHUB02PORT;

       }elsif ( $NIMBUS_DATACENTER =~ /LON/i) {
               print "Setting Nimbus up for LON.\n";
               $NIMBUS_HUBDOM = $NIMBUS_LONHUB01DOM;
               $NIMBUS_HUBNAME = $NIMBUS_LONHUB01NAME;
               $NIMBUS_HUBIP = $NIMBUS_LONHUB01IP;
               $NIMBUS_HUBROBOT = $NIMBUS_LONHUB01ROBOT;
               #$NIMBUS_SECDOM = $NIMBUS_LONHUB02DOM;
               #$NIMBUS_SECHUBNAME = $NIMBUS_LONHUB02NAME;
               #$NIMBUS_SECHUBIP = $NIMBUS_LONHUB02IP;
               #$NIMBUS_SECHUBROBOT = $NIMBUS_LONHUB02ROBOT;
               #$NIMBUS_SECHUBPORT = $NIMBUS_LONHUB02PORT;

       }elsif ( $NIMBUS_DATACENTER =~ /SAT/i) {
               print "Setting Nimbus up for SAT.\n";
               $NIMBUS_HUBDOM = $NIMBUS_SATHUB01DOM;
               $NIMBUS_HUBNAME = $NIMBUS_SATHUB01NAME;
               $NIMBUS_HUBIP = $NIMBUS_SATHUB01IP;
               $NIMBUS_HUBROBOT = $NIMBUS_SATHUB01ROBOT;
               #$NIMBUS_SECDOM = $NIMBUS_SATHUB02DOM;
               #$NIMBUS_SECHUBNAME = $NIMBUS_SATHUB02NAME;
               #$NIMBUS_SECHUBIP = $NIMBUS_SATHUB02IP;
               #$NIMBUS_SECHUBROBOT = $NIMBUS_SATHUB02ROBOT;
               #$NIMBUS_SECHUBPORT = $NIMBUS_SATHUB02PORT;


       }else {
               print "ERROR: $NIMBUS_DATACENTER is not a valid option.\n";
               exit (1);
       }


   # real work is going to get done here
		system ( "mkdir -p /opt/nimbus/robot/" );

       #writing /opt/nimbus/robot/robot.cfg
       system ( "echo \"<controller>\n\" > $NIMBUS_ROBOT_CONFIG");
       system ( "echo \"    domain = RACKSPACE\n\" >> $NIMBUS_ROBOT_CONFIG");
       system ( "echo \"    hub = $NIMBUS_HUBNAME\n\" >> $NIMBUS_ROBOT_CONFIG");
       system ( "echo \"    hubip = $NIMBUS_HUBIP\n\" >> $NIMBUS_ROBOT_CONFIG");
       system ( "echo \"    hubrobotname = $NIMBUS_HUBROBOT\n\" >> $NIMBUS_ROBOT_CONFIG");
       system ( "echo \"    hubport = 48002\n\" >> $NIMBUS_ROBOT_CONFIG");
       system ( "echo \"    access_0 = 0\n\" >> $NIMBUS_ROBOT_CONFIG");
       system ( "echo \"    access_1 = 1\n\" >> $NIMBUS_ROBOT_CONFIG");
       system ( "echo \"    access_2 = 2\n\" >> $NIMBUS_ROBOT_CONFIG");
       system ( "echo \"    access_3 = 3\n\" >> $NIMBUS_ROBOT_CONFIG");
       system ( "echo \"    access_4 = 4\n\" >> $NIMBUS_ROBOT_CONFIG");
       system ( "echo \"    robotname = $NIMBUS_CUSTOMER_NUMBER-$NIMBUS_SERVER_NUMBER\n\" >> $NIMBUS_ROBOT_CONFIG");
       system ( "echo \"    robotip_alias = $NIMBUS_IPADDR\n\" >> $NIMBUS_ROBOT_CONFIG");
       #system ( "echo \"    secondary_domain = $NIMBUS_SECDOM\n\" >> $NIMBUS_ROBOT_CONFIG");
       #system ( "echo \"    secondary_hub = $NIMBUS_SECHUBNAME\n\" >> $NIMBUS_ROBOT_CONFIG");
       #system ( "echo \"    secondary_hubrobotname = $NIMBUS_SECHUBROBOT\n\" >> $NIMBUS_ROBOT_CONFIG");
       #system ( "echo \"    secondary_hubport = $NIMBUS_SECHUBPORT\n\" >> $NIMBUS_ROBOT_CONFIG");
       #system ( "echo \"    secondary_hubip = $NIMBUS_SECHUBIP\n\" >> $NIMBUS_ROBOT_CONFIG");
       #system ( "echo \"    loglevel = 0\n\" >> $NIMBUS_ROBOT_CONFIG");
       #system ( "echo \"    autoremove = no\n\" >> $NIMBUS_ROBOT_CONFIG");
       #system ( "echo \"    suspend_on_loopback_only = no\n\" >> $NIMBUS_ROBOT_CONFIG");
       #system ( "echo \"    hub_update_interval = 900\n\" >> $NIMBUS_ROBOT_CONFIG");
       #system ( "echo \"    temporary_hub_broadcast = no\n\" >> $NIMBUS_ROBOT_CONFIG");
       #system ( "echo \"    system_uptime_qos = no\n\" >> $NIMBUS_ROBOT_CONFIG");
       #system ( "echo \"    unmanaged_security = domain_locked\n\" >> $NIMBUS_ROBOT_CONFIG");
       #system ( "echo \"    set_qos_source = yes\n\" >> $NIMBUS_ROBOT_CONFIG");
       #system ( "echo \"    first_probe_port = 48003\n\" >> $NIMBUS_ROBOT_CONFIG");
       #system ( "echo \"    os_user1 =$NIMBUS_CUSTOMER_NUMBER \n\" >> $NIMBUS_ROBOT_CONFIG");
       system ( "echo \"</controller>\" >> $NIMBUS_ROBOT_CONFIG");



       #building the command to run


       $NIMBUS_COMMAND = "echo \"1\n/opt/nimbus/tmp\nno\nyes\nEnterprise\n$NIMBUS_HUBNAME\ninstall_linux\nrobotsetup\nzxwsus\n/opt/nimbus\nEnterprise\n\" | $NIMBUS_PROG -I $NIMBUS_HUBIP";

       system("$NIMBUS_COMMAND");

       system("/etc/init.d/nimbus stop");
		sleep 10;


       #writing /opt/nimbus/request.cfg


       system ( "echo \"<distribution request>\n\" > $NIMBUS_REQUESTCFG");
       system ( "echo \"packages = Intensiveprobes\n\" >>  $NIMBUS_REQUESTCFG");
       system ( "echo \"</distribution request>\" >> $NIMBUS_REQUESTCFG");


       #writing of the config files is all done starting nimbus up

       system ( "/etc/init.d/nimbus start");

} # END OF SUB NIMBUS