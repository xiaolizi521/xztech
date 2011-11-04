#!/usr/bin/perl -w

# Name: ldap_grp_audit.pl
# Usage: ldap_grp_audit.pl -g <groupname>
# Purpose: Performs an LDAP search based on group name. Outputs to console and CSV file.
# Useful for fast group auditing.
# Creator: Adam Hubscher
# Date: 1/22/2008

use Getopt::Std;

%options = ();
getopt('g', \%options);

if (defined $options{g}) {
	$group = $options{g};
} else {
     usage();
}

sub usage {
	
	die("Please execute this script using the '-g' switch followed by the group you are questioning.\n IE: 'perl ldap_grp_audit.pl -g core_admins'\n");
	
}	

$filename = $group . ".csv";


my @ldaptest = `ldapsearch -D 'cn=Subschema' -x -h edir1.dfw1.corp.rackspace.com -p 389 -b 'ou=groups,o=rackspace' | grep "cn=im_"`;

foreach(@ldaptest)

{
	$_ =~ s/dn: cn=im_//;
	$_ =~ s/,ou=Groups,o=rackspace//;
	$_ =~ s/UK_M5/UK M5/;
	$_ =~ s/UK M6\)/UK M6/;	
	print "$_";
}

#$csv = $group . ',';

@ldaptest = sort(@ldaptest);

for($a=0; $a<=$#ldaptest; $a++) {
	my $temp = $ldaptest[$a];
#	$ldaptest[$a] =~ s/dn: cn=im_//;
#	$ldaptest[$a] =~ s/,ou=Groups,o=rackspace//;
#	$ldaptest[$a] =~ s/UK_M5/UK M5/;
#	$ldaptest[$a] =~ s/UK M6\)/UK M6/;
	my @userslist = getUsers($ldaptest[$a]);
	$temp =~ s/\n//;
	$csv .= "{sr_group,{\"$temp\",\"jabber.rackspace.com\"},\n[{name,\"$temp\"},\n{displayed_groups,[";
	
	foreach(@ldaptest){
		
		my $v = $_;
		$v =~ s/\n//;
		$csv .= "\"$v\",\n";
	}
	
        $csv =~ s/,+$//;

	$csv .= "]},\n{description,\"$temp\"}]}.\n";
	
	foreach (@userslist)

	{
		my $t = $_;
		$t =~ s/\n//;
		$csv .= "{sr_user,{\"$t\",\"jabber.rackspace.com\"},{\"$temp\",\"jabber.rackspace.com\"}}.\n";
	}
}

#print "$csv\n";
open(MYOUTFILE, ">$filename");

print MYOUTFILE "$csv";

close(MYOUTFILE);

print "File with list of users for group $group has been output to the following file: $filename\n";

sub getUsers {

	my ($usergroup) = @_;
	$usergroup =~ s/\n//;
	my @ldapusers = `ldapsearch -D 'cn=Subschema' -x -h edir1.dfw1.corp.rackspace.com -p 389 -b 'cn=im_$usergroup,ou=Groups,o=rackspace' | grep uniqueMember`;
	foreach(@ldapusers)
	
	{
		$_ =~ s/uniqueMember: cn=//;
		$_ =~ s/,ou=Users,o=rackspace//;
	}
	return @ldapusers;
}
