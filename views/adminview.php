<?php	/** @file adminview.php
@date:  Jan 27th 2013
@author Thaker:Priyank
@brief This file is a View file for the "Admin Form View" 
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
		
		<script>
			$(function() {
					$( "#date_assign" ).datetimepicker({dateFormat: "yy-mm-dd",changeMonth: true,	changeYear: true});
					$( "#date_from" ).datetimepicker({dateFormat: "yy-mm-dd",changeMonth: true,	changeYear: true});
					$( "#date_to" ).datetimepicker({dateFormat: "yy-mm-dd",changeMonth: true,	changeYear: true});
					$( "#date_expiry" ).datetimepicker({dateFormat: "yy-mm-dd",changeMonth: true,	changeYear: true});
				});
		</script>
		
		<title>Admin PMP Forms</title>
        </head>
    <body bgcolor="#FFFFFF" >
	 <div id="form_login_header"><span>Admin PMP Forms</span><div id="logo"></div></div>
<!--        <div class="corner_logo"><img src="res/images/bell_corner.png"></div>-->
        <div id="form_wrapper" >				
        <div class="row">
        <div class ="twelve columns">                         
        <form method="post" action="AdminFormPanel" name="list_Form" id="lF" autocomplete="off" class="nice" novalidate>					
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
			<div class="row">
				<div class="four columns">
					<p class='lead' style="padding-top:30px "> Please select the form.</p>
				</div>
				<div class="seven columns offset-by-one">
					<?php if(!isset($_SESSION['error_message_date']))	{	?>
					<p class="lead" style="padding-top:30px;"><b><?php foreach($cur_form_name as $key=>$value){	echo $value;	}	?></b></p>
					<?php 	}  if(isset($_SESSION['selected_cand_name']))	{	?>
					<p class="lead" ><b><?php echo $_SESSION['selected_cand_name'];	}	?></b></p>		
				</div>	
			</div>	
			<div class="row">
				<div class="four columns">
				<div class="row">
					<select id="select01" name="form_names" >
				<option SELECTED>--Please Select Form--</option>						
				<?php 	foreach($title as $key=>$value):?>		<!--	Foreach Loop Starts	-->			
								<option value="<?php echo $value['id'];?>"><?php echo $value['title'];?></option>	
					<?php endforeach; ?>				
					</select>
				</div>			
				<div class="row">
					<div class="one columns offset-by-four ">
						<p class='lead'> OR	</p>
					</div>
				</div>				
				<div class="row">
						<p class='lead'> Please select a Candidate.</p>
				</div>				
				<div class="row">
					<select id="select03" name="cand_names01" style="width:270px">	
						<option SELECTED >Select a Candidate</option>		
						<?php foreach($cand_names_list as $key=>$value)	{			?>
								<option value="<?php echo $value['pmpid'];?>"><?php echo $value['username'];?></option>	
						<?php } ?>				
					</select>
					</div>
				<div class="row">
				<div class="date_field">
						<label for="Date_From">From:</label>	<br	/>
						<input id="date_from" class="input-text" type="text" 	
						<?php	if($date_from	!= NULL)	{	?>
							placeholder="<?php echo $date_from	?>"
						<?php } else	{	?>
						placeholder="Click to select date" 
						<?php	}	?>
						name="date_from" readonly="">
						<br	/>
						<label for="Date_To">To:</label>	<br	/>
						<input id="date_to" class="input-text" type="text" 
						<?php	if($date_from	!= NULL)	{	?>
							placeholder="<?php echo $date_to	?>"
						<?php } else	{	?>
						placeholder="Click to select date" 
						<?php	}	?>
						name="date_to" readonly="">
				</div>
				</div>				
				<div class="row">
				<div class="alert-box.warning">
					<?php if(isset($_SESSION['error_message_date']))	{?>
						<font color="red" style="border-color: #C00000; background-color: rgba(255,0,0,0.15);"><?php echo	$_SESSION['error_message_date'];	}?>	</font>
				</div>	
				</div>				
				<br	/>	<br	/>
				<div class="row">
				<div class="two columns offset-by-three">
							<input type="submit" class=" medium blue nice round button radius" value="Next" name="NEXT" >
				</div>
				</div>
				<br	/>
				<div class="row">			
				<div class="alert-box.warning">
					<?php if(isset($_SESSION['error_message_both_cat']))	{?>
						<font color="red" style="border-color: #C00000; background-color: rgba(255,0,0,0.15)"><?php echo	$_SESSION['error_message_both_cat'];	}?>	</font>
				</div>	
				</div>
				<div class="row">			
				<div class="alert-box.warning">
					<?php if(isset($_SESSION['error_message_none']))	{?>
						<font color="red" style="border-color: #C00000; background-color: rgba(255,0,0,0.15)"><?php echo	$_SESSION['error_message_none'];	}?>	</font>
				</div>	
				</div>
		</form>
				<div class="row">
				<?php  if(isset($_SESSION['form']) && (!isset($_SESSION['error_message_date'])))	{  ?>			<!-- if no form selected Or Error In Selecting Date Range div shall not be seen	-->
<!--- error may occur from here -->
				<form method="post" action="AdminFormPanel" name="add_form" id="aF" autocomplete="off" class="nice" novalidate>
					<div class="row">
						<div class="twelve columns">
						<p class="lead" style="padding-top:50px;"> Assign Form to a Candidate.</p>
						</div>
					</div>					
					<div class="row">
					<select id="select02" name="cand_names" style="width:270px">	
						<option SELECTED >Select a Candidate</option>												
						<?php foreach($cand_names_list as $key=>$value)	{			?>
								<option value="<?php echo $value['pmpid'];?>"><?php echo $value['username'];?></option>							
						<?php } ?>									
					</select>
					</div>
					<div class="row">
					<div class="alert-box.warning">
						<?php if(isset($_SESSION['error_message_cand_name']))	{?>
						<font color="red" style="border-color: #C00000; background-color: rgba(255,0,0,0.15)"><?php echo	$_SESSION['error_message_cand_name'];	}?>	</font>
					</div>	
					</div>				
					<br	/>					
					<div class="date_field candidate">
						<label for="Date">Assign Date:</label>	<br	/>
						<input id="date_assign" class="input-text" type="text" placeholder="Click to select date" name="date_assign" readonly="">
					</div>					
					<div class="row">			
					<div class="alert-box.warning">
						<?php if(isset($_SESSION['error_message_assign_date']))	{?>
						<font color="red" style="border-color: #C00000; background-color: rgba(255,0,0,0.15)"><?php echo	$_SESSION['error_message_assign_date'];	}?>	</font>
					</div>	
					</div>
					<br	/>					
					<div class="date_field candidate">
						<label for="Date_expiry">Expiry Date:</label>	<br	/>
						<input id="date_expiry" class="input-text" type="text" placeholder="Click to select date" name="date_expiry" readonly="">
					</div>					
					<div class="row">
					<div class="alert-box.warning">
						<?php if(isset($_SESSION['error_message_expiry_date']))	{?>
						<font color="red" style="border-color: #C00000; background-color: rgba(255,0,0,0.15)"><?php echo	$_SESSION['error_message_expiry_date'];	}?>	</font>
					</div>	
					</div>										
					<br	/>					
					<div class="two columns offset-by-two">
							<input type="submit" class="blue nice round button radius" value="Add Candidate" name="Add_Candidate" >
					</div>					
					<div class="row">
						<div class="error_date">
						</div>							
					</div>								
				</form>
				<?php } ?>		<!--	End of if -->				
				</div>                                
				</div>
                            <div class="eight columns">
					<table class="ten columns centered" id="cand_data" style="">	
					<?php if ($info != NULL) { ?>
						<thead>
								<tr>
 										<td style="text-align:center" rowspan="2"> <label for="pmpid"><b>PMP ID</b></label>	</td>
										<td style="text-align:center" rowspan="2"> <label for="Date_assigned"><b> DATE Assigned </b></label></td>
										<td style="text-align:center" rowspan="2"> <label for="Date_completed"><b> DATE Completed </b></label></td>
										<td style="text-align:center" rowspan="2"> <label for="Expiry_date"><b> Expiry DATE </b></label></td>
										<td style="text-align:center" rowspan="2"> <label for="Status"><b> Status </b></label></td>
										<td style="text-align:center" rowspan="2"> <label for="form_link"><b> Form Link </b></label></td>
								</tr>
						</thead>
						<tbody>
					                     <?php if ($info != NULL) {
                                                foreach ($info as $key=>$value) { ?>		
                                                    <tr>
                                                            <td style="text-align:center"> <label for="<?php echo $value['id'];?>"><b><?php echo $value['name'];?></b></label>	</td>
                                                            <td style="text-align:center"> <label for="<?php echo $value['assign_date'];?>"><b><?php echo $value['assign_date'];?></b></label>	</td>
                                                           <td style="text-align:center"> <label for="<?php echo $value['comp_date'];?>"><b><?php echo $value['comp_date'];?></b></label>	</td>
                                                            <td style="text-align:center"> <label for="<?php echo $value['expiry_date'];?>"><b><?php echo $value['expiry_date'];?></b></label>	</td>
										<td style="text-align:center"> <label for="<?php echo $value['status'];?>"><b><?php echo $value['status'];?></b></label>	</td>
										<td style="text-align:center"> <?php if(($value['status'] == "completed")|| ($value['status'] == "in progress") )	{	?>
                                                                <a href="/associatesurvey/formpanel?form_id=<?php echo $_SESSION['form'];?>&id=<?php echo $value['id'];?>&assignment_id=<?php echo $value['assignment_id'];?>" target="_blank"><b>Link</b></a>		
										<?php	}	?>	</td>
                                                    </tr>
                                                <?php }
                                        }?>									
						</tbody>
					<?php	}		else if($cand_info	!=	NULL)	{ ?>	
						<thead>
								<tr>
 										<td style="text-align:center" rowspan="2"> <label for="form_name"><b>Form Name</b></label>	</td>
										<td style="text-align:center" rowspan="2"> <label for="Date_assigned"><b> DATE Assigned </b></label></td>
										<td style="text-align:center" rowspan="2"> <label for="Date_completed"><b> DATE Completed </b></label></td>
										<td style="text-align:center" rowspan="2"> <label for="Expiry_date"><b> Expiry DATE </b></label></td>
										<td style="text-align:center" rowspan="2"> <label for="Status"><b> Status </b></label></td>
										<td style="text-align:center" rowspan="2"> <label for="form_link"><b> Form Link </b></label></td>
								</tr>
						</thead>					
						<tbody>								
								<?php foreach($cand_info as $key=>$value):?>		
									<tr>
										<td style="text-align:center"> <label for="<?php echo $value['title'];?>"><b><?php echo $value['title'];?></b></label>	</td>
										<td style="text-align:center"> <label for="<?php echo $value['assign_date'];?>"><b><?php echo $value['assign_date'];?></b></label>	</td>
										<td style="text-align:center"> <label for="<?php echo $value['comp_date'];?>"><b><?php echo $value['comp_date'];?></b></label>	</td>
										<td style="text-align:center"> <label for="<?php echo $value['expiry_date'];?>"><b><?php echo $value['expiry_date'];?></b></label>	</td>
										<td style="text-align:center"> <label for="<?php echo $value['status'];?>"><b><?php echo $value['status'];?></b></label>	</td>
										<td style="text-align:center"> <?php if(($value['status'] == "completed")|| ($value['status'] == "in progress") )	{	?>
											<a href="/associatesurvey/formpanel?form_id=<?php echo $value['form_id'];?>&id=<?php echo $value['pmpid'];?>&assignment_id=<?php echo $value['assignment_id'];?>" target="_blank"><b>Link</b></a>		
										<?php	}	?>	</td>
									</tr>
								<?php endforeach; ?>									
						</tbody>
					<?php	}		else if($cand_form_info	!=	NULL)	{ ?>	
						<thead>
								<tr>
 										<td style="text-align:center" rowspan="2"> <label for="form_name"><b>Form Name</b></label>	</td>
										<td style="text-align:center" rowspan="2"> <label for="Date_assigned"><b> DATE Assigned </b></label></td>
										<td style="text-align:center" rowspan="2"> <label for="Date_completed"><b> DATE Completed </b></label></td>
										<td style="text-align:center" rowspan="2"> <label for="Expiry_date"><b> Expiry DATE </b></label></td>
										<td style="text-align:center" rowspan="2"> <label for="Status"><b> Status </b></label></td>
										<td style="text-align:center" rowspan="2"> <label for="form_link"><b> Form Link </b></label></td>
								</tr>
						</thead>					
						<tbody>								
								<?php foreach($cand_form_info as $key=>$value):?>		
									<tr>
										<td style="text-align:center"> <label for="<?php echo $value['title'];?>"><b><?php echo $value['title'];?></b></label>	</td>
										<td style="text-align:center"> <label for="<?php echo $value['assign_date'];?>"><b><?php echo $value['assign_date'];?></b></label>	</td>
										<td style="text-align:center"> <label for="<?php echo $value['comp_date'];?>"><b><?php echo $value['comp_date'];?></b></label>	</td>
										<td style="text-align:center"> <label for="<?php echo $value['expiry_date'];?>"><b><?php echo $value['expiry_date'];?></b></label>	</td>
										<td style="text-align:center"> <label for="<?php echo $value['status'];?>"><b><?php echo $value['status'];?></b></label>	</td>
										<td style="text-align:center"> <?php if(($value['status'] == "completed")|| ($value['status'] == "in progress") )	{	?>
											<a href="/associatesurvey/formpanel?form_id=<?php echo $value['form_id'];?>&id=<?php echo $value['pmpid'];?>&assignment_id=<?php echo $value['assignment_id'];?>" target="_blank"><b>Link</b></a>		
										<?php	}	?>	</td>
									</tr>
								<?php endforeach; ?>									
						</tbody>
						<?php	}		else if(($cand_form_info	==	NULL) && ($cand_info	==	NULL) && ($info == NULL) && ((isset($_SESSION['selected_cand_name'])) || (isset($_SESSION['form']))) ) { ?>
						<thead>
								<tr>
 										<td style="text-align:center" rowspan="2"> <label for="No_forms"><b>No Forms Have Been Assigned.</b></label>	</td>
								</tr>
						</thead>									
					<?php }	?>	
					</table>					
				</div>						
			</div>				
		</div>
		</div>             
        </div>      
  </body>
</html>