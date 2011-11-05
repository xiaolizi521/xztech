<?
/*
** Query Example:
**
** $query = "select * from users";
**
** Data Example:
**
** $result = $test->query($query);
**
** print_r($result->fetch_assoc());
**
**
*/
include ("config/required.php");

$what = ( ( isset($_GET['what']) ) ? $_GET['what'] : "" );
switch ( $what )
{
	case "users":
		populateUsers();
		break;
	case "teams":
		populateTeams();
		break;
	case "data":
		populateData();
		break;
	case "profiles":
		populateProfiles();
		break;
	default:
		echo "<center>";
		echo "<a href=populate.php?what=users>Populate Users</a><br />";
		echo "<a href=populate.php?what=teams>Populate Teams</a><br />";
		echo "<a href=populate.php?what=data>Populate Data</a><br />";
		echo "<a href=populate.php?what=profiles>Populate Profils</a><br />";
		echo "</center>";
		break;
}

function populateUsers()
{
	$whatpulse = new DB ( DB_HOST, DB_USER, DB_PASS, DB_NAME );
	$wsitest   = new DB ( DB_HOST, DB_USER, DB_PASS, 'wsitest' );
	
	$result = $whatpulse->query( "SELECT user, password, email, logdate FROM whatpulse" );
	
	$total = $result->num_rows;
	$sofar = 0;
	$x = 0;
	
	while ($user = $result->fetch_assoc())
	{
		$query = sprintf("INSERT INTO users (username,password,date) VALUES ('%s','%s','%s')",
							mysql_escape_string($user['user']),
							mysql_escape_string($user['password']),
							mysql_escape_string($user['logdate']));
		$wsitest->query($query);
		
		$x++;
		
		if ($x == 1000)
		{
			$sofar += $x;
			$x = 0;
			
			echo "Inserted $sofar/$total rows...<br />";
		}
		
		if ($sofar == $total || ($x + $sofar) == $total)
		{
			$inserted = $sofar + $x;
			echo "Inserted $inserted/$total rows!<br />Done...";
		}
	}
}

function populateTeams()
{
	$whatpulse = new DB ( DB_HOST, DB_USER, DB_PASS, DB_NAME );
	$wsitest   = new DB ( DB_HOST, DB_USER, DB_PASS, 'wsitest' );
	
	$result = $whatpulse->query( "SELECT tname, tkeys, tclicks, tmembers FROM whatpulse" );
	
	$total = $result->num_rows;
	$sofar = 0;
	$x = 0;
	
	while ($team = $result->fetch_assoc())
	{
		$query = sprintf( "INSERT INTO teams (`name`,`keys`,`clicks`,`members`) VALUES ('%s','%s','%s','%s')",
							mysql_escape_string($team['tname']),
							mysql_escape_string($team['tkeys']),
							mysql_escape_string($team['tclicks']),
							mysql_escape_string($team['tmembers']) );
							
		$wsitest->query($query);
		
		$x++;
		
		if ( $x == 1000 )
		{
			$sofar += $x;
			$x = 0;
			
			echo "Inserted $sofar/$total rows...<br />";
		}
		
		if ( $sofar == $total || ($x + $sofar) == $total )
		{
			$inserted = $sofar + $x;
			echo "Inserted $inserted/$total rows!<br />Done...";
		}
	}
}

function populateData()
{
	$whatpulse = new DB ( DB_HOST, DB_USER, DB_PASS, DB_NAME );
	$wsitest   = new DB ( DB_HOST, DB_USER, DB_PASS, 'wsitest' );
	
	$result = $wsitest->query( "SELECT id, username FROM users" );
	
	while ( $user = $result->fetch_assoc() )
	{
		$query = sprintf( "SELECT tkc, tmc, rank, trank, tname FROM whatpulse WHERE `user` = '%s'", 
							mysql_escape_string($user['username']) );
	
		$res = $whatpulse->query( $query );
		$info = $res->fetch_assoc();
		
		$tquery = sprintf( "SELECT id FROM teams WHERE `name` = '%s'",
							mysql_escape_string($info['tname']) );
		$tres = $wsitest->query( $tquery );
		$teaminfo = $tres->fetch_assoc();
				
		$insert = sprintf( "INSERT INTO data (`%s`,`%s`,`%s`,`%s`,`%s`,`%s`) VALUES ('%s','%s','%s','%s','%s','%s')", 							"userid", "keys", "clicks", "rank", "tid", "trank",
							mysql_escape_string($user['id']),
							mysql_escape_string($info['tkc']),
							mysql_escape_string($info['tmc']),
							mysql_escape_string($info['rank']),
							mysql_escape_string($teaminfo['id']),
							mysql_escape_string($info['trank']) );
							
		$wsitest->query( $insert );
	}
}

function populateProfiles()
{
	$whatpulse = new DB ( DB_HOST, DB_USER, DB_PASS, DB_NAME );
	$wsitest   = new DB ( DB_HOST, DB_USER, DB_PASS, 'wsitest' );
	
	$result = $wsitest->query( "SELECT id, username FROM users" );
	
	while ( $user = $result->fetch_assoc() )
	{
		$query = sprintf( "SELECT country, email FROM whatpulse WHERE `user` = '%s'", 
							mysql_escape_string($user['username']) );
							
		$res = $whatpulse->query( $query );
		$info = $res->fetch_assoc();
		
		$insert = sprintf( "INSERT INTO profiles (`userid`,`location`,`email`) VALUES ('%s','%s','%s')",
							mysql_escape_string($user['id']),
							mysql_escape_string($info['country']),
							mysql_escape_string($info['email']) ); 
							
		$wsitest->query( $insert );
	}
}

?>