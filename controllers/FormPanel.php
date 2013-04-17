<?php

namespace controllers;

if (!defined('BASEPATH'))
    die("No direct script access");

/**
 * Controller that passes information from the model to the view and vice-versa
 * Linked to CandidateModel (model) and CandidateForms (view)
 */
class FormPanel extends Controller {

    public $model;

    function __construct($parms=array()) {
	
        $sess = new Session();

  /*      if(($sess->check_auth() === false) || ($sess->has_inc_forms()===false))
        {
            header("Location: logout");
            exit;
        }
   */

        //For notifications
        //require(BASEPATH . 'classes/notifier.php');
        //constructor
        parent::__construct($parms);
         
        //Loads the model
        $this->model = $this->load->model('CandidateModel');
        
        //Handles any POST method made to the server side (i.e. the controller)
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            
            //Handles the form responess to be written to the db
            if ((isset($_POST['question']))) {
                $this->handle_submission();
            } else {
                //Handles the status updates to be written to the db
                $form_id = $_POST['form_id'];
                if ($form_id == 10) {
                    $status_id = 5; //PIQ
                } else if ($form_id == 11) {
                    $status_id = 8;  //IAQ
                } else if ($form_id == 12) {
                    $status_id = 9; //PIA (or skill test)
                }
                $this->set_form_start($_POST['pmpid'], $status_id);
            }
            
            if (isset($_POST['submit']))   
            {
                $assignment_id = $_SESSION['assignment_id'];
                $result=$this->model->submit_form($assignment_id);
            }
        }
        //Handles any GET method made to the server side
        else {
            if (isset($_GET['time'])) {
                $this->get_time($_GET['time'], $_GET['fid']);
            }
        //    if (isset($_SESSION['form'])) {
                //Ensures that the current form is reloaded if the user attempts to refresh
			
          //      $this->load_form($_SESSION['form']);
           // } 
			else if (isset($_REQUEST['form_id']) && isset($_REQUEST['id']) && isset($_REQUEST['assignment_id'])){
		
				$_SESSION['pmpid'] = $_REQUEST['id'];
				$_SESSION['assignment_id']	=	$_REQUEST['assignment_id'];	
				if($_SESSION['user']	!=	"Admin")	{
						$form_expired	=	$this->model->check_form_validity($_REQUEST['id'], $_REQUEST['form_id'], $_REQUEST['assignment_id']);
						
						if($form_expired	== 	"true")	{
							  die ("The Validity of the form has expired.");
						}
						else	{
							$this->begin_next_form($_REQUEST['id'], $_REQUEST['form_id'], $_REQUEST['assignment_id']);
						}
				}
				else	{
							$this->begin_next_form($_REQUEST['id'], $_REQUEST['form_id'], $_REQUEST['assignment_id']);
				}
			
			} 
		//	  else {
			  
        //        $this->begin_next_form();
         //   } 			
			else {
			   die ("no form specified");
			}
        }
    }
    
	/**
     * Begins the next assigned form for a user
	 * @param integer $pmpid The pmpid of the Candidate 
	 * @param integer $form_id The id of the form 
	 * @param integer $assignment_id The assignment id ,unique, to all forms assigned
     */
    function begin_next_form($pmpid, $form_id, $assignment_id) {

        $form = "";
        
            $form = $this->model->fetch_user_forms($pmpid, $form_id, $assignment_id);
			
		/** $form is an array containing $form['title'] and $form['form_id']
         * 
         * load up the form here and display to user to fill out. After it is submitted enter responses into the
         * database and log the user out using: header("Location: logout");
         */
        if (empty($form)) {
            echo "<h1>No Forms Assigned</h1>";
            echo "<a href=" . ("logout") . ">Return to Log in page </a>";
        } else {
            $this->load_form($form);
        }
    }

    /**
     *
     * @param type $form describes the form
     */
    function load_form($form) {
        $form_title = $form['title'];
        $pmpid = $_SESSION['pmpid'];
		$assignment_id	=	$form['id'];
		
        $formToDisplay = $this->fetch_form($form_title, $pmpid, $assignment_id);
		
        $form_id = $formToDisplay->id;		
		
    //    $name = $this->get_candidate_name($pmpid);
     //   $last_initial_pos = strripos($name, " ");
      //  $name = substr($name, 0, ($last_initial_pos + 2));
	    //$name = 'john doe';
        $formToDisplay->candidate_name = $name;

        $formToDisplay->pmpid = $pmpid;
        $status_id = $formToDisplay->begin_id;
        $formToDisplay->begin_time = $this->get_start_time($status_id, $pmpid, $date);
//        if(!(isset($_SESSION['begin_time']))){
//            $_SESSION['begin_time'] = $formToDisplay->begin_time;
//        }
        
        $data['form'] = $formToDisplay;
	
        if (!(isset($_SESSION['form']))) {
            $_SESSION['form'] = $form;
        }

        $this->load->view('CandidateForms', $data);
    }

    /** 
     * this is called when a post request is made to the controller (i.e. the form is submitted)
     *  
     */
    function handle_submission() {
        $form_id = $_POST['form_id'];
        $questions = $_POST['question'];
        //$questions = filter_input(INPUT_POST, 'question', FILTER_UNSAFE_RAW);
        $pmpid = $_POST['pmpid'];
		$assignment_id	=	$_SESSION['assignment_id'];
        $status_id;
		
		if($_SESSION['user']	!=	"Admin")	{
			$this->save_responses($form_id, $questions, $pmpid, $assignment_id);
			}
			
        if (!(isset($_POST['final']))) {
            if ($form_id == 10) {
                $status_id = 10; //PIQ
            } else if ($form_id == 11) {
                $status_id = 12; //IAQ
            } else if (($form_id == 13) || ($form_id == 14)) {
                $status_id = 11; //Aptitude test
            }
            if (!(empty($status_id))) {
                $this->set_form_end($pmpid, $status_id);
            }
        }
		
        if (!(isset($_POST['final']))) {
            $skill_id = 12;
            $date = Date("Y-m-d");
            $status_id = $this->model->get_begin_status_id($skill_id);
            if ($form_id == $skill_id) {
                //go to aptitude test
                $data = $this->keep_session_data($_SESSION);
//                $pmpid = $_SESSION['pmpid'];

                session_unset();
                $_SESSION = $data;
                $this->begin_next_form();
            } 
			else {
                header("Location: submit");
            }
        }
    }

    /**
     * What the function does
     * @param integer $form_title describes the form name
     * @param integer $pmpid The pmpid of the Candidate 
	 * @param integer $assignment_id The assignment id ,unique, to all forms assigned
     * @return array An associative array containing the form (sections ... included) 
     */
    function fetch_form($form_title, $pmpid, $assignment_id) {
		
       
        //Load the form
        $form = $this->model->load_form($form_title);

        //Load the sections 
        $form->sections = $this->model->load_sections($form->id);

        //Load the questions accodring to the section
        foreach ($form->sections as &$section) {
            $section->questions = $this->model->load_questions($section->id);
        }

        //Load the choices for each question
        foreach ($form->sections as &$section) {
            foreach ($section->questions as &$question) {
                $question->choices = $this->model->load_choices($question->id);
                foreach ($question->choices as &$choice) {
                    $choice->validations = $this->model->load_validations($choice->id);
                    $choice->responses = $this->model->fetch_user_responses($pmpid, $form->id, $question->id, $assignment_id);
                }
            }
        }
        return $form;
    }
    
    /**
     *
     * @param integer $pmpid The pmpid of the Candidate 
     * @param type $date
     * @param type $form_title describes the form name
     */
    function assign_form($pmpid, $date, $form_title) {
        $form = $this->model->load_form($form_title);
        $this->model->assign_form($pmpid, $date, $form->id);
    }
    
    /**
     *
     * @param integer $form_id The id of the form 
     * @param type $questions
     * @param integer $pmpid The pmpid of the Candidate 
     * @param integer $assignment_id The assignment id ,unique, to all forms assigned
	 */
    function save_responses($form_id, $questions, $pmpid, $assignment_id) {
        if (empty($date)) {
            $date = Date('Y-m-d');
        }
		

        //start the transaction        
        // $this->model->start_form_transaction();  
//        $this->model->delete_all_responses($form_id, $pmpid, $date);
//        foreach($questions as $key=>$value){
//            foreach($value as $ques_id=>$c_ids){
//                if(($key != 'radio')){
//                    foreach($c_ids as $c_id=>$resp){
//                        $choice_id = $c_id;
//                        $question_id = $ques_id;
//                        $user_response = $resp;
////                        if (empty($user_response)){
////                            $this->model->abort();
////                            die("Fatal error: <br/>Where Question ID = $question_id <br/> and Choice ID = $choice_id");
////                        }
//                        $this->model->record_responses($choice_id, $question_id,$form_id, $pmpid, $date, $user_response);        
//                    }
//                }
//                else{
//                    $choice_id = $c_ids;
//                    $question_id = $ques_id;
////                    if (empty($choice_id)) {
////                        $this->model->abort();
////                        die("Fatal error: <br/>Where Question ID = $question_id <br/> and Choice ID = $choice_id");
////                    }
//                    $this->model->record_responses($choice_id, $question_id, $form_id, $pmpid, $date);        
//                }
//            }
//        }        
        //$this->model->commit_transaction();

        $this->model->start_form_transaction();
        $this->model->delete_all_responses($form_id, $pmpid, $date, $assignment_id);

        foreach ($questions as $key => $question) {
            foreach ($question as $ques_id => $c_ids) {
                if (($key == 'radio')) {
                    $choice_id = $c_ids;
                    $question_id = $ques_id;
                    $this->model->record_responses($choice_id, $question_id, $form_id, $pmpid, $date, $assignment_id);
                } else if ($key == 'check') {
                    foreach ($c_ids as $c_id => $resp) {
                        $choice_id = $c_id;
                        $question_id = $ques_id;
                        $this->model->record_responses($choice_id, $question_id, $form_id, $pmpid, $date, $assignment_id);
                    }
                }
                  else if ($key=='signature') {
                      foreach ($c_ids as $c_id => $resp) {
                        $choice_id = $c_id;
                        $question_id = $ques_id;
                        $user_response = '{signed}'.$resp;
                                            /*$file1=fopen("C:\\xampp\\htdocs\\out_SaveUserR.sql","w");
                                            $file2=fopen("C:\\xampp\\htdocs\\out_SaveResp.sql","w");
                                            fwrite($file2,$resp);
                                            fwrite($file1,$user_response);
                                            fclose($file1);fclose($file2);*/
                        $this->model->record_responses($choice_id, $question_id, $form_id, $pmpid, $date, $assignment_id, $user_response);
                        
                      }
                      //echo ('signature');
                      //die();
                } else {
                    foreach ($c_ids as $c_id => $resp) {
                        $choice_id = $c_id;
                        $question_id = $ques_id;
                        $user_response = $resp;
                        $this->model->record_responses($choice_id, $question_id, $form_id, $pmpid, $date, $assignment_id, $user_response);
                    }
                }
            }
        }
        $this->model->commit_transaction();
    }

    /**
     *
     * @param integer $pmpid The pmpid of the Candidate 
     * @param type $status_id 
     */
    function set_form_start($pmpid, $status_id) {
        $timestamp = date("H:i:s");
        $this->model->set_status_time($pmpid, $status_id, $timestamp);

        //Notifier::SendStatusNotifications($pmpid, $status_id);
    }
    
    /**
     *
     * @param integer $pmpid The pmpid of the Candidate 
     * @param type $status_id 
     */
    function set_form_end($pmpid, $status_id) {
        $timestamp = date("H:i:s");
        $this->model->set_status_time($pmpid, $status_id, $timestamp);
//        echo "status id is : $status_id";
//        die();
        //Notifier::SendStatusNotifications($pmpid, $status_id);
    }

    /**
     *
     * @param integer $pmpid The pmpid of the Candidate 
     * @return type 
     */
    function get_candidate_name($pmpid) {
        return $this->model->fetch_candidate_name($pmpid);
    }

    /**
     *
     * @param type $status_id
     * @param integer $pmpid The pmpid of the Candidate 
     * @param type $date
     * @return type 
     */
    function get_start_time($status_id, $pmpid, $date) {
        return $this->model->fetch_start_time($status_id, $pmpid, $date);
    }

    function get_time($start_time, $form_id) {
        $current_time = date('h:i:s');
        $time_in_secs = strtotime($current_time) - strtotime($start_time);
        if (($form_id == 12) || ($form_id == 13) || ($form_id == 14)) {
            $time_remianing = (1800 - $time_in_secs);
            $time = gmdate("i:s", $time_remianing);
        } else {
            $time = gmdate("i:s", $time_in_secs);
        }

        echo $time;
        die();
    }

    /**
     *
     * @param type $session
     * @return type 
     */
    function keep_session_data($session) {
        $data = array(
            'username' => $session['username'],
            'room' => $session['room'],
            'pmpid' => $session['pmpid']
        );

        return $data;
    }

}