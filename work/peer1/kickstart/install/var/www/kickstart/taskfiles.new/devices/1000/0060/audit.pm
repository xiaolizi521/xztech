sub bbu_check() {
        use strict;
        my $bbu = `/tmp/MegaCli -adpallinfo -aall | grep BBU | grep -o Present`;
        $bbu =~ s/\s+$//;
        if (($bbu eq "Present")) {
                $_[0]->{"raid_controller_bbu"} = "1";
                print $_[0]->{"raid_controller_bbu"} . "\n";
        }
}

sub sas_check() {
        use strict;
        my $sas = `/tmp/MegaCli -pdlist -aall | grep -m1 "SAS Address(1)" | awk '{ print \$3 }'`;
	$sas =~ s/\s+$//;
        if ( $sas ne '') {
                $_[0]->{"sas_drive"} = "1";
                print $_[0]->{"sas_drive"} . "\n";
        }
}
	
sub raid_hash() {
        use strict;
        my $raid_model = shift @_;
        my $index = 0;
        my $model_hash;
        my $hash_seed;
        my @raid_model_;
        my $letters = join( "", ("a".."z"), ("A".."Z") );
        my $numbers = join( "", ("0".."9") );
        @raid_model_ = split( "", $raid_model );
        while ( $#raid_model_ < 8 ) { push @raid_model_, $raid_model_[-1]; }
        foreach( "0".."8" ) {
                $hash_seed .= $raid_model_[ $_ ];
        }

        foreach( split( "", $hash_seed ) ) {
                $index = index( $letters, $_ );
                if( $index == -1 ) { $index = index( $numbers, $_ );}
                if( $index == -1 ) { $index = 0; }
                $model_hash .= $index % 10;
        }
        return $model_hash;
}

sub audit()
{

        use strict;

#        $_[0]->{"lsi"} = "8708";
#        print "lsi card detected: ";
#        print $_[0]->{"lsi"} . "\n";

        my @extract_cli = ("/bin/tar", "xzf", "/tmp/cli.tgz", "-C", "/tmp/");
        system(@extract_cli) == 0 or die "failed @extract_cli : $?" ;
        my $raid_model = `/tmp/MegaCli -adpallinfo -aall | grep Product | awk '{ print \$6 }'`;
        $raid_model =~ s/\s+$//;
        if ($raid_model eq "8708ELP") {
                $_[0]->{"raid_controller"} = "8708ELP";
                $_[0]->{"raid_controller_hash"} = &raid_hash("8708ELP");
                $_[0]->{"raid_controller_ports"} = "8";
                print "LSI 8708ELP card detected: ";
                print $_[0]->{"raid_controller"} . "\n";
                print $_[0]->{"raid_controller_hash"} . "\n";
                print $_[0]->{"raid_controller_ports"} . "\n";
                &bbu_check;
		&sas_check;
        }
        elsif ($raid_model eq "8708EM2") {
                $_[0]->{"raid_controller"} = "8708EM2";
                $_[0]->{"raid_controller_hash"} = &raid_hash("8708EM2");
                $_[0]->{"raid_controller_ports"} = "8";
                print "LSI 8708EM2 card detected: ";
                print $_[0]->{"raid_controller"} . "\n";
                print $_[0]->{"raid_controller_hash"} . "\n";
                print $_[0]->{"raid_controller_ports"} . "\n";
                &bbu_check;
		&sas_check;
        }
        # this will get the information for the physical drives.  We can use a seperate
        # command to get the RAID level, but Ocean is not quite ready to handle that info
        # it needs to be added as we redo the taskfiles and audit, though
        my @summary_info = `/tmp/MegaCli -PDList -a0`;

        my $hdd_id = "a";
               
        foreach(@summary_info) 
        {
                # This is solely based on the fact that the the Raw Size is listed
                # before the model information.  Not a good idea but this needs to be
                # working ASAP
                chomp( my $line = $_ ) ; 
                if ($line =~ /^Raw Size: (\d*).*/)
                {
                        my $size = $1;
                        # The value is specified in MB so we should only need to multiply
                        # by 1024 to get the kbytes, which is what I think PIT wants
                        $_[0]->{"hdd_sd${hdd_id}_size"} = $size * 1024;
                }
                if ($line =~ /^Inquiry Data: .*/)
                {
                        my @data = split(/\s+/,$line);
                        $_[0]->{"hdd_sd${hdd_id}_model"} = $data[3];
                        $hdd_id++;
                }
        }
} # End of LSI detection

1
