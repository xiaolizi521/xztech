<?php

/*
**
** WSI Signature Images Project
**
** Purpose: Class to control the production and use of
** Signature images created within and for the project.
**
**
** Usage: TBD
** 
** Method Requirements:
**
** MySQL 4.1/5.0+ Database
** Table Structure TBD
** PHP 5.0+
** Brain
** Fingers
** Life optional
**
** Created By: Adam Hubscher <AKA OFFBEATADAM/AGENTGREASY>
**
** USE EXPLICITLY DENIED UNLESS OTHERWISE COMMANDED TO DO SO.
*/

class Image {
	
	public $image;
	public $db;
	public $cacheobj;
	
	function __construct() {
		
		$this->db = new DB(DB_HOST, DB_USER, DB_PASS, DB_NAME);
		
	}
	
	final public function prepCache() {
		
		$this->cacheObj = new iCache();
		
		return $this->cacheObj;
	}
	
	final public function display() {
		
		return $this->cacheObj->getImage();
	}
	
}



?>