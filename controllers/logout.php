<?php

namespace controllers;

if (!defined('BASEPATH'))
    die("No direct script access");

/**
 * This file is a Controller file for Logout
 */
	
class Logout extends Controller {
    
    function __construct($parms=array()) {
     
        parent::__construct($parms);
        //session_destroy();
        //header("Location: ".BASEURL."login");
        exit;
    }
  

}