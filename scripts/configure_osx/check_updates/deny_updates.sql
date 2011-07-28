CREATE OR REPLACE FUNCTION deny_updates() RETURNS trigger AS
$$

	BEGIN { strict->import(); }

	use lib "/opt/local/share/postgresql83/contrib";
	use check_helper;
	
	my @column_names;

	# This should be a row level trigger */
	die 'should be called as a row level trigger' unless ($_TD->{level} eq 'ROW');

	# Do nothing if event is not UPDATE 
	return if ($_TD->{event} ne 'UPDATE');

	die "$_TD->{name}, missing options list argument"
	unless ($_TD->{argc} >= 1);

	my $options_arg = uc(shift @{$_TD->{args}});
    
	my $oldtuple = $_TD->{old};
	my $newtuple = $_TD->{new};
	
	if ($_TD->{argc} > 1) {
		@column_names = @{$_TD->{args}};
	} else {
		@column_names = ();
	}
	
	my $ch = new check_helper($_TD);
	my $result;
	
	eval {
		$result = $ch->check_columns($options_arg, @column_names);
	};
	die $@ if ($@);
	
	if (!$result) {
		die $ch->{errmsg};
	}

	return;
$$ LANGUAGE plperlu;

