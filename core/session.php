<?php

namespace core;

if (!defined('BASEPATH'))
    die("No direct script access");


//global $strName, $hashPassword, $sessData;
//session_register("strName");
//session_register("hashPassword");
//session_register("sessData");
/**
 * Class that contains the Session
 */
class Session {

    public $dbh;
    public $db;

    function __construct() {

        // session_register("role");
        $this->db = new Database('candidate');

        // $this->check_auth();
    }
    

    public function check_auth($chk_only=false) {
    /*    if (isset($_SESSION['username']) && isset($_SESSION['pwd'])) {
            $date = Date('Y-m-d');

            $first_name = substr($_SESSION['username'],0,strpos($_SESSION['username'],' '));
            $last_name = substr($_SESSION['username'],strpos($_SESSION['username'],' ') + 1);
            
            $this->dbh = $this->db->connect();
//            $sql = "select pmpid from interviewee where pmpid = ? and fetch_history_date = '$date'";
            $sql = "select pmpid from interviewee 
                WHERE firstname = '$first_name' and lastname = '$last_name' and fetch_history_date = '$date'";
            
            $sth = $this->dbh->prepare($sql);
//            $sth->bindParam(1, $first_name);
//            $sth->bindParam(2, $last_name);
//            echo $sql;
//            die();
            $sth->execute();
            
            $sth->setFetchMode(PDO::FETCH_ASSOC);
            $row = $sth->fetch();

         
            if (($sth->rowCount() === 1) && ($_SESSION['pwd'] === md5('bellcanada'))) {
                $_SESSION['pmpid'] = $row['pmpid'];
                $pmpid = $_SESSION['pmpid'];
                
                $sqlr = "select room_number as room from room join interviewee on room.id = interviewee.room_assignment 
                WHERE pmpid = $pmpid and fetch_history_date = '$date'";
                
                $sth = $this->dbh->prepare($sqlr);
                $sth->execute();
                $sth->setFetchMode(PDO::FETCH_ASSOC);
                $result = $sth->fetch();                
                $_SESSION['room'] = $result['room'];
                $flag = true;
//                echo $_SESSION['room'];
//                die();
//                print_r($_SESSION);
//                die();
                
            } else {
                session_destroy();                
                if ($sth->rowCount() > 1) {
                    die("More than one matching candidate found. Contact ICT Software dept.");
                }
                $flag = false;
            }
           
            return $flag;
        }	
        else
        {
            return false;
        }	*/
		
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
							
							$flag	=	true;											 
						}
						else{
								$_SESSION['useridlogedin'] =  $pmpid;
								$_SESSION['usernamelogedin'] = $username;
								$flag	=	true;
								
							//$this->load->view('adminloginview');	//redirect View		with Failure
						}	
							//break;
							return $flag;							
						
					}else{
					
							$message= "<div class='alert alert-error'>
										<strong>Error!</strong> Please enter username and password.
										<button type='button' class='close' data-dismiss='alert'>&times;</button>
									</div>";
							return $flag;
							//$this->load->view('adminloginview');	//redirect View		with Failure
						
					}
				}
				//print_r($response);
			}
			catch (Exception $exc) {
				$this->load->view('adminloginview');		//redirect View		with Failure
				print_r($exc);
				echo $exc->getTraceAsString();
			}
		}else{
				return $flag;	
				//$this->load->view('adminloginview');		//redirect View		with Failure
			
		}
		//$conn=null;
    }
    /**
     * checks if the user has incomplete forms for the current date
     * @return type boolean; false if no available forms for user, true otherwise
     */
    public function has_inc_forms() {
        $date = Date('Y-m-d');
        $sql = "SELECT COUNT( form_id ) available
                FROM (
                SELECT form_id, (
                SELECT COUNT( choice_id ) ans
                FROM response rs
                WHERE rs.pmpid =fa.id
                AND rs.fetch_date = fa.date
                AND rs.form_id = fa.form_id
                )answers
                FROM form_assignment fa
                WHERE fetch_date = :date
                AND pmpid =:id
                )t1
                WHERE t1.answers=0";
        
        $this->dbh = $this->db->connect();
        $sth = $this->dbh->prepare($sql);
        $array = array(
            ':id' => $_SESSION['pmpid'],
            ':date' => $date
        );
        $sth->execute($array);

        $result = $sth->fetch();
        $dbh = null;
       
        if ( $result['available'] === 0) {
            session_destroy();
            return false;
        } else {
         
            return true;
        }
    }

    function hash($orig) {
        $this->dbh = $this->db->connect();
        $sql = "select PASSWORD(?) pass";
        $sth = $this->dbh->prepare($sql);
        $sth->bindParam(1, $orig);
        $sth->execute();
        $sth->setFetchMode(PDO::FETCH_ASSOC);
        $row = $sth->fetch();
        $this->dbh = null;
        return($row['pass']);
    }
}