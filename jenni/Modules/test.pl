use Geo::Weather;
my $weather = new Geo::Weather;
$weather->get_weather('78258');
print $weather->report();

