<?php

namespace classes;

/**
 * Class that contains the form status of each form
 */
class FormStatus {

    /**
     * Used to store the..
     * @var string $form_ids_str
     */
    private $form_ids_str;

    /**
     * Used to store the..
     * @var integer $pmpid
     */
    public $pmpid;

    /**
     * Used to store ...
     * @var string $name
     */
    public $name;

    // public $num_responses; //array

    /**
     * Used to store ...
     * @var $form_responses
     */
    public $form_responses;

    /**
     * Used to ..
     * @var $form_ids
     */
    private $form_ids;

    /**
     * Used to store the form ID's for a specific user
     * @var $num_responses_str
     */
    private $num_responses_str;

    /**
     * Used to store the number of responses for a sepcific form
     * @var array $status_vals
     */
    public static $status_vals = array('0' => "unassigned", '1' => "assigned");

    /**
     * Determines if the user has been assigned the form or has completed it
     */
    function __construct() {
        $form_ids = explode(',', $this->form_ids_str);
        $response = explode(',', $this->num_responses_str);
        foreach ($form_ids as $key => $value) {
            if ($value == '')
                continue;
            if (!isset($response[$key]) || $response[$key] == '')
                $this->form_responses[$value] = 0;
            else
                $this->form_responses[$value] = $response[$key];
        }
    }

    /**
     * Determines if the user has been assigned the form or has completed it
     * @param integer $id The pmpid of the user
     * @return string
     */
    public function form_status($id) {
        if (!isset($this->form_responses[$id])) {
            return '0';
        } else if ($this->form_responses[$id] == 0)
            return '1'; //FormStatus::$status_vals[
        else
            return '2';
    }

    public function is_complete($id) {
        if ($this->form_status($id) == '2')
            return true;
        else
            return false;
    }

}