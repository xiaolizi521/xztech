<?
/**
 * Cory Becker
 * May 18, 2007
 * www.corybecker.com
 */

class db
{
	// ------------------------------------------------------------
	// Class fields
	// ------------------------------------------------------------
	var $db = array(	'hostname' => 'localhost',
						 	'username' => 'iac_timesheet',
							'password' => 'timesheet',
							'database' => 'iac_timesheet'  );
					
	var $con = array( 'ref' => null,  
							'state' => 0 );
		
	var $result = array( 'ref' => null, 
								'rows' => null,
								'array' => null );
										
	var $debug = false;
		
	// ------------------------------------------------------------
	// Constructors
	// ------------------------------------------------------------
	function __construct()
	{
		if($this->debug) debug( "DB", "Constructed" );
		$this->connect();
	}
	
	function db()
	{
		$this->__construct();
	}
	
	// ------------------------------------------------------------
	// Core databse interaction methods
	// ------------------------------------------------------------
	function connect()
	{
		if( $this->con['state'] != 1 )
		{
			$this->con['ref'] = @mysql_connect( $this->db['hostname'], $this->db['username'], $this->db['password'] ) or die( "<p><strong>Database server offline.</strong></p><p>Please try again soon or contact your web host.</p>" );
			mysql_select_db( $this->db['database'] );
			$this->con['state'] = 1;
		}
	}
	
	
	// ------------------------------------------------------------
	// SQL execution method
	// ------------------------------------------------------------
	function query( $sql )
	{
		@mysql_free_result( $this->result['ref'] );
		
		//echo $sql."<br><br>";
		
		if($this->debug) debug( "Query", $sql );
				
		@$this->result['ref'] = mysql_query( $sql );
		@$this->result['rows'] = mysql_num_rows( $this->result['ref'] );
	}
	
	
	// ------------------------------------------------------------
	// Database helper methods
	// ------------------------------------------------------------
	function add( $table, $array )
	{	
		
		$i = 0;
		foreach( $array as $key => $value)
		{
			$i++;
			
			if( is_int( $value ) )
			{
				$values .= " ".mysql_real_escape_string($value);
				$columns .= " `".mysql_real_escape_string($key)."`";
			}
			else
			{
				$values .= " '".mysql_real_escape_string($value)."'";
				$columns .= " `".mysql_real_escape_string($key)."`";
			}
			
			if( $i != count($array) )
			{
				$values .= ",";
				$columns .= ",";
			}
		}
		
		$query = "INSERT INTO `$table` ($columns) VALUES($values	);";
		
		$this->query( $query );
	}
	
	function updateNoId( $table, $array )
	{
		if($this->debug) debug( "updateNoId(#,#,#)", "updateNoId Method" );
		
		if( count($array) > 0 )
		{
			$query .= "UPDATE $table SET";
			$i = 0;
			foreach( $array as $key => $value )
			{			
				$i++;		
				
				if( is_int( $value ) )
					$query .= " ".mysql_real_escape_string($key)." = ".mysql_real_escape_string($value);
				else
					$query .= " ".mysql_real_escape_string($key)." = '".mysql_real_escape_string($value)."'";	
					
				// Add comma to separate set statements
				if( $i != count($array) )
					$query .= ",";
			}
			$this->query( $query );		
		}
	}
	
	function update( $table, $array, $whereArray )
	{
		if($this->debug) debug( "update(#,#,#)", "Update method" );
		
		if( count($array) > 0 )
		{
			$query .= "UPDATE $table SET";
			$i = 0;
			foreach( $array as $key => $value )
			{			
				$i++;		
				
				if( is_int( $value ) )
					$query .= " ".mysql_real_escape_string($key)." = ".mysql_real_escape_string($value);
				else
					$query .= " ".mysql_real_escape_string($key)." = '".mysql_real_escape_string($value)."'";	
					
				// Add comma to separate set statements
				if( $i != count($array) )
					$query .= ",";
			}
			
			$i = 0;
			$where = " WHERE";
			
			foreach( $whereArray as $key => $value )
			{
				$i++;
				if( is_int( $value ) )
					$where .= " ".mysql_real_escape_string($key)." = ".mysql_real_escape_string($value);
				else
					$where .= " ".mysql_real_escape_string($key)." = '".mysql_real_escape_string($value)."'";	
					
				// Add comma to separate set statements
				if( $i != count($whereArray) )
					$where .= " AND";
			}
			$query .= $where;
			$this->query( $query );
		}
	}
		
	function get( $table, $whereArray )
	{
		if($this->debug) debug( "get(#,#)", "Get method" );
		
		$query = "SELECT * FROM `$table`";
		
		if( count($whereArray) > 0 )
		{
			$i = 0;
			
			$query .= " WHERE";

			foreach( $whereArray as $key => $value)
			{	
				$i++;
				
				if( is_int( $value ) )
					$query .= " `".mysql_real_escape_string($key)."` = ".mysql_real_escape_string($value);
				else
					$query .= " `".mysql_real_escape_string($key)."` = '".mysql_real_escape_string($value)."'";
					
				if( $i != count($whereArray) )
					$query .= " AND";
			}
				
		}

		$this->query( $query );
		
		return $this->result['array'];
	}
	
	function getID( $table, $id )
	{
		$query = "SELECT * FROM $table WHERE id = $id";

		$this->query( $query );
		
		return $this->result['array'];
	}
	
	function delete( $table, $whereArray )
	{
		$i = 0;
		
		foreach( $whereArray as $key => $value)
		{	
			$i++;
			
			if( is_int( $value ) )
				$where .= " ".mysql_real_escape_string($key)." = ".mysql_real_escape_string($value);
			else
				$where .= " ".mysql_real_escape_string($key)." = '".mysql_real_escape_string($value)."'";
				
			if( $i != count($whereArray) )
				$query .= " AND";
		}
		
		$this->query( "DELETE FROM $table WHERE$where" );
		
	}
	
	function deleteAll( $table )
	{
		$this->query( "DELETE FROM $table" );
	}
	
	// Remove methods (EXACT same thing as delete methods)
	function remove( $table, $whereArray )
	{
		$this->delete( $table, $whereArray );
	}
	
	function removeAll( $table )
	{
		$this->deleteAll( $table );
	}
	
	// ------------------------------------------------------------
	// Scaffolding methods
	// ------------------------------------------------------------
	function scaffold( $action )
	{
		switch( $action )
		{
			case( "view" ):
				$this->scaffoldView();
				break;
		}
	}
	
	function scaffoldView()
	{
		if( $this->result['rows'] == 0 )
			echo "<br><strong>No result!</strong><br";
		else
		{
			var_dump( $this->result['array']);
			echo "<table border=1>";				
 			foreach( $this->result['array'] as $key => $value )			// NEED TO ITERATE THROUGH EACH ROW, NOT JUST ONE!
			{
				echo "<tr><td>$key</td><td>$value</td></tr>";
			}
		}
	}
	
	// function hasNextRow();
	// function nextRow();
	

}
?>
