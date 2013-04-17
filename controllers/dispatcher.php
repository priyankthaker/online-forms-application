<?php if(!defined('BASEPATH')) die("direct script access is not permitted");

namespace controllers;

/**
 * dispatches requests to the appropriate controller 
 */
class Dispatcher {
    public $segments = array();
	public $args = array();
	function __construct(){
		
		//grab the requested URL
		$data = parse_url($_SERVER['REQUEST_URI']);
		$this->segments = explode('/',$data['path']);
		
		if (isset($data['query']))
			parse_str($data['query'], $args);
	    
		//the first segment (segment[0]) is our application folder and segment[1] is the requested controller
		if(isset($this->segments[2])) 
		{
		
			
			include ($this->segments[2].'.php');
			if(class_exists($this->segments[2])){
			
				new $this->segments[2]($this->args);
				}
			else
				echo "Not a valid page";
				
		
			//TODO: add some error handling if the class doesn't exist, i.e. 404 error or go to index
		}
	}

}