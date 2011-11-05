<?php

    class MailHandler {
        
        public $host = 'localhost';
        public $port = '143';
        public $username = 'abuse@gvocom.com';
        public $password = 'bickford5132';
        public $service = 'imap';
        public $service_option = 'notls';
        public $boxname = 'INBOX';
        public $link;
        public $stdRules;
        public $nonstdRules;
        
        function __construct() {
            
            $this->link = imap_open("{".$this->host.":".$port."}" . $this->boxname,$this->username,$this->password,CL_EXPUNGE);
            
            if(!$this->link):
            
                die("Could not connect to mail server. Error: " . imap_last_error());
                return false;
            
            endif;
            
            $this->db = new DB(DB_HOST,DB_USER,DB_PASS,DB_NAME);
            
            $query = "select id,rule from rules where type = 'std'";
            $result = $this->db->query($query);
            $this->stdRules = $result->fetch_assoc_all();
            $result->free();
            
            $query = "select id,rule from rules wehre type = 'nonstd'";
            $result = $this->db->query($query);
            $this->nonstdRules = $result->fetch_assoc_all();
            $result->free();
            
            return true;
            
        }
        
        function processBounce($msgnum, $total, $s = FALSE) {
            
        }
        
        function getNewMail() {
            
            $totalMsgs = imap_num_msg($this->link);
            $fetched = $totalMsgs;
            $processed = 0;
            $unprocessed = 0;
            $deleted = 0;
            $moved = 0;
            
            for($x = 0; $x < $totalMsgs; $x++):
            
                $header = imap_fetchheader($this->link,$x);
                
                if (preg_match ("/Content-Type:((?:[^\n]|\n[\t ])+)(?:\n[^\t ]|$)/is",$header,$match)) {
                    
                  if (preg_match("/multipart\/report/is",$match[1]) && preg_match("/report-type=[\"']?delivery-status[\"']?/is",$match[1])) {
                      
                      $processed = $this->processBounce($x,$totalMsgs);
                    
    }