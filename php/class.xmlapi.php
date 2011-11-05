<?php

/*
 *
 * This is an abstraction of the cPanel XML API for scripted use.
 *
 */

class cpxml {

	public $host;
	public $curl;

	private $port = 2087;
	private $protocol = "https://";
	private $debug = 0;

	function __construct($host){

		$this->host = $host;

		$this->curl = curl_init();

		curl_setopt($this->curl, CURLOPT_SSL_VERIFYPEER,0);
		curl_setopt($this->curl, CURLOPT_RETURNTRANSFER,1);
		curl_setopt($this->curl, CURLOPT_SSL_VERIFYHOST,0);
	}

	function __destruct(){

		curl_close($this->curl);
	}

	function auth($method, $username, $password){

		if($method === "hash"):

			$header[0] = "Authorization: WHM $user:" . preg_replace("'(\r|\n)'","",$hash);
			curl_setopt($this->curl, CURLOPT_HTTPHEADER,$header);
		else:

			curl_setopt($this->curl, CURLOPT_USERPWD, $username.":".$password);
		endif;
	}

	function 
