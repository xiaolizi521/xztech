#!/usr/bin/perl -w
#
BEGIN {
    use lib '/exports/kickstart/lib';
    require 'sbks.pm';
}
 
use strict;
use LWP::Simple;
use LWP::UserAgent;
use XML::Simple;
use CGI ':cgi-lib';
use CGI ':standard';
use Data::Dumper;
use POSIX;

print header();

my $post = new CGI;
my $postdata = $post->Vars();
my $macaddr = untaint('macaddr', $postdata->{'macaddr'});

#$macaddr = '00:25:90:04:f1:5a';
kslog("info", "[$macaddr] Partition Master called for $macaddr, gathering ingredients for recipe");

my $defaults = {};

# Ubuntu (All Versions)
$defaults->{'Ubuntu.+10'} = '<drives><drive><partitions><partition><mount>/boot</mount><size>100</size><format>ext3</format><label>boot</label><lvm>false</lvm><bootable>true</bootable></partition><partition><mount>/</mount><size>auto</size><format>ext3</format><label>root</label><lvm>true</lvm><bootable>false</bootable></partition><partition><mount>swap</mount><size>1024</size><format>swap</format><label></label><lvm>true</lvm><bootable>false</bootable></partition></partitions></drive></drives>';

# Debian (All Versions) DO NOT TOUCH WORDPRESS DEBIAN
$defaults->{'Debian.+Lenny'} = '<drives><drive><partitions><partition><mount>/boot</mount><size>100</size><format>ext3</format><label>boot</label><lvm>false</lvm><bootable>true</bootable></partition><partition><mount>/</mount><size>auto</size><format>ext3</format><label>root</label><lvm>true</lvm><bootable>false</bootable></partition><partition><mount>swap</mount><size>1024</size><format>swap</format><label></label><lvm>true</lvm><bootable>false</bootable></partition></partitions></drive></drives>';
$defaults->{'Debian.+Etch'} = '<drives><drive><partitions><partition><mount>/boot</mount><size>100</size><format>ext3</format><label>boot</label><lvm>false</lvm><bootable>true</bootable></partition><partition><mount>/</mount><size>auto</size><format>ext3</format><label>root</label><lvm>false</lvm><bootable>false</bootable></partition><partition><mount>swap</mount><size>1024</size><format>swap</format><label></label><lvm>false</lvm><bootable>false</bootable></partition></partitions></drive></drives>';

# Fedora
$defaults->{'Fedora.+13'} = '<drives><drive><partitions><partition><mount>/boot</mount><size>128</size><format>ext2</format><label>boot</label><lvm>false</lvm><bootable>true</bootable></partition><partition><mount>/</mount><size>auto</size><format>ext3</format><label>root</label><lvm>true</lvm><bootable>false</bootable></partition><partition><mount>swap</mount><size>1024</size><format>swap</format><label></label><lvm>true</lvm><bootable>false</bootable></partition></partitions></drive></drives>';

# RHEL
$defaults->{'RHEL'} = '<drives><drive><partitions><partition><mount>/boot</mount><size>128</size><format>ext2</format><label>boot</label><lvm>false</lvm><bootable>true</bootable></partition><partition><mount>/</mount><size>auto</size><format>ext3</format><label>root</label><lvm>true</lvm><bootable>false</bootable></partition><partition><mount>swap</mount><size>1024</size><format>swap</format><label></label><lvm>true</lvm><bootable>false</bootable></partition></partitions></drive></drives>';

# RHES
$defaults->{'RHES'} = '<drives><drive><partitions><partition><mount>/boot</mount><size>128</size><format>ext2</format><label>boot</label><lvm>false</lvm><bootable>true</bootable></partition><partition><mount>/</mount><size>auto</size><format>ext3</format><label>root</label><lvm>true</lvm><bootable>false</bootable></partition><partition><mount>swap</mount><size>1024</size><format>swap</format><label></label><lvm>true</lvm><bootable>false</bootable></partition></partitions></drive></drives>';

# RHAS
$defaults->{'RHAS'} = '<drives><drive><partitions><partition><mount>/boot</mount><size>128</size><format>ext2</format><label>boot</label><lvm>false</lvm><bootable>true</bootable></partition><partition><mount>/</mount><size>auto</size><format>ext3</format><label>root</label><lvm>true</lvm><bootable>false</bootable></partition><partition><mount>swap</mount><size>1024</size><format>swap</format><label></label><lvm>true</lvm><bootable>false</bootable></partition></partitions></drive></drives>';

# CentOS 5 (LVM)
$defaults->{'CentOS.+5'} = '<drives><drive><partitions><partition><mount>/boot</mount><size>128</size><format>ext2</format><label>boot</label><lvm>false</lvm><bootable>true</bootable></partition><partition><mount>/</mount><size>auto</size><format>ext3</format><label>root</label><lvm>true</lvm><bootable>false</bootable></partition><partition><mount>swap</mount><size>1024</size><format>swap</format><label></label><lvm>lvm</lvm><bootable>false</bootable></partition></partitions></drive></drives>';

# CentOS 4 (NO LVM)
$defaults->{'CentOS.+4'} = '<drives><drive><partitions><partition><mount>/boot</mount><size>128</size><format>ext2</format><label>boot</label><lvm>false</lvm><bootable>true</bootable></partition><partition><mount>/</mount><size>auto</size><format>ext3</format><label>root</label><lvm>false</lvm><bootable>false</bootable></partition><partition><mount>swap</mount><size>1024</size><format>swap</format><label></label><lvm>false</lvm><bootable>false</bootable></partition></partitions></drive></drives>';

# Apparently Windows 2003 in SB only has a single partition
$defaults->{'Windows.+2003.+P1SB'} = '<drives><drive><partitions><partition><mount>C</mount><size>auto</size><format>ntfs</format><label>OS</label><lvm>false</lvm><bootable>true</bootable></partition></partitions></drive></drives>';

# Windows (2008 All, 2003 P1MH + BASE)
$defaults->{'Windows.+2003.+BASE|Windows.+2003.+P1MH|Windows.+2008'} = '<drives><drive><partitions><partition><mount>C</mount><size>51200</size><format>ntfs</format><label>OS</label><lvm>false</lvm><bootable>true</bootable></partition><partition><mount>D</mount><size>auto</size><format>ntfs</format><label>DATA</label><lvm>false</lvm><bootable>false</bootable></partition></partitions></drive></drives>';

#print Dumper(keys(%$defaults));
my $dbh = ks_dbConnect();
my $sth = $dbh->prepare("SELECT ol.osload, oln.osname FROM mac_list ml JOIN xref_macid_osload xmo ON xmo.mac_list_id = ml.id JOIN os_list ol ON ol.id = xmo.os_list_id JOIN os_list_name oln ON oln.id = ol.id WHERE ml.mac_address = ?");
my $results = $sth->execute($macaddr);
if( $results != 1 ) {
	kslog("info", "[$macaddr] No os found for $macaddr, skipping partition recipe building");
	exit;
}

my $osload = $sth->fetchrow_hashref;
my $os = $osload->{'osname'};
my $load = $osload->{'osload'};
$sth = $dbh->prepare("SELECT value as partition_xml FROM postconf p JOIN mac_list ml ON ml.id = p.mac_list_id WHERE p.param = 'PARTITION_XML' AND ml.mac_address = ?");
$results = $sth->execute($macaddr);
my $partition_info = "";
my $partition_xml = "";
if( $results != 1 ) {
	kslog("info", "[$macaddr] No custom recipe provided for $macaddr, checking if we have a default recipe..");
	for my $oskey ( keys(%$defaults) ) {
		#print "Checking $load ($os) against $oskey.... ";
		if( $os =~ m/$oskey/i ) {
			kslog("info", "[$macaddr] Found default recipe for $oskey");
			#print "MATCHED $load!\n";
			$partition_xml = $defaults->{$oskey};
		} else {
			#print "SKIPPED NO MATCH\n";
		}
	}

	if( $partition_xml eq "" ) {
		kslog("info", "[$macaddr] No default recipe found, exiting with no recipe returned");
		exit;
	}
} else {
	kslog("info", "[$macaddr] Found custom recipe for $macaddr, using it");
	$partition_info = $sth->fetchrow_hashref;
	$partition_xml = $partition_info->{'partition_xml'};
}
my $partition_doc = XMLin($partition_xml);
my @partitions = ();
if( ref($partition_doc->{drive}->{partitions}->{partition}) eq 'ARRAY' ) {
	@partitions = @{$partition_doc->{drive}->{partitions}->{partition}};
} else {
	push(@partitions, $partition_doc->{drive}->{partitions}->{partition});
}

if( $os =~ m/Windows/i ) {
	kslog("info", "[$macaddr] Generating Windows compat recipe");
	write_windows_partitions(@partitions);
} elsif( $os =~ m/Debian/i || $os =~ m/Ubuntu/i ) {
	kslog("info", "[$macaddr] Generating Debian compat recipe");
	write_debian_compat_partitions(@partitions);
} elsif( $os =~ m/RHEL/i || $os =~ m/RHES/i || $os =~ m/RHAS/i || $os =~ m/CentOS/i || $os =~ m/Fedora/i ) {
	kslog("info", "[$macaddr] Generating RHEL compat recipe");
	write_redhat_compat_partitions(@partitions);
} else {
        #print "Unknown OS found, skipping custom partitioning\n";
}



#########################################################################
## Windows
##############
sub rewrite_windows_partitions {
	print "REM diskpart.txt created by partition_master.cgi\n";
	print "select disk 0\n";
	print "online disk noerr\n";
	print "select partition 0\n";

}

sub write_windows_partitions {
	my @partitions = @_;
	# DO NOT CHANGE THIS REM, The windows install depends on this to ensure there is a valid diskpart file returned
	print "REM diskpart.txt created by partition_master.cgi\n";
	print "select disk 0\n";
	print "online disk noerr\n";
	print "attributes disk clear readonly noerr\n";
	print "clean\n";
	print "convert mbr noerr\n";
	foreach( @partitions ) {
		my $partition = $_;
		#print "select disk 0\n";
		print "create partition primary";
		if( $partition->{size} ne 'auto' ) {
			print " size=$partition->{size}\n";
		} else {
			print "\n";
		}
		if( $partition->{bootable} eq 'true' ) {
			print "active\n";
		}
		print "format FS=$partition->{format} LABEL=\"$partition->{label}\" QUICK\n";
		print "assign letter=$partition->{mount}\n";
	}
}

#########################################################################
## RHEL/CentOS
##############
sub write_redhat_compat_partitions {
	my @partitions = @_;
	my $has_lvm = 0;

	foreach( @partitions) {
		my $partition = $_;
		if( $partition->{lvm} eq 'true' ) {
			$has_lvm = 1;
		}
	}

	print "zerombr yes\n";
	print "clearpart --all --drives \$drive1 --initlabel\n";
	foreach (@partitions) {
		my $partition = $_;
		if( $partition->{lvm} ne 'true' ) {
			if( $partition->{size} eq 'auto' ) {
				print "partition $partition->{mount} --fstype=$partition->{format} --size=1024 --grow --ondisk \$drive1 --asprimary\n";
			} else {
				print "partition $partition->{mount} --fstype=$partition->{format} --size=$partition->{size} --ondisk \$drive1 --asprimary\n";
			}	
		}
	}
	if( $has_lvm == 1 ) {
		print "partition pv.01 --size=1024 --grow --ondisk \$drive1\n";
		print "volgroup SysVolGroup --pesize=32768 pv.01\n";
		my $volIndex = 0;
		foreach( @partitions ) {
			my $partition = $_;
			if( $partition->{lvm} eq 'true' ) {
				print "logvol $partition->{mount} --fstype=$partition->{format} --vgname=SysVolGroup";
				print " --name=SysVol$volIndex";
				if( $partition->{size} eq 'auto' ) {
					print " --size=1024 --grow\n";
				} else {
					if( $partition->{format} eq 'swap' ) {
						print " --size=$partition->{size} --maxsize=".($partition->{size}*2)."\n";
					} else {
						print " --size=$partition->{size} --maxsize=$partition->{size}\n";
					}
				}
			}
			$volIndex++;
		}
	}
}

#########################################################################
## Debian/Ubuntu 
##############
sub write_debian_compat_partitions {
	my @partitions = @_;
	my $has_lvm = 0;
	foreach (@partitions) {
		my $partition = $_;
		if( $partition->{lvm} eq 'true') {
			$has_lvm = 1;
		}
	}
	my $partition_recipe = "# Generated by partition_master.cgi\n";
	if( $has_lvm == 1 ) {
		$partition_recipe = $partition_recipe."d-i partman-auto/method string lvm\n";
	} else {
		$partition_recipe = $partition_recipe."d-i partman-auto/method string regular\n";
	}

	$partition_recipe = $partition_recipe."d-i partman-auto/expert_recipe string \\\n";
	$partition_recipe = $partition_recipe."\tboot-root :: \\\n";
	foreach (@partitions) {
		#print "Partition\n";
		my $partition = $_;
		if( $partition->{mount} eq '/boot' ) {
			$partition_recipe = $partition_recipe."\t\t$partition->{size} ".($partition->{size} + 100)." $partition->{size} ";
		} else {
			if( $partition->{size} eq 'auto' ) {
				$partition_recipe = $partition_recipe."\t\t500 10000 1000000000 ";	
			} elsif( $partition->{format} eq 'swap' ) {
				$partition_recipe = $partition_recipe."\t\t".($partition->{size}/2)." ".$partition->{size}." 300% ";
			} else {
				$partition_recipe = $partition_recipe."\t\t$partition->{size} $partition->{size} $partition->{size} ";	
			}
		}
		if( $partition->{format} eq 'swap' ) {
			$partition_recipe = $partition_recipe."linux-swap \\\n";
			if( $partition->{lvm} eq 'true' ) {
				$partition_recipe = $partition_recipe."\t\t\t\$lvmok{ } \t\\\n";
			}
			$partition_recipe = $partition_recipe."\t\t\tmethod{ swap } format{ } \\\n\t\t. \\\n";
		} else {
			$partition_recipe = $partition_recipe."$partition->{format} \\\n";
			if( $partition->{bootable} eq 'true' ) {
				$partition_recipe = $partition_recipe."\t\t\t\$primary{ } \$bootable{ } \\\n";
			}
			if( $partition->{lvm} eq 'true' ) {
				$partition_recipe = $partition_recipe."\t\t\t\$lvmok{ } \\\n";
			}
			$partition_recipe = $partition_recipe."\t\t\tmethod{ format } format{ } \\\n";
			$partition_recipe = $partition_recipe."\t\t\tuse_filesystem{ } filesystem{ $partition->{format} } \\\n\t\t\tmountpoint{ $partition->{mount} } \\\n\t\t. \\\n";
		}
	}

	# Strip off the last \ since its not required.
	print substr $partition_recipe, 0, -2;
}
