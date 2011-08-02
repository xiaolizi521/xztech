burnin() {

        # Okay, this is not really doing anything right now, but it is a step in the right
        # direction.  The idea is to modularize the RAID burnin (and other add-on products
        # for that matter.  We want any burnin specific stuff into a function that can be called
        # so that we don't need to hack up this taskfile everytime there is a new product
        postlog "INFO" "Detected LSI MegaRaid SAS 8308ELP"

        tar -xzvf /tmp/cli.tgz -C /tmp/
        if [[ -x /tmp/MegaCli ]] ; then
                postlog "INFO" "Got MegaRaid cli from the tarball"
        else
                postlog "ERR" "Failed to get MegaRaid cli from the tarball"
        fi

}

