<?php

namespace controllers;

/** @file adminlogout.php
@date:  Feb 1st 2013
@author Thaker:Priyank
@brief This file is a Controller file for the "Admin Form View" which functions upon Logout
*/

if (!defined('BASEPATH'))
    die("No direct script access");
/**
 *	 This file is a Controller file for the "Admin Form View" which functions upon Logout
 */
class adminlogout extends Controller {
    
    function __construct($parms=array()) {
     
        parent::__construct($parms);
        session_destroy();
        header("Location: ".BASEURL."adminlogin");	//redirect Controller
        exit;
    }
  

}