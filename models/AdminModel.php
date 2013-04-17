<?php

namespace models;

/** @file AdminModel.php
@date:  Jan 27th 2013
@author Thaker:Priyank
@brief This file is a Model file for the "Admin Form View" 
*/


if (!defined('BASEPATH'))
    die("No direct script access");

//NOTE: sc.events.pmpID = pmp_app.ID -> pmp_app.pmpID (actual pmpID)
/**
 * Model for forms that Admin Can See
 * Linked to AdminFormPanel contoroller
 */
class AdminModel extends Model {

    private $dbh;

    function __construct() {
        parent::__construct();
        $this->dbh = $this->candidate->connect();
    }

   	/**
     * Fetches the names of the forms that are present in the database every time the page is loaded.
     * @return array $result An associative array containing names of the forms to be displayed 
     */
	public function fetch_forms_names()	{
	
		$sql	=	"SELECT id,title FROM `" . TBL_PREFIX . "_interview_form`";
	
		$sth = $this->dbh->prepare($sql) or die("couldn't prepare");
        $sth->execute();
        $dbh = null;
		$result	=  $sth->fetchALL(PDO::FETCH_ASSOC);
		return $result;
	
	}
	
	/**
     * Fetches the name of the form that the admin has selected to view its content
     * @param integer $form_id The id of the form selected by the admin
     * @return variable $name variable containing the name of the current form	 
     */
	public function	cur_form_name($form_id)	{
	
		$sql	=	"SELECT title FROM `" . TBL_PREFIX . "_interview_form` where id=$form_id";	
		$sth = $this->dbh->prepare($sql) or die("coulnd't prepare");
        $sth->execute();
        $dbh = null;
		
		$name	=  $sth->fetchALL(PDO::FETCH_COLUMN);
		return $name;
	}
	
	/**
     * Fetches the info of the users who have been assigned the selected form
     * @param integer $form_id The id of the form selected by the admin
     * @param datetime $date_from The date that the admin enters from where the data is asked to be viewed, value can be NULL
     * @param datetime $date_to The date that the admin enters till when the data is asked to be viewed , value can be NULL
     * @return array $data An associative array containing the data of all the candidates who have been assigned the selected form 
     */
	public function fetch_candidate_info($form_id, $date_from , $date_to){
		
			if(($date_from	!=	NULL) && ($date_to	!=	NULL)){
			
			if($date_from	> $date_to)	{
			
				$_SESSION['error_message_date']	=	"Please Select a Valid Date Range.";
				return ;
			}
			else	{	
			
			$sql = 	"SELECT f_assign.pmpid as id, f_assign.fetch_date as assign_date, f_assign.completed_date as comp_date, f_assign.expiry_date, f_assign.id as assignment_id, f_assign.status
					FROM (	SELECT DISTINCT f_a.fetch_date, f_a.pmpid, f_a.form_id, f_a.expiry_date, f_a.id, f_a.completed_date, f_a.status
							FROM `". TBL_PREFIX . "_form_assignment` f_a	WHERE f_a.form_id =$form_id)	f_assign
					WHERE f_assign.fetch_date	BETWEEN '$date_from' AND '$date_to'";	
			}
			}			
			else	if(($date_from	!=	NULL) && ($date_to	==	NULL)){
			
			$sql = 	"SELECT f_assign.pmpid as id, f_assign.fetch_date as assign_date, f_assign.completed_date as comp_date, f_assign.expiry_date, f_assign.id as assignment_id, f_assign.status
					FROM (	SELECT DISTINCT f_a.fetch_date, f_a.pmpid, f_a.form_id, f_a.expiry_date, f_a.id, f_a.completed_date, f_a.status
							FROM `". TBL_PREFIX . "_form_assignment` f_a	WHERE f_a.form_id =$form_id)	f_assign
					WHERE f_assign.fetch_date	BETWEEN '$date_from' AND UTC_TIMESTAMP()";							
			}
			else	if(($date_from	==	NULL) && ($date_to	!=	NULL)){
			
			$sql = 	"SELECT f_assign.pmpid as id, f_assign.fetch_date as assign_date, f_assign.completed_date as comp_date, f_assign.expiry_date, f_assign.id as assignment_id, f_assign.status
					FROM (	SELECT DISTINCT f_a.fetch_date, f_a.pmpid, f_a.form_id, f_a.expiry_date, f_a.id, f_a.completed_date, f_a.status
							FROM `". TBL_PREFIX . "_form_assignment` f_a	WHERE f_a.form_id =$form_id)	f_assign
					WHERE f_assign.fetch_date	BETWEEN '$date_from' AND '$date_to'";							
			}
			else	{	
				
				$sql = 	"SELECT f_assign.pmpid as id, f_assign.fetch_date as assign_date, f_assign.completed_date as comp_date, f_assign.expiry_date, f_assign.id as assignment_id, f_assign.status
					FROM (	SELECT DISTINCT f_a.fetch_date, f_a.pmpid, f_a.form_id, f_a.expiry_date, f_a.id, f_a.completed_date, f_a.status
							FROM `". TBL_PREFIX . "_form_assignment` f_a	WHERE f_a.form_id =$form_id)	f_assign";
				
			}
					
			$sth=$this->dbh->prepare($sql)	or die("coulnd't prepare");
			$sth->execute();
			$dbh=null;
					
			$data	=	$sth->fetchALL(PDO::FETCH_ASSOC);
			return $data;
	}
	
	/**
     * Fetches the info of the Selected User who have been assigned various forms 
	 * @param integer $pmpid The pmpid of the Candidate selected by the admin
     * @param datetime $date_from The date that the admin enters from where the data is asked to be viewed, value can be NULL
     * @param datetime $date_to The date that the admin enters till when the data is asked to be viewed , value can be NULL
     * @return array $data An associative array containing the data of all the forms who have been assigned the selected Candidate 
     */
	public function fetch_selected_candidate_info($pmpid, $date_from , $date_to){
		
			if(($date_from	!=	NULL) && ($date_to	!=	NULL)){
			
			if($date_from	> $date_to)	{
			
				$_SESSION['error_message_date']	=	"Please Select a Valid Date Range.";
				return ;
			}
			else	{	
			
			$sql = 	"SELECT form_names.title,form_assign.form_id, form_assign.fetch_date as assign_date,form_assign.completed_Date as comp_date, form_assign.expiry_date, form_assign.status, form_assign.id as assignment_id, form_assign.pmpid
							FROM (SELECT fi.id, fi.title FROM `". TBL_PREFIX . "_interview_form` fi)	form_names
							INNER JOIN	(SELECT f_a.id, f_a.form_id, f_a.fetch_date, f_a.completed_date,  f_a.expiry_date, f_a.status, f_a.pmpid
							FROM  `". TBL_PREFIX . "_form_assignment` f_a
							WHERE	f_a.pmpid =$pmpid
							AND f_a.fetch_date	BETWEEN '$date_from' AND '$date_to'
							ORDER BY CURDATE( ) )	form_assign
							ON form_assign.form_id = form_names.id";	
			}
			}			
			else	if(($date_from	!=	NULL) && ($date_to	==	NULL)){
			
			$sql = 	"SELECT form_names.title,form_assign.form_id, form_assign.fetch_date as assign_date,form_assign.completed_Date as comp_date, form_assign.expiry_date, form_assign.status, form_assign.id as assignment_id, form_assign.pmpid
							FROM (SELECT fi.id, fi.title FROM `". TBL_PREFIX . "_interview_form` fi)	form_names
							INNER JOIN	(SELECT f_a.id, f_a.form_id, f_a.fetch_date, f_a.completed_date,  f_a.expiry_date, f_a.status, f_a.pmpid
							FROM  `". TBL_PREFIX . "_form_assignment` f_a
							WHERE	f_a.pmpid =$pmpid
							AND f_a.fetch_date	BETWEEN '$date_from' AND UTC_TIMESTAMP()
							ORDER BY CURDATE( ) )	form_assign
							ON form_assign.form_id = form_names.id";							
			}
			else	if(($date_from	==	NULL) && ($date_to	!=	NULL)){
			
			$sql = 	"SELECT form_names.title,form_assign.form_id, form_assign.fetch_date as assign_date,form_assign.completed_Date as comp_date, form_assign.expiry_date, form_assign.status, form_assign.id as assignment_id, form_assign.pmpid
							FROM (SELECT fi.id, fi.title FROM `". TBL_PREFIX . "_interview_form` fi)	form_names
							INNER JOIN	(SELECT f_a.id, f_a.form_id, f_a.fetch_date, f_a.completed_date,  f_a.expiry_date, f_a.status, f_a.pmpid
							FROM  `". TBL_PREFIX . "_form_assignment` f_a
							WHERE	f_a.pmpid =$pmpid
							AND f_a.fetch_date	BETWEEN '$date_from' AND '$date_to'
							ORDER BY CURDATE( ) )	form_assign
							ON form_assign.form_id = form_names.id";							
			}
			else	{	
				
				$sql = 	"SELECT form_names.title,form_assign.form_id, form_assign.fetch_date as assign_date,form_assign.completed_Date as comp_date, form_assign.expiry_date, form_assign.status, form_assign.id as assignment_id, form_assign.pmpid
							FROM (SELECT fi.id, fi.title FROM `". TBL_PREFIX . "_interview_form` fi)	form_names
							INNER JOIN	(SELECT f_a.id, f_a.form_id, f_a.fetch_date, f_a.completed_date,  f_a.expiry_date, f_a.status, f_a.pmpid
							FROM  `". TBL_PREFIX . "_form_assignment` f_a
							WHERE	f_a.pmpid =$pmpid
							ORDER BY CURDATE( ) )	form_assign
							ON form_assign.form_id = form_names.id";
			}
					
			$sth=$this->dbh->prepare($sql)	or die("coulnd't prepare");
			$sth->execute();
			$dbh=null;
			
			$data	=	$sth->fetchALL(PDO::FETCH_ASSOC);
			return $data;
	}
		
	/**
     * Fetches the info of the Selected User and the Selected form which has been assigned
	 * @param integer $form_id The id of the form selected by the admin
     * @param integer $pmpid The pmpid of the Candidate selected by the admin
     * @param datetime $date_from The date that the admin enters from where the data is asked to be viewed, value can be NULL
     * @param datetime $date_to The date that the admin enters till when the data is asked to be viewed , value can be NULL
     * @return array $data An associative array containing the data of Selected form assigned the selected Candidate 
     */
	public function fetch_selected_candidate_form_info($form_id, $pmpid, $date_from , $date_to){
		
			if(($date_from	!=	NULL) && ($date_to	!=	NULL)){
			
			if($date_from	> $date_to)	{
			
				$_SESSION['error_message_date']	=	"Please Select a Valid Date Range.";
				return ;
			}
			else	{	
			
			$sql = 	"SELECT form_names.title,form_assign.form_id, form_assign.fetch_date as assign_date,form_assign.completed_Date as comp_date, form_assign.expiry_date, form_assign.status, form_assign.id as assignment_id, form_assign.pmpid
							FROM (SELECT fi.id, fi.title FROM `". TBL_PREFIX . "_interview_form` fi)	form_names
							INNER JOIN	(SELECT f_a.id, f_a.form_id, f_a.fetch_date, f_a.completed_date,  f_a.expiry_date, f_a.status, f_a.pmpid
							FROM  `". TBL_PREFIX . "_form_assignment` f_a
							WHERE f_a.pmpid =$pmpid
							AND f_a.form_id = $form_id
							AND f_a.fetch_date	BETWEEN '$date_from' AND '$date_to'
							ORDER BY CURDATE( ) )	form_assign
							ON form_assign.form_id = form_names.id";	
			}
			}			
			else	if(($date_from	!=	NULL) && ($date_to	==	NULL)){
			
			$sql = 	"SELECT form_names.title,form_assign.form_id, form_assign.fetch_date as assign_date,form_assign.completed_Date as comp_date, form_assign.expiry_date, form_assign.status, form_assign.id as assignment_id, form_assign.pmpid
							FROM (SELECT fi.id, fi.title FROM `". TBL_PREFIX . "_interview_form` fi)	form_names
							INNER JOIN	(SELECT f_a.id, f_a.form_id, f_a.fetch_date, f_a.completed_date,  f_a.expiry_date, f_a.status, f_a.pmpid
							FROM  `". TBL_PREFIX . "_form_assignment` f_a
							WHERE f_a.pmpid =$pmpid
							AND f_a.form_id = $form_id
							AND f_a.fetch_date	BETWEEN '$date_from' AND UTC_TIMESTAMP()
							ORDER BY CURDATE( ) )	form_assign
							ON form_assign.form_id = form_names.id";							
			}
			else	if(($date_from	==	NULL) && ($date_to	!=	NULL)){
			
			$sql = 	"SELECT form_names.title,form_assign.form_id, form_assign.fetch_date as assign_date,form_assign.completed_Date as comp_date, form_assign.expiry_date, form_assign.status, form_assign.id as assignment_id, form_assign.pmpid
							FROM (SELECT fi.id, fi.title FROM `". TBL_PREFIX . "_interview_form` fi)	form_names
							INNER JOIN	(SELECT f_a.id, f_a.form_id, f_a.fetch_date, f_a.completed_date,  f_a.expiry_date, f_a.status, f_a.pmpid
							FROM  `". TBL_PREFIX . "_form_assignment` f_a
							WHERE f_a.pmpid =$pmpid
							AND f_a.form_id = $form_id
							AND f_a.fetch_date	BETWEEN '$date_from' AND '$date_to'
							ORDER BY CURDATE( ) )	form_assign
							ON form_assign.form_id = form_names.id";							
			}
			else	{	
				
				$sql = 	"SELECT form_names.title,form_assign.form_id, form_assign.fetch_date as assign_date,form_assign.completed_Date as comp_date, form_assign.expiry_date, form_assign.status, form_assign.id as assignment_id, form_assign.pmpid
							FROM (SELECT fi.id, fi.title FROM `". TBL_PREFIX . "_interview_form` fi)	form_names
							INNER JOIN	(SELECT f_a.id, f_a.form_id, f_a.fetch_date, f_a.completed_date,  f_a.expiry_date, f_a.status, f_a.pmpid
							FROM  `". TBL_PREFIX . "_form_assignment` f_a
							WHERE f_a.pmpid =$pmpid
							AND f_a.form_id = $form_id
							ORDER BY CURDATE( ) )	form_assign
							ON form_assign.form_id = form_names.id";
			}
					
			$sth=$this->dbh->prepare($sql)	or die("coulnd't prepare");
			$sth->execute();
			$dbh=null;
			
			$data	=	$sth->fetchALL(PDO::FETCH_ASSOC);
			return $data;
	}	
	
	/**
     * Assignes the selected candidate to the selected form with the assigning date and expiry date selected.
     * @param integer $pmpid The pmpid of the user
     * @param datetime $date_assign The date that the form is assigned
     * @param integer $form_id The form ID of the selected form
     * @param datetime $date_expiry The date that the form will expire 
     */
	public function add_candidate_name($pmpid, $date_assign , $form_id, $date_expiry)	{	
			
			$sql	=	"INSERT into	`".TBL_PREFIX . "_form_assignment` (fetch_date , pmpid, form_id, expiry_date)
						VALUES
						('$date_assign',	'$pmpid',	'$form_id', '$date_expiry')";
						
			$sth=$this->dbh->prepare($sql) or die("coulnd't prepare");
			$sth->execute();
			$dbh=null;	
			return;
	}		
}
?>