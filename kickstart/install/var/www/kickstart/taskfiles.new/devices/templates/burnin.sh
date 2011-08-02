# This is a template for what is expected in the burnin task file.  This file should contain
# any commands required to burnin the specified device.  Currently burnin is designed to use a 
# shell script.  Unless that is changed, this function needs to be implemented as a shell script

burnin() {

        postlog "INFO" "LOG INFORMATION ABOUNT DETECTED DEVICE";

        # Untar any CLI tools that you may require
        tar -xzvf /tmp/cli.tgz -C /tmp/

        # Check to make sure that the unpacking was successful
        if [[ -x /tmp/<CLI file> ]] ; then
                postlog "INFO" "Got $CLI cli from the tarball"
        else
                postlog "ERR" "Failed to get $CLI cli from the tarball"
        fi

        #
        # Insert commands for device specific burnin here
        #

}

