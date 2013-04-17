<?php

namespace classes;

require_once('iPrintable.php');

/**
 * Class that contains the sections of the form
 */
class Section implements iPrintable{
    
    /**
     * Used to store the unique section id
     * @var integer $id
     */
    public $id;
    
    /**
     * Used to store title of a section
     * @var string $title
     */
    public $title;
    
    /**
     * Used to store the unique number for the section order
     * @var int $s_order
     */
    public $s_order;
    
    /**
     * Used to store the form id for a section
     * @var integer $form_id
     */
    public $form_id;
    
    /**
     * Used to store an array of questions for each section
     * @var string $questions
     */
    public $questions; //array of questions
   
    public $numbered;
    /**
     * This is and inherited funtion that prints the secitons to the webpage
     * @param type $html - The text, in html, that will be placed on the form
     * @return type - none 
     */
    public function toHtml($html='')
    {
       
       $html .= "<div class='row'>";
       $html .= "<div class='twelve columns'>";
       $html .= "<div class='section_head'>SECTION ".$this->s_order.": ".$this->title;
       $html .= "</div>";
       $html .= "<div class='save-status'>Saving..</div>";
       
       //$html .=;
       $questionHtml = "";       
       foreach($this->questions as $question)
       {
           if($this->numbered)
           {
               $html .= "<div class='ques_row'>";
               $html .= "<span class='ques_num'></span>";
               $html .= "<li class='ques_li'>";
               //$html .= "<div class='row'>".$question->toHtml($questionHtml)."</div>";
               $html .= $question->toHtml($questionHtml);
               $html .= "</li>";
               $html .="</div>";
           }
           else
           {
                $html .= "<div class='row'>".$question->toHtml($questionHtml)."</div>";
           }
       }
       $html .="</div></div>";
       return $html;
    }
}