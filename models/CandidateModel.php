<?php

namespace models;

if (!defined('BASEPATH'))
    die("No direct script access");

//NOTE: sc.events.pmpID = pmp_app.ID -> pmp_app.pmpID (actual pmpID)
/**
 * Model for forms that does CRUD for candidate forms
 * Linked to FormPanel contoroller
 */
class CandidateModel extends Model {

    private $dbh;

    function __construct() {
        parent::__construct();
        $this->dbh = $this->candidate->connect();
        require(BASEPATH . 'classes/form.php');
        require(BASEPATH . 'classes/section.php');
        require(BASEPATH . 'classes/question.php');
        require(BASEPATH . 'classes/choice.php');
        require(BASEPATH . 'classes/validation.php');
        require(BASEPATH . 'classes/response.php');
        require(BASEPATH . 'classes/FormStatus.php');
    }

    /**
     * Determines if the candidate exists in the interviewee table for today's date
     * @param integer $id The pmpid of the user
     * @return boolean True if the candidate exists in the table for today 
     */
    public function check_user($id) {
        $date = Date('Y-m-d');
        $sql = "select * from `" . TBL_PREFIX . 
		"_interviewee` where pmpid = :id and fetch_history_date = '$date' limit 1";
        $sth = $this->dbh->prepare($sql);
        $array = array(":id" => $id);
        $sth->execute($array);

        if ($sth->fetch() !== false)
            return true;
        else
            return false;
        $dbh = null;
    }

    /**
     * Aborts transaction, so if it fails it doesn't write partial data
     */
    public function abort() {
        $this->dbh->rollBack();
    }

    /**
     * Start the transaction before entering the form to ensure valid entry
     */
    public function start_form_transaction() {
        $this->dbh->beginTransaction();
    }

    /**
     * if the form is valid, commit the transaction
     */
    public function commit_transaction() {
        $this->dbh->commit();
    }

	/**
     * checks the expiry date of the form assigned if the user accessing the form is a Candidate
     * @param integer $id The pmpid of the user
     * @param integer $form_id The form_id of the form
	 * @param integer $assignment_id The assignment id ,unique, to all forms assigned
	 * @return 
     */
	public function check_form_validity($id, $form_id, $assignment_id) {
		
		 $date = date('Y-m-d H:i:s');
		 
		$sql	=	"Select expiry_date from `" . TBL_PREFIX . "_form_assignment` fa
					where fa.pmpid = $id
					and fa.form_id = $form_id
					and fa.id	=	$assignment_id";
		
		$sth = $this->dbh->prepare($sql)  or die("coulnd't prepare");
        $sth->execute();
		$dbh = null;
        $expiry_date = $sth->fetchALL(PDO::FETCH_COLUMN);
			
		if(strtotime($date)	>	strtotime($expiry_date['0']))	{
			$expired	=	"true";
		}
		return	$expired;	
	}
    /**
     * Fetches available form for the user which hasn't been completed yet as is the next completable form (according to form_id)
     * @param integer $id The pmpid of the user
     * @param integer $form_id The form_id of the form
	 * @param integer $assignment_id The assignment id ,unique, to all forms assigned
	 * @return array $data An associative array containing 'title' (form title), 'fetch_date', 'form_id'
     */
    public function fetch_user_forms($id, $form_id, $assignment_id) {
        $date = Date('Y-m-d');
	
        $sql = "select fm.title, fa.fetch_date,fa.form_id, fa.id from `" . TBL_PREFIX . "_form_assignment` fa
                inner join 
                `" . TBL_PREFIX . "_interview_form` fm on fm.id = fa.form_id 
                left join 
                (select ro.form_id,ro.fetch_date,ro.pmpid, ro.assignment_id, count(choice_id) done from `" . TBL_PREFIX . "_response` ro group by ro.fetch_date,ro.pmpid,ro.form_id, ro.assignment_id) fr
                on fr.fetch_date = fa.fetch_date
                and fr.pmpid = fa.pmpid   
                and fr.form_id = fa.form_id
				and fr.assignment_id	=	$assignment_id
                where fa.pmpid = $id
				and fa.form_id = $form_id
				and fa.id	=	$assignment_id
             
                order by fm.order limit 1";
				//  and done is null
				
        $sth = $this->dbh->prepare($sql);
        $sth->execute();
        $data = $sth->fetch();
		
		if (empty($data)) {
		   
			$sth = $this->dbh->prepare("select count(*) `exists` from `" . TBL_PREFIX . "_interview_form` where id = :form_id");
			$sth->execute(array(':form_id' => $form_id));
		    
			$result = $sth->fetch(PDO::FETCH_ASSOC);
			
			/*if ($result['exists'] > 0) {	
				$sql = "insert into `" . TBL_PREFIX . "_form_assignment` (fetch_date, form_id, pmpid, signature) \n" .
					   "values (curdate(), $form_id, $id, '')";
				$check = $this->dbh->exec($sql);
				if ($check) {
				   $this->fetch_user_forms($id, $form_id, $assignment_id);
				}						
			}*/
		}
	   
	//	die();
        $dbh = null;
        return $data;
    }
    
    /**
     * Fetches the form information as defined by the Form Class
     * @param string $form_title The title of the form
     * @return array $form An associative array containing the form data as defined by the Form Class
     */
    function load_form($form_title) {
        $sql = "SELECT *
            FROM `" . TBL_PREFIX . "_interview_form`
            WHERE title = '$form_title'";
        $sth = $this->dbh->prepare($sql);
        $sth->execute();

        $form = $sth->fetchALL(PDO::FETCH_CLASS, 'Form');
        //$form =  $sth->fetch(PDO::FETCH_CLASS, 'Form');

        $dbh = null;
        return($form[0]);
    }

    /**
     * Fetches the sections of a specific form as defined by the Section Class
     * @param integer $form_id A unique ID for a form
     * @return array $sections An associative array containing the section data as defined by the Section Class
     */
    function load_sections($form_id) {
        $sql = "SELECT id, title, s_order, Interview_form_id
            FROM `" . TBL_PREFIX . "_section` 
            WHERE Interview_form_id = $form_id
            ORDER BY s_order";
        $sth = $this->dbh->prepare($sql);
        $sth->execute();
        $sections = $sth->fetchALL(PDO::FETCH_CLASS, 'Section');
        $dbh = null;
        return($sections);
    }

    /**
     * Fetches the questions for a specific form defined by the Question Class (according to the sections)
     * @param integer $section_id A unique ID for each section
     * @return array $questions An associative array containing the question data as defined by the Question Class
     */
    function load_questions($section_id) {
        $sql = "SELECT id, q_order, question_text, section_id, image
            FROM `" . TBL_PREFIX . "_question` 
            WHERE section_id = $section_id
            ORDER BY q_order";
        $sth = $this->dbh->prepare($sql);
        $sth->execute();
        $questions = $sth->fetchALL(PDO::FETCH_CLASS, 'Question');
        $dbh = null;

        return($questions);
    }
    
    /**
     * Fetches the choices for form questions defined by the Choice Class
     * @param integer $question_id A unique ID for each question
     * @return array $choices An associative array containing the choice data as defined by the Choice Class
     */
    function load_choices($question_id) {
        $sql = "SELECT id, label, c_order, question_id, type_id
            FROM `" . TBL_PREFIX . "_choice` 
            WHERE question_id = $question_id
            ORDER BY c_order";
        $sth = $this->dbh->prepare($sql);
        $sth->execute();
        $choices = $sth->fetchALL(PDO::FETCH_CLASS, 'Choice');
        $dbh = null;
        return($choices);
    }
    
    /**
     * Fetches the form validations defined by the Validation Class
     * @param integer $choice_id A unique ID for each choice
     * @return array $validations An associative array containing the choice data as defined by the Validation Class
     */
    function load_validations($choice_id) {
        $sql = "SELECT type
            FROM `" . TBL_PREFIX . "_validation` a INNER JOIN `" . TBL_PREFIX . "_validation_type` b
            ON a.type_id = b.id
            WHERE choice_id = $choice_id";
        $sth = $this->dbh->prepare($sql);
        $sth->execute();
        $validations = $sth->fetchALL(PDO::FETCH_CLASS, 'Validation');
        $dbh = null;
        return($validations);
    }

    /**
     * Assigns forms to users
     * @param integer $pmpid A unique (ID) for user
     * @param date $date Date to assign the form.
     * @param integer $form_id An integer
     */
    function assign_form($pmpid, $date, $form_id) {
        $sql = "INSERT INTO `" . TBL_PREFIX . "_form_assignment`
            (fetch_date, pmpid, form_id)
            VALUES('$date', $pmpid, $form_id)";

        $sth = $this->dbh->prepare($sql);
        $sth->execute();
        $dbh = null;
    }
    
    /**
     * Writes the user responses to the database
     * @param integer $choice_id The ID for each choice
     * @param integer $question_id The ID for each question
     * @param integer $form_id The ID for the form
     * @param integer $pmpid The pmpid for the user
     * @param date $date The date the form was completed
	 * @param integer $assignment_id The assignment id ,unique, to all forms assigned
     * @param string $user_response The user response to be written to the db
     */
    function record_responses($choice_id, $question_id, $form_id, $pmpid, $date, $assignment_id, $user_response='') {
        if ($date == "") {
            $date = Date("Y-m-d");
        }
        
	
        if ($user_response != "")
         {
            if (substr($user_response,0,8)=='{signed}')
        
            {
                
                $user_signature=substr($user_response,8);
                if (empty($user_signature)){
                    $sql = "INSERT INTO `" . TBL_PREFIX . "_response` 
                    SET choice_id=$choice_id, question_id=$question_id, fetch_date='$date', pmpid=$pmpid, form_id=$form_id, assignment_id = $assignment_id, user_entry='{unsigned}'
                    ON DUPLICATE KEY UPDATE question_id=$question_id, choice_id=$choice_id, user_entry='{unsigned}';";
                    $sql.="UPDATE `" . TBL_PREFIX . "_form_assignment` 
                    SET signature=NULL WHERE id=$assignment_id AND pmpid=$pmpid AND form_id=$form_id;";
                }
                else
                {
                    $user_signature=str_replace("'","\'",$user_signature);
                    $sql = "INSERT INTO `" . TBL_PREFIX . "_response` 
                    SET choice_id=$choice_id, question_id=$question_id, fetch_date='$date', pmpid=$pmpid, form_id=$form_id, assignment_id = $assignment_id, user_entry='{signed}'
                    ON DUPLICATE KEY UPDATE question_id=$question_id, choice_id=$choice_id, user_entry='{signed}';";
                    $sql.="UPDATE `" . TBL_PREFIX . "_form_assignment` 
                    SET signature='$user_signature' WHERE id=$assignment_id AND pmpid=$pmpid AND form_id=$form_id;";
                }
                /*
                $file1=fopen("C:\\xampp\\htdocs\\out_DB.sql","w");
                fwrite($file1,$user_signature."\n".$sql);
                fclose($file1);//*/
            }
            else
            {
                $sql .= "INSERT INTO `" . TBL_PREFIX . "_response` 
            SET choice_id=$choice_id, question_id=$question_id, fetch_date='$date', pmpid=$pmpid, form_id=$form_id, assignment_id='$assignment_id' ,  user_entry='$user_response'
                ON DUPLICATE KEY UPDATE question_id=$question_id, choice_id=$choice_id, user_entry='$user_response';";
            }
			
				$sql.=" UPDATE `" . TBL_PREFIX . "_form_assignment` 
                    SET status='in progress' WHERE id=$assignment_id AND pmpid=$pmpid AND form_id=$form_id AND status !='completed';" ;
            
        }
       else {
            $sql = "INSERT INTO `" . TBL_PREFIX . "_response` 
            SET choice_id=$choice_id, question_id=$question_id, fetch_date='$date', pmpid=$pmpid, form_id=$form_id , assignment_id='$assignment_id'
            ON DUPLICATE KEY UPDATE question_id=$question_id, choice_id=$choice_id";
            }
        $this->dbh->exec('SET NAMES utf8');
        $sth = $this->dbh->prepare($sql);
        $sth->execute();
        $dbh = null;
    }
    
    /**
     * Fetches the user response that had already been recorded (everytime the user clicks the next button a partial save is done
     * @param integer $pmpid The pmpid of the user
     * @param integer $form_id The form ID
     * @param integer $question_id The question ID
	 * @param integer $assignment_id The assignment id ,unique, to all forms assigned
     * @return array $responses An associative array containing the responses that a user has already recorded 
     */
    function fetch_user_responses($pmpid, $form_id, $question_id , $assignment_id) {
        $sql = "SELECT user_entry, pmpid, resp.question_id, choice_id, label, type_id
            FROM `" . TBL_PREFIX . "_response` resp INNER JOIN `" . TBL_PREFIX . "_choice` ch ON resp.choice_id = ch.id
            AND resp.question_id = ch.question_id
            WHERE form_id = $form_id
            AND pmpid = $pmpid
            AND resp.question_id = $question_id
			AND resp.assignment_id	=	$assignment_id";
			
        $sth = $this->dbh->prepare($sql);
        $sth->execute();
        $dbh = null;

        $responses = $sth->fetchALL(PDO::FETCH_CLASS, 'Response');
        
        foreach($responses as $response)            
        {
            if ($response->type_id == 13 && substr($response->user_entry,0,8)=='{signed}'){
                $sql = "SELECT signature FROM `" . TBL_PREFIX . "_form_assignment` 
                WHERE form_id = $form_id
                AND pmpid = $pmpid
                AND id = $assignment_id";
                $this->dbh->exec('SET NAMES utf8');
                $sth = $this->dbh->prepare($sql);
                $sth->execute();
                $dbh = null;
                $signature=$sth->fetchALL();
                //echo $signature[0]['signature'];
                $response->signature=$signature[0]['signature'];
                //echo $response->signature;
            }
        }        
    
        return $responses;
    }
	
	
    /**
     * Determines which users have been assigned
     * forms and whether they have completed the form
     * @param date $date The date of the form assignment
	 * @param integer $pmpid The pmpid of the Candidate 
     * @return array $data An array of FormStatus objects
     */
    public function get_form_statuses($date, $pmpid) {
        if (empty($date)) {
            $date = Date("Y-m-d");
        }
        //check this query once there is some data in the db
        $sql = "select k.pmpid,k.name, group_concat(k.num_responses) responses_str, group_concat(k.form_id)
                form_ids_str from
                (
                select interviewee.pmpid,concat(firstname,' ',lastname) name, num_responses, t2.form_id 
                from ". TBL_PREFIX . "_interviewee
                left join 
                (select fetch_date,fa.pmpid,fa.form_id
                from form_assignment fa
                where fetch_date = '$date') t2
                on fetch_history_date = t2.fetch_date
                and t2.pmpid = interviewee.pmpid
                left join
                (select pmpid, form_id, count(question_id) num_responses from
                response
                where fetch_date = '$date'
                group by form_id,pmpid
                ) c1
                on c1.pmpid = interviewee.pmpid
                and c1.form_id = t2.form_id
                where interviewee.fetch_history_date = '$date'
                and interviewee.pmpid = $pmpid
                order by name,t2.form_id
                ) k
                group by k.pmpid
                order by k.pmpid,k.form_id";

        $dbh = $this->candidate->connect();
        $sth = $dbh->prepare($sql);
        $sth->execute();
        $data = $sth->fetch();
        $dbh = null;
        return $data;
    }
    
    /**
     * Writes the status time for a specific status
     * @param integer $pmpid The pmpid of the user
     * @param integer $status_id The status id that is to be written to the database
     * @param time $timestamp Current Time
     */
    public function set_status_time($pmpid, $status_id, $timestamp) {

    }
    
    /**
     * Fetches the name of the form user
     * @param integer $pmpid The pmpid of the user
     * @return string $name The name of the user
     */
    function fetch_candidate_name($pmpid) {
        $date = Date('Y-m-d');
        $sql = "SELECT CONCAT(firstname,' ',lastname)
            FROM interviewee where pmpid = $pmpid
            AND fetch_history_date = '$date'";
        $sth = $this->dbh->prepare($sql);
        $sth->execute();
        $name = $sth->fetch();

        $dbh = null;
        return ($name[0]);
    }

    /**
     * Deletes the old data for a specific form (used when doing partial saves 
     * @param integer $form_id The form ID
     * @param integer $pmpid The pmpid of the user
     * @param date $date The date to delete the data
	 * @param integer $assignment_id The assignment id ,unique, to all forms assigned
     */
    function delete_all_responses($form_id, $pmpid, $date, $assignment_id) {
        if (empty($date)) {
            $date = Date("Y-m-d");
        }
        $sql = "DELETE from `" . TBL_PREFIX . "_response`
            WHERE form_id=$form_id and fetch_date='$date' and pmpid=$pmpid and assignment_id=$assignment_id";

        $sth = $this->dbh->prepare($sql);
        $sth->execute();
        
        $sql.="UPDATE `" . TBL_PREFIX . "_form_assignment` 
        SET signature= NULL WHERE fetch_date='$date' AND pmpid=$pmpid AND form_id=$form_id;";
        $sth = $this->dbh->prepare($sql);
        $sth->execute();
        
        $dbh = null;
    }
    
    /**
     * Fetches the start time of a form (to display on the timer in the view)
     * @param integer $status_id The status ID to get the time for
     * @param integer $pmpid The pmpid of the user
     * @param date $date The date of the form assignment
     * @return time $time The start time of a specific form 
     */
    function fetch_start_time($status_id, $pmpid, $date) {
        if (empty($date)) {
            $date = Date("Y-m-d");
        }
        $sql = "SELECT time from `" . TBL_PREFIX . "_interviewee_status` 
            WHERE Interviewee_pmpid=$pmpid 
            AND Interviewee_fetch_history_date='$date'
            AND status_id=$status_id";

        $sth = $this->dbh->prepare($sql);
        $sth->execute();
        $time = $sth->fetch();
        $dbh = null;
        return ($time[0]);
    }
    
    /**
     * Fetches the status ID for the start event of a form
     * @param integer $form_id The form ID
     * @return integer $status The status ID for the start of the form event
     */
    function get_begin_status_id($form_id) {
        $sql = "SELECT begin_id from `" . TBL_PREFIX . "_interview_form` 
            WHERE id=$form_id";

        $sth = $this->dbh->prepare($sql);
        $sth->execute();
        $status = $sth->fetch();
        $dbh = null;
        return ($status[0]);
    }
    
    /** CURRENTLY UNUSED AND THIS IS LOADED ON LOG-IN
     * Fetches the assigned room for the user
     * @param integer $pmpid The pmpid of the Candidate 
     * @param date $date The date of the form assignment
     * @return string 
     */
    function get_room($pmpid, $date='') {
        if ($date == '') {
            $date = date('Y-m-d');
        }
        $sql = "SELECT room_number from `" . TBL_PREFIX . "_room`
            JOIN `" . TBL_PREFIX . "_interviewee` on room.id=room_assignment
            WHERE pmpid=$pmpid AND fetch_history_date='$date'";
        $sth = $this->dbh->prepare($sql);
        $sth->execute();
        $result = $sth->fetch();
        $dbh = null;
        return ($result[0]);
    }

    /**
     * Used to update form assignment status and submission date
     * @param integer $assignment_id The assignment id ,unique, to all forms assigned
     * @return bool : success or failure
     */
    function submit_form($assignment_id){
        $completed_date=date('Y-m-d H:i:s');
        $sql =	"UPDATE  `" . TBL_PREFIX . "_form_assignment` SET status='completed', completed_date='$completed_date' WHERE id = $assignment_id";
        $sth = $this->dbh->prepare($sql);
        if ($sth->execute())
        {
            $dbh = null;
            return true;
        }
        else
        {
            $dbh = null;
            return false;
        }
    }
}

