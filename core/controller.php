<?php if(!defined('BASEPATH')) die("direct script access is not permitted");

namespace core;

Database helper class. Database settings appear in /core/settings.ini. connect() returns a handle to a connection
/**
 * Class that contains the Loading feature
 */
abstract class Controller {
   public $load;
   public $model;
  // public $db;
   public $args = array();
   function __construct($parms)
   {
		if(!empty($parms) && is_array($parms))
		{
			foreach($parms as $key => $value)
				$this->args[$key]=$value;
		}  
      $this->load = new Load();
	//  $this->db = new Database();
     // $this->model = new Model();

      // determine what page you're on
     // $this->home();
   }
   // get a query parameter by name
   public function get_var_byname($str)
   {
		if(isset($this->args[$str]))
			return $this->args[$str];
		else return false;
   }
 /*  function home()
   {
      $data = $this->model->user_info();
      $this->load->view('someview.php', $data);
   }*/
}
