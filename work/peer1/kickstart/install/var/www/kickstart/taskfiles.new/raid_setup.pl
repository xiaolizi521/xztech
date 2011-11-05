#!/usr/bin/perl

use Data::Dumper;
my @raid_types = ();
my $raid_index = 0;
my $drive_count = 0;
my $drive_pos = 0;
my @tw_cli_cards = ('8006-2LP', '9650SE-2LP', '9650SE-4LPML', '9650SE-8LPML', '9690SA-4I', '9690SA-8I', '9650SE-16ML');
my @megacli_cards = ('8708ELP', '8708EM2', '9260-4i', '9260-8i', '9260-16i');
my @cfggen_cards = ('SAS1064E', 'SAS1068E');
my @sas2ircu_cards = ('SAS2008');
my $raid_controller = "";
my $raid_model = "";
my $drives_available = 0;
my $drive_enclosure = 0;
my @drive_slots = ();

sub ks_register {
	my($mac_address, $status, $ks_url) = @_;
	`curl -s -X POST -d 'macaddr=$mac_address&status=$status' http://$ks_url/cgi-bin/register.cgi`;
}

sub ks_updateks {
	my($mac_address, $osload, $ks_url) = @_;
	`curl -s -X POST -d 'macaddr=$mac_address&osload=$osload' http://$ks_url/cgi-bin/updateks.cgi`;
}

my @configs = `cat /etc/local.sh`;
chomp(@configs);
my $system_configs = {};
foreach( @configs ) {
	my @pieces = split("=", $_);
	$system_configs->{$pieces[0]} = $pieces[1];
}

ks_register($system_configs->{'MACADDR'}, 'hwraid_setup', $system_configs->{'KSIPADDR'});

my @postconf = `curl -s -X POST -d 'macaddr=$system_configs->{'MACADDR'}&update=no' http://$system_configs->{'KSIPADDR'}/cgi-bin/postconf.cgi`;
chomp(@postconf);
my $postconf_configs = {};
foreach( @postconf ) {
	my @pieces = split("=", $_);
	$postconf_configs->{$pieces[0]} = $pieces[1];
}

if( !exists $postconf_configs->{'HW_RAID0_LVL'} ) {
	print "[ERROR] RAID not detected in postconf.\n";
	ks_register($system_configs->{'MACADDR'}, 'hwraid_setup_fail', $system_configs->{'KSIPADDR'});
	exit;
}

while( exists $postconf_configs->{sprintf("HW_RAID%s_LVL", $raid_index)} ) {
	push(@raid_types, sprintf("%s,%s", $postconf_configs->{sprintf("HW_RAID%s_LVL", $raid_index)}, $postconf_configs->{sprintf("HW_RAID%s_NUM_DRIVES", $raid_index)}));
	print "[INFO] Found RAID config for lvindex $raid_index.\n";
	$raid_index++;
}

# Configure the number of RAID cards we have in the system
my $raid_card_count = `lspci | egrep -c -i 'raid|Fusion-MPT'`;
chomp($raid_card_count);
if( $raid_card_count eq '0' ) {
	# Throw error, we need at least 1 raid card
	print "[ERROR] Found 0 RAID cards, exiting.\n";
	ks_register($system_configs->{'MACADDR'}, 'hwraid_setup_fail', $system_configs->{'KSIPADDR'});
	exit;
} elsif( $raid_card_count ne '1' ) {
	# throw error, apparently we can only use 1 raid card.
	print "[ERROR] Found more then 1 RAID card, exiting.\n";
	ks_register($system_configs->{'MACADDR'}, 'hwraid_setup_fail', $system_configs->{'KSIPADDR'});
	exit;
}

# Configure our RAID Vendor
my $raid_vendor = `lspci | egrep -i 'raid|Fusion-MPT' | egrep -o '3ware|LSI|Fusion-MPT|Fusion-MPT SAS-2' | tail -1`;
chomp($raid_vendor);
if( $raid_vendor eq 'Fusion-MPT' ) {
	$raid_vendor = 'LSI-Fusion-MPT';
} elsif ( $raid_vendor eq 'Fusion-MPT SAS-2' ) {
	$raid_vendor = 'LSI-Fusion-MPT2';
}

# Verify we have a valid vendor
if( $raid_vendor eq '3ware' || $raid_vendor eq 'LSI' || $raid_vendor eq 'LSI-Fusion-MPT' || $raid_vendor eq 'LSI-Fusion-MPT2' ) {
	print "[INFO] Found a RAID card by $raid_vendor\n";
} else {
	print "[ERROR] Invalid RAID card vendor found. Vendor found: '$raid_vendor'\n";
	ks_register($system_configs->{'MACADDR'}, 'hwraid_setup_fail', $system_configs->{'KSIPADDR'});
	exit;
}

# Download our RAID utils
# Probe for controller number, can be c0-9 or a0-a9
# Probe for controller model, lspci will not show it
# Check for existing data and blow it away if there
if( $raid_vendor eq '3ware' ) {
	print "[INFO] Downloading RAID utils\n";
	`wget http://$system_configs->{'KSIPADDR'}/kickstart/taskfiles.new/devices/13c1/1004/cli.tgz 2>&1`;
	print "[INFO] Extracting RAID utils\n";
	`tar xzf cli.tgz -C /sbin/`;

	$raid_controller = `tw_cli show | grep c[0-9] | awk '{print \$1}'`;
	chomp($raid_controller);
	print "[INFO] Found RAID controller at position $raid_controller.\n";

	$raid_model = `tw_cli show | grep c[0-9] | awk '{print \$2}'`;
	chomp($raid_model);
	print "[INFO] Found RAID model $raid_model.\n";

	if( !grep(/$raid_model/, @tw_cli_cards) ) {
		print "[ERROR] RAID model $raid_model not supported.\n";
		ks_register($system_configs->{'MACADDR'}, 'hwraid_setup_fail', $system_configs->{'KSIPADDR'});
		exit;
	}

	my @partitions = `tw_cli /$raid_controller show | grep p[0-9] | grep -o u[0-9] | uniq`;
	chomp(@partitions);
	foreach( @partitions ) {
		`tw_cli /$raid_controller/$_ del quiet noscan`;
	}
	print "[INFO] Deleted any existing RAID setup\n";

	$drives_available = `tw_cli /$raid_controller show | grep OK | grep -o p[0-7] | wc -l`;
	chomp($drives_available);
	print "[INFO] Found $drives_available available drives.\n";

	$drive_enclosure = undef;
	@drive_slots = undef;

} elsif( $raid_vendor eq 'LSI' ) {
	print "[INFO] Downloading RAID utils\n";
	`wget http://$system_configs->{'KSIPADDR'}/kickstart/taskfiles.new/devices/1000/0060/cli.tgz 2>&1`;
	print "[INFO] Extracting RAID utils\n";
	`tar xzf cli.tgz -C /sbin/`;

	$raid_controller = `MegaCli -adpallinfo -aall | grep 'Adapter #' | grep -o [0-9]`;
	chomp($raid_controller);
	print "[INFO] Found RAID controller at position $raid_controller.\n";

	$raid_model = `MegaCli -adpallinfo -a$raid_controller | grep Product | awk '{print \$NF}'`;
	chomp($raid_model);
	print "[INFO] Found RAID model $raid_model.\n";
	if( !grep(/$raid_model/, @megacli_cards) ) {
		print "[ERROR] RAID model $raid_model not supported.\n";
		ks_register($system_configs->{'MACADDR'}, 'hwraid_setup_fail', $system_configs->{'KSIPADDR'});
		exit;
	}

	`MegaCli -cfgclr -a$raid_controller`;
	`MegaCli -cfgforeign -clear -a$raid_controller`;
	print "[INFO] Deleted any existing RAID setup\n";

	$drives_available = `MegaCli -adpallinfo -a$raid_controller | grep Disks | head -n1 | awk '{print \$3}'`;
	chomp($drives_available);
	print "[INFO] Found $drives_available available drives.\n";

	$drive_enclosure = `MegaCli -encinfo -a$raid_controller | grep ID | awk '{print \$4}'`;
	chomp($drive_enclosure);

	@drive_slots = `MegaCli -pdlist -a$raid_controller | grep Slot | awk '{print \$3}'`;
	chomp(@drive_slots);

	foreach (@drive_slots) {
		my $drive_slot = $_;
		print "[INFO] Marking drive $drive_slot in enclosure $drive_enclosure as good\n";
		`MegaCli -pdmakegood -physdrv "[$drive_enclosure:$drive_slot]" -a$raid_controller`;
	}

} elsif( $raid_vendor eq 'LSI-Fusion-MPT' ) {
	print "[INFO] Downloading RAID utils\n";
	`wget http://$system_configs->{'KSIPADDR'}/kickstart/taskfiles.new/devices/1000/0056/cli.tgz 2>&1`;
	print "[INFO] Extracting RAID utils\n";
	`tar xzf cli.tgz -C /sbin/`;

	$raid_controller = `cfggen list | sed '/^\$/d' | awk '{print \$1}' | grep -o [0-9]`;
	chomp($raid_controller);
	print "[INFO] Found RAID controller at position $raid_controller.\n";

	$raid_model = `cfggen list | sed '/^\$/d' | awk '{print \$2}' | tail -1`;
	chomp($raid_model);
	print "[INFO] Found RAID model $raid_model.\n";
	if( !grep(/$raid_model/, @cfggen_cards) ) {
		print "[ERROR] RAID model $raid_model not supported.\n";
		ks_register($system_configs->{'MACADDR'}, 'hwraid_setup_fail', $system_configs->{'KSIPADDR'});
		exit;
	}

	`cfggen $raid_controller delete noprompt`;
	print "[INFO] Deleted any existing RAID setup\n";

	$drives_available = `cfggen $raid_controller display | grep -c -i 'Device is a Hard disk'`;
	chomp($drives_available);
	print "[INFO] Found $drives_available available drives.\n";

	$drive_enclosure = `cfggen $raid_controller display | grep 'Enclosure#' | awk '{print \$3}'`;
	chomp($drive_enclosure);
	@drive_slots = `cfggen $raid_controller display | grep -i -A 4 'Device is a Hard disk' | grep -i 'Slot #' | awk '{print \$4}'`;
	chomp(@drive_slots);
	print "[INFO] Drive slots are: @drive_slots \n";
} elsif( $raid_vendor eq 'LSI-Fusion-MPT2' ) {
	print "[INFO] Downloading RAID utils\n";
	`wget http://$system_configs->{'KSIPADDR'}/kickstart/taskfiles.new/devices/1000/0072/cli.tgz 2>&1`;
	print "[INFO] Extracting RAID utils\n";
	`tar xzf cli.tgz -C /sbin/`;

	$raid_controller = `sas2ircu list | grep '^  ' | awk '{print \$1}' | grep -o [0-9]`;
	chomp($raid_controller);
	print "[INFO] Found RAID controller at position $raid_controller.\n";

	$raid_model = `sas2ircu list | grep '^  ' | awk '{print \$2}' | tail -1`;
	chomp($raid_model);
	print "[INFO] Found RAID model $raid_model.\n";
	if( !grep(/$raid_model/, @sas2ircu_cards) ) {
		print "[ERROR] RAID model $raid_model not supported.\n";
		ks_register($system_configs->{'MACADDR'}, 'hwraid_setup_fail', $system_configs->{'KSIPADDR'});
		exit;
	}

	`sas2ircu $raid_controller delete noprompt`;
	print "[INFO] Deleted any existing RAID setup\n";

	$drives_available = `sas2ircu $raid_controller display | grep -c 'Device is a Hard disk'`;
	chomp($drives_available);
	print "[INFO] Found $drives_available available drives.\n";

	$drive_enclosure = `sas2ircu $raid_controller display | grep 'Enclosure#' | awk '{print \$3}'`;
	chomp($drive_enclosure);
	@drive_slots = `sas2ircu $raid_controller display | grep 'Slot #' | awk '{print \$4}'`;
	chomp(@drive_slots);
}

# TW_CLI based cards
sub tw_cli_raid_generic {
	my($raid_controller, $raid_model, $drives_available, $drive_enclosure, $drive_count, $drive_pos, $raid_level, @drive_slots) = @_;

	if( $raid_level eq '60' ) {
		print "\t[FAILED] Reason: RAID60 not supported.\n";
		ks_register($system_configs->{'MACADDR'}, 'hwraid_setup_fail', $system_configs->{'KSIPADDR'});
		exit;
	}
	my $bbu_chk = `tw_cli info | grep c[0-9] | awk '{print \$9}'`;
	chomp($bbu_chk);
	my $bbu_opt = 'nocache';
	if( $bbu_chk eq 'Charging' || $bbu_chk eq 'Yes' || $bbu_chk eq 'OK' ) {
		$bbu_opt = '';
	}

	if( $drive_count eq '1' ) {
		$cmd = sprintf("tw_cli /%s add type=single disk=%s %s", $raid_controller, $drive_pos, $bbu_opt);
	} else {
		$cmd = sprintf("tw_cli /%s add type=raid%s disk=%s-%s %s", $raid_controller, $raid_level, $drive_pos, ($drive_count - 1 + $drive_pos), $bbu_opt);
	}
	$result = `$cmd`;
	if( $? ) {
		# We have an error
		print "\t[FAILED] Reason: command returned $result\n";
		ks_register($system_configs->{'MACADDR'}, 'hwraid_setup_fail', $system_configs->{'KSIPADDR'});
		exit;
	} else {
		return "\t[OK]";
	}
}

# MegaCLI based cards
sub megacli_raid0156 {
	my($raid_controller, $raid_model, $drives_available, $drive_enclosure, $drive_count, $drive_pos, $raid_level, @drive_slots) = @_;
        my @drives = ();
        for( my $i = $drive_pos; $i < ($drive_count + $drive_pos); $i++ ) {
                push(@drives, "$drive_enclosure:$drive_slots[$i]");
       	}
	$cmd = sprintf("MegaCli -cfgldadd -R%s \"[%s]\" -a%s",$raid_level, join(",", @drives), $raid_controller);
	my $results = `$cmd`;
	chomp($results);
	if( grep(/Configured the adapter/, $results ) ) {
		return "\t[OK]";
	} else {
		print "\t[FAIL] Reason: $results\n";
		ks_register($system_configs->{'MACADDR'}, 'hwraid_setup_fail', $system_configs->{'KSIPADDR'});
		exit;
	}
}

sub megacli_raid10 {
	my($raid_controller, $raid_model, $drives_available, $drive_enclosure, $drive_count, $drive_pos, @drive_slots) = @_;
	my $drive_arrays = $drive_count / 2;
	my @arrays = ();
	for( my $i = 0; $i < $drive_arrays; $i++ ) {
		my @drives = ();
		for( my $j = ($i * 2); $j < ($i * 2 + 2); $j++ ) {
			push(@drives, "$drive_enclosure:$drive_slots[$j]");
		}
		push( @arrays, sprintf("-array%s \"[%s]\"", $i, join(",", @drives)));
	}
	$cmd = sprintf("MegaCli -cfgspanadd -R10 %s -a%s", join(" ", @arrays), $raid_controller);
	my $results = `$cmd`;
	chomp($results);
	if( grep(/Configured the adapter/, $results ) ) {
		return "\t[OK]";
	} else {
		print "\t[FAILED] Reason: $results\n";
		ks_register($system_configs->{'MACADDR'}, 'hwraid_setup_fail', $system_configs->{'KSIPADDR'});
		exit;
	}
}

sub megacli_raid50 {
	my($raid_controller, $raid_model, $drives_available, $drive_enclosure, $drive_count, $drive_pos, @drive_slots) = @_;
	my $drive_arrays = $drive_count / 2;
	my @arrays = ();
	for( my $i = 0; $i < 2; $i++ ) {
		my @drives = ();
		for( my $j = ($i * $drive_arrays); $j < ($i * $drive_arrays + $drive_arrays); $j++ ) {
			push(@drives, "$drive_enclosure:$drive_slots[$j]");
		}
		push( @arrays, sprintf("-array%s \"[%s]\"", $i, join(",", @drives)));
	}
	$cmd = sprintf("MegaCli -cfgspanadd -R50 %s -a%s", join(" ", @arrays), $raid_controller);
	my $results = `$cmd`;
	chomp($results);
	if( grep(/Configured the adapter/, $results ) ) {
		return "\t[OK]";
	} else {
		print "\t[FAILED] Reason: $results\n";
		ks_register($system_configs->{'MACADDR'}, 'hwraid_setup_fail', $system_configs->{'KSIPADDR'});
		exit;
	}
}

sub megacli_raid60 {
	my($raid_controller, $raid_model, $drives_available, $drive_enclosure, $drive_count, $drive_pos, @drive_slots) = @_;
	my $drive_arrays = $drive_count / 2;
	my @arrays = ();
	for( my $i = 0; $i < 2; $i++ ) {
		my @drives = ();
		for( my $j = ($i * $drive_arrays); $j < ($i * $drive_arrays + $drive_arrays); $j++ ) {
			push(@drives, "$drive_enclosure:$drive_slots[$j]");
		}
		push( @arrays, sprintf("-array%s \"[%s]\"", $i, join(",", @drives)));
	}
	$cmd = sprintf("MegaCli -cfgspanadd -R60 %s -a%s", join(" ", @arrays), $raid_controller);
	my $results = `$cmd`;
	chomp($results);
	if( grep(/Configured the adapter/, $results ) ) {
		return "\t[OK]";
	} else {
		print "\t[FAILED] Reason: $results\n";
		ks_register($system_configs->{'MACADDR'}, 'hwraid_setup_fail', $system_configs->{'KSIPADDR'});
		exit;
	}
}

# CFGGEN based cards
sub cfggen_raid_generic {
	my($raid_controller, $raid_model, $drives_available, $drive_enclosure, $drive_count, $drive_pos, $raid_level, @drive_slots) = @_;
	my $raid_key = '';
	if( $raid_level eq '0' ) {
		if( $drive_count eq '1' ) {
			return "\t [OK]";
		}
		$raid_key = 'is';
	} elsif( $raid_level eq '1' ) {
		if( $drive_count eq '2' ) {
			$raid_key = 'im';
		} else {
			$raid_key = 'ime';
		}
	} elsif( $raid_level eq '5' ) {
		$raid_key = 'ime';
	} else {
		print "\t[FAILED] Reason: RAID$raid_level not supported by hardware\n";
		ks_register($system_configs->{'MACADDR'}, 'hwraid_setup_fail', $system_configs->{'KSIPADDR'});
		exit;
	}

	my @arrays = ();
	foreach( @drive_slots ) {
		push( @arrays, sprintf("%s:%s", $drive_enclosure, $_));
	}
	my $cmd = sprintf("cfggen %s create %s max %s qsync noprompt", $raid_controller, $raid_key, join(" ", @arrays));
	$results = `$cmd`;
	return "\t[OK]";
}


# sas2ircu based cards
sub sas2ircu_raid_generic {
	my($raid_controller, $raid_model, $drives_available, $drive_enclosure, $drive_count, $drive_pos, $raid_level, @drive_slots) = @_;
	my $raid_key = '';
	if( $raid_level eq '0' ) {
		if( $drive_count eq '1' ) {
			shift @drive_slots;
			return "\t [OK]";
		}
		$raid_key = 'RAID0';
	} elsif( $raid_level eq '1' ) {
		if( $drive_count eq '2' ) {
			$raid_key = 'RAID1';
		} else {
			$raid_key = 'RAID1E';
		}
	} elsif( $raid_level eq '5' ) {
		$raid_key = 'RAID1E';
	} elsif( $raid_level eq '10' ) {
		$raid_key = 'RAID10';
	} else {
		print "\t[FAILED] Reason: RAID$raid_level not supported by hardware\n";
		ks_register($system_configs->{'MACADDR'}, 'hwraid_setup_fail', $system_configs->{'KSIPADDR'});
		exit;
	}

	my @arrays = ();
	foreach( @drive_slots ) {
		push( @arrays, sprintf("%s:%s", $drive_enclosure, $_));
	}
	my $cmd = sprintf("sas2ircu %s create %s max %s qsync noprompt", $raid_controller, $raid_key, join(" ", @arrays));
	# This always reports a failure even when it works properly
	$result = `$cmd`;
	return "\t [OK]";
}

foreach ( @raid_types ) {
	my @raid_level_config = split /,/, $_;
	my $raid_level = $raid_level_config[0];
	my $drive_count = $raid_level_config[1];

	if( $drive_count eq '0' ) {
		print "[ERROR] No drives selected in array.\n";
		ks_register($system_configs->{'MACADDR'}, 'hwraid_setup_fail', $system_configs->{'KSIPADDR'});
		exit;
	}

	if( $raid_level eq '1' && $drive_count eq '1' ) {
		print "[ERROR] Invalid drive count: 1, raid 1 requires atleast 2 drives.\n";
		ks_register($system_configs->{'MACADDR'}, 'hwraid_setup_fail', $system_configs->{'KSIPADDR'});
		exit;
	} elsif( $raid_level eq '5' && $drive_count < 3 ) {
		print "[ERROR] Invalid drive count: $drive_count, RAID 5 requires atleast 3 drives.\n";
		ks_register($system_configs->{'MACADDR'}, 'hwraid_setup_fail', $system_configs->{'KSIPADDR'});
		exit;
	} elsif( $raid_level eq '6' && $drive_count < 4 ) {
		print "[ERROR] Invalid drive count: $drive_count, RAID 6 requires atleast 4 drives.\n";
		ks_register($system_configs->{'MACADDR'}, 'hwraid_setup_fail', $system_configs->{'KSIPADDR'});
		exit;
	} elsif( $raid_level eq '10' && $drive_count < 4 ) {
		print "[ERROR] Invalid drive count: $drive_count, RAID10 requires atleast 4 drives.\n";
		ks_register($system_configs->{'MACADDR'}, 'hwraid_setup_fail', $system_configs->{'KSIPADDR'});
		exit;
	} elsif( $raid_level eq '10' && $drive_count % 2 != 0 ) {
		print "[ERROR] Drive count not even, unable to continue\n";
		ks_register($system_configs->{'MACADDR'}, 'hwraid_setup_fail', $system_configs->{'KSIPADDR'});
		exit;
	} elsif( $raid_level eq '50' && $drive_count < 6 ) {
		print "[ERROR] Invalid drive count: $drive_count, RAID50 requires atleast 6 drives.\n";
		ks_register($system_configs->{'MACADDR'}, 'hwraid_setup_fail', $system_configs->{'KSIPADDR'});
		exit;
	} elsif( $raid_level eq '50' && $drive_count % 2 != 0 ) {
		print "[ERROR] Drive count not even, unable to continue\n";
		ks_register($system_configs->{'MACADDR'}, 'hwraid_setup_fail', $system_configs->{'KSIPADDR'});
		exit;
	} elsif( $raid_level eq '60' && $drive_count < 8 ) {
		print "[ERROR] Invalid drive count: $drive_count, RAID60 requires atleast 8 drives.\n";
		ks_register($system_configs->{'MACADDR'}, 'hwraid_setup_fail', $system_configs->{'KSIPADDR'});
		exit;
	} elsif( $raid_level eq '60' && $drive_count % 2 != 0 ) {
        	print "[ERROR] Drive count not even, unable to continue\n";
		ks_register($system_configs->{'MACADDR'}, 'hwraid_setup_fail', $system_configs->{'KSIPADDR'});
                exit;
	}

	print "[INFO] Creating a RAID level $raid_level_config[0] with $raid_level_config[1] drives.\n";

	if( $raid_vendor eq '3ware' ) {
		print tw_cli_raid_generic($raid_controller, $raid_model, $drives_available, $drive_enclosure, $drive_count, $drive_pos, $raid_level, @drive_slots);
	} elsif ($raid_vendor eq 'LSI' ) {
		if( $raid_level eq '0' || $raid_level eq '1' || $raid_level eq '5' || $raid_level eq '6' ) {
			print megacli_raid0156($raid_controller, $raid_model, $drives_available, $drive_enclosure, $drive_count, $drive_pos, $raid_level, @drive_slots);
		} elsif ( $raid_level eq '10' ) {
			print megacli_raid10($raid_controller, $raid_model, $drives_available, $drive_enclosure, $drive_count, $drive_pos, @drive_slots);
		} elsif ( $raid_level eq '50' ) {
			print megacli_raid50($raid_controller, $raid_model, $drives_available, $drive_enclosure, $drive_count, $drive_pos, @drive_slots);
		} elsif ( $raid_level eq '60' ) {
			print megacli_raid60($raid_controller, $raid_model, $drives_available, $drive_enclosure, $drive_count, $drive_pos, @drive_slots);
		}
	} elsif ($raid_vendor eq 'LSI-Fusion-MPT' ) {
		print cfggen_raid_generic($raid_controller, $raid_model, $drives_available, $drive_enclosure, $drive_count, $drive_pos, $raid_level, @drive_slots);
	} elsif ($raid_vendor eq 'LSI-Fusion-MPT2' ) {
		print sas2ircu_raid_generic($raid_controller, $raid_model, $drives_available, $drive_enclosure, $drive_count, $drive_pos, $raid_level, @drive_slots);
	}
	$drive_pos += $drive_count;

	print "\n";
}

if( $raid_vendor eq 'LSI' ) {
	print "[INFO] Setting up card for BBU options";
	my $bbu_chk = `MegaCli -adpallinfo -a$raid_controller | grep BBU | grep -o Present`;
	chomp($bbu_chk);
	my @virtual_disks = `MegaCli -ldinfo -lall -a$raid_controller | grep "Virtual Disk" | awk '{print \$3}'`;
	chomp(@virtual_disks);
	foreach( @virtual_disks ) {
		if( $bbu_chk eq 'Present' ) {
			`MegaCli -ldsetprop -WB -l$_ -a$raid_controller`;
			`MegaCli -ldsetprop -Cached -l$_ -a$raid_controller`;
			`MegaCli -ldsetprop -nocachedbadbbu -l$_ -a$raid_controller`;
			`MegaCli -ldsetprop -endskcache -l$_ -a$raid_controller`;
		} else {
			`MegaCli -ldsetprop -WT -l$_ -a$raid_controller`;
			`MegaCli -ldsetprop -Direct -l$_ -a$raid_controller`;
			`MegaCli -ldsetprop -nocachedbadbbu -l$_ -a$raid_controller`;
			`MegaCli -ldsetprop -disdskcache -l$_ -a$raid_controller`;
		}
	}
	print "\t [OK]\n";
}

print "\nQuick summary:\n";
print "Vendor: $raid_vendor\n";
print "Card: $raid_model\n\n";

print "Setting the status to hwraid_setup_done on kickstart\n";
ks_register($system_configs->{'MACADDR'}, 'hwraid_setup_done', $system_configs->{'KSIPADDR'});

print "Setting the PXE target to $postconf_configs->{'OSLOAD'} on kickstart\n";
ks_updateks($system_configs->{'MACADDR'}, $postconf_configs->{'OSLOAD'}, $system_configs->{'KSIPADDR'});

print "Sleeping...\n";
sleep(60);
print "Rebooting...\n";
`/sbin/reboot`;
