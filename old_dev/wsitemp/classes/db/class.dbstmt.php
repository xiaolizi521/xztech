<?php

final class DBStmt extends mysqli_stmt  {

	protected $varsBound = false;
	protected $results;
	
	function __construct($link, $query) {
		
		parent::__construct($link, $query);
	}

	public function fetch_assoc() {
	     
	    /*
	     * MySQLi prepared statements require result sets to be bound.
	     * 
	     * Checks first that variables are in fact bound.
	     * 
	     * Then executes the data result fetch.
	     */
	    
	    if (!$this->varsBound) {
		
		    $meta = $this->result_metadata();
		            
		    while ($column = $meta->fetch_field()) {
		                
		    /*
		     * Columns with spaces cause syntax errors.
		     * 
		     * Note: Column names with spaces is bad practice anyways.
		     * 
		     * Always wear protection.
		     */
		    
		    $columnName = str_replace(' ', '_', $column->name);
		                
		    $bindVarArray[] = &$this->results[$columnName];
		
		    }
	            
			call_user_func_array(array($this, 'bind_result'), $bindVarArray);
	            
			$this->varsBound = true;
	    }
	
		if ($this->fetch() != null) {
		            
			/*
			* [array] $this->results contains references and not data.
			* 
			* Now setting array to be associative data with the following loop.
			*/
			    
			foreach ($this->results as $k => $v) {
				
				$results[$k] = $v;
			}
			            
			return $results;
		} 
		        
		else {
			
			return null;
		        
		}
	    
	}
	    
	function __destruct() {
			
		$this->close();
			
	}
}

?>
