<?php
class wpulseXML {
  var $parser;
  var $tag;
  var $item;
  var $end_element;
  var $count = 1;
  var $user;
  
	function start_element($parser, $tag, $attributes) {
    $this->end_element = FALSE;
    	if ('user' == $tag) {
      		//echo "<strong>Tag: " . $tag . "</strong><br />";
    	  	$this->item = new wpulseUSER;
    	  	$this->tag = $tag;

    	  	
    	}
    	
    	elseif ('team' == $tag) {
    	  //echo "<strong>Tag: " . $tag . "</strong><br />";
    	  	$this->item->team = TRUE;
    	  	$this->tag = $tag;
    	}
    	
    	elseif (!empty($this->item)) {
      //echo "<strong>Tag: " . $tag . "</strong><br />";
    	  	$this->tag = $tag;
    	}
	}
  
	function end_element($parser, $tag) {
    	$this->end_element = TRUE;
    	if ('user' == $tag) {
	      	$this->item->update();
    	  	unset($this->item);
    	}
	}

	function character_data($parser, $data) {
		if(!$this->end_element) {
			if (!empty($this->item)) {
			    if($this->item->team && $this->tag == 'rank') {
			      $foo = explode(" of ",$data);
			      $this->item->teamrank = $foo[0];
			      $this->item->teammembers = $foo[1];
			    }
			    else {
			      if($this->item->team) {
			        $foo = 'team';
			        $foo .= $this->tag;
			        
			        $this->item->{$foo} = $data;
			      }
			      else {
					$this->item->{$this->tag} = $data;
		    	  }
				}
			}
			// $this->tag . "<br>" . $data . "<br>" . $this->tag . "<br><br>";
			$user[$count][$this->tag] = $data;
		}
		else {
			$count++;
		}
		//		echo html_entity_decode($data) . "<br />";	
		
	}
}


class wpulseUSER {
  
  public $name;
  public $country;
  public $rank;
  public $clicks;
  public $keys;
  public $teamname;
  public $teamclicks;
  public $teamkeys;
  public $teamrank;
  public $teammembers;
  public $db;
  public $db_host;
  public $db_user;
  public $db_pass;
  public $db_name;
  public $team;
  public $currusername;	
  
	function __construct () {
	  	  		  
	  $this->db_host = 'localhost';
	  	$this->db_user = 'offbea2_whatpuls';
	  	$this->db_pass = 'pulsestats';
	  	$this->db_name = 'offbea2_pulsestats';
	  	$this->currusername = $_POST['username'];
		$this->db = mysql_connect($this->db_host,$this->db_user,$this->db_pass) or die('connection failed to database');
		mysql_select_db($this->db_name, $this->db);
	}
	
	function update() {

		if ($this->currusername === $this->name) {
			//	echo $this->name . "<br>";
		  	if ($this->team) {
				$query = "UPDATE `whatpulse` SET "
				. "tkc = '" . str_replace(",","",$this->keys)
				. "', tmc = '" . str_replace(",","",$this->clicks)
				. "', rank ='" . str_replace(",","",$this->rank)
				. "', tname ='" . mysql_escape_string($this->teamname)
				. "', tkeys ='" . str_replace(",","",$this->teamkeys)
				. "', tclicks ='" . str_replace(",","",$this->teamclicks)
				. "', trank ='" . str_replace(",","",$this->teamrank)
				. "', country ='" . mysql_escape_string($this->country)
				. "', tmembers = '" . $this->teammembers ."' where user = '" . mysql_escape_string($this->name) . "'";
	
			}
	
			else {
	
				$query = "UPDATE `whatpulse` SET "
				. "tkc = '" . str_replace(",","",$this->keys)
				. "', tmc = '" . str_replace(",","",$this->clicks)
				. "', rank ='" . str_replace(",","",$this->rank)
				. "', country ='" . $this->country ."' where user = '" . mysql_escape_string($this->name) . "'";
			}
			/*}
			else {
				$query = "INSERT INTO `whatpulse` (`user`,`tkc`,`tmc`,`rank`,`tname`,`tkeys`,`tclicks`,`trank`,`country`,`tmembers`)
				VALUES (" . mysql_escape_string($this->name) . "," . $this->keys . "," . $this->clicks . "," . $this->rank . "," . mysql_escape_string($this->teamname). "," . $this->teamkeys . "," . $this->teamclicks . "," . $this->teamrank . "," . $this->country . "," . $this->teammembers . ")";
					}*/
		mysql_query($query) or die('mysql query failed' . mysql_error());
		}
	}
	
	function __destruct() {
	}
}
?>