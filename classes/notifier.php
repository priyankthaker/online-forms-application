<?php

namespace classes;

if (!defined('BASEPATH'))
    die("No direct script access");
/**
 * Class that contains the notifier
 */
class Notifier {

    public $dbh1;
    public $dbh2;
    public $db1;
    public $db2;

    function __construct() {
        $this->db1 = new Database('candidate');
        $this->db2 = new Database('webtime');
    }

//    public static function SendCheckInNotifications($candidates) {
//        $msg_type = 'check-in';
//        if($candidates->attendance_id == 2){
//            $obj = new Notifier();
//            $obj->ComposeAndSend($obj, $candidates->id, $msg_type,'');  
//        }
//        else if($candidates->attendance_id == 3){
//            $obj = new Notifier();
//            $obj->ComposeAndSend($obj, $candidates->id, $msg_type,'');
//        }
//    }
    
    
    public static function SendStatusNotifications($pmpid, $status_id) {
//     echo $pmpid;
     
        if($status_id == 9){
            //send notification to interviewers with estimated time of completion
            $obj = new Notifier();
            $obj->ComposeAndSend($obj, $pmpid, $status_id);
            //exit;
        }
        else if($status_id == 12){
//            echo $status_id;
//            die();
            //send notification to interviewers to indicate end of IAQ
            $obj = new Notifier();
            $obj->ComposeAndSend($obj, $pmpid, $status_id);
            //exit;
        }
    }
    
    static private function ComposeAndSend($object, $pmpid, $status_id=''){
        $date = date('Y-m-d');
        $object->dbh1 = $object->db1->connect();
                

        $sql = "SELECT title, inter1_pmpid, inter2_pmpid, inter3_pmpid, room_number, CONCAT(firstname, ' ', lastname) as name 
            FROM interviewee
            left JOIN room ON interviewee.room_assignment = room.id
            WHERE pmpid = $pmpid and fetch_history_date = '$date'";
        

        $sth = $object->dbh1->prepare($sql);
        $sth->execute();
        $cand_details = $sth->fetch(PDO::FETCH_ASSOC);
        $object->dbh1 = null;
        $prefix = "";
        $pronoun = "";
        $msg = "";

        if($status_id == 9){
            $msg_type = 'PIA-start';

            $object->dbh1 = $object->db1->connect();
            $sql = "SELECT time FROM interviewee_status
                WHERE Interviewee_pmpid = $pmpid and Interviewee_fetch_history_date = '$date' and status_id =$status_id";
            
//            echo $sql;

            $sth = $object->dbh1->prepare($sql);
            $sth->execute();
            $begin_time = $sth->fetch(PDO::FETCH_ASSOC);
            $object->dbh1 = null;
            
            //calculate 32mins after the start of the IAQ
            $end_time = (strtotime($begin_time['time'])+1920);
            $time = date('h:i A', $end_time);
            $subject = 'Interview Ready';
            $msg = 'Good Morning,<br/>';
            $msg .= $cand_details['name'] . " will be ready at " . $time . " in room " . $cand_details['room_number'] . ".";
        }
        else if($status_id == 12){
            $msg_type = 'IAQ-end';
            $subject = 'IAQ Complete';
            $msg = 'Hello,<br/>';
            $msg .= $cand_details['name'] . 'has completed IAQ. You can proceed with the second half in 10 minutes.';
        }
          
        $msg .= "<br/><br/>Thank you, <br/>
                -- <br/>
                Talent Management Team<br/>
                Professional Management Program<br/>
                Bell Mobiliy";
        
        
        //get the email addresses for the interviewers
        $sqli;
        if(isset($cand_details['inter1_pmpid'])){
            $object->dbh2 = $object->db2->connect();
            $inter1_pmpid = $cand_details['inter1_pmpid'];
            $sqli = "SELECT email FROM tc_user WHERE id IN($inter1_pmpid";
            if(isset($cand_details['inter2_pmpid'])){
                $inter2_pmpid = $cand_details['inter2_pmpid'];
                $sqli .= ", $inter2_pmpid";
            }
            if(isset($cand_details['inter3_pmpid'])){
                $inter3_pmpid = $cand_details['inter3_pmpid'];
                $sqli .= ", $inter3_pmpid";
            }
            $sqli .= ")";
            
        }
         
        if(isset($sqli)){
            $sth = $object->dbh2->prepare($sqli);
            $sth->execute();
            $addresses = $sth->fetchALL(PDO::FETCH_ASSOC);
           // $addresses[count($addresses)]['email'] =  'recruitment.room@bellpmp.org';
        }
        else{
            $subject = "No Interviewers Assigned";
            $msg = "No Interviewers have been assigned to any of the candidates.";
        }
        //put this in teh email settings for the footer?
        $msg .= "<br/><br/>Thank you, <br/>
                -- <br/>
                Talent Management Team<br/>
                Professional Management Program<br/>
                Bell Mobiliy";
        // $addresses[count($addresses)]['email'] =  'recruitment.room@bellpmp.org';
        //$addresses[count($addresses)]['email'] =  'shane.whittaker@bellpmp.com';
        PredisHandler::setEmailBody($msg, $subject, $pmpid, $msg_type, $addresses);
        
        
        //$object->SendEmail($subject, $msg, $addresses);
        
    }
    
    function toPostField($fields) {
        
        $fields_string = '';
        foreach($fields as $key=>$value) { 
            $fields_string .= urlencode($key).'='. urlencode($value).'&';    
        }
        
        return $fields_string;
    }
    
    function SendEmail($subject, $msg, $addresses){
        //send using nojs
        echo $msg;
        return;
        $url = NODEURL . 'sendMails';
       
        
        $fieldString  = $this->toPostField( array(
            'message' => $msg,
            'subject' => $subject,
            'addresses' =>   json_encode($addresses)
        ));
        echo $fieldString;
        $c = curl_init($url);
        curl_setopt($c, CURLOPT_POST, true);
        curl_setopt($c, CURLOPT_POSTFIELDS, $fieldString);
        curl_setopt($c, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($c, CURLOPT_CONNECTTIMEOUT_MS, 1);
        $page = curl_exec($c);
        curl_close($c);
        
        
//        require_once "Mail.php";
//
//        $from = "irispmp@gmail.com";
////        $to = $addresses;
//        $subject = "Candidate Arrived";
//        $body = $msg;
//
//        $host = "ssl://smtp.gmail.com";
//        $port = "465";
//        $username = "irispmp@gmail.com";
//        $password = "bellcanada01";
//        
//        foreach($addresses as $email){
//            $headers = array('From' => $from,
//            'To' => $email['email'],
//            'Subject' => $subject,
//            "Content-type" => "text/html; charset=iso-8859-1");
//            $smtp = Mail::factory('smtp', array('host' => $host,
//                    'port' => $port,
//                    'auth' => true,
//                    'username' => $username,
//                    'password' => $password));
//        
//            $mail = $smtp->send($email['email'], $headers, $body);
//        }
//        
//        
//
////        if (PEAR::isError($mail)) {
////            echo("<p>" . $mail->getMessage() . "</p>");
////        } else {
////            echo("<p>Message successfully sent!</p>");
////        }
    }

}