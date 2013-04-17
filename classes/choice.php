<?php

namespace classes;

require_once('iPrintable.php');

/**
 * Class that contains the choices of each question
 */
class Choice implements iPrintable{
    
    /**
     * Used to store the unique id of a choice
     * @var integer $id
     */
    public $id;
    
    /**
     * Used to store the label that will be displayed of a choice
     * @var string $label
     */
    public $label;
    
    /**
     * Used to store unique order of a choice
     * @var integer $c_order
     */
    public $c_order;
    
    /**
     * Used to store unqique id of a question of each choice
     * @var integer $question_id
     */
    public $question_id;
    
    /**
     * Used to store the (correct) answer of a choice
     * @var string $answer
     */
    public $answer;
    
    /**
     * Used to store the..
     * @var string $type_id
     */
    public $type_id;
    
    /**
     * Used to store an array of validations of each choice
     * @var string array $validations
     */
    public $validations; //array of validations
    
    /**
     * Used to store an array of the responses of a choice
     * @var string array $responses
     */
    public $responses;
    
    /**
     * Used to store an array of the responses of a choice
     * @var string array $responses
     */    
    public $highlight;
    
    /**
     * This is and inherited funtion that prints the choices to the webpage
     * @param type $html - The text, in html, that will be placed on the form
     * @return type - none 
     */
    public function toHtml($html='')
    {
        //check type field to determine the input type
        //$html .= "<div class='row'>";
        $html = $this->printType($this->type_id, $this->label, $this->question_id, $this->id);
        //$typeHtml = "";
        //$html .= "</div>";
        return $html;
    }
 
    /**
     * This function determines the choice type to be placed after a question
     * @param type $type - The type of questionv
     * @param type $label - The label of the question
     * @param type $id - The type_idof the choice
     * @return string - the type as html 
     */
    private function printType($type, $label, $ques_id, $id){
        $html = "";
        $html .= "<div class='";
        
        $selected_id = false;
        $user_resp = null;
        $hasResponses = empty($this->responses);
        
        
        foreach($this->responses as $response)            
        {
            if ($response->choice_id == $this->id){
                $selected_id = true;
                $user_resp = $response;
            }
        }        
        
        foreach($this->validations as $value)
        {
            if ($value->type === 'If_Yes')
            {
                $html .= "followUp_yes ";
            }
            else if ($value->type === 'If_No')
            {
                $html .= "followUp_no ";
            }
            else if ($value->type === 'If_Other')
            {
                $html .= "followUp_other ";
            } else {
			
			    $html .= "{$value->type} ";
			}
        }
   
        
        if ($type == 0){
            $html .= "input_text'><label for='$id'>$label</label><input name='question[text][$ques_id][$id]'  type='text' id='$id' class='input-text'";
            if($selected_id == false)
                $html .= "/>"; 
            else
                $html .= " value='$user_resp->user_entry'/>";
            $html .= "</div>";
        }
		
		else if($type	==	22	){
			$html .= "If_Other'><label for='$id'>$label</label><input name='question[text][$ques_id][$id]'  type='text' id='$id' class='input-text' ";
            if($selected_id == false)
                $html .= "/>"; 
            else
                $html .= " value='$user_resp->user_entry'/>";
            $html .= "</div>";
				
		}
		else if($type	==	23	){
			$html .= "If_Yes'><label for='$id'>$label</label><input name='question[text][$ques_id][$id]'  type='text' id='$id' class='input-text'";
            if($selected_id == false)
                $html .= "/>"; 
            else
                $html .= " value='$user_resp->user_entry'/>";
            $html .= "</div>";
		
				}
        else if ($type == 1){
//            $html .= "radio_button'><label for='$id'><input name='question[radio][$ques_id]' type='radio' id='$label' value='$id'";
//            if($selected_id == false)
//                $html .= "/>";
//            else 
//                $html .= " CHECKED/>";
//            $html .= "<span class='custom radio choice_span'></span><li style='margin-left:30px;'> $label</li></label></div>";
            
            $html .= "radio_button'><input name='question[radio][$ques_id]' type='radio' id='$id' value='$id' style='vertical-align:top; margin-left: 15px; margin-right:10px;'";
            if($selected_id == false){
                $html .= "/>";
            }
            else{
                $html .= " CHECKED/>";
            }
            $html .= "<label for='$id' style='display:inline-block; width:90%;'>$label</label></div>";
        }
            
        else if ($type == 2){
            $html .= "oneToTenUnique'><label for='$id' class='form_label'>$label</label><input type='number' id='$ques_id' name='question[number][$ques_id][$id]' max = '10' min = '1'";
            if($selected_id == false)
                 $html .= " placeholder='1'>";
            else
                $html .= " value='$user_resp->user_entry'>";
            $html .= "</div>";
        }
        else if ($type == 17) {
            $html .="oneToFiveUnique'><label for='$id' class='form_label'>$label</label><input type='number' id='$ques_id' name='question[number][$ques_id][$id]' max = '5' min = '1'";
              if($selected_id == false)
                 $html .= " placeholder='1'>";
            else
                $html .= " value='$user_resp->user_entry'>";
            $html .= "</div>";
        }
        else if ($type == 3){
            $html .= "oneToTen'><label for='$id' class='form_label'>".$label."</label><input type='number' id='$id' name='question[number][$ques_id][$id]' max = '10' min = '1'";
            if($selected_id == false)
                $html .= " placeholder='1'>"; 
            else
                $html .= " value='$user_resp->user_entry'>"; 
            $html .= "</div>";
        } 
		else if ($type == 21){
            $html .= "oneToFive'><label for='$id' class='form_label'>$label</label><input type='number' id='$ques_id' name='question[number][$ques_id][$id]' max = '5' min = '1'";
            if($selected_id == false)
                 $html .= " placeholder='1'>";	
            else
                $html .= " value='$user_resp->user_entry'>";
            $html .= "</div>";
        }
        else if ($type == 4){
            $html .= "date_field'><label for='$id'>".$label."</label><input type='text' name='question[date][$ques_id][$id]' id='$id'";
            if($selected_id == false)
                $html .= " placeholder='Click to select date'";
            else
                $html .= " value='$user_resp->user_entry'";
            $html .=  " class='small input-text'></div>";
        }
        else if ($type == 5){
            $html .= "oneToOneHundred'><label for='$id' class='form_label'>".$label."</label><input type='number' name='question[number][$ques_id][$id]' id='$id' max='100' min='0'";
            if($selected_id == false)
                $html .= " placeholder='1'></div>";
            else
                $html .= " value='$user_resp->user_entry'></div>";
        }
        else if ($type == 6){
            $html .= "oneToSixUnique'><label for='$id' class='form_label'>".$label."</label><input type='number' name='question[number][$ques_id][$id]' id='$id' max = '6' min='1'";
            if($selected_id == false)
                $html .= " placeholder='1'>";
            else
                $html .= " value='$user_resp->user_entry'>";
            $html .= "</div>";
        }       
        else if  ($type == 7){
//            $html .= "start_time'><label for='$id' name='$label'>".$label."</label class='form_label'><div class='time_input'><input type='text' name='question[date][$ques_id][$id]' class='small input-text'";
//            if($selected_id == false)
//                $html .= " placeholder='Click to select time'/>";
//            else
//                $html .= " value='$user_resp->user_entry'/>";
            $start_time = "07:00";
            $end_time = "12:00";
            $html .= "start_time'>";
            $html .= "<label for='$id'>" . $label . "</label><select id='$id' name='question[text][$ques_id][$id]' style='display: block'>";
            if($selected_id == false){
                
                $html .= "<option selected>Select a time</option>";
                
                while (strtotime($start_time) <= strtotime($end_time)) {
                    $html .= "<option>" . date("h:i A", strtotime($start_time)) . "</option>";
                    $start_time = date("H:i", strtotime("$start_time +30 minutes"));
                }
                $html .= "<option value='N/A'>N/A</option>";
            }
            else{
                $html .= "<option>Select a time</option>";
                while (strtotime($start_time) <= strtotime($end_time)) {
                    $time_option = date("h:i A", strtotime($start_time));
                    if ($user_resp->user_entry == $time_option) {
                        $html .= "<option selected>".$time_option."</option>";
                    }
                    else{
                        $html .= "<option>".$time_option."</option>";
                    }
                    $start_time = date("H:i", strtotime("$start_time +30 minutes"));
                }
                if ($user_resp->user_entry == 'N/A') {
                    $html .= "<option selected>N/A</option>";
                }
                else{
                    $html .= "<option>N/A</option>";
                }
            }
            $html .= "</select></div>";

        }
        else if ($type == 8){
//           $html .= "end_time'><label for='$id' name='$label' >".$label."</label><div class='time_input'><input type='text' name='question[date][$ques_id][$id]' class='small input-text'";
//           if($selected_id == false)               
//                $html .= " placeholder='Click to select time' />";
//           else
//               $html .= " value='$user_resp->user_entry' />";
//           $html .= "</div></div>";
            $start_time = "15:00";
            $end_time = "21:00";
            $html .= "end_time'>";
            $html .= "<label for='$id'>" . $label . "</label><select id='$id' name='question[text][$ques_id][$id]' style='display: block'>";
            if($selected_id == false){
                $html .= "<option selected>Select a time</option>";                
                while (strtotime($start_time) <= strtotime($end_time)) {
                    $time_option = date("h:i A", strtotime($start_time));
                    $html .= "<option>".$time_option. "</option>";
                    $start_time = date("H:i", strtotime("$start_time +30 minutes"));
                }
                $html .= "<option>N/A</option>";
            }            
            else{
                $html .= "<option>Select a time</option>";
                while (strtotime($start_time) <= strtotime($end_time)) {
                    $time_option = date("h:i A", strtotime($start_time));
                    if ($user_resp->user_entry == $time_option) {
                        $html .= "<option selected>".$time_option."</option>";
                    }
                    else{
                        $html .= "<option>".$time_option."</option>";
                    }
                    $start_time = date("H:i", strtotime("$start_time +30 minutes"));
                }
                if ($user_resp->user_entry == 'N/A') {
                    $html .= "<option selected>N/A</option>";
                }
                else{
                    $html .= "<option>N/A</option>";
                }
                
            }
            $html .= "</select></div>";
        }
        else if($type == 9){
            $html .= "check_box'><label for='$id'><input type='checkbox' name='question[check][$ques_id][$id]' id='$id'";
            if ($selected_id == false)
                $html .= ">";
            else
                $html .= " CHECKED>";
            $html .= "<span class='custom checkbox'></span>$label</label></div>";
        }
        else if ($type == 10){
            $html .= "email_text'><label for='$id'>$label</label><input name='question[text][$ques_id][$id]' id='$id' type='email' class='input-text'";
            if($selected_id == false)
                $html .= "/></div>"; 
            else{
                $html .= " value='$user_resp->user_entry'/></div>";
            }
        }
        else if ($type == 11){
//            $html .= "radio_pair_left'><label for name='$id'><input type='radio' name='question[radio][$ques_id]' id='$label' value='$id'";
//            if($selected_id == false)
//                $html .= "/>";
//            else
//                $html .= " CHECKED/>";
//            $html .= "<span class='custom radio'></span> $label</label></div>";
            $html .= "radio_pair_left'><input name='question[radio][$ques_id]' type='radio' id='$id' value='$id' style='vertical-align:top; margin-right:10px;'";
            if ($selected_id == false) {
                $html .= "/>";
            } else {
                $html .= " CHECKED/>";
            }
            $html .= "<label for='$id' style='display:inline-block; width:90%;'>$label</label></div>";
        }
        else if ($type == 12){
//            $html .= "radio_pair_right'><label for name='$id'><input type='radio' name='question[radio][$ques_id]' id='$label' value='$id'";
//            if($selected_id == false)
//                $html .= "/>";
//            else
//                $html .= " CHECKED/>";
//            $html .= "<span class='custom radio'></span> $label</label></div>";
            $html .= "radio_pair_right'><input name='question[radio][$ques_id]' type='radio' id='$id' value='$id' style='vertical-align:top; margin-right:10px;'";
            if ($selected_id == false) {
                $html .= "/>";
            } else {
                $html .= " CHECKED/>";
            }
            $html .= "<label for='$id' style='display:inline-block; width:90%;'>$label</label></div>";
            $html .= "<hr>";
        }
        else if ($type == 24){
            $html .="readOnlyText'>$label</div>";
        }
        else if ($type == 13){
 /*               $html .= "signature'>";
                $html .= "<p class='drawItDesc'>Draw your signature</p>";
                $html.= "<ul class='sigNav'>";
                $html .= "<li class='clearButton'><a href='#clear'>Clear</a></li>";
                $html .= "</ul>";
                $html .= "<div class='sig sigWrapper'>";
                $html .= "<canvas class='pad' width='500' height='300'></canvas>";
                $html .= "<input type='hidden' name='output' class='output'>";
                $html .= "</div>";*/
                $html .= "sigPad'>";
                if($_SESSION['user']	!=	"Admin") {
                    $html .="<ul class='sigNav'>";
                    $html .="<li class='drawIt'><a href='#draw-it' >$label</a></li>";
                    $html .="<li class='clearButton'><a href='#clear'>Clear</a></li>";
                    $html .="</ul>";
                }
                $html .="<div class='sig sigWrapper'>";
                $html .="<div class='typed'></div>";
                $html .="<canvas class='pad' width='298' height='98'></canvas>"; 
                $html .= "<input type='hidden' name='question[signature][$ques_id][$id]' class='output signature'";
                /*$html .= "<p>previous user response: $user_resp->user_entry /// signature: ";
                if (empty($user_resp->signature))
                    $html.='{Empty}';
                else
                    $html .="$user_resp->signature";
                $html .="</p>";*/
                $html .="></p></div></div>";
				
				if($_SESSION['user']	!=	"Admin")	{
                if (empty($user_resp->signature))
                    $html .="<script>   $(document).ready(function() { $('.sigPad').signaturePad({drawOnly:true,validateFields : false}); /*$('.pad').attr('height',98);*/ });";
                else
                {
                    $signature=  str_replace("'","\'",$user_resp->signature);
                    $html .="<script>   $(document).ready(function() { $('.sigPad').signaturePad({drawOnly:true,validateFields : false}).regenerate('$signature');}); ";
                }
				}
				else {
				if (empty($user_resp->signature))
                    $html .="<script>   $(document).ready(function() { $('.sigPad').signaturePad({readOnly:true,validateFields : false}); /*$('.pad').attr('height',98);*/ });";
                else
                {
                    $signature=  str_replace("'","\'",$user_resp->signature);
                    $html .="<script>   $(document).ready(function() { $('.sigPad').signaturePad({readOnly:true,validateFields : false}).regenerate('$signature');}); ";
                }
				}
                //$sig = $user_resp->user_entry;
                //if ($sig);
                $html.=" </script>";
        }
        else if ($type == 14){
            $html .= "input_text_area'><label for='$id' class='form_inline_label'>$label</label><textarea id='$id' name='question[text][$ques_id][$id]' maxlength='700'";
            if($selected_id == false){
                $html .= ">";
            }
            else{
                $html .= " value='$user_resp->user_entry'>$user_resp->user_entry";
            }$html .= "</textarea></div>";
        }
        else if ($type == 15){
            $html .= "phone_number'><label for='$id'>$label</label><input type='text' id='$id' name='question[text][$ques_id][$id]'";
            if($selected_id == false){
                $html .= " placeholder='000-000-0000' class='input-text'></div>";
            }
            else{
                $html .= " value='$user_resp->user_entry'class='input-text'></div>";
            }
        }
        else if ($type == 16){
            $html .= "department'>";
            if($selected_id == false){
                $html .= $label."<select id='$label' name='question[date][$ques_id][$id]' style='display: block' class='select'>";
                $infile = @fopen(BASEURL."/res/departments.txt", "r")  or die("Couldn't open");
                if($infile){
                    $html .= "<option selected>Please Select Your Department</option>";
                    while(($buffer = fgets($infile, 4096)) !== false){
                        $html .= "<option>".$buffer."</option>";
                    }
                    fclose($infile);

                }
                $html .="</select></div>";
            }
            else{
                $html .= $label."<select id='$label' name='question[text][$ques_id][$id]' style='display: block'>";
                $infile = @fopen(BASEURL."/res/departments.txt", "r")  or die("Couldn't open");
                if($infile){
                    $html .= "<option>Please Select Your Department</option>";
                    while(($buffer = fgets($infile, 4096)) !== false){
                        if($user_resp->user_entry == trim($buffer)){
                            $html .= "<option selected>".$buffer;
                        }
                        else{
                            $html .= "<option>".$buffer."</option>";
                        }
                    }
                    fclose($infile);
                }
                $html .="</select></div>";
            }
            
        }  
		else if ($type == 18){
            $html .= "college'>";
            if($selected_id == false){
                $html .= $label."<select id='$label' name='question[date][$ques_id][$id]' style='display: block' class='select'>";
                $infile = @fopen(BASEURL."/res/Colleges.txt", "r")  or die("Couldn't open");
                if($infile){
                    $html .= "<option selected>Please Select Your School</option>";
                    while(($buffer = fgets($infile, 4096)) !== false){
                        $html .= "<option>".$buffer."</option>";
                    }
                    fclose($infile);

                }
                $html .="</select></div>";
            }
            else{
                $html .= $label."<select id='$label' name='question[text][$ques_id][$id]' style='display: block'>";
                $infile = @fopen(BASEURL."/res/Colleges.txt", "r")  or die("Couldn't open");
                if($infile){
                    $html .= "<option>Please Select Your School</option>";
                    while(($buffer = fgets($infile, 4096)) !== false){
                        if($user_resp->user_entry == trim($buffer)){
                            $html .= "<option selected>".$buffer;
                        }
                        else{
                            $html .= "<option>".$buffer."</option>";
                        }
                    }
                    fclose($infile);
                }
                $html .="</select></div>";
            }
            
        }
		else if ($type == 19){
            $html .= "language'>";
            if($selected_id == false){
                $html .= $label."<select id='$label' name='question[date][$ques_id][$id]' style='display: block' class='select'>";
                $infile = @fopen(BASEURL."/res/Languages.txt", "r")  or die("Couldn't open");
                if($infile){
                    $html .= "<option selected>Please Select A Language</option>";
                    while(($buffer = fgets($infile, 4096)) !== false){
                        $html .= "<option>".$buffer."</option>";
                    }
                    fclose($infile);

                }
                $html .="</select></div>";
            }
            else{
                $html .= $label."<select id='$label' name='question[text][$ques_id][$id]' style='display: block'>";
                $infile = @fopen(BASEURL."/res/Languages.txt", "r")  or die("Couldn't open");
                if($infile){
                    $html .= "<option>Please Select A Language</option>";
                    while(($buffer = fgets($infile, 4096)) !== false){
                        if($user_resp->user_entry == trim($buffer)){
                            $html .= "<option selected>".$buffer;
                        }
                        else{
                            $html .= "<option>".$buffer."</option>";
                        }
                    }
                    fclose($infile);
                }
                $html .="</select></div>";
            }
            
        }	
		else if ($type == 20){
            $html .= "movie'>";
            if($selected_id == false){
                $html .= $label."<select id='$label' name='question[date][$ques_id][$id]' style='display: block' class='select'>";
                $infile = @fopen(BASEURL."/res/Movies.txt", "r")  or die("Couldn't open");
                if($infile){
                    $html .= "<option selected>Please Select A Movie</option>";
                    while(($buffer = fgets($infile, 4096)) !== false){
                        $html .= "<option>".$buffer."</option>";
                    }
                    fclose($infile);

                }
                $html .="</select></div>";
            }
            else{
                $html .= $label."<select id='$label' name='question[text][$ques_id][$id]' style='display: block'>";
                $infile = @fopen(BASEURL."/res/Movies.txt", "r")  or die("Couldn't open");
                if($infile){
                    $html .= "<option>Please Select A Movie</option>";
                    while(($buffer = fgets($infile, 4096)) !== false){
                        if($user_resp->user_entry == trim($buffer)){
                            $html .= "<option selected>".$buffer;
                        }
                        else{
                            $html .= "<option>".$buffer."</option>";
                        }
                    }
                    fclose($infile);
                }
                $html .="</select></div>";
            }
            
        }
        return $html;

    }
}