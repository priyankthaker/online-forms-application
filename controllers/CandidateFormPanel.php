<?php

namespace controllers;

/** @file CandidateFormPanel.php
@date:  Feb 22nd 2013
@author Thaker:Priyank
@brief This file is a Controller file for the "Candidate Form View" 
*/

if (!defined('BASEPATH'))
    die("No direct script access");

/**
 * Controller that passes information from the model to the view and vice-versa
 * Linked to CandModel (model) and Candidateview (view)
 */
class CandidateFormPanel extends Controller {

    public $model;
   
    function __construct($parms = array()) {

        //create a new class of Session

        $sess = new Session();

        if (!isset($_SESSION['usernamelogedin'])) {
            header("Location: " . BASEURL . "login");	//controller
            exit;
        }
		
        //For notifications
        //require(BASEPATH . 'classes/notifier.php');
        //constructor
        parent::__construct($parms);

        //Loads the model
        $this->model = $this->load->model('CandModel');

		//$_SESSION['usernamelogedin']	=	"1141801218";

        //Handles any POST method made to the server side (i.e. the controller)
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {          

            //check if LOGOUT button is clicked
          if ($_POST['LOGOUT']) {

                session_destroy();
                header("Location: " . BASEURL . "login");
                //$this->forms_name();
            }
        } else {

            // GET Method
            $this->forms_data($_SESSION['useridlogedin']);
		}	
    }
	
    /**
     * What the function does
     * Gets the information of all form and loads in the view as Table
     */
    function forms_data($pmpid) {
		$forms['list'] = $this->model->fetch_forms_names($pmpid);
		$this->load->view('Candidateview', $forms);
    }  
}