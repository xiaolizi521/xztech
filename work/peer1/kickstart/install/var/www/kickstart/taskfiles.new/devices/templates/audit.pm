# This is a template for what is expected in the audit device task file.  This file should contain
# any commands required to audit the specified device.  At the point that this function is called,
# the audit task file is executing some inline perl.  Therefore this needs to be a perl module


sub audit()
{
        # We are passed a reference to a hash.  Any modifications to this needs to be made
        # through the $_[0] variable as that updates the data in memory instead of making a copy
        # of it (causing us to lose any changes when we exit the function)

        # The MODEL information in the assignment below needs to be an integer value due to the 
        # way that the information will eventually get put into the database
        $_[0]->{"MANUFACTURER"} = "MODEL";

        # Just echoing some information to the screen
        print "DEVICE X detected: ";
        print $_[0]->{"DEVICE MAKE"} . "\n";

        # Extract any required CLI tools
        my @extract_cli = ("/bin/tar", "xzf", "/tmp/cli.tgz", "-C", "/tmp/"); 
        system(@extract_cli) == 0 or die "failed @extract_cli : $?" ; 

        # This is where you will put the commands required for auditing the device.  The example
        # below if from the audit of the 8308 LSI MegaRaid card and is used as an example
        # of what you are trying to do.  
        
        # Get the summary information for the device
        my @summary_info = `/tmp/MegaCli -PDList -a0`;

        my $hdd_id = "a";
        
        # Parse through the returned data and record the hard disk size and model
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
