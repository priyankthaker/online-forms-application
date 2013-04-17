<?php

namespace classes;

/**
 * Class that contains the responses of each choices
 */
class Response {
    /**
     * Used to store the user entry for a specific choice
     * @var $user_entry
     */
    public $user_entry;
    
    /**
     * Used to store the date of a response
     * @var $fetch_date
     */
    public $fetch_date;
    
    /**
     * Used to store the user id for a response
     * @var $pmpid
     */
    public $pmpid;
    
    /**
     * Used to store the question id for a response
     * @var $question_id
     */
    public $question_id;
    
    /**
     * Used to store the choide id for a response
     * @var $choice_id
     */
    public $choice_id;
    
    /**
     * Used to store the label value of a response (or choice)
     * @var $label
     */
    public $label;
    
    /**
     * Used to store the type id of a a response
     * @var $type_id
     */
    public $type_id;    
    
    /**
     * Used to store the signature from survey_form_assignment
     * for signature response ONLY
     * @var $signature
     */
    public $signature;
}