<?php

require_once("class.db.php");


/* UserDB interface. */

class UserDB extends DB implements iDB {
	
	// Implementation of function mysqli_stmt_prepare
	public function stmtPrepare(){
		
	}
	
	// Implementation of function mysqli_stmt_execute
	public function stmtExecute(){
		
	}
	
	// Implementation of function mysqli_stmt_close
	public function stmtClose(){
		
	}
	
	// Implementation of function mysqli_stmt_bind_param
	public function bindParms(){
		
	}
	
	// Implementation of function mysqli_stmt_bind_result
	public function bindResult(){
		
	}
	
	// Implementation of function mysqli_stmt_fetch
	public function fetchRes(){
		
	}

	// Implementation of function mysqli_stmt_affected_rows and mysqli_stmt_num_rows
	public function rowsAffected() {
		
	}
	
}

?>