<?php

include "class.wsidb.php";


/* XML Parser Class */
/* Note: This is specifically designed for a direct purpose, however the
/* class can be used as a framework for all future XML designs.
/* It is inherently universal. */

class wpulseXML {
    var $parser;
    var $tag;
    var $item;
    var $end_element;
    var $count = 1;
    var $user;
    protected $wsiDB;
    
    function __construct () {
        try {
            $this->wsiDB = new wsiDB('hostname', 'username', 'password', 'db');
        }
        catch(ConnectException $exception) {
           echo "Connection Error\n";
           var_dump($exception->getMessage());
        }
        catch(Exception $exception) {
           echo "Other Script Error\n";
           var_dump($exception->getMessage());
        }
    }
    
    function start_element($parser, $tag, $attributes) {
        $this->end_element = FALSE;
        if ('user' == $tag) {
      //DEBUG: echo "<strong>Tag: " . $tag . "</strong><br />";
            $this->item = new wpulseUSER($this->wsiDB);
            $this->tag = $tag;
        }

        elseif ('team' == $tag) {
     //DEBUG: echo "<strong>Tag: " . $tag . "</strong><br />";
            $this->item->team = TRUE;
            $this->tag = $tag;
        }

        elseif (!empty($this->item)) {
     //DEBUG: echo "<strong>Tag: " . $tag . "</strong><br />";
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
        if($this->tag == "name") {
		$username = $this->wsiDB->real_escape_string($data);
            try {
                $result = $this->wsiDB->query("SELECT * FROM `whatpulse` where `user` = '" . $username . "'");
                if (!$result->num_rows) {return 0;}
                else { $this->item->name=$data;}
            }
            
            catch(QueryException $exception) {
                echo "Query Error\n";
                var_dump($exception->getMessage());
            }
            
            catch(Exception $exception) {
                echo "Other Script Error\n";
                var_dump($exception->getMessage());
            }

            $result->close();
        }
            
        
            if (!empty($this->item)) {
                if($this->item->team) {
                    if( $this->tag == 'rank') {
                        $bar = explode(" of ",$data);
                        $this->item->teamrank = $bar[0];
                        $this->item->teammembers = $bar[1];
                    }
                    else {
                        $foo = 'team';
                        $foo .= $this->tag;
                        $this->item->{$foo} = $data;
                    }
                }
                else {
                    $this->item->{$this->tag} = $data;
                }
            }
                
    //DEBUG: echo html_entity_decode($data) . "<br />";	

    }
    
    function __destruct() {
        $this->wsiDB->close();
    }
}

?>