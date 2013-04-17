<?php	/** @file adminview.php
@date:  Feb 22nd 2013
@author Thaker:Priyank
@brief This file is a View file for the "Candidate Form View" 
*/
?>

<?php if (!defined('BASEPATH'))
    die("No direct script access"); ?>

<!DOCTYPE HTML>
<html>
    <head>
<!--        <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.7.1/jquery.min.js"></script>-->
        <script src="<?php echo BASEURL ?>/res/js/jquery.min.js"></script> 
	        
		<link type="text/css" href="<?php echo BASEURL ?>/res/css/foundation.min.css" rel="stylesheet" />	
		<link type="text/css" href="<?php echo BASEURL ?>/res/css/app.css" rel="stylesheet" />
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
        
		<script src="<?php echo BASEURL ?>/res/js/modernizr.foundation.js"></script>
		<!-- Included JS Files (Compressed) -->
		<script src="<?php echo BASEURL ?>/res/js/jquery.js"></script>
		<script src="<?php echo BASEURL ?>/res/js/foundation.min.js"></script>
  
		<!-- Initialize JS Plugins -->
		<script src="<?php echo BASEURL ?>/res/js/app.js"></script>
		<script src="<?php echo BASEURL ?>/res/js/jquery.ui.core.js"></script>
		<script src="<?php echo BASEURL ?>/res/js/jquery.ui.datepicker.js"></script>
		<script src="<?php echo BASEURL ?>/res/js/jquery-ui-timepicker-addon.js"></script>		
		
		<!-- Reload Page every  2 mins -->
		<!--<meta http-equiv="refresh" content="120; url=<?php echo BASEURL ?>CandidateFormPanel" />	-->
		
		<title>PMP Forms</title>
        </head>
    <body bgcolor="#FFFFFF" >
	
	 <div id="form_login_header"><span>PMP Forms</span><div id="logo"></div></div>
<!--        <div class="corner_logo"><img src="res/images/bell_corner.png"></div>-->
        <div id="form_wrapper" >       
				
        <div class="row">
        <div class ="twelve columns"> 	
                        
        <form method="post" action="CandidateFormPanel" name="list_Form" id="lF" autocomplete="off" class="nice" novalidate>

						
			<div class="row">
					<div class="five columns">
					
					<div class='form_title'>
						<?php echo $_SESSION['usernamelogedin'];	?>	
					</div>
					</div>
					
					<div class="two columns offset-by-five" style="padding-top:13px">
							<input type="submit" class="green nice button radius"  value="Logout" name="LOGOUT" >
					</div>
						
			</div>
				
			<br	/>	<br	/>	
			
			<div class="row">
									
				<div class="twelve columns">
					<table class="twelve columns centered" id="cand_form_data" style="">
					<?php	if($list	!=	NULL)	{	?>
						<thead>
								<tr>
 										<td style="text-align:center; width:30%" rowspan="2"> <label for="form_name"><b>Form Name</b></label>	</td>
										<td style="text-align:center" rowspan="2"> <label for="completion_date"><b> Completion Date </b></label></td>
										<td style="text-align:center" rowspan="2"> <label for="form_status"><b> Status </b></label></td>
										<td style="text-align:center" rowspan="2"> <label for="form_link"><b> Form Link </b></label></td>
								</tr>
						</thead>
					<?php	} ?>
						<tbody>
								
								<?php foreach($list as $key=>$value):?>		
									<tr>
										<td style="text-align:center; width:30%"> <label for="<?php echo $value['title'];?>"><b><?php echo $value['title'];?></b></label>	</td>
										<td style="text-align:center"> <label for="<?php echo $value['expiry_date'];?>"><b><?php echo $value['expiry_date'];?></b></label>	</td>
										<td style="text-align:center"> <label for="<?php echo $value['form_stat'];?>"><b><?php echo $value['status'];?></b></label>	</td>
										<td style="text-align:center"> 
											<a href="/associatesurvey/formpanel?form_id=<?php echo $value['form_id'];?>&id=<?php echo $_SESSION['useridlogedin'];?>&assignment_id=<?php echo $value['assignment_id'];?>"><b>Link</b></a>		
									</tr>
								<?php endforeach; ?>									
						</tbody>
						
					</table>
					
				</div>
						
			</div>
			
		</form>		
		</div>
		</div>         
        </div>                
  </body>
</html>