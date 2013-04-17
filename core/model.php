<?php if(!defined('BASEPATH')) die("No direct script access");

namespace core;
/**
 * Class that contains the Model
 */
abstract class Model {
	public $load;
	//public $db;
	
   function __construct(){
	$this->load = new Load();
	//$this->db = new Database();
	$this->load_dbs();
   }
   private function load_dbs()
   {
		$ini_array = parse_ini_file("databases.ini",true) or die("model could not find databases.ini file");
		
		//each database can be accessed in a model via $this->'dbname'
		foreach ($ini_array as $key=>$value)
			$this->{$key} = $this->load->load_db($key);
			
	
   
   }
 
}
