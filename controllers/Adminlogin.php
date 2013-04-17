<?php

namespace controllers;

/** @file adminlogin.php
@date:  Feb 1st 2013
@author Thaker:Priyank
@brief This file is a Controller file for the "Admin Form View" which functions upon Login
*/


if (!defined('BASEPATH'))
    die("No direct script access");

/**
 *	Controller file for the "Admin Form View" which functions upon Login
 */	
class Adminlogin extends Controller {

    public $load;
	public $dbh;
	public $db;
	
    
    function __construct($parms=array()) {
        $this->load = new Load();
        if ( isset($_POST['form_username']) )
            $this->check_login();
        else
           $this->load->view('adminloginview'); 

    }
	
	function check_login()	{
	 
			
		//require 'db_connect_pdo.php';
		
		$un = "bellpmpclient";
                $pwd = "NuheDredR5wA235";
		define ("SERVICE", "http://192.168.0.40/iRecognize/helperfunctions.php");
			
		 $this->db = new Database('candidate');

		  $this->dbh = $this->db->connect();
		  
		$UN = htmlspecialchars(strip_tags($_POST['form_username']));
		$PWD = htmlspecialchars(strip_tags($_POST['form_password'], ENT_QUOTES));

		$postdata = '&un='.$UN.'&pwd='.$PWD."&uname=".$un."&paswd=".$pwd;

			   
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
		/*if (curl_errno($ch)) {
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
		}*/

		$total = (time() - $before);

		$response = json_decode($response);
	
		if(!empty($response)){
			$query = "SELECT pmpid FROM user_login WHERE pmpid = :pmpid";
			try {
				$result=$this->dbh->prepare($query) or die("coulnd't prepare");
				foreach($response as $users)
				{
					//get json items to parameters
					$pmpid = $users->pmpid;
					$username = $users->User_Name;
					$password = $users->password;
					$password_input = $users->User_Password;
					//if password is matched then
					if($password == $password_input){
						$result->execute(array(":pmpid"=>$pmpid));
						if($result->rowCount() > 0){
							//start session to store user login name
							session_start();
							$_SESSION['useridlogedin'] =  $pmpid;
							$_SESSION['usernamelogedin'] = $username;
							$_SESSION['user']	=	"Admin";
                                                       	header("Location: AdminFormPanel");		//redirect Controller
							$this->load->view('adminview');				//redirect View with Success
						}
						else{
							$this->load->view('adminloginview',array("loginErr"=>"Error: user does not have administrative privilege"));	//redirect View		with Failure (User is not admin)
						}
						break;
					}else{
					
							/*$message= "<div class='alert alert-error'>
										<strong>Error!</strong> Please enter username and password.
										<button type='button' class='close' data-dismiss='alert'>&times;</button>
									</div>";*/
					
							$this->load->view('adminloginview',array("loginErr"=>"Error: user name and password do not match"));	//redirect View		with Failure (Incorrect userName/password)
						
					}
				}
				//print_r($response);
			}
			catch (Exception $exc) {
				$this->load->view('adminloginview',array("loginErr"=>"Database error: cannot retrieve adminstrator information"));		//redirect View		with Failure (DB error - cannot retrieve admin users)
				print_r($exc);
				echo $exc->getTraceAsString();
			}
		}else{

			
									
			$this->load->view('adminloginview',array("loginErr"=>"Database error: cannot retrieve PMP user information"));		//redirect View		with Failure (DB error - cannot retrieve PMP users)
			
		}
		//$conn=null;
	}
}