burnin() {

        postlog "INFO" "Found 3ware controller, unpacking the cli tools"

        tar -xzvf /tmp/cli.tgz -C /tmp

        ctl=`/tmp/tw_cli info | awk '/^c[0-9]/ {print $1}'`
        raid_type=`/tmp/tw_cli info ${ctl} | awk '/^u[0-9]/ {print $2}'`
        postlog "INFO" "RAID configuration: $raid_type"
}

