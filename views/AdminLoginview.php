<?php 

/** @file adminLoginview.php
@date:  Feb 1st 2013
@author Thaker:Priyank
@brief This file is a View file for the "Admin Form View" which functions for the Login
*/
?>


<!DOCTYPE html>
<html>
    <head>  <link rel="stylesheet" type="text/css" href="<?php echo BASEURL ?>/res/css/form.css" />
        <link rel="stylesheet" href="<?php echo BASEURL ?>/res/css/foundation.css">
        <!--Added for dialog Feb 26, 2013-->
        <link rel="stylesheet" href="<?php echo BASEURL ?>/res/css/jquery-ui-1.8.21.custom.css">
        <link rel="stylesheet" href="<?php echo BASEURL ?>/res/css/dialog.css">
        
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.7.1/jquery.min.js"></script>
        <script src="<?php echo BASEURL ?>/res/js/modernizr.foundation.js"></script>
        
        <script src="<?php echo BASEURL ?>/res/js/jquery-ui-1.8.21.custom.min.js"></script>
        <script src="http://cdnjs.cloudflare.com/ajax/libs/socket.io/0.9.6/socket.io.min.js"></script>
        <script src="<?php echo BASEURL ?>/res/js/assist.js"></script>
        <!--Added for dialog Feb 26, 2013-->
        <script src="<?php echo BASEURL ?>/res/js/dialog.js"></script>
        <title>AdminLogin</title>
    </head>
    
    <?php
    if (isset($loginErr)){?>
        
                <script>
			$(document).ready(function () { 
                            showMsgDialog(myDialog,'Error','<?php echo $loginErr?>',1);
			});
		</script>
       
    <?php }?>
    
    <body>
        <div id="form_login_header"><span>Admin	PMP Forms </span><div id="logo"></div></div>
        <form method="post" autocomplete="off" url="adminlogin">
            
            <div class="row">
                                <div id ="login_layout">
                    <input type="text" name="form_username" id="username"
                                                   placeholder="Username"                            
                                                      />
                    <input type="password" name="form_password" id="password" placeholder="Password"/>
                    <input type="submit" class="large green nice button radius" value="Login"/>
                </div>
            </div>
        </form>
<!--        <div id="chat"><a href="#"><span class="label radius">Click to enable chat</span></div>-->
        <div >
		
		
		</div>
                
        
          
    </body>
</html>