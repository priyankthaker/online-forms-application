<?php

namespace classes;

require_once('iPrintable.php');

/**
 * Class that contains the questions for each section
 */
class Question implements iPrintable{
    /**
     * Used to store id of a question
     * @var $id
     */
    public $id;
    
    /**
     * Used to store a number that contains the order of a question
     * @var $q_order
     */
    public $q_order;
    
    /**
     * Used to store the actual question (or question text)
     * @var $question_text
     */
    public $question_text;
    
    /**
     * Used to store the section id of a question
     * @var $section_id
     */
    public $section_id;
        
    /**
     * Stores an array of choices
     * @var $choices
     */
    public $choices; //array of questions
    
    
    
    public $image; //array of questions

    
    /**
     * This is and inherited funtion that prints the questions to the webpage
     * @param type $html - The text, in html, that will be placed on the form
     * @return type - none 
     */
    public function toHtml($html='')
    {      
       
       $html .= "<div class = 'question'><span style='display:block; width:90%; margin-bottom: 15px'>";
       $html .= $this->question_text;
	   $html .=	"</span>";
       if ($this->image != ''){
           $html .= "<div class='question_image'><img src='".BASEURL."res/images".$this->image."'/></div>";
       }
//       $html .= "</div>";
       
       
       $choicenHtml = "";
       //$html .= "<br/>";
//       $html .= "<ul>";
       foreach($this->choices as &$choice)
       {
           $html .= $choice->toHtml($choicenHtml);
       }
//       $html .= "</ul>";
       $html .="</div>";
//       $html .= "<hr>";
       return $html;
    }
}