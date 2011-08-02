sub audit()
{

        use strict;

        $_[0]->{"lsi"} = "8308";
        print "lsi card detected: ";
        print $_[0]->{"lsi"} . "\n";

        my @extract_cli = ("/bin/tar", "xzf", "/tmp/cli.tgz", "-C", "/tmp/"); 
        system(@extract_cli) == 0 or die "failed @extract_cli : $?" ; 
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
