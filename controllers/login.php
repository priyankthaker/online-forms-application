<?php

namespace controllers;

if (!defined('BASEPATH'))
    die("No direct script access");
	
/**
 * This file is a Controller file for the "candidatelogin" which functions upon Login
 */
class Login extends Controller {

    public $load;
    
    function __construct($parms=array()) {
        $this->load = new Load();
        if ( isset($_POST['form_username']) )
            $this->check_login();
        else {
            $this->load->view('candidatelogin'); 
        }
    }

    function check_login() {

        $sess = new Session();
      
//        if ((!isset($_SESSION['pmpid']))) {
        
        if ((!isset($_SESSION['username']))) {
            
            if (isset($_POST["form_username"]) && isset($_POST["form_password"])) {             
//              $_SESSION['pmpid'] = $_POST["form_username"];
                $_SESSION['username'] = $_POST["form_username"];                
                $_SESSION['pwd'] = md5($_POST["form_password"]);
                
                $this->check_login();
                //exit;
            } 
            else {
                $data='';
                if (isset($_SESSION['name'])){
                    $data['message'] = 'Thank you for completing the '.$_SESSION['form_name'].'.<br/>One of our Recruitment Associates will be with you shortly to further assist you with the process';
                    $data['username'] = $_SESSION['name'];
                    $data['room'] = $_SESSION['room'];
                }
                $this->load->view('candidatelogin', $data);
                //exit;
            }
        }
        else
        {
            if ($sess->check_auth(true) /*&& $sess->has_inc_forms()*/) {
                header("Location: CandidateFormPanel");
				$this->load->view('Candidateview');
                //exit;
            } 
            else {
                @session_destroy();
                $this->load->view('candidatelogin',array("loginErr"=>"Error: user name and password do not match"));
                exit;
            }
        }
    }

}