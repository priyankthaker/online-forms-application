<?php if (!defined('BASEPATH'))
    die("No direct script access"); ?>
<!DOCTYPE HTML>
<html>
    <head>
        <meta charset="utf-8">
<!--        <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.7.1/jquery.min.js"></script>-->
        <script src="<?php echo BASEURL ?>/res/js/jquery.min.js"></script> 
<!-- YAO : Adding signature pad starts      -->        
        <link rel="stylesheet" href="<?php echo BASEURL ?>/res/sigPad/build/jquery.signaturepad.css">
        <script src="<?php echo BASEURL ?>/res/sigPad/sigPadCompression.js"></script>
        <script src="<?php echo BASEURL ?>/res/sigPad/jquery.signaturepad.js"></script>
<!-- YAO   Adding signature pad ends     -->
        <link type="text/css" href="<?php echo BASEURL ?>/res/css/form.css" rel="stylesheet" />
        <link rel="stylesheet" href="<?php echo BASEURL ?>/res/css/reveal.css"/>
        <link rel="stylesheet" href="<?php echo BASEURL ?>/res/css/foundation.css"/>
<!--        <link type="text/css" href="<?php echo BASEURL ?>/res/css/mobiscroll-1.6.min.css" rel="stylesheet" />-->
        <link type="text/css" href="<?php echo BASEURL ?>/res/css/mobiscroll-2.0.custom.min.css" rel="stylesheet" />
        <link type="text/css" href="<?php echo BASEURL ?>/res/css/validationEngine.jquery.css" rel="stylesheet" />
        <link type="text/css" href="<?php echo BASEURL ?>/res/css/template.jquery.css" />
        <link rel="stylesheet" href="http://code.jquery.com/ui/1.9.2/themes/base/jquery-ui.css"/>

        <!--[if lt IE 9]><script src="<?php echo BASEURL ?>/application/res/css/flashcanvas.js"></script><![endif]-->
<!--        <script src="<?php echo BASEURL ?>/res/js/jquery.signaturepad.min.js"></script>-->
        <script src="<?php echo BASEURL ?>/res/js/json2.min.js"></script>  
		<script src="<?php echo BASEURL ?>/res/js/foundation.js"></script>  
        
	<!--[if lt IE 9]>
		<link rel="stylesheet" href="stylesheets/ie.css">
	<![endif]-->	
	<script src="<?php echo BASEURL ?>/res/js/modernizr.foundation.js"></script>
        <script type="text/javascript" src="<?php echo BASEURL ?>/res/js/jquery.reveal.js"></script>
<!--        <script type="text/javascript" src="<?php echo BASEURL ?>/res//js/mobiscroll-1.6.min.js"></script>-->
        <script type="text/javascript" src="<?php echo BASEURL ?>/res/js/mobiscroll-2.0.custom.min.js"></script>
        <script type="text/javascript" src="<?php echo BASEURL ?>/res/js/jquery-ui-1.8.21.custom.min.js"></script>
        <script  type="text/javascript" src="<?php echo BASEURL ?>/res/js/form.js"></script>  
        <title>Form View</title>
        </head>
		
		<?php	if($_SESSION['user']	==	"Admin")	{	?>
			<script>
			$(document).ready(function () {
				for(i=0;i<document.forms.length;i++){
					for(j=0;j<document.forms[i].elements.length;j++){
							document.forms[i].elements[j].disabled='true';
					}
				}
			});
			</script>
			<?php	}	?>	
			
    <body bgcolor="#FFFFFF" ondragstart="return false" onselectstart="return false">
<!--        <div class="corner_logo"><img src="<?php echo BASEURL ?>/res/images/bell_corner.png"></div>-->
        <div id="form_wrapper" >
            <div class="container">
                <div class="row">
                    <div class ="twelve columns">

                    <input type="hidden" value="<?php echo $_SESSION['user'];	?>" id="user_type" name="User" / >
					
        <form method="post" action="FormPanel" name="uploadForm" id="uF" autocomplete="off" class="nice" novalidate>
        <?php        
        echo $form->toHtml();
        ?>
            <div class='form_navigators'>
                <div id='previous'><a class='previous blue nice button radius'>Previous</a></div>
                <div id='next'><a class='next blue nice button radius'>Next</a></div>
            </div>
            <div id="submit"><input type="submit" name="submit" class="large green nice button radius" value="Submit"></div>
        </form>
                    </div>
                </div>
            </div>
        </div>        
        
        <div class='footer'><hr>
                BELL MOBILITY PROFESSIONAL MANAGEMENT PROGRAM<br/>
                5025 Creekbank Road, Mississauga, ON, L4W 0B6 (905) 212-1001 www.thepmp.ca<br/>
                Professional Management Program restricted document. Any distribution, replication, 
                publication, and revision of contents of this document are not allowed unless written consent
                from the program is received.
        </div>

        <div id="instructionModal" class="reveal-modal">
             <img src="<?php echo BASEURL ?>/res/images/PMP_Logo_smaller.png">
             <p class="lead"></p>
             <p id="details">Feel free to speak with your Interviewer for clarification of any questions.
                 <br/>
                 <b>Please remember to submit your questionnaire at the end.</b>
             </p>
             <br/>
             <p style="text-align: center;">
                BELL MOBILITY PROFESSIONAL MANAGEMENT PROGRAM<br/>
                5025 Creekbank Road, Mississauga, ON, L4W 0B6 (905) 212-1001 www.thepmp.ca<br/>
                Professional Management Program restricted document. Any distribution, replication, 
                publication, and revision of contents of this document are not allowed unless written consent
                from the program is received.
             </p>
             <div class="modal-buttons"><span><a class ="close_intro_modal green radius nice button">Begin</a></span><span><a class ="log-out red radius nice button">Cancel</a></span></div>
        </div>
        <div id="blankModal" class="reveal-modal">
             <p class="lead" id="blank_modal"></p>
        </div>
  </body>

</html>