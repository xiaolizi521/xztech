sub bbu_check() {
        use strict;
        my $bbu = `/tmp/tw_cli info | grep c[0-9] | awk '{ print \$9 }'`;
        $bbu =~ s/\s+$//;
        # ndurr@2009-01-14: should a charging be considered working?
        if (($bbu eq "Charging" || $bbu eq "Yes")) {
                $_[0]->{"raid_controller_bbu"} = "1";
                print $_[0]->{"raid_controller_bbu"} . "\n";
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

#        $_[0]->{"3ware"} = "7000";
#        print "3ware card detected: ";
#        print $_[0]->{"3ware"} . "\n";

        my @extract_cli = ("/bin/tar", "xzf", "/tmp/cli.tgz", "-C", "/tmp/"); 
        system(@extract_cli) == 0 or die "failed @extract_cli : $?" ; 
        my $raid_model = `/tmp/tw_cli show | grep c[0-9] | awk '{ print \$2 }'`;
        $raid_model =~ s/\s+$//;
        $_[0]->{"raid_controller"} = $raid_model;
        $_[0]->{"raid_controller_hash"} = &raid_hash($raid_model);
        $_[0]->{"raid_controller_ports"} = "2";
        print "3ware $raid_model card detected: ";
        print $_[0]->{"raid_controller"} . "\n";
        print $_[0]->{"raid_controller_hash"} . "\n";
        print $_[0]->{"raid_controller_ports"} . "\n";
        &bbu_check;
        my @twctls;  # list of all controller interfaces in the server
        my @summary_info = `/tmp/tw_cli info`;
        my $tw_ctl = "" ;
        foreach my $infoline (@summary_info) {
                chomp( my $line = $infoline ) ; 
                my @field = split( /\s+/, $line );
                if ( ($field[0]) && ($field[0] =~ /^c[0-9]/ ) ) 
                {
                        push( @twctls, $field[0] );  # add it to our list
                }
        }

        my $scsi_suffix = "a";
        foreach my $controller (@twctls) {
                # sort to make sure we get stuff in order, grep to ignore the header
                # information
                my @ctl_info = sort( grep {/^p[0-9]/} `/tmp/tw_cli info $controller`);

                foreach my $line (@ctl_info) {
                        chomp( $line );
                        # fields were interested in are:
                        # 0 = partition name (p0)
                        # 2 = unit
                        # 3 = disk size (in GB)
                        # 6 = serial (aka, model)
                        my @f = split( /\s+/, $line );
                        $f[1] =~ /OK/ || next;

                        # so, for each entry here, were going to record the 
                        # model and size for the drive, then increment the 
                        # scsi_suffix.  c0p0 == sda, c0p1 == b, c1p0 == c, etc

                        # isolate the model
                        chomp( my $model_string = `/tmp/tw_cli info $controller $f[0] model` );
                        my @stuff = split( /=/, $model_string );
                        $stuff[1] =~ s/^\s+//g;
                        chomp ( $stuff[1] );
                        $_[0]->{"hdd_sd${scsi_suffix}_model"} = $stuff[1];
                        # we have to multipy this number by 1024^2 because PIT divides it
                        # by the same number
                        $_[0]->{"hdd_sd${scsi_suffix}_size"} = $f[3] * 1024 * 1024;
                        # bump to the next device
                        $scsi_suffix++;
                }
        }
} # end of the 3ware drive audit 

1
