package MACFun;

BEGIN {
	use Exporter();
	our ( $VERSION, @ISA, @EXPORT, @EXPORT_OK, %EXPORT_TAGS );

	$VERSION     = 1.00;
	@ISA         = qw(Exporter);
	@EXPORT      = ();
	%EXPORT_TAGS = ();
	@EXPORT_OK   = qw();
}

#############################
#   Standard perl modules   #
#############################
use strict;
use warnings;
use DBI;

# DB tables
my $mac_list     = "mac_list";
my $os_list      = "os_list";
my $pxe_list     = "pxe_list";
my $status_list  = "status_list";
my $task_list    = "task_list";
my $macid_ipinfo = "xref_macid_ipaddr";
my $macid_osinfo = "xref_macid_osload";

#Class Instantiation
sub new {
	my ( $class, %argv ) = @_;
	my $this = bless {
		_dbh         => undef,    # Database handle
		_macaddr     => undef,    # MAC address
		_halfmac     => undef,    # Last half of MAC address
		_pxemac      => undef,    # Name of PXE file for mac
		_macID       => undef,
		_osload      => undef,    # Current OS load
		_osloadID    => undef,
		_pxe         => undef,    # Current PXE target
		_pxeID       => undef,
		_task        => undef,    # Current task
		_taskID      => undef,
		_status      => undef,    # Current status
		_statusID    => undef,
		_oldstatus   => undef,    # Last status
		_oldstatusID => undef,
		_timestamp   => undef,    # Last update
		_ipaddr      => undef,
		_vlan        => undef,
		_licenses    => undef,    # Base64 encoded licenses
		_postconf    => undef,    # Hash ref of config
		_hardware    => undef,    # Hash ref of hardware
		_updates     => {},       # Array ref of updated vars
		_errors      => [],       # Last error message
		_has3ware    =>
		  undef # This is a boolean flag that checks to see there is 3ware card installed.
	}, $class;

	foreach ( keys %argv ) {
		if (/^-?dbh/i) {
			$this->{_dbh} = delete( $argv{$_} );
		}
		elsif (/^-?macaddr/i) {
			$this->{_macaddr} = delete( $argv{$_} );
		}
	}

	if ( my $dbh = $this->{_dbh} ) {
		$dbh->ping() == 1 or $this->error("DBH dead?");
	}

	if ( my $macaddr = lc( $this->{_macaddr} ) ) {

		# Store the lowercase MAC
		$this->{_macaddr} = $macaddr;

		# Create pxemac
		( $this->{_pxemac} = $macaddr ) =~ s/:/-/g;
		$this->{_pxemac} = "01-" . $this->{_pxemac};

		# Store the last 3 octets in _halfmac
		( $this->{_halfmac} = $macaddr ) =~ s/^(\w{2}:){3}//g;

		# Get the macID or create the MAC object
		( $this->macID() ) || $this->create();
	}

	$this;
}

sub create {
	my $self = shift();
	my $dbh  = $self->dbh();
	$dbh->do( "INSERT INTO $mac_list (mac_address) VALUES(?)",
		undef, $self->macaddr() );

	if ( $self->macID() == 0 ) {
		$self->error("Unable to create MAC object");
		return undef;
	}

	$self->status("new");
	$self->osload("sbrescue");
	$self->pxe("sbrescue");
	$self->task("default");
	$self->vlan("200");
	$self->ipaddr("0.0.0.0");
	$self->update();

	$self;
}

sub setUpdate {
	my $self = shift();
	my ( $update, $set ) = @_;

	$self->{_updates}->{$update} = $set;
}

sub error {
	my $self = shift();

	if (@_) {
		my $error = shift();
		push @{ $self->{_errors} }, $error;
	}

	return $self->{_errors};
}

sub dbh {
	my $self = shift();
	return $self->{_dbh};
}

sub macaddr {
	my $self = shift();
	return $self->{_macaddr};
}

sub halfmac {
	my $self = shift();
	return $self->{_halfmac};
}

sub pxemac {
	my $self = shift();
	return $self->{_pxemac};
}

sub macID {
	my $self = shift;
	if ( $self->{_macID} ) { return $self->{_macID}; }
	else {
		my $dbh    = $self->dbh();
		my $result =
		  $dbh->selectall_arrayref(
			"SELECT id FROM $mac_list WHERE mac_address = ?",
			undef, $self->macaddr() );
		if ( $result->[0] ) {
			$self->{_macID} = $result->[0]->[0];
		}
		else {
			$self->{_macID} = undef;
			$self->error("Unable to get MAC ID");
		}
		return $self->{_macID};
	}
}

sub osload {
	my $self = shift();

	if (@_) {
		$self->{_osload}   = shift();
		$self->{_osloadID} = undef;
		$self->osloadID();

		# Fun stuff.  Since an OS load can now be a combination of a
		# PXE target and task, we need to set them independently
		if ( $self->{_osload} =~
			/(burnin|default|deb30ks|deb31ks|sbrescue|zerofill)/ )
		{
			$self->pxe("sbrescue");
			$self->task($1);
		}
#                elsif ( $self->{_osload} =~ /hw_raidsetup/ )
#                {
#                        $self->pxe("sbrescue");
#                        $self->task($1);
#                }
		elsif ( $self->{_osload} =~ /^win2k8.*$/ )
                {
                        $self->pxe("win2k8");
                        $self->task("win2k8_tasks");
                }
		elsif ( $self->{_osload} =~ /^win2k3?.*$/ ) 
		{    
			#$self->pxe("sbrescue");
			#$self->task("windows-copy");
                        $self->pxe("win2k8");
                        $self->task("win2k8_tasks");

		}
		elsif ( $self->{_osload} =~ /ctbmr?.*$/ ) 
		{    
			#$self->pxe("sbrescue");
			#$self->task("windows-copy");
                        $self->pxe("win2k8");
                        $self->task("win2k8_tasks");
		}
		else 
		{
			$self->pxe( $self->{_osload} );
			$self->task("sbrescue");
		}

		$self->setUpdate( "osload", 1 );
		return $self->{_osload};
	}
	elsif ( $self->{_osload} ) { return $self->{_osload}; }
	else {
		my $dbh    = $self->dbh();
		my $result = $dbh->selectall_arrayref(
"SELECT mac_list_id, t2.id,t2.osload FROM xref_macid_osload t1,$os_list t2 WHERE t1.mac_list_id = ? AND t2.id = t1.os_list_id",
			undef, $self->macID()
		);
		if ($result) {
			$self->{_osloadID} = $result->[0]->[1];
			$self->{_osload}   = $result->[0]->[2];
		}
		else {
			$self->{_osloadID} = undef;
			$self->{_osload}   = undef;
			$self->error("Unable to get OS load");
		}
		$self->setUpdate( "osload", "0" );
		return $self->{_osload};
	}
}

#JR@10-05-2006: This is a new fuctions (Please see comments below)
#Detect 3ware card:  This method checks to see if there is a 3ware Raid card installed
sub has3ware {
	my $self = shift();

	#If value is already set in the object then return value
	if ( $self->{_has3ware} ) { return $self->{_has3ware}; }

	#Make call to database to dry to retrieve it.
	else {
		my $dbh = $self->dbh();

#Samle Query: select distinct value from hardware where mac_list_id in (select id from mac_list where mac_address = '00:30:48:59:1e:e4') and param = '3ware';
		my $result = $dbh->selectall_arrayref(
"select distinct value from hardware where mac_list_id in (select id from mac_list where mac_address = ?) and param = '3ware';",
			undef, $self->macaddr()
		);
		if ( $result->[0] ) {
			$self->{_has3ware} = 1;
		}
		else {
			$self->{_has3ware} = undef;
		}

		return $self->{_has3ware};
	}
}

# Set the default windows task:  This method will dynamically set the default windows taskfile based on the type of windows
# based on the name.
sub setWinTaskfile {

}

sub osloadID {
	my $self = shift();

	if ( $self->{_osloadID} ) { return $self->{_osloadID}; }
	else {
		my $dbh    = $self->dbh();
		my $result =
		  $dbh->selectall_arrayref( "SELECT id FROM $os_list WHERE osload = ?",
			undef, $self->osload() );
		if ( $result->[0] ) {
			$self->{_osloadID} = $result->[0]->[0];
		}
		else {
			$self->{_osloadID} = undef;
			$self->error( "Unable to get os_list ID for " . $self->osload() );
		}
		return $self->{_osloadID};
	}
}

sub pxe {
	my $self = shift;

	# If data is provided, set PXE and queue for update
	if (@_) {
		$self->{_pxe}   = shift;
		$self->{_pxeID} = undef;
		$self->pxeID();
		$self->setUpdate( "pxe", 1 );
		return $self->{_pxe};
	}

# If no data is provided and PXE is already set, return and leave update queue as is
	elsif ( $self->{_pxe} ) { return $self->{_pxe}; }

# No data provided, PXE not set - get information from DB, remove from update queue
	else {
		my $dbh    = $self->dbh();
		my $result = $dbh->selectall_arrayref(
"SELECT mac_list_id, t2.id, t2.pxefile FROM xref_macid_osload t1, $pxe_list t2 WHERE t1.mac_list_id = ? AND t2.id = t1.pxe_list_id",
			undef, $self->macID()
		);
		if ($result) {
			$self->{_pxeID} = $result->[0]->[1];
			$self->{_pxe}   = $result->[0]->[2];
		}
		else {
			$self->{_pxeID} = undef;
			$self->{_pxe}   = undef;
			$self->error("Unable to get PXE target");
		}
		$self->setUpdate( "pxe", "0" );
		return $self->{_pxe};
	}
}

sub pxeID {
	my $self = shift();

	if ( $self->{_pxeID} ) { return $self->{_pxeID}; }
	else {
		my $dbh    = $self->dbh();
		my $result =
		  $dbh->selectall_arrayref(
			"SELECT id FROM $pxe_list WHERE pxefile = ?",
			undef, $self->pxe() );
		if ( $result->[0] ) { $self->{_pxeID} = $result->[0]->[0]; }
		else {
			$self->{_pxeID} = 0;
			$self->error( "Unable to get pxe_list ID for " . $self->pxe() );
		}
		return $self->{_pxeID};
	}
}

sub task {
	my $self = shift();

	if (@_) {
		$self->{_task}   = shift();
		$self->{_taskID} = undef;
		$self->taskID();

		$self->setUpdate( "task", 1 );
		return $self->{_task};
	}
	elsif ( $self->{_task} ) { return $self->{_task}; }
	else {
		my $dbh    = $self->dbh();
		my $result = $dbh->selectall_arrayref(
"SELECT mac_list_id, task_list_id, t2.taskfile FROM xref_macid_osload t1, $task_list t2 WHERE t1.mac_list_id = ? AND t2.id = t1.task_list_id",
			undef, $self->macID()
		  )
		  || 0;
		if ($result) {
			$self->{_taskID} = $result->[0]->[1];
			$self->{_task}   = $result->[0]->[2];
		}
		else {
			$self->{_taskID} = undef;
			$self->{_task}   = undef;
			$self->error("Unable to get MAC task");
		}
		$self->setUpdate( "task", "0" );
		return $self->{_task};
	}
}

sub taskID {
	my $self = shift();

	if ( $self->{_taskID} ) { return $self->{_taskID}; }
	else {
		my $dbh    = $self->dbh();
		my $result =
		  $dbh->selectall_arrayref(
			"SELECT id FROM $task_list WHERE taskfile = ?",
			undef, $self->task() );
		if ( $result->[0] ) {
			$self->{_taskID} = $result->[0]->[0];
		}
		else {
			$self->{_taskID} = 0;
			$self->error( "Unable to fetch ID for " . $self->task() );
		}
		return $self->{_taskID};
	}
}

sub status {
	my $self = shift();

	if (@_) {
		$self->{_oldstatus}   = $self->{_status}   || "new";
		$self->{_oldstatusID} = $self->{_statusID} || 1;
		$self->{_status}      = shift();
		$self->{_statusID}    = undef;
		$self->statusID();
		$self->{_timestamp} = time();

		$self->setUpdate( "status", 1 );
		return $self->{_status};
	}
	elsif ( $self->{_status} ) { return $self->{_status}; }
	else {
		my $dbh    = $self->dbh();
		my $result = $dbh->selectall_arrayref(
"SELECT mac_list_id, old_status_id, t2.status as old_status, new_status_id, t3.status as new_status, date_added FROM macid_status_current t1, $status_list t2, $status_list t3 WHERE mac_list_id = ? AND t2.id = t1.old_status_id AND t3.id = t1.new_status_id ORDER BY date_added DESC LIMIT 1",
			undef, $self->macID()
		);
		if ($result) {
			$self->{_oldstatusID} = $result->[0]->[1];
			$self->{_oldstatus}   = $result->[0]->[2];
			$self->{_statusID}    = $result->[0]->[3];
			$self->{_status}      = $result->[0]->[4];
			$self->{_timestamp}   = $result->[0]->[5];
		}
		else {
			$self->{_statusID} = undef;
			$self->{_status}   = undef;
			$self->error("Unable to get MAC status");
		}
		$self->setUpdate( "status", "0" );
		return $self->{_status};
	}
}

sub statusID {
	my $self = shift();

	if ( $self->{_statusID} ) { return $self->{_statusID}; }
	else {
		my $dbh    = $self->dbh();
		my $result =
		  $dbh->selectall_arrayref(
			"SELECT id FROM $status_list WHERE status = ?",
			undef, $self->status() );
		if ( $result->[0] ) {
			$self->{_statusID} = $result->[0]->[0];
		}
		else {
			$self->{_statusID} = 0;
			$self->error( "Unable to get status_list ID for " . $self->status() );
		}
		return $self->{_statusID};
	}
}

sub timestamp {
	my $self = shift();

	if ( $self->{_timestamp} ) { return $self->{_timestamp}; }
	else {
		my $dbh    = $self->dbh();
		my $result = $dbh->selectall_arrayref(
"SELECT mac_list_id,date_added FROM macid_status_current WHERE mac_list_id = ? ORDER BY timestamp DESC LIMIT 1",
			undef, $self->macID()
		);
		if ($result) {
			$self->{_timestamp} = $result->[0]->[1];
		}
		else {
			$self->{_timestamp} = 0;
			$self->error("Unable to get MAC timestamp");
		}
		return $self->{_timestamp};
	}
}

sub ipaddr {
	my $self = shift;

	if (@_) {
		$self->{_ipaddr} = shift;
		$self->setUpdate( "ipaddr", 1 );
		return $self->{_ipaddr};
	}
	elsif ( $self->{_ipaddr} ) { return ( $self->{_ipaddr} ); }
	else {
		my $dbh    = $self->dbh();
		my $result = $dbh->selectall_arrayref(
"SELECT mac_list_id,ip_address FROM $macid_ipinfo WHERE mac_list_id = ?",
			undef, $self->macID()
		);
		if ($result) {
			$self->{_ipaddr} = $result->[0]->[1];
		}
		else {
			$self->{_ipaddr} = "0.0.0.0";
			$self->error("Unable to get MAC ipaddr");
		}
		$self->setUpdate( "ipaddr", 0 );
		return $self->{_ipaddr};
	}
}

sub vlan {
	my $self = shift();

	if (@_) {
		$self->{_vlan} = shift();

		$self->setUpdate( "vlan", 1 );
		return $self->{_vlan};
	}
	elsif ( $self->{_vlan} ) { return $self->{_vlan}; }
	else {
		my $dbh    = $self->dbh();
		my $result = $dbh->selectall_arrayref(
"SELECT mac_list_id,vlan_id FROM $macid_ipinfo WHERE mac_list_id = ?",
			undef, $self->macID()
		);
		if ($result) {
			$self->{_vlan} = $result->[0]->[1];
		}
		else {
			$self->{_vlan} = 0;
			$self->error("Unable to get MAC vlan");
		}
		$self->setUpdate( "vlan", 0 );
		return $self->{_vlan};
	}
}

sub set_rebooted {
	my $self = shift();
	$self->setUpdate( "rebooted", 1 );
}

sub get_rebooted {
	my $self = shift();
	if ( $self->{_rebooted} ) { return $self->{_rebooted}; }
	else {
		my $dbh      = $self->dbh();
		my $rebooted = $dbh->selectall_arrayref(
"SELECT mac_list_id, max(date_added) AS date_added FROM macid_reboot_history WHERE mac_list_id = ? GROUP BY mac_list_id",
			undef, $self->macID
		);
		if ( $rebooted->[0] ) {
			$self->{_rebooted} = $rebooted->[0]->[1];
		}
		else {
			$self->{_rebooted} = undef;
			$self->error("Unable to get MAC reboot timestamp");
		}
		$self->setUpdate( "rebooted", 0 );
		return $self->{_rebooted};
	}
}

# $self->hardware($hashref)
# $hasref = $self->hardware()
sub hardware {
	my $self = shift();

	if (@_) {
		$self->{_hardware} = shift();
		$self->setUpdate( "hardware", 1 );
		return $self->{_hardware};
	}
	elsif ( $self->{_hardware} ) { return $self->{_hardware}; }
	else {
		my $dbh    = $self->dbh();
		my $result = $dbh->selectall_arrayref(
"SELECT mac_list_id,param,value FROM hardware WHERE mac_list_id = ?",
			undef, $self->macID()
		);
		if ( $result->[0] ) {
			$self->{_hardware} = {};
			foreach my $row ( @{$result} ) {
				$row->[1] =~ s/(^\s+|\s+$)//g;
				$row->[2] =~ s/(^\s+|\s+$)//g;
				$row->[2] =~ y/A-Z/a-z/;
				$self->{_hardware}->{ $row->[1] } = $row->[2];
			}
		}
		else {
			$self->{_hardware} = undef;
			$self->error("Unable to get MAC hardware");
		}
		$self->setUpdate( "hardware", 0 );
		return $self->{_hardware};
	}
}

sub postconf {
	my $self = shift;
	if (@_) {
		$self->{_postconf} = shift();
		$self->setUpdate( "postconf", 1 );
		return $self->{_postconf};
	}
	elsif ( $self->{_postconf} ) { return $self->{_postconf}; }
	else {
		my $dbh    = $self->dbh();
		my $result = $dbh->selectall_arrayref(
"SELECT mac_list_id,param,value FROM postconf WHERE mac_list_id = ?",
			undef, $self->macID()
		);
		if ( ($result) && ( scalar( @{$result} ) > 0 ) ) {
			foreach my $row ( @{$result} ) {
				$self->{_postconf}->{ $row->[1] } = $row->[2];
			}
		}
		else {
			$self->{_postconf} = {};
			$self->error("Unable to get MAC postconf");
		}
		$self->setUpdate( "postconf", 0 );
		return $self->{_postconf};
	}
}

sub licenses {
	my $self = shift;
	if (@_) {
		$self->{_licenses} = shift();
		$self->setUpdate( "licenses", 1 );
		return $self->{_licenses};
	}
	elsif ( $self->{_licenses} ) { return $self->{_licenses}; }
	else {
		my $dbh    = $self->dbh();
		my $result = $dbh->selectall_arrayref(
"SELECT mac_list_id,licenses FROM licenses WHERE mac_list_id = ? LIMIT 1",
			undef, $self->macID()
		);
		if ($result) {
			$self->{_licenses} = $result->[0]->[1];
		}
		else {
			$self->{_licenses} = undef;
			$self->error("Unable to get MAC licenses");
		}
		$self->setUpdate( "licenses", 0 );
		return $self->{_licenses};
	}
}

# END of the mundane stuff

sub update {
	my $self    = shift();
	my $updates = $self->{_updates};
	if ( scalar( keys( %{$updates} ) ) == 0 ) { return 0; }

	my $dbh = $self->dbh();

	if ( $updates->{hardware} ) {
		$dbh->do( "DELETE FROM hardware WHERE mac_list_id = ?",
			undef, $self->macID() );
		my $sth1 =
		  $dbh->prepare(
			"INSERT INTO hardware (mac_list_id,param,value) VALUES (?,?,?)");
		while ( my ( $param, $value ) = each( %{ $self->hardware() } ) ) {
			next if ( $param =~ /macaddr|ipaddr|status/i );
			$value =~ s/(^\s+|\s+$)//g;
			$value =~ y/A-Z/a-z/;
			$sth1->execute( $self->macID(), $param, $value );
		}
		$sth1->finish();
	}

	if ( $updates->{postconf} ) {
		$dbh->do( "DELETE FROM postconf WHERE mac_list_id = ?",
			undef, $self->macID() );
		my $sth1 =
		  $dbh->prepare(
			"INSERT INTO postconf (mac_list_id,param,value) VALUES (?,?,?)");
		while ( my ( $param, $value ) = each( %{ $self->postconf() } ) ) {
			$sth1->execute( $self->macID(), $param, $value );
		}
		$sth1->finish();
	}

	if ( $updates->{licenses} ) {
		$dbh->do( "DELETE FROM licenses WHERE mac_list_id = ?",
			undef, $self->macID() );
		$dbh->do( "INSERT INTO licenses (mac_list_id,licenses) VALUES (?,?)",
			undef, $self->macID(), $self->licenses() );
	}

	# Update OS info
	if ( $updates->{osload} or $updates->{pxe} or $updates->{task} ) {

#printf "UPDATE $macid_osinfo SET os_list_id = %d, pxe_list_id = %d, task_list_id = %d WHERE mac_list_id = %d", $self->osloadID, $self->pxeID, $self->taskID, $self->macID;
		my $rows = $dbh->do(
"UPDATE $macid_osinfo SET os_list_id = ?, pxe_list_id = ?, task_list_id = ? WHERE mac_list_id = ?",
			undef,
			$self->osloadID,
			$self->pxeID,
			$self->taskID,
			$self->macID
		);
		if ( $rows < 1 ) {
			$dbh->do(
"INSERT INTO $macid_osinfo (mac_list_id, os_list_id, pxe_list_id, task_list_id) VALUES(?,?,?,?)",
				undef,
				$self->macID,
				$self->osloadID,
				$self->pxeID,
				$self->taskID
			);
		}
	}

	# Update IP info
	if ( $updates->{ipaddr} or $updates->{vlan} ) {
		my $rows = $dbh->do(
"UPDATE $macid_ipinfo SET vlan_id = ?, ip_address = ? WHERE mac_list_id = ?",
			undef, $self->vlan, $self->ipaddr, $self->macID
		);
		if ( $rows < 1 ) {
			$dbh->do(
"INSERT INTO $macid_ipinfo (mac_list_id, vlan_id, ip_address) VALUES(?,?,?)",
				undef, $self->macID, $self->vlan, $self->ipaddr
			);
		}
	}

	# Update status
	if ( $updates->{status} ) {
		$dbh->do(
"INSERT INTO macid_status_history (mac_list_id, old_status_id, new_status_id) VALUES(?,?,?)",
			undef, $self->macID, $self->{_oldstatusID}, $self->{_statusID}
		);
		my $rows = $dbh->do(
"UPDATE macid_status_current SET old_status_id = ?, new_status_id = ?, date_added = now() WHERE mac_list_id = ?",
			undef, $self->{_oldstatusID}, $self->{_statusID}, $self->macID
		);
		if ( $rows < 1 ) {
			$dbh->do(
"INSERT INTO macid_status_current (mac_list_id,old_status_id,new_status_id) VALUES(?,?,?)",
				undef,
				$self->macID(),
				$self->{_oldstatusID},
				$self->{_statusID}
			);
		}

		if (   ( $self->status() eq "kickstarted" )
			&& ( $self->postconf()->{customer_number} ) )
		{
			$dbh->do(
"INSERT INTO macid_product_history (mac_list_id,product) VALUES (?,?)",
				undef,
				$self->macID(),
				join( "-",
					$self->postconf()->{customer_number},
					$self->postconf()->{server_number} )
			);
		}

	}

	if ( $updates->{rebooted} ) {
		$dbh->do(
"INSERT INTO macid_reboot_history (mac_list_id,reboot_status,date_added) VALUES(?,?,now())",
			undef, $self->macID, $self->status
		);
	}

	return 0;
}

sub logError {
	my $self    = shift;
	my $message = shift;
	my $dbh     = $self->dbh();

	my $rows = $dbh->do(
		"INSERT INTO macid_error_history 
        (mac_list_id, old_status_id, new_status_id, error_message, date_added)
        VALUES (?, ?, ?, ?, ?)", undef,
		$self->macID(), 1, $self->statusID(), $message, "now()"
	);

	return $rows;
}

sub lastError {
	my $self = shift;
	my $dbh  = $self->dbh();
	my $return;

	my $result = $dbh->selectall_hashref(
"SELECT mac_list_id, error_message, date_added FROM macid_error_history WHERE mac_list_id = ? ORDER BY date_added DESC LIMIT 1",
		"mac_list_id", undef, $self->macID()
	);

	if ( $result->{ $self->macID() } ) {
		$return = $result->{ $self->macID };
	}
	else {
		$return = { message => "none", date_added => "2001-01-01 00:00:00+00" };
	}
	$return->{macaddr} = $self->macaddr();

	return $return;
}

sub retire {
	my $self = shift;
	my $dbh  = $self->dbh();

	# We want to preseve hardware information
	#$dbh->do("DELETE FROM hardware WHERE macid = ?", undef, $self->macID);
	$dbh->do( "DELETE FROM postconf WHERE mac_list_id = ?",
		undef, $self->macID );
	$self->status("new");
	$self->ipaddr("0.0.0.0");
	$self->osload("sbrescue");
	$self->pxe("sbrescue");
	$self->task("default");
	$self->update();
	return 0;
}

1;
