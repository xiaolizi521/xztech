#!/usr/bin/perl -w

use strict;
use File::Copy;
use RPM qw(vercmp);
use RPM::Constants ':rpmtag';
use RPM::Header;

my ($base, $target, $update, $doit);

sub update_list {
	my $rhver = shift();
	my $href = shift();

    if ($rhver =~ /^fc([123])$/) {
        my $updateDir = "/mirrors/fedora/fedora-linux-core-updates/$1/i386";
        print "Building update list from: $updateDir\n";
        build_list($updateDir, $href)
    }
    else {
	    my @subdirs = ("i386","athlon","noarch");
	    foreach my $dir (@subdirs) {
            my $updateDir = "$update/$rhver/en/os/$dir";
            print "Building update list from: $updateDir\n";
            build_list($updateDir, $href)
	    }
    }
}

sub build_list {
	my $dir = shift();
	my $href = shift();

    opendir DH, "$dir";
    foreach my $file (grep(/\.rpm$/, readdir(DH))) {
        my $rpm = new RPM::Header $dir."/".$file;

        my ($name, $version, $release) = $rpm->NVR();
        my $arch = $rpm->{ARCH};

        # If we already have info on this package, check if we have a newer file
        if ($href->{$name} && $href->{$name}->{arch} eq $arch) {
			my $oldver = $href->{$name}->{version};
			my $oldrel = $href->{$name}->{release};
            my $cmp = vercmp($oldver, $oldrel, $version, $release);
            # -1 a < b
            #  0 a == b
            #  1 a > b
            if ($cmp > 0) {
                # a > b, get rid of b
                #print "[DEBUG] : $name $oldver-$oldrel > $version-$release\n";
                print "Removing: ".$rpm->source_name()."\n";
                unlink($rpm->source_name()) if ($doit);
                next;
            }
            elsif ($cmp < 0) {
                # a < b , get rid of a
                #print "[DEBUG] : $name $oldver-$oldrel < $version-$release\n";
                print "Removing: ".$href->{$name}->{filename}."\n";
                unlink($href->{$name}->{filename}) if ($doit);
            }
            else { next; }
        }

        $href->{$name} = {
            version => $version,
            release => $release,
            arch    => $arch,
            filename=> $rpm->source_name()
            };
        ($href->{$name}->{rpmname} = $rpm->source_name()) =~ s/.*\///;
    }
    closedir(DH);

	return 0;
}

my $rhver = $ARGV[0];
($rhver) || die "rhver";
if (($ARGV[1]) && ($ARGV[1] eq "doit")) {
	$doit = 1;
}

if ($rhver =~ /^fc([123])$/) {
    $base = "/exports/installs/linux/fedora/core$1";
    $target = "$base/Fedora/RPMS";
    $update= "/mirrors/fedora/fedora-linux-core-updates/$1/i386";
}
else {
    $base = "/exports/installs/linux/redhat/".$rhver;
    $target = "$base/RedHat/RPMS";
    $update = "/mirrors/redhat/redhat/linux/updates";
}

print "Base: $base\n";
print "Target: $target\n";
print "Update: $update\n";

my $old = {};
my $new = {};
build_list($target, $old);
update_list($rhver, $new);

# foreach my $name (keys(%{$new})) { print "$new->{$name}->{filename}\n"; }
# foreach my $name (keys(%{$old})) { print "$old->{$name}->{filename}\n"; }

#print "$old->{'rpm'}->{filename}\n";
#print "$new->{'rpm'}->{filename}\n";

foreach my $name (keys(%{$new})) {
	#print "$new->{$name}->{'filename'}\n";
	#print "$old->{$name}->{'filename'}\n";
	if (!$old->{$name}) {
		print "Copying: ".$new->{$name}->{filename}."\n";
		copy($new->{$name}->{filename}, $target) if ($doit);
	}
	else {
		my $oldver = $old->{$name}->{version};
		my $oldrel = $old->{$name}->{release};
		my $newver = $new->{$name}->{version};
		my $newrel = $new->{$name}->{release};
		#print "$name $oldver-$oldrel $newver-$newrel .. \t";
        my $cmp = vercmp($oldver, $oldrel, $newver, $newrel);
        #print "$cmp\n";
        # -1 a < b
        #  0 a == b
        #  1 a > b
		if ($cmp < 0) {
            # a < b , remove a then copy b
			print "Removing: ". $old->{$name}->{rpmname}."\n";
            unlink($old->{$name}->{filename}) if ($doit);
			print " Copying: ".$new->{$name}->{rpmname}."\n";
			copy($new->{$name}->{filename}, $target) if ($doit);
		}
	}
}

if ($doit) {
    print "Running genhdlist .. ";
    #system("/usr/lib/anaconda-runtime/genhdlist",$base);
    print "done.\n";
}

# rsync -av -e ssh --delete /exports/installs/linux/redhat/rhel3/ 64.34.161.6:/exports/installs/linux/redhat/rhel3/
# rsync -av -e ssh --delete /exports/installs/linux/redhat/rhel4/ 64.34.161.6:/exports/installs/linux/redhat/rhel4/
# rsync -av -e ssh --delete /exports/installs/linux/fedora/core3/ 64.34.161.6:/exports/installs/linux/fedora/core3/
# rsync -av -e ssh --delete /exports/installs/linux/fedora/core4/ 64.34.161.6:/exports/installs/linux/fedora/core4/

exit 0;

