#!/usr/bin/perl -w

use strict;

sub findfs {
    my $label = shift();

    # For now this can be static.
    #if ($label =~ /^\/(\d+)?$/) { return "/dev/hda3"; }
    #elsif ($label =~ /^\/boot(\d+)?$/) { return "/dev/hda1"; }

    my $fs;
    foreach my $dev (qw(hda hdb hdc hdd sda sdb sdc sdd)) {
        $fs = `findfs LABEL=$label | grep $dev`;
        last if ($fs)
    }
    chomp($fs);

    return $fs;
}

postlog("INFO: Removing LABEL statements from fstab");

rename("/etc/fstab","/etc/fstab.$$");
open IFH, "</etc/fstab.$$" or die "open(): $!";
open FSTAB, ">/etc/fstab";
while (<IFH>) {
    chomp;

    my @line = split(/\s+/, $_);
    postlog("INFO: line $. == ".join("\t", @line));

    if ($line[0] =~ /^LABEL=(\/(\w+|\d+)?)/) {
        $line[0] = findfs($1);
    }
    elsif ($line[0] =~ /^LABEL=SWAP-(\w+)$/) {
        $line[0] = "/dev/$1";
    }

    postlog("INFO: line $. => ".join("\t", @line));

    print FSTAB join("\t", @line)."\n";
}
close FSTAB;
close IFH;

postlog("INFO: Removing LABEL statements from grub");

rename("/boot/grub/grub.conf", "/boot/grub/grub.conf.$$");
open IFH, "</boot/grub/grub.conf.$$" or die "open(): $!";
open GRUB, ">/boot/grub/grub.conf";
while (<IFH>) {
    chomp;
    my $line = $_;

    if ($line =~ /LABEL=(\/(\w+|\d+)?)/) {
        my $label = $1;
        my $fs = findfs($label);
        postlog("INFO: line $. == $line");
        $line =~ s/(.*)=LABEL=$label(.*)/$1=$fs $2/;
        postlog("INFO: line $. => $line");
    }

    print GRUB $line."\n";
}
close GRUB;
close IFH;

chdir("/boot/grub");
unlink("menu.lst");
symlink("grub.conf", "menu.lst");

chdir("/etc");
unlink("grub.conf");
symlink("../boot/grub/grub.conf", "grub.conf");

#unlink("/etc/fstab.$$");
#unlink("/boot/grub/menu.$$");

1;

