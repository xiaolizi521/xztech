package check_helper;

use strict;

sub new
{
	my $class = shift;
	my $self = {};
	$self->{trigger} = shift;
	$self->{errmsg} = "";
	return bless $self, $class;
}

sub check_columns
{
	my $self = shift;
	my $options_arg = shift;
	my @column_names = @_;
	
	my @allowed_options = qw/ALLOW_LIST ONLY_FROM_NULL/;
	my $oldtuple = $self->{trigger}{old};
	my $newtuple = $self->{trigger}{new};
	my $tablename = $self->{trigger}{table_name};
	my @deny_columns;
	my %options;
   	
	# For backwards compatibility, see if we just have a boolean looking value
    if ($options_arg eq 'TRUE' or $options_arg eq 'T' or 
        $options_arg eq 'YES' or $options_arg eq 'Y' or $options_arg eq 1) 
    {
        $options{ALLOW_LIST} = 1;
    } elsif ($options_arg eq 'FALSE' or $options_arg eq 'F' or $options_arg eq 'NO' or $options_arg eq 'N' or $options_arg eq 0) 
    {
        $options{ALLOW_LIST} = 0;
    } else {	
    	# Treat the first argument as a list of options (which could be empty)
    	foreach my $option (split (/,\s*/, $options_arg)) {
			if(grep $_ eq $option, @allowed_options) { 
				# Note that this version of grep is supposedly faster
            	$options{$option} = 1;
        	} else {
            	die ("Unknown option ('$option') specified in first argument (allowed options are " . join(' ', @allowed_options) . ")");
			}
        }
    }

	# check if all passed columns really exist 
	foreach (@column_names) {
		die "column \"$_\" doesn't exist in relation $tablename" 
			unless exists $newtuple->{$_};
	}

	# Get the resulting set of columns. If ALLOW_LIST is true then we should
	# block only those columns not included in the list passed as a first param.

	if ($options{ALLOW_LIST} == 0) {
		@deny_columns = @column_names;
	} else {

		# Make a hash of column names to speed up searching
		my %column_names_hash;

		foreach my $colname (@column_names) {
			$column_names_hash{$colname} = 1;
		}

		# Get all relation columns that are not in column_names list and put
		# their names to the deny list.
		foreach my $colname (keys %{$oldtuple}) {
			push @deny_columns, $colname unless defined $column_names_hash{$colname};
		}
	}

	# Disallow updates for each column from deny list unless ONLY_FROM_NULL
	# is set and old tuple has NULL in the column to be updated
	foreach (@deny_columns) {
        if ($oldtuple->{$_} ne $newtuple->{$_}) {
        	unless ($options{ONLY_FROM_NULL} == 1 and !defined $oldtuple->{$_}) {
				$self->{errmsg} = sprintf "update of attribute \"%s\" denied by the trigger \"%s\"", $_, $self->{trigger}{name};
				return 0;
			}
        }
	}
	return 1;
}

sub check_condition 
{
	my $self = shift;
	my $condition = shift;
	my @columns = @_;
	my @args;
	my $argno;
	
	::elog(::DEBUG, "condition: $condition");
	# prepare a list of arguments, substituting OLD and NEW with perl equivalents
	foreach (@columns) {
		if (/^(OLD|NEW)\.(.*)$/) {
			my ($tupname, $attname) = ($1, $2);
			
			# double backslashes in $2
			$attname =~ s/\\/\\\\/;
			# also escape single quote characters.
			$attname =~ s/\'/\\\'/;
		 	my $argname = '$self->{trigger}{'.lc($tupname).'}{\''.$attname.'\'}';
			::elog(::DEBUG, "substition result: $argname");
			
			my $val = eval $argname;
			
			# do additional processing of non-numeric values
			if ($val =~ /\D/) {
				# double backslashes in argument
				$val =~ s/\\/\\\\/;
				# strip first and last quotes
				$val =~s/^\'(.*)\'$/\1/;
				# escape single quote characters
				$val =~ s/\'/\\\'/;
				# put value in quotes unless it is already quoted
				$val = "'".$val."'" unless $val =~ /^\'.*\'$/;
				# treat the argument as escape string
				$val = 'E'.$val;
			}
			
			::elog(::DEBUG, "argument value: $val");
			push @args, $val;
		} else {
			die "argument $argno should be the reference to a column of OLD or NEW tuples";
		}
		$argno++;
	}
	
	# form a condition string to check
	$condition = sprintf $condition, @args;
	my $condition_sql = "SELECT $condition AS result";
	::elog(::DEBUG, "condition_sql: $condition_sql, condition: $condition");
	
	# execute condition as SQL and get the result
	my $res = ::spi_exec_query($condition_sql);
	my $result = $res->{rows}[0]->{result};
	
	die "expression $condition_sql should return exactly one row" unless $res->{processed} == 1;
	die "expression $condition_sql result is not defined" unless defined $result;
	
	::elog(::DEBUG, "result: $result");
	
	if ($result eq 't') {
		::elog(::DEBUG, "allow");
		return 1;
	} elsif ($result eq 'f') {
		::elog(::DEBUG, "deny");
		$self->{errmsg} = sprintf "expression %s is false, %s is not allowed", 
			$condition, $self->{trigger}{event};
		return 0;
	}
	
	die "expression $condition_sql should return a result of boolean type";	
}

1;
