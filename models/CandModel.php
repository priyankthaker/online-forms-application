<?php

namespace models;

/** @file CandModel.php
@date:  Feb 22nd 2013
@author Thaker:Priyank
@brief This file is a Model file for the "Candidate Form View" 
*/


if (!defined('BASEPATH'))
    die("No direct script access");

//NOTE: sc.events.pmpID = pmp_app.ID -> pmp_app.pmpID (actual pmpID)
/**
 * Model for Candidate forms
 * Linked to CandidateFormPanel contoroller
 */
class CandModel extends Model {

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
     * Fetches the names of the forms that are present in the database every time the page is loaded.
     * @return array $result An associative array containing names of the forms to be displayed 
     */
	public function fetch_forms_names($pmpid)	{
	
		$sql	=	"SELECT form_names.title,form_assign.form_id, form_assign.expiry_date, form_assign.status, form_assign.id as assignment_id
						FROM (SELECT fi.id, fi.title FROM `" . TBL_PREFIX . "_interview_form` fi)	form_names
						INNER JOIN	(SELECT f_a.id, f_a.form_id, f_a.expiry_date, f_a.status
						FROM  `". TBL_PREFIX . "_form_assignment` f_a
						WHERE CURDATE( ) < f_a.expiry_date
						AND f_a.pmpid =$pmpid
						ORDER BY CURDATE( ) )	form_assign
						ON form_assign.form_id = form_names.id";
	
	
		$sth = $this->dbh->prepare($sql) or die("couldn't prepare");
        $sth->execute();
        $dbh = null;
		
		$result	=  $sth->fetchALL(PDO::FETCH_ASSOC);
				
		return $result;
	
	}
}

?>