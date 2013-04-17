<?php if (!defined('BASEPATH'))
    die("No direct script access"); ?>
<!DOCTYPE HTML>
<html>
    <head>
<!--        <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.7.1/jquery.min.js"></script>-->
        <script src="<?php echo BASEURL ?>/res/js/jquery.min.js"></script> 
        <link rel="stylesheet" href="<?php echo BASEURL ?>/res/css/reveal.css">        
        <link rel="stylesheet" href="<?php echo BASEURL ?>/res/css/foundation.css">    
	<script src="<?php echo BASEURL ?>/res/js/modernizr.foundation.js"></script>
        <script type="text/javascript" src="<?php echo BASEURL ?>/res/js/jquery.reveal.js"></script>
        <script type="text/javascript">
        $(document).ready(function(){
             $('#submitModal').reveal({
                    animation: 'fadeAndPop',                   //fade, fadeAndPop, none
                    animationspeed: 300,                       //how fast animtions are
                    closeonbackgroundclick: false
            });
            $(".close_submit_modal").click(function(){        
                $('#submitModal').trigger('reveal:close');
                location.href = 'login?message=1';
                //window.location = "http://thepmp.ca/"
            });
        });    
        </script>
        <title>Form View</title>
        </head>
    <body>
        <form>
        </form>
        <div id="submitModal" class="reveal-modal">
            <img src="<?php echo BASEURL ?>/res/images/PMP_Logo_smaller.png">
            <h1>Thank You</h1>
             <p class="lead">The questionnaire has been submitted successfully</p>
             <div style="text-align: center;"><a class ="close_submit_modal green radiuos nice button">OK</a></div>
        </div>

  </body>

</html>