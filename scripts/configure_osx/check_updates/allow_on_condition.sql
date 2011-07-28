CREATE OR REPLACE FUNCTION allow_on_condition() RETURNS trigger AS
$$
	BEGIN { strict->import(); }
	
	use lib "/opt/local/share/postgresql83/contrib";
	use check_helper;
	
	# should be called as a row-level trigger
	die "should be called as a row-level trigger" unless ($_TD->{level} eq 'ROW');
	
	# check for the correct number of arguments
	die "$_TD->{name}, wrong number of arguments" unless ($_TD->{argc} >= 1);
	
	my $condition = shift @{$_TD->{args}};
	my @args = @{$_TD->{args}};
	
	my $ch = new check_helper($_TD);
	my $result;
	
	eval {
		$result = $ch->check_condition($condition, @args);
	};
	die $@ if ($@);
	
	if (!$result) {
        die $ch->{errmsg};
    }
	
	return;

$$ LANGUAGE plperlu;
