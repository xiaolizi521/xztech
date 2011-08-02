#!/usr/bin/perl -w

use strict;
use File::Copy;

open SSHD_IN, "</etc/ssh/sshd_config";
open SSHD_OUT, ">/tmp/sshd_config.$$";

while (<SSHD_IN>) {
    chomp;
    s/^(PermitRootLogin).*/$1 no/;
    print SSHD_OUT $_."\n";
}

close SSHD_OUT;
close SSHD_IN;

if (-s "/tmp/sshd_config.$$") {
    copy("/etc/ssh/sshd_config","/etc/ssh/sshd_config.$$");
    copy("/tmp/sshd_config.$$", "/etc/ssh/sshd_config");
    unlink("/tmp/sshd_config.$$");
    postlog("INFO: Modify sshd_config complete");
}
else {
    postlog("FATAL: Modify sshd_config failed");
}

if (-s "/etc/inittab")
{
        ## BEGIN INITTAB ##
        my $deb_kbhack='kb::kbrequest:/sbin/getty -n -l /bin/bash tty12 115200';
        my $rhl_kbhack='kb::kbrequest:/sbin/agetty -n -l /bin/bash tty12 115200';
        my $real_kbhack;

        if (-e "/etc/debian_version") { $real_kbhack = $deb_kbhack; }
        if (-e "/etc/redhat-release") { $real_kbhack = $rhl_kbhack; }

        open INITTAB_IN, "</etc/inittab";
        open INITTAB_OUT, ">/tmp/inittab.$$";

        while (<INITTAB_IN>) {
                if (/^kb/) { print INITTAB_OUT $real_kbhack."\n"; }
                else { print INITTAB_OUT $_; }
        }

        close INITTAB_OUT;
        close INITTAB_IN;

        if (-s "/tmp/inittab.$$") {
                copy("/tmp/inittab.$$", "/etc/inittab");
                unlink("/tmp/inittab.$$");
                postlog("INFO: Modify inittab complete");
        }
        else {
                postlog("FATAL: Modify inittab failed");
        }
        ## END INITTAB ##
}

## BEGIN SUDOERS ##
open SUDO_IN, "</etc/sudoers";
open SUDO_OUT, ">/tmp/sudoers.$$";

while (<SUDO_IN>) {
    if (/^beach/) { print SUDO_OUT "beach ALL=NOPASSWD: ALL\n"; }
    else { print SUDO_OUT $_; }
}

close SUDO_OUT;
close SUDO_IN;

if (-s "/tmp/sudoers.$$") {
    copy("/tmp/sudoers.$$", "/etc/sudoers");
    unlink("/tmp/sudoers.$$");
    chmod(0440, "/etc/sudoers");
    postlog("INFO: Modify sudoers complete");
}
else {
    postlog("FATAL: Modify sudoers failed");
}
## END SUDOERS ##

open SYSCTL, ">>/etc/sysctl.conf";
print SYSCTL "kernel.panic = 300
kernel.sysrq = 1
net.ipv4.tcp_syncookies = 1
";
close SYSCTL;

1;
