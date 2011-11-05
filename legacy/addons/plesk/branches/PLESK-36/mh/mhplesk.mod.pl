#!/usr/bin/perl -w
    
use strict;
use LWP::UserAgent;

my $workdir = "/tmp/plesk95";
my $licdir = "/tmp/plesk95/licenses";
my($release, $product, $arch, $version, $macaddr);

mkdir("$workdir");
chdir("$workdir") || exit 1;

if (-f "/etc/redhat-release") 
{
    $release = `cat /etc/redhat-release`;
    $release =~ /(.*)\ release\ (.*)\ \((.*)\)/;

    # The first match is the operating system type
    $product = $1;
    # The second match is the verstion
    $version = $2;

    # The following is just used to create the product names and the versions
    if ($product =~ /Red Hat/i)
    {   
        $product = "RedHat";
        $version =~ s/(\d).*/el$1/ ;
    }
    elsif ( $product =~ /CentOS/i )
    {
        $product = "CentOS";
        $version =~ s/(\d).*/$1.x/ ;
    }
}

if ( `uname -m` =~ /x86_64|amd64/ )
{    
    $arch = "x86_64";
}
else
{   
    $arch = "i386";
}

# Let's retrieve the licenses

######################################################
# Subroutine Definitions
######################################################
#Function to get the MAC address inforamtion from the installed
#Network card
# ndurr@2009-01-07: changing to code developed for for secipaddr.mod
# now requires an interface
sub getmac {
	my $iface = shift;
	my $mac = qx(/sbin/ifconfig $iface | awk '/HWaddr/ {print \$5}');
	chomp( $mac ); 
	return $mac ;
}

# need to determine the unconigured interface
# should have a link if more than 2 interfaces exist
# should not have a ip address if the interface we are planning on using
# returns interfaces that have a link and no ip address configured
# todo
# should throw an error if there is only 1 interface exists
sub find_pri_nic {
	my $pri_int ;
	my @ifaces=`ifconfig -a` ;
	for (@ifaces) {
		# find lines that start with characters not whitespace
		if ( /^\w/ ) {
			# take the array returned from split and assign the 1st element to $int
			my ( $int ) = split(/\s/);
			if ( $int =~ /eth[0-9]/ ) { 
				my @link_check=`ethtool $int`;
				for (@link_check) {
					if ( /\s+Link detected: yes/ ) {
						if ( &get_ip($int) ) {
							$pri_int = $int;
							return $pri_int;
						} else { 
							# ndurr@2009-01-11: Blank because some style guide suggested 
							# not sure how I feel about this.
						}
					}
				}
			} else { 
				# postlog("INFO: skipping $int");
			}
		}
	}
	return $pri_int;
}

sub lwpfetch {
	my %input = @_;
	my ($url, $post, $file);
	foreach (keys %input) {
		if (/^url$/i) { $url = $input{$_}; }
		elsif (/^post$/i) { $post = $input{$_}; }
		elsif (/^file$/i) { $file = $input{$_}; }
		else { next; }
	}

	my ($auth, $method, $content, $ua, $req, $res);
	$method = 'GET';
	$content = '';

	($url) || return [ '1', 'Missing information: URL' ];
	if ($url =~ /\@/) {
		$url =~ s/http:\/\/(\w+:\w+)\@/http:\/\//;
		$auth = $1;
	}

	if ($post) {
		$content = $post;
		$method = 'POST';
	}

	$ua = LWP::UserAgent->new();
	$req = HTTP::Request->new($method => $url);
	if ($auth) { $req->authorization_basic(split(/:/, $auth)) }
	if ($content) {
		$req->content($content);
		$req->content_type('application/x-www-form-urlencoded');
	}

	if ($file) {
		$res = $ua->request($req, $file);
		(-f $file) || return [ 1, 'Download failed: $file' ];
	}
	else { $res = $ua->request($req); }

	if ($res->is_success()) {
		return [ 0, $res->content() ];
	}
	elsif ($res->is_error()) {
		return [ 1, $res->status_line() ];
	}
}

#Fetch the license information and store the license tgz file.
lwpfetch('url' => "http://kickstart/cgi-bin/licenses.cgi",
   	'post' => "macaddr=${macaddr}",
	'file' => "/tmp/plesk95/licenses.tgz");
	
system("tar -xvzf licenses.tgz");

# Check for a Plesk license
opendir DH, "$licdir";
my @licenses = grep { /^PLSK.*\.xml$/ && -s "$licdir/$_" } readdir(DH);
closedir DH;
if (scalar(@licenses) == 0) { postlog("FATAL: Plesk license not found"); }

my $psaver = "9.5";

my $file = sprintf("psa_%s_%s_%s_%s.tgz",$psaver, $product, $version, $arch);
my $baseurl = sprintf("http://kickstart/installs/panels/plesk/linux/%s",$psaver);
my $fullurl = "$baseurl/$file";                                
                                                               
my $postres = lwpfetch(url => $fullurl, file => "$workdir/$file");
if (($postres->[0] == 0) && (-f "$workdir/$file")) {
        postlog("INFO: Download of psa tarball ($file) successful");
} else {
        postlog("FATAL: Download of psa tarball ($file) failed");
}

$file = "mhinstaller.sh";
$baseurl = "http://kickstart/installs/panels/plesk/linux/$psaver/mh";
$fullurl = "$baseurl/$file";                                
                                                               
$postres = lwpfetch(url => $fullurl, file => "$workdir/$file");
if (($postres->[0] == 0) && (-f "$workdir/$file")) {
        postlog("INFO: Download of install script ($file) successful");
} else {
        postlog("FATAL: Download of install script ($file) failed");
}

#Download the SSL fix
my $fname="";
if ( $product =~ /RedHat/i ){

	if ( $version =~ /el5/i ){
		if($arch =~ /i386/i){
			$fname="sw-cp-server-1.0-6.201004011432.rhel5.i386.rpm";
		} elsif ($arch =~ /x86_64/i){
			$fname="sw-cp-server-1.0-6.201004011432.rhel5.x86_64.rpm";
		}
	} elsif ($version =~ /el4/i){
		if($arch =~ /i386/i){
			$fname="sw-cp-server-1.0-6.201004011137.rhel4.i386.rpm";
		} elsif ($arch =~ /x86_64/i){
			$fname="sw-cp-server-1.0-6.201004011235.rhel4.x86_64.rpm";
		}
	}
}
if($fname ne ""){
	$baseurl = sprintf("http://kickstart/installs/panels/plesk/linux/%s",$psaver);
	$file=$fname;
	$fullurl = "$baseurl/$file";
	print "Fetching plesk ssl fix file $fullurl\n";
	$postres = lwpfetch(url => $fullurl, file => "$workdir/$file");
	if (($postres->[0] == 0) && (-f "$workdir/$file")) {
	        postlog("INFO: Download of SSL fix script ($file) successful");
	} else {
	        postlog("FATAL: Download of SSL fix script ($file) failed");
	}
} 

chmod(0755, "$workdir/mhinstaller.sh");