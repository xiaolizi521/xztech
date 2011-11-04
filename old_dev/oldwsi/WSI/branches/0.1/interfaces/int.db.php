<?php

/*
** DB Interface
** 
** Author: Adam Hubscher ~AKA~ OffbeatAdam/AgentGreasy
** Date: March 14th, 2008
**
** Purpose: To create an extensible framework for adding different levels of database interaction.
** 
** Use: Implement the interface, define the base required functions. Add additional functionality as needed.
**
*/

interface iDB {

// incomplete, finish later.
	public function prepare($statement, $execute, $iteration, $type);
	
	protected function execSingle();
	protected function execAssocSingle();
	protected function execAll();
	protected function execAssocAll();
}
?>