<?php
/* 
	+-----------------------------------------------------------------------------------+
	| XML Tags 																			|
	| -----------------------------------------											|
	|																					|
	| Class to generate & order XML tags from PHP arrays								|
	+																					+
	| Version 1.1																		|	
	| * 08/03/2008 : added XML namespace support										|																					
	+																					+
	| Developed by Dave Shanley <theshanman@googlemail.com>								|
	+																					+
	| This program is free software; you can redistribute it and/or modify				|
	| it under the terms of the GNU General Public License as published by				|
	| the Free Software Foundation; either version 2 of the License, or					|
	| (at your option) any later version.												|
	|																					|
	| This program is distributed in the hope that it will be useful,					|
	| but WITHOUT ANY WARRANTY; without even the implied warranty of					|
	| MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the						|
	| GNU General Public License for more details.										|
	| You should have received a copy of the GNU General Public License					|
	| along with this program; if not, write to the Free Software						|
	| Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA			|
	+-----------------------------------------------------------------------------------+
*/

/* generates XML content from an associative array */

class XMLGenerator {
	
	/* define internal indent counter */
	var $indent;
	var $op;
	
	/* constructor */
	function XMLTags() {
		$this->indent=0;
	}
	
	/* calculate the number of indents to generate for tag */
	function CalculateIndent() {
		
		$op="";
			for($x=0; $x < $this->indent; $x++) {
		
					$op.="    ";
		
			}
			return $op;
	}
	
	/* create tag */
	function CreateTag($name,$attribs='',$endline=true,$close=false) {
		
				$op=$this->CalculateIndent()."<";
				
				/* look for a multiple label type and strip off the *NUM */
				$name=preg_replace("/%[0-9].*/","",$name);
				
				$op.=$name;
			
				/* if an array of attributes have been submitted then build in the key/value pairs */
				if(is_array($attribs)) {
			
					for($n=0; $n < count($attribs); $n++) {
							$key=@key($attribs);
							$op.=" ". $key ."=\"". $attribs[$key] ."\"";
							@next($attribs);
					}
				
				}
				
				if($close) {
					$op.="/>";
				} else {
					$op.=">";
					$this->indent++;
				}
				if($endline) {
							$op.="\n";
				}
		
			
			return $op;
	}

	
	
	/* create close tag */
	function CreateCloseTag($name,$indent=true) {
			
			/* look for a multiple label type and strip off the *NUM */
			$name=preg_replace("/%[0-9].*/","",$name);
				
			$this->indent--;
	
			$op="";
			if($indent) {
		
					$op.=$this->CalculateIndent();
			
			}
	
			$op.="</".$name.">\n";
			return $op; 
	
	}	
	
	/* create data element */	
	function CreateElement($name,$value,$attribs='') {
			
			/* if there is a value to the tag then process */
			if(strlen($value) > 0) {
				if($attribs!="") {
		
					$op=$this->CreateTag($name,$attribs,false);
					
				} else {
		
					$op=$this->CreateTag($name,"",false);
		
				}
		
				$op.=$value;
				$op.=$this->CreateCloseTag($name,false);
			
			/* no value, do a quick close off */
			} else {
				
				if($attribs!="") {
		
					$op=$this->CreateTag($name,$attribs,true, true);
					
				} else {
		
					$op=$this->CreateTag($name,"",true, true);
		
				}
				
			}
			return $op;
	
	}
	
	/* create XML Comment */
	function CreateComment($comment) {
		
			$op=$this->CalculateIndent()."<!-- ".$name." -->\n";
			return $op;
	
	}
	
	/* walk through an array to build XML document */
	function BuildXMLDocument($data,$buildHeader=false) {
		
			if(!is_array($data)) {
			
				return "[ERROR: BuildXMLDocument requires argument to be an array]";
			
			}
			/* return the entire XML document */
			$XMLData = "";
			if($buildHeader) $XMLData .= $this->CreateHeader();
			$XMLData.=$this->WalkXMLArray($data);
			return $XMLData;	
		
	}
	
	/* recursive walkthrough for XML array */
	function WalkXMLArray($array) {
	
		$op="";
		
			for($x=0; $x < count($array); $x++) {
				
				$key=@key($array);
				
				if(is_array($array[$key])) {
					$params=array();
					/* check to see next item isn't a parameter */
					$nextKey = @key($array[$key]);
					
					
					if(stristr($nextKey,"param")) {
							
							/* if using the multidimensional xml type, then extract properly */
							$params = $this->ExtractParameters($array[$key]);
							
							if(is_array($params['value'])) {
									$valueArray = $params['value'];
									unset($params['value']);
									$op.=$this->CreateTag($key,$params);
									$op.=$this->WalkXMLArray($valueArray);
									$op.=$this->CreateCloseTag($key);
							} else {
							
								$tagValue=$params['value'];
								unset($params['value']);
								$op.=$this->CreateElement($key,$tagValue,$params);
							}
					} else {
				
						/* recurse the array if found */
					
						$op.=$this->CreateTag($key);
						$op.=$this->WalkXMLArray($array[$key]);
						$op.=$this->CreateCloseTag($key);
						
					}
			
			} else {
				
					/* before building the element, look for attribute declarations */
					$attrs = explode("||",$array[$key]);
					$xmlAttributes=array();
				
					if(sizeof($attrs)>1) {
					
						for($m=0; $m < count($attrs); $m++) {
							if(preg_match("/PARAM:(\w*)=([\w\W]*)/",$attrs[$m],$match)) {
							
								$xmlAttributes[$match[1]]=$match[2];
								$array[$key]=preg_replace("/".$match[0]."/","",$array[$key]);
							
							}
							
						}
					}
					
					$array[$key]=preg_replace("/\|/","",$array[$key]);				
					$op.=$this->CreateElement($key,$array[$key],$xmlAttributes);
		
				}
	
				@next($array);
			}
			
			return $op;	
	}		
	
	/* extract parameters from array */
	function ExtractParameters($array) {
			$params= array();
			for($y=0; $y < count($array); $y++) {
			    	$key=@key($array);
					
					/* check for parameter match */
					if(preg_match("/param[0-9]{1}/i",$key)) {
					
							$values = explode(":",$array[$key]);
							if(is_array($values)) {
								$values[0] = str_replace("||",":",$values[0]);
								$values[1] = str_replace("||",":",$values[1]);
								$params[$values[0]]=$values[1];
							}
					}
					
					/* now extract the value */
					if(preg_match("/value/i",$key)) {
					
							$params['value']=$array[$key];
					
					}
						
				@next($array);
			}
			return $params;
	}

	
	/* create XML header */
	function CreateHeader() {
			return "<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n";
	}
	

}
# EOF
?>