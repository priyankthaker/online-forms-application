<?php

namespace controllers;

/** @file AdminFormPanel.php
@date:  Jan 27th 2013
@author Thaker:Priyank
@brief This file is a Controller file for the "Admin Form View" 
*/

if (!defined('BASEPATH'))
    die("No direct script access");

/**
 * Controller that passes information from the model to the view and vice-versa
 * Linked to AdminModel (model) and Adminview (view)
 */
class AdminFormPanel extends Controller {

    public $model;
   // public $associates;
	
	
    function __construct($parms = array()) {

        //create a new class of Session

        $sess = new Session();

        if (!isset($_SESSION['usernamelogedin'])) {
            header("Location: " . BASEURL . "adminlogin");	//controller
            exit;
        }


        //For notifications
        //require(BASEPATH . 'classes/notifier.php');
        //constructor
        parent::__construct($parms);


        //Loads the model
        $this->model = $this->load->model('AdminModel');

    

        //Handles any POST method made to the server side (i.e. the controller)
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {

            //check if Next button is clicked

            if ($_POST['NEXT']) {
			
				//remove a error variable in the session
				unset($_SESSION['error_message_date']);
				unset($_SESSION['error_message_cand_name']);
				unset($_SESSION['error_message_assign_date']);
				unset($_SESSION['error_message_expiry_date']);
				unset($_SESSION['error_message_both_cat']);
				unset($_SESSION['error_message_none']);
				unset($_SESSION['form']);
				unset($_SESSION['selected_cand_name']);
							

				if(	($_POST['form_names'] != '--Please Select Form--') && ($_POST['cand_names01'] != 'Select a Candidate')	)			{
						$form_id = $_POST['form_names'];
						$pmpid	=	$_POST['cand_names01'];	
						$date_from = $_POST['date_from'];
						$date_to = $_POST['date_to'];
						$_SESSION['date_from'] = $_POST['date_from'];
						$_SESSION['date_to'] = $_POST['date_to'];
						$this->get_data_cand_form($form_id, $pmpid, $date_from, $date_to);
				}
                else if (($_POST['form_names'] != '--Please Select Form--') || ($_POST['cand_names01'] != 'Select a Candidate')	) {
					if (($_POST['form_names'] != '--Please Select Form--'))			{		
						$form_id = $_POST['form_names'];
						$date_from = $_POST['date_from'];
						$date_to = $_POST['date_to'];
						$_SESSION['date_from'] = $_POST['date_from'];
						$_SESSION['date_to'] = $_POST['date_to'];
						$_SESSION['form'] = $form_id;
						$this->get_data($form_id, $date_from, $date_to);
					}
					else	{
						
						$pmpid	=	$_POST['cand_names01'];	
						$date_from = $_POST['date_from'];
						$date_to = $_POST['date_to'];
						$_SESSION['date_from'] = $_POST['date_from'];
						$_SESSION['date_to'] = $_POST['date_to'];
						$this->get_data_cand($pmpid, $date_from, $date_to);
					}
				}
				else 	if(	($_POST['form_names'] == '--Please Select Form--') && ($_POST['cand_names01'] == 'Select a Candidate')	)			{
					$_SESSION['error_message_none']	=	"Please Select Either Form or Candidate and click Next";
						$this->forms_name();					
				}
            }

            //check if Add candidate button is clicked
            else if ($_POST['Add_Candidate']) {
			
				//remove error variables
				unset($_SESSION['error_message_cand_name']);
				unset($_SESSION['error_message_assign_date']);
				unset($_SESSION['error_message_expiry_date']);
			

                if ((isset($_POST['cand_names']))) {	

					if(($_POST['cand_names'] != "Select a Candidate") && ($_POST['date_assign'] != "")	&& ($_POST['date_expiry'] != ""))	{	
				
					$pmpid = $_POST['cand_names'];
                    $date_assign =	$_POST['date_assign'];
					$date_expiry	=	$_POST['date_expiry'];	
					
                    $this->add_cand_name($pmpid, $date_assign, $date_expiry); //adding candidate
					}
					
					else	{
							//defining appropriate errors
							if(($_POST['cand_names'] == "Select a Candidate"))	{
								$_SESSION['error_message_cand_name']	=	"Select a Candidate";
								$form_id = $_SESSION['form'];
								$date_from = $_SESSION['date_from'];
								$date_to = $_SESSION['date_to'];
								$this->get_data($form_id, $date_from, $date_to);
							}
							else if(($_POST['date_assign'] == ""))	{
								$_SESSION['error_message_assign_date']	=	"Select a Form Assigning Date";
								$form_id = $_SESSION['form'];
								$date_from = $_SESSION['date_from'];
								$date_to = $_SESSION['date_to'];
								$this->get_data($form_id, $date_from, $date_to);							
							}
							else if(($_POST['date_expiry'] == ""))	{
								$_SESSION['error_message_expiry_date']	=	"Select a Form Expiry Date";
								$form_id = $_SESSION['form'];
								$date_from = $_SESSION['date_from'];
								$date_to = $_SESSION['date_to'];
								$this->get_data($form_id, $date_from, $date_to);							
							}
					}
                }
            } else if ($_POST['LOGOUT']) {

                session_destroy();
                header("Location: " . BASEURL . "Adminlogin");
                //$this->forms_name();
            }
        } else {

            // GET Method

            if (isset($_SESSION['form'])) {

                $form_id = $_SESSION['form'];
                $date_from = $_SESSION['date_from'];
                $date_to = $_SESSION['date_to'];
                $this->get_data($form_id, $date_from, $date_to);
            } else {
                $this->forms_name();				
            }
		}	
    }

	
	/**
     * What the function does
     * Gets the information of the particular form and Candidate and loads in the view as Table
	 * @param integer $form_id describes the form
	 * @param integer $pmpid The pmpid of the Candidate selected by the admin
	 * @param datetime $date_from The date that the admin enters from where the data is asked to be viewed, value can be NULL
     * @param datetime $date_to The date that the admin enters till when the data is asked to be viewed , value can be NULL
     */
    function get_data_cand_form($form_id, $pmpid, $date_from, $date_to) {
		
		$data['cand_form_info'] = $this->model->fetch_selected_candidate_form_info($form_id, $pmpid, $date_from, $date_to);
		$data['title'] = $this->model->fetch_forms_names();
        $data['cand_names_list'] = $this->get_cand_list();
        $data['date_from'] = $date_from;
        $data['date_to'] = $date_to;
		
		//Convert Selected Candidate's id to Name to show iv View
		foreach($data['cand_names_list'] as $key=>$val)	{
					if($data['cand_names_list'][$key]['pmpid']==$pmpid)	
					{
						$_SESSION['selected_cand_name']	=	$val['username'];
					}
		}
		$this->load->view('adminview', $data);
    }
	
    /**
     * What the function does
	 * Gets the information of the particular form and loads in the view as Table
     * @param integer $form_id describes the form
	 * @param datetime $date_from The date that the admin enters from where the data is asked to be viewed, value can be NULL
     * @param datetime $date_to The date that the admin enters till when the data is asked to be viewed , value can be NULL
     */
    function get_data($form_id, $date_from, $date_to) {

       
		$data['cur_form_name']	=	$this->model->cur_form_name($form_id);
        $data['info'] = $this->model->fetch_candidate_info($form_id, $date_from, $date_to);
        $data['title'] = $this->model->fetch_forms_names();
        $data['cand_names_list'] = $this->get_cand_list();
        $data['date_from'] = $date_from;
        $data['date_to'] = $date_to;
		
		//convert pmpids into names to view
		foreach($data['info'] as $key=>$data['info2'])	{
			foreach($data['cand_names_list'] as $key1=>$val)	{
					if($data['info'][$key]['id']==$val['pmpid'])	
					{
						$data['info'][$key]['name']=$val['username'];
					}
			}
		}			
		$this->load->view('adminview', $data);
    }
	
	/**
     * What the function does
     * Gets the information of the particular Candidate and loads in the view as Table
	 * @param integer $pmpid  The pmpid of the Candidate selected by the admin
	 * @param datetime $date_from The date that the admin enters from where the data is asked to be viewed, value can be NULL
     * @param datetime $date_to The date that the admin enters till when the data is asked to be viewed , value can be NULL
     */
    function get_data_cand($pmpid, $date_from, $date_to) {
		
		$data['cand_info'] = $this->model->fetch_selected_candidate_info($pmpid, $date_from, $date_to);
	    $data['title'] = $this->model->fetch_forms_names();
        $data['cand_names_list'] = $this->get_cand_list();
        $data['date_from'] = $date_from;
        $data['date_to'] = $date_to;
		
		//Convert Selected Candidate's id to Name to show iv View
		foreach($data['cand_names_list'] as $key=>$val)	{
					if($data['cand_names_list'][$key]['pmpid']==$pmpid)	
					{
						$_SESSION['selected_cand_name']	=	$val['username'];
					}
		}
		$this->load->view('adminview', $data);
    }

    /**
     * What the function does
     * Gets the information of all form names and loads in the view as Select Drop down
     */
    function forms_name() {

        $forms['title'] = $this->model->fetch_forms_names();
		$forms['cand_names_list'] = $this->get_cand_list();
        $this->load->view('adminview', $forms);
    }

    /**
     * What the function does
	 * Posts the data of candidate , assigns the form and loads the table of assigned candidates for that form
	 * @param integer $pmpid The pmpid of the Candidate selected by the admin
     * @param integer $form_id 	describes the form
     * @param integer $date_assign describes the date selected
     */
    function add_cand_name($pmpid, $date_assign, $date_expiry) {
		$form_id = $_SESSION['form'];
		$this->model->add_candidate_name($pmpid, $date_assign, $form_id, $date_expiry);
        $this->get_data($form_id, $date_from, $date_to);
    }

    /**
     * What the function does
     * Calls the Service to get the list of all the candidates, that can be assigned a particular form, and loads the list in view to show
     */
    function get_cand_list() {


        //require 'db_connect_pdo.php';

        $un = "bellpmpclient";
        $pwd = "NuheDredR5wA235";
        define("SERVICE", "http://192.168.0.40/iRecognize/helperfunctions.php");

        $this->db = new Database('candidate');

        $this->dbh = $this->db->connect();


        $postdata = "&uname=" . $un . "&paswd=" . $pwd . "&list_tc_user=1";

        //$url = SERVICE.$postdata; //for GET
        $url = SERVICE;

        $ch = curl_init();

        //Set the useragent
        $agent = $_SERVER["HTTP_USER_AGENT"];
        curl_setopt($ch, CURLOPT_USERAGENT, $agent);
        //curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.001 (windows; U; NT4.0; en-US; rv:1.0) Gecko/25250101');
        //set url
        curl_setopt($ch, CURLOPT_URL, $url);

        //This is a POST data
        curl_setopt($ch, CURLOPT_POST, count($postdata));

        //Set the post data
        curl_setopt($ch, CURLOPT_POSTFIELDS, $postdata);
        // Set so curl_exec returns the result instead of outputting it.
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        // follow location redirects
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
        //trust the https certificate
        //curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        // Get the response and close the channel.
        $before = time();
        $response = curl_exec($ch);
        /* if (curl_errno($ch)) {
          // this would be your first hint that something went wrong
          die('Couldn\'t send request: ' . curl_error($ch));
          } else {
          // check the HTTP status code of the request
          $resultStatus = curl_getinfo($ch, CURLINFO_HTTP_CODE);
          if ($resultStatus == 200) {
          echo "ok";
          // everything went better than expected
          } else {
          // the request did not complete as expected. common errors are 4xx
          // (not found, bad request, etc.) and 5xx (usually concerning
          // errors/exceptions in the remote script execution)

          die('Request failed: HTTP status code: ' . $resultStatus);
          }
          } */

        $total = (time() - $before);
		$response = json_decode($response, true);
		
		return $response;
    }
}