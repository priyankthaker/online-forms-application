<?php

namespace controllers;

if (!defined('BASEPATH'))
    die("No direct script access");

/**
 * A controller that determines what happens after a form has been submitted
 * @pre  - a form has been submitted
 * @post - the user's name, completed form, current room and pmpid are kept in the session 
 */
class Submit extends Controller {

    function __construct($parms=array()) {

        parent::__construct($parms);

		//$data = $this->keep_session_data($_SESSION);
        //session_unset();
        //$_SESSION = $data;
		header('Location: CandidateFormPanel');
       
		exit;
    }

    /**
     * kjhjkh
     * @param type $session
     * @return array containing the username, completed form name, room and pmpid
     */
    function keep_session_data($session) {
        $form = $session['form'];

        $data = array(
            'name' => $session['username'],
            'form_name' => $form['title'],
            'room' => $session['room'],
            'pmpid' => $session['pmpid']
        );

        return $data;
    }

}

/**
 * Use this aray to test the submit controller
 */ 
/*
        $form = array (
          'title' => 'Test Form'  
        );
        $test = array (
            'pmpid' => "030801",
            'username' => 'Shane Whitty',
            'room' => '5-18',
            'form' => $form
        );
        $data = $this->keep_session_data($test);
        session_destroy();
 * */
 