<?php

namespace classes;

require_once('iPrintable.php');
/**
 * Class that contains the form
 */
class Form implements iPrintable {

    /**
     * Used to store the id of the form
     * @var $id
     */
    public $id;

    /**
     * Used to store the title of the form
     * @var $title
     */
    public $title;

    /**
     * Used to store an array of sections
     * @var $sections
     */
    public $sections;

    /**
     * Used to store the pmpid of the candidate
     * @var $pmpid
     */
    public $pmpid;

    /**
     * Used to store the name of the candidate
     * @var $candidate_name
     */
    public $candidate_name;

    /**
     * Used to store the signature of the candidate
     * @var $signature
     */
    public $signature;
    
    
    /**
     * Used to store a variable that determines whether a test is numbered of not
     * @var $signature
     */
    public $numbered;
    
    
    public $begin_id;
    
    public $begin_time;

    /**
     * This is and inherited funtion that prints the entire form to the webpage
     * @param type $html - The text, in html, that will be placed on the form
     * @return type - none 
     */
    public $timed;

    public function toHtml($html='') {
        $html = "<div class='form_t'><div class='form_title'>$this->title </div>";
        $html .= "<div class='form_candidate_name'>" . $this->candidate_name . "</div>";
        $html .= "</div><div class='form_date'>" . Date('d/m/Y') . "</div>";
        $html .= "<div style='padding-bottom:5px;'><div id='progressbar' style='height:20px; width:100%;'><span class='progress_bar'></span></div></div>";

        $html .= "<input type='hidden' name='form_id' value='$this->id'/>";
        $html .= "<input type='hidden' name='user_sig' value='$this->signature'/>";
        $html .= "<input type='hidden' name='pmpid' value='$this->pmpid'/>";
        if (isset($this->timed)) {
            $html .= "<div class='timer_div'>";
            $html .= "<div class='timer_container'>";
            $html .= "<input";
            if ($this->timed == 1) {
                $html .= " name='timer_down'";
            } else if ($this->timed == 2) {
                $html .= " name='timer_up'";
            }
            $html .= " id='timer' value='$this->begin_time' style='display: none;'/>";
            $html .= "</div></div>";
        }

        $html .= "<div class='formData'>";
        $sectionHtml = "";
        if ($this->numbered) {
            $html .= "<ul class='form_ol'>";
//            $html .= "<ol >";
        }
        foreach ($this->sections as $section) {
            $section->numbered = $this->numbered;
            $html .= "<div class='section'>" . $section->toHtml($sectionHtml);
            $html .="</div>";
        }
        if ($this->numbered) {
            $html .= "</ul>";
        }
        $html .= "</div>";

        return $html;
    }

}
