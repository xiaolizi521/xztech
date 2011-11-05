#!/usr/bin/perl
# vmware.pl
# A simple configuration script for VMware ESXi hosts. This was developed
# to provide functionality to the existing Winstart/Kickstart infrastructure
# and eliminate dependencies on other servers or manual processes.
# 
# Initially work on the replacement was going to be based on the vSphere
# Command Linux Interface (vCLI) but some functions are only available
# using the vSphere API. Some functions may still require the vCLI, this
# has yet to be determined.
#
# This code wasn't written to be perfect and there is a lot of improvement
# that can be made. Hopes are this will be made obsolete by new features
# in the VMware weasel installer, such as support to install to USB, or
# our product such as boot-to-san which will make this obsolete.
#
# Convention will be to return "0" when no error has occurred. This means
# implicit checking, 'if (function()) {}' may not work as desired. Please
# check explicitly instead, 'if (function() == 0) {}'.

use strict;
use warnings;
use CGI ':standard';
use POSIX;
use IO::Socket;
use Socket qw(inet_aton inet_ntoa);
use File::Temp qw(:mktemp);
use VMware::VIRuntime;
use Data::Dumper;

# Hard-code a debug flag
my $debug = 0;

# The BEGIN block is included following convention of other Kickstart
# scripts. This block executes before any other commands are processed
# and functionally puts it above the previous lines keeping the use
# statements a little cleaner.
BEGIN {
    use lib "/exports/kickstart/lib";
    require 'sbks.pm';
}

# Set the licenses for provisioning. At the moment we use fixed license
# keys but in the future there is hope this will be database driven.
my $licenses = {
    standard => 'T103J-6HH84-N8H3R-08F04-1M50N',
    advanced => 'TH0A1-4JH96-J8T3H-004A4-2N70N',
    enterpriseplus  => 'EJ0AJ-0L2E7-18J31-0G1A2-A5X1N'
};

# Define anything appearing under "Software: Advanced Settings" in the
# vSphere client view for the host configuration here. Types are very
# sensitive so must be defined as a PrimType object.
my $advancedSettings = {
    'Disk.UseDeviceReset' => new PrimType(0, 'long')
};

# Kickstart states valid for configuration. Only the "booting" state is
# valid for provisioning today because VMware uses a uid string which
# causes DHCP to disregard the previously assigned address.
my @states = ('booting');

# Although these states aren't valid for provisioning, it is possible to
# use "Reset System Configuration" within the ESXi console to restore the
# system to default settings. This allows for easier development testing.
if (defined $debug) {
    push @states, ('kickstarted', 'postconf_fail', 'postconf');
}

# Create a database connection from sbks.pm.
my $dbh = ks_dbConnect();

# Query the database to get the MAC address of machines installing ESXi
# from this server. Note, the state "booting" is the only one in which the
# DHCP address is updated in the Winstart interface.
my $query = "SELECT mac_address FROM kickstart_map WHERE osload like 'esx%'"
          . " and new_status in ('" . join("','", @states) . "')";
my $result = $dbh->selectall_hashref($query,"mac_address");

# Iterate through the hosts pending completion
foreach my $macaddr(keys %{$result}) {
    # Most of the functions rely on a MACFun object which queries the
    # database by MAC address and provides information such as the current
    # IP address, status, postconf information, etc.
    my $macobj = MACFun->new(dbh => $dbh, macaddr => $macaddr);
    my $ipAddress = $macobj->ipaddr();

    # Check to see if the socket is open. If it is, the system should be
    # ready to receive commands. If not, set the status to "ks_wait" as
    # we are waiting for Kickstart to complete.
    if (hostConnect($ipAddress, $macaddr) == 0) {
        updateStatus($macobj,"Starting VMware ESXi Configuration", "postconf");
        configHost($macaddr,$ipAddress,$macobj);

        # Disconnect from the ESXi host
        Util::disconnect();
    }
}

# Disconnect from the database. Unfortunately there's only one $dbh for
# all programs using sbks.lib and disconnecting it when we're done breaks
# MACfun.pm which also does queries.
$dbh->disconnect();

# connect()
# Attempt three times to get a connection to the VMware ESXi host. It takes
# several seconds after the port is available before it's ready to service
# requests through the vSphere API.
#
# Returns 0 if no error has occurred, 1 if there was an error.
sub hostConnect {
    my $ipAddress = shift;
    my $macaddr = shift;
    my $attempts = 3;
    my $webService = "https://$ipAddress/sdk/webService";
    my $warning = "%s Attempt %d/%d failed to connect\n";

    # Check port 443 to see if it's open. This port is not open during
    # installation (unlike 902 which is open during installation).
    my $socket = new IO::Socket::INET(
        PeerAddr=>$ipAddress,
        PeerPort=>'443',
        Proto=>'tcp',
        Timeout=>'10'
    );

    if (!$socket) {
        return 1;
    }

    # Attempt to connect three times, after which exit. Failures should
    # be logged in the kslog but not displayed in the Winstart interface
    # because the host may not be provisoned yet.
    for (my $i = 0; $i < $attempts; $i++) {
        eval {
            Util::connect($webService,'root','');
        };
        if ($@) {
            chomp $@;
            kslog('warning', sprintf($warning, $macaddr, $i + 1, $attempts));
            sleep(10);
        } else {
            kslog('info', "$macaddr Successful connection");
            return 0;
        }
    }

    # The connection failed after repeated attempts
    return 1;
}

# configHost
# Configure an ESXi host with the options specified in the postconf. If
# the postconf is empty or if configuration fails, log the error and set
# the status to "postconf_fail".
#
# Returns 0 if no errors have occurred, 1 if there was an error.
sub configHost {
    my($macaddr,$ipAddress,$macobj) = @_;

    # Poll the postconf information. If it's not found, fail.
    my $postconf = $macobj->postconf();
    if (!$postconf) {
        updateStatus($macobj,"Failed: Postconf Empty", "postconf_fail");
        return 1;
    }

    # Attempt to validate the IP configuration. Will fail configuration
    # if the values are completely invalid but will only warn if the
    # gateway isn't reachable as this is fixable.
    my $address = inet_aton($postconf->{'IPADDR'});
    if (!defined $address) {
        updateStatus($macobj,"Failed: Invalid IP Address ($postconf->{'IPADDR'})", "postconf_fail");
        return 1;
    }
    my $netmask = inet_aton($postconf->{'NETMASK'});
    if (!defined $netmask) {
        updateStatus($macobj,"Failed: Invalid Netmask ($postconf->{'NETMASK'})", "postconf_fail");
        return 1;
    }
    my $gateway = inet_aton($postconf->{'GATEWAY'});
    if (!defined $gateway) {
        updateStatus($macobj,"Failed: Invalid Netmask ($postconf->{'GATEWAY'})", "postconf_fail");
        return 1;
    }

    # Non-fatal error to the configuration
    if (($netmask & $address) ne ($netmask & $gateway)) {
        updateStatus($macobj,"Warning, gateway $postconf->{'GATEWAY'} unreachable from $postconf->{'IPADDR'}/$postconf->{'NETMASK'}. This may be fixed using the ESXi console interface.");
    } else {
        updateStatus($macobj,"Network configuration validated, no errors");
    }

    # Prepare to configure the host
    my $content = Vim::get_service_content();
    my $views = Vim::find_entity_views(view_type => 'HostSystem');
    my $host = @{$views}[0];

    # Find the management objects that are used to set options below.
    my $networkManager = Vim::get_view(mo_ref => $host->configManager->networkSystem);
    my $accountManager = Vim::get_view(mo_ref => $content->accountManager);
    my $licenseManager = Vim::get_view(mo_ref => $content->licenseManager);
    my $authorizationManager = Vim::get_view(mo_ref => $content->authorizationManager);
    my $advancedOption = Vim::get_view(mo_ref => $host->configManager->advancedOption);

    # Place the host into maintenance mode. This does not seem to be
    # necessary on hosts that aren't currently hosting guests.
    if ($host->summary->runtime->inMaintenanceMode == 0) {
        eval {
            updateStatus($macobj,"Entering maintenance mode");
            $host->EnterMaintenanceMode_Task(timeout => 10);
        };
        if ($@) {
            chomp $@;
            updateStatus($macobj,"Unable to enter maintenance mode: $@", "postconf_fail");
            return 1;
        }
    } else {
        updateStatus($macobj,"Host already in maintenance mode");
    }

    my $license;
    # Set the license to the one indicated by postconf. This is the only
    # required function not supported within the vCLI.
    if (exists $postconf->{'vmwarelicense'}) {
        $license = $postconf->{'vmwarelicense'};
    } else {
        updateStatus($macobj,"No license selected, using 'standard'.");
        $license = 'standard';
    }

    eval {
        updateStatus($macobj,"Setting the license to $license");
        $licenseManager->UpdateLicense(licenseKey => $licenses->{$license});
    };

    # Catch any errors from the eval block and report them here
    if ($@) {
        chomp $@;
        updateStatus($macobj,"Unable to update license: $@", "postconf_fail");
        return 1;
    }

    # Set the password for the root user
    if (!exists $postconf->{'RPASS'}) {
        updateStatus($macobj,"Root password not defined, leaving empty");
    } else {
        eval {
            updateStatus($macobj,"Configuring the root password.");
            my $user = HostAccountSpec->new(id => 'root', password => $postconf->{'RPASS'});
            $accountManager->UpdateUser(user => $user);
        };

        # Catch any errors from the eval block and report them here
        if ($@) {
            chomp $@;
            updateStatus($macobj,"Unable to update root password: $@", "postconf_fail");
            return 1;
        }
    }

    my $privileges = [
       'VirtualMachine.Interact.ConsoleInteract',
       'VirtualMachine.Interact.PowerOn',
       'VirtualMachine.Interact.PowerOff',
       'VirtualMachine.Interact.Reset',
       'VirtualMachine.Interact.Suspend',
       'VirtualMachine.Interact.ToolsInstall',
       'VirtualMachine.State.CreateSnapshot',
       'VirtualMachine.State.RevertToSnapshot',
       'VirtualMachine.State.RemoveSnapshot',
       'VirtualMachine.State.RenameSnapshot',
       'VirtualMachine.Config.UpgradeVirtualHardware'
    ];
    my $roleId = $authorizationManager->AddAuthorizationRole(
        name => "Customer Access",
        privIds => $privileges
    );

    # Create users as specified by postconf. This is bound to get ugly
    # because postconf uses key/value pairs instead of serialized objects.
    if (exists $postconf->{'PUSER'} && exists $postconf->{'PPASS'}) {
        if (createUser($macobj, $accountManager, $postconf->{'PUSER'}, $postconf->{'PPASS'}, 'Customer Account', $roleId) != 0) {
            return 1;
        }
    }

    # Assign the user to a role
    my $permissions = [
        new Permission(
            principal => $postconf->{'PUSER'},
            group => 'false',
            roleId => $roleId,
            propagate => 'true'
        )
    ];

    my $folder = new ManagedObjectReference(
        type => 'Folder',
        value => 'ha-folder-root'
    );

    # Assign the user to the role created above
    $authorizationManager->SetEntityPermissions(
        entity => $folder,
        permission => $permissions
    );

    # Set DNS information.
    eval {
        updateStatus($macobj,"Configuring DNS.");
        my $config = new HostDnsConfig(
            dhcp => 'false',
            hostName => $postconf->{'HOST'},
            domainName => 'pubip.peer1.net',
            address => [ $postconf->{'DNS1'}, $postconf->{'DNS2'} ],
            searchDomain => [ 'pubip.peer1.net' ]
        );
        $networkManager->UpdateDnsConfig(config => $config);
    };
    if ($@) {
        chomp $@;
        updateStatus($macobj,"Unable to set DNS configuration: $@", "postconf_fail");
        return 1;
    }

    # Advanced Configuration.
    eval {
        my $options = [];
        updateStatus($macobj,"Setting advanced options.");
        foreach my $key(keys %{$advancedSettings}) {
            push @{$options}, new OptionValue(
                key => $key,
                value => $advancedSettings->{$key}
            );
        }
        $advancedOption->UpdateOptions(changedValue => $options);
    };
    if ($@) {
        chomp $@;
        updateStatus($macobj,"Unable to set advanced options: $@", "postconf_fail");
        return 1;
    }

    if ($license eq "standard") {
        my $interfaces = ["vmnic1","vmnic2","vmnic3"];
        # Add a switch to host the VM Network using all available interfaces
        if (addVSwitch($networkManager,$macobj,"vSwitch1",$interfaces,0) != 0) {
            return 1;
        }
    } else {
        my @ip = split(/\./, $postconf->{'IPADDR'});
        my $vMotionIp = '10.0.0.' . $ip[3];
        # The current product adds a redundant management interface
        # Add a redundant port to the management network
        if (updateVSwitch($networkManager,$macobj,"vSwitch0",["vmnic0","vmnic1"]) != 0) {
            return 1;
        }

        # Add a switch to host the VM Network. Unlike before tihs will only
        # have a single interface as the add-on card won't be present yet.
        if (addVSwitch($networkManager,$macobj,"vSwitch1",["vmnic2"],0) != 0) {
            return 1;
        }
        # Add the vMotion network as a kernel port and private port group
        if (addVSwitch($networkManager,$macobj,"vSwitch2",["vmnic3"],1,$vMotionIp) != 0) {
            return 1;
        }
        my $vmotionManager = Vim::get_view(mo_ref => $host->configManager->vmotionSystem);
        $vmotionManager->SelectVnic(device => "vmk1");

        # Add a vCenter portgroup on the same vSwitch as management
        if (addPortGroup($networkManager,$macobj,"vCenter Network","vSwitch0",496) != 0) {
            return 1;
        }
    }

    # Must remove the VM Network port group before adding that group
    # to the dedicated vSwitch for the VM Network.
    if (removePortGroup($networkManager,$macobj,"VM Network") != 0) {
        return 1;
    }

    # Add the port group to the second vSwitch
    if (addPortGroup($networkManager,$macobj,"VM Network","vSwitch1",0) != 0) {
        return 1;
    }

    # Add the provisioning network to the first switch
    if (addPortGroup($networkManager,$macobj,"Provisioning","vSwitch0",495) != 0) {
        return 1;
    }

    # Modify the ESX host configuration file. We pass the postconf here
    # because we've already retrieved it; this way we don't need to query
    # the object (possibly saving a call to the database).
    modifyESXConf($postconf, $ipAddress, $macobj, $postconf->{'RPASS'});

    # Exit maintenance mode. Note, this will require the reboot task to
    # be forced but will save work for DCO.
    eval {
        updateStatus($macobj,"Exiting maintenance mode.");
        $host->ExitMaintenanceMode_Task(timeout => 10);
    };
    if ($@) {
        chomp $@;
        updateStatus($macobj,"Unable to exit maintenance mode: $@", "postconf_fail");
        return 1;
    }

    # Reboot the host applying changes made to /etc/esx.conf. Note
    eval {
        updateStatus($macobj,"Rebooting the system.");
        $host->RebootHost_Task(force => 'true');
    };
    if ($@) {
        chomp $@;
        updateStatus($macobj,"Unable to reboot system: $@", "postconf_fail");
        return 1;
    }

    # Update the logs and the status to indicate completion.
    updateStatus($macobj,"Installation complete.", "kickstarted");
}

sub removePortGroup {
    my($networkManager,$macobj,$name) = @_;
    eval {
        updateStatus($macobj,"Removing port group $name");
        $networkManager->RemovePortGroup(pgName => $name);
    };

    # Catch any errors from the eval block and report them here
    if ($@) {
        chomp $@;
        updateStatus($macobj,"Unable to remove $name: $@", "postconf_fail");
        return 1;
    }
    return 0;
}

sub updateVSwitch {
    my($networkManager,$macobj,$vswitch,$interfaces) = @_;
    my $config = new HostVirtualSwitchSpec(
        numPorts => 64,
        bridge => new HostVirtualSwitchBondBridge(
            nicDevice => $interfaces
        )
    );
    eval {
        updateStatus($macobj,"Updating $vswitch");
        $networkManager->UpdateVirtualSwitch(
            vswitchName => $vswitch,
            spec => $config
        );
    };

    # Catch any errors from the eval block and report them here
    if ($@) {
        chomp $@;
        updateStatus($macobj,"Unable to update $vswitch: $@", "postconf_fail");
        return 1;
    }
    return 0;
}

sub addPortGroup {
    my($networkManager,$macobj,$name,$vswitch,$vlan) = @_;
    my $config = new HostNetworkConfig(
        portgroup => [ new HostPortGroupConfig(
            changeOperation => 'add',
            spec => new HostPortGroupSpec(
                name => $name,
                vswitchName => $vswitch,
                vlanId => $vlan,
                policy => new HostNetworkPolicy
            )
        )]
    );
    eval {
        updateStatus($macobj,"Adding port group $name to $vswitch");
        $networkManager->UpdateNetworkConfig(
            config => $config,
            changeMode => "modify"
        );
    };

    # Catch any errors from the eval block and report them here
    if ($@) {
        chomp $@;
        updateStatus($macobj,"Unable to add group $name: $@", "postconf_fail");
        return 1;
    }
    return 0;
}

sub addVSwitch {
    my($networkManager,$macobj,$name,$interfaces,$isVMotion,$ipAddress) = @_;

    # Add the requested network to the host configuration.
    updateStatus($macobj,"Configuring vSwitch $name.");
    my $portgroup;
    my $vnic;
    my $config;

    if ($isVMotion == 1) {
        $config = new HostNetworkConfig(
            vswitch => [ new HostVirtualSwitchConfig(
                changeOperation => 'add',
                name => $name,
                spec => new HostVirtualSwitchSpec(
                    numPorts => 64,
                    bridge => new HostVirtualSwitchBondBridge(
                        nicDevice => $interfaces
                    )
                )
            )],
            vnic => [ new HostVirtualNicConfig(
                changeOperation => 'add',
                portgroup => 'vMotion Network',
                spec => new HostVirtualNicSpec(
                    ip => new HostIpConfig(
                        dhcp => 'false',
                        ipAddress => $ipAddress,
                        subnetMask => '255.255.255.0',
                    )
                )
            )],
            portgroup => [ new HostPortGroupConfig(
                changeOperation => 'add',
                spec => new HostPortGroupSpec(
                    name => "vMotion Network",
                    vswitchName => $name,
                    vlanId => 0,
                    policy => new HostNetworkPolicy
                )
            )]
        );
    } else {
        $config = new HostNetworkConfig(
            vswitch => [ new HostVirtualSwitchConfig(
                changeOperation => 'add',
                name => $name,
                spec => new HostVirtualSwitchSpec(
                    numPorts => 64,
                    bridge => new HostVirtualSwitchBondBridge(
                        nicDevice => $interfaces
                    )
                )
            )]
        );
    }

    eval {
        updateStatus($macobj,"Adding vSwitch $name using @{$interfaces}.");
        $networkManager->UpdateNetworkConfig(
            config => $config,
            changeMode => "modify"
        );
    };

    # Catch any errors from the eval block and report them here
    if ($@) {
        chomp $@;
        updateStatus($macobj,"Unable to add vSwitch $name: $@", "postconf_fail");
        return 1;
    }
    return 0;
}

# updateStatus($macobj,$status,$message)
# Provides a friendly method to update the status. This hides the fact we
# use the logError function in the MACfun object to pass status messages.
sub updateStatus {
    my $macobj = shift;
    my $macaddr = $macobj->{'_macaddr'};
    if (my $message = shift) {
        $macobj->logError($message);
        print "$macaddr $message\n" if defined $debug;
        kslog('info', "$macaddr $message.");
    }
    if (my $status = shift) {
        $macobj->status($status);
    }
    return $macobj->update();
}

sub createUser {
    my ($macobj, $accountManager, $username, $password, $description, $roleId) = @_;
    my $user = new HostAccountSpec(
        id => $username,
        password => $password,
        description => $description
    );
    # Set the password for the root user
    eval {
        updateStatus($macobj,"Creating user $username");
        $accountManager->CreateUser(user => $user);
    };

    # Catch any errors from the eval block and report them here
    if ($@) {
        chomp $@;
        updateStatus($macobj,"Unable to create user: $@", "postconf_fail");
        return 1;
    }
    return 0;
}

# modifyESXConf
#
# ESX and ESXi store the VMKernel configuration in /etc/esx.conf and,
# while it's possible to script a new kernel port, it's cleaner to update
# the existing configuraton. The 'vifs' command is used to retrieve and
# store the file. The rest of the code is used to edit key values.
sub modifyESXConf {
    my ($postconf,$ipAddress,$macobj,$password) = @_;
    my $program;
    my @args;

    # Standard arguments for the command line interface. The reason for
    # using parameterized arguments is escaping can be difficult.
    my @stdargs = (
        '--username', 'root',
        '--server', $ipAddress,
        '--password', $password
    );

    my $tmpHostConf = mktemp("/tmp/esx.conf.XXXXXXXXXX");
    my $curHostConf = {};
    my $newHostConf = {
        '/adv/Misc/HostIPAddr' => $postconf->{'IPADDR'},
        '/adv/Net/ManagementAddr' => $postconf->{'IPADDR'},
        '/net/routes/kernel/gateway' => $postconf->{'GATEWAY'},
        '/net/vmkernelnic/child[0000]/dhcp' => 'false',
        '/net/vmkernelnic/child[0000]/dhcpDns' => 'false',
        '/net/vmkernelnic/child[0000]/dhcpv6' => 'false',
        '/net/vmkernelnic/child[0000]/ipv4address' => $postconf->{'IPADDR'},
        '/net/vmkernelnic/child[0000]/ipv4netmask' => $postconf->{'NETMASK'},
        '/net/vswitch/child[0000]/portgroup/child[0000]/vlanId' => "496"
    };

    # Create a temporary file to store the host's esx.conf

    updateStatus($macobj,"Retrieving the configuration from the host");
    $program = 'vifs';
    @args = ('--get', '/host/esx.conf', $tmpHostConf);
    system($program, @stdargs, @args);

    # Read the configuration into key/value pairs in memory
    open FILE, $tmpHostConf;
    while (my $line = <FILE>) {
        my($key,$value)=split(/\s*=\s*/, $line);
    chomp $value;
        $curHostConf->{$key} = $value;
    }
    close FILE;

    # Modify the keys of interest to us (IP address, etc.)
    foreach my $key(keys %{$newHostConf}) {
        $curHostConf->{$key} = sprintf('"%s"', $newHostConf->{$key});
    }

    # Write the updated configuration back to the disk
    open FILE, ">$tmpHostConf";
    foreach my $key(sort{$a cmp $b} keys %{$curHostConf}) {
        printf FILE "%s = %s\n", $key, $curHostConf->{$key};
    }
    close FILE;

    updateStatus($macobj,"Writing new configuration to the host");
    $program = 'vifs';
    @args = ('--put', $tmpHostConf, '/host/esx.conf');
    system($program, @stdargs, @args);

    # When done with the configuration, unlink the file
    unlink $tmpHostConf;
    return 0;
}

__END__

# vim: ts=4 sts=4 sw=4 ft=perl nu expandtab
