/** @file form.js
@date: 15th January 2013
@author: Priyank Thaker
@brief This file is used to control and validate online forms using jQuery
*/


/**
Document ready function.
Handles the overall flow of the HTML in the CandidateForms view
Plugins used: 
jquery.reveal.js
jquery.reveal.js
mobiscroll-1.6.js
foundation.js
json.min.js
*/
//'use strict';

var current = 0; // used to keep track of the current page
//the url of the server being used
var baseurl = 'http://localhost/associatesurvey/';
$(document).ready(function () {
    prompt_on_leave = 0;
    var form_id = $('[name="form_id"]').val();
    $('.formData').hide();
    $('.footer').hide('fast');
    $('.form_navigators').hide('fast');
    $('div.save-status').hide();
    var begin_time = $('#timer').val();
    var mi = 0, sec = 0;
	
  /*  //this handles the timer on each form
    var form_timer = $('#timer');
    if (form_timer.length > 0){
        form_timer.scroller({
            preset: 'time',
            theme: 'default',
   //         theme: 'android-ics', 
            display: 'inline',
            mode: 'scroller',
            readonly: true,
            timeFormat: 'ii:ss',
            timeWheels: 'iiss',
            width: 50,
            height: 20
        });
    }
    */
    //used to save every 5 minutes
	
        setInterval( function (){
            $('div.save-status').show();            
            var partial_data = 'final="0"&'+ $('form').serialize();                    
            $.ajax({
                url: "FormPanel?",
                type: "POST",
                data: partial_data
            }).done(function() {
                $('div.save-status').hide();
            });
        },300000);

    
    //does not show a modal for these two forms (Logic and Reasoning)
    if((form_id == 13) || (form_id == 14)){
        var top = $('div.form_title');
        $('html,body').animate({
            scrollTop: top.position().top
            });
        $('.formData').show();
//        var cookie_data = document.cookie.split(';');
//        var time_left
//        for (var i = 0; i < cookie_data.length; i++){
//            if(cookie_data[i].indexOf('time_left') != -1){
//                time_left = cookie_data[i].slice(cookie_data[i].indexOf('=')+1);
//            }
//        }        
//        mi = parseInt(time_left.slice(0, time_left.indexOf(':')), 10);
//        sec = parseInt(time_left.slice(time_left.indexOf(':')+1), 10);
//        form_timer.scroller('setValue', [mi, sec], false);
//        start();
        //        setTimeout(function(){
        //           
        //        }, 2000);
        
        //Modal at the begnining of every form except the logic and reasoniong after the skill test   
        $.ajax({
            url: "FormPanel?time=" + begin_time + '&fid=' + form_id,
            type: "GET",
            dataType: 'html',
            success: function (data){
                mi = parseInt(data.slice(0,data.indexOf(":")),10);
                sec = parseInt(data.slice(data.indexOf(":")+1),10);                    
                if(form_timer.attr('name') == 'timer_up'){
                    form_timer.scroller('setValue', [mi, sec], false);
                }
                else if(form_timer.attr('name') == 'timer_down'){
                    if((mi <= 0) && (sec <= 0)){
                        stop();
                        $('#blank_modal').html('The 30 minute time limit has been reached and your test has been submitted');
                        $('#blankModal').reveal({
                            animation: 'fade',                 //fade, fadeAndPop, none
                            animationspeed: 300,               //how fast animtions are
                            closeonbackgroundclick: false      //the class of a button or element that will close an open modal
                        }); 
                        var data = $('form').serialize();
                        $.ajax({
                            url: "FormPanel?",
                            type: "POST",
                            data: data
                        }).done(function() {
                            //alert('time has run out');
                            clearInterval(timer);
                            timer = false;
                            prompt_on_leave = 1;
                            setTimeout( function(){
                                $('#blankModal').trigger('reveal:close');
                                window.location = baseurl + 'CandidateFormPanel';
								window.close();
                            }, 5000);
                        });
                    }
                    else{
                        form_timer.scroller('setValue', [mi, sec], false);
                    }   
                }
                start();
            }
        });
    }
    else{
	
			//Display Form Information
				var	modal_instructions	=	'Dear Associates, this survey will allow us to better plan event logistics for program activities.'; 
					modal_instructions +=	'It will enable us to better understand your interests as well as to better manage resources for your development and training.';
					modal_instructions += 	'In completing the survey, you may skip any questions that you are uncomfortable answering.';
					modal_instructions +=	'The results of this survey will be kept strictly confidential, and will not be used for marketing purposes.';
					modal_instructions += 	' If you have any questions or concerns, please feel free to speak with any member of the Program Office. We thank you for your input.';
			
			
			modal_instructions += '<br/><br/>Please take your time to complete the questionnaire to the best of your ability';
			modal_instructions +=' Click on \'Begin\' to continue.<b>';
			
		if(form_id == 12){
            modal_instructions += '<br/><b>Please note that this section, the Pre-Interview Assessment (PIA), will be timed for 30 minutes.';
            modal_instructions +=' Click on \'Begin\' to continue.<b>';
        }
        var modal_details = '';
		
		if(  document.getElementById('user_type').value	!=	"Admin")	{
		$('p.lead').html(modal_instructions);
		$('#instructionModal').reveal({
            animation: 'fade',                 //fade, fadeAndPop, none
            animationspeed: 300,               //how fast animtions are
            closeonbackgroundclick: false      //the class of a button or element that will close an open modal
        });
		}
		else	{
				 $('.formData').show();
		}
    }
    //stop the form from submitting automatically
    var handler = function (event) {
        event.preventDefault();
    }
    $("[name='uploadForm']").bind('submit', handler);
    //-------------------------------
    
    
    $(".close_intro_modal").click(function () {
        $('#instructionModal').trigger('reveal:close');
        var top = $('div.form_title');
        $('html,body').animate({
                scrollTop: top.position().top});
        $('.formData').show();
      /*  if(begin_time.length == 0){
            var pmp_id = $('[name="pmpid"]').val();        
            //Ajax call to insert form start time
            $.ajax({
                url: "FormPanel?",
                type: "POST",
                dataType: 'application/JSON',
                data: "start_time=''&form_id=" + form_id + "&pmpid=" + pmp_id
            }).done(function(){
            
                });
            if ($('#timer').length == 1){
                stop();
                start();
            }
        }
        else{
            //make ajax call to get the current server time and initalize the offset
            $.ajax({
                url: "FormPanel?time=" + begin_time + '&fid=' + form_id,
                type: "GET",
                dataType: 'html',
                success: function (data){
                    mi = parseInt(data.slice(0,data.indexOf(":")),10);
                    sec = parseInt(data.slice(data.indexOf(":")+1),10);                    
                    if(form_timer.attr('name') == 'timer_up'){
                        form_timer.scroller('setValue', [mi, sec], false);
                    }
                    else if(form_timer.attr('name') == 'timer_down'){
                        if((mi <= 0) && (sec <= 0)){
                            stop();
                            $('#blank_modal').html('The 30 minute time limit has been reached and your test has been submitted');
                            $('#blankModal').reveal({
                                animation: 'fade',                 //fade, fadeAndPop, none
                                animationspeed: 300,               //how fast animtions are
                                closeonbackgroundclick: false      //the class of a button or element that will close an open modal
                            }); 
                            var data = $('form').serialize();
                            $.ajax({
                                url: "FormPanel?",
                                type: "POST",
                                data: data
                            }).done(function() {
                                //alert('time has run out');
                                clearInterval(timer);
                                timer = false;
                                prompt_on_leave = 1;
                                setTimeout( function(){
                                    $('#blankModal').trigger('reveal:close');
                                    window.location = baseurl + 'submit';
                                }, 5000);
                            });
                        }
                        else{
                            form_timer.scroller('setValue', [mi, sec], false);
                        }
                        
                    }
                    start();
                }
            });
            if ($('#timer').length == 1){
//                stop();
//                start();
            }
        }*/
        
        
    });
    
    $(".log-out").click(function () {
        prompt_on_leave = 1;
					
                    $('#blank_modal').html('Closing Form..');                    
                    $('#blankModal').reveal();
					window.location = baseurl + 'CandidateFormPanel';       
    });
     
	var timer = false;
    function countDown() {
        if (sec == 0) {
            sec = 59
            mi--;
        }
        else {
            sec--;
        }
        if (mi < 60) {
            form_timer.scroller('setValue', [mi, sec], false, 0.5);
        }
        //        else {
        //            clearInterval(timer);
        //            timer = false;
        //        }
        if ((mi == 0) && (sec == 0)){
            $('#blank_modal').html('The 30 minute time limit has been reached and your test has been submitted');
            $('#blankModal').reveal({
                animation: 'fade',                 //fade, fadeAndPop, none
                animationspeed: 300,               //how fast animtions are
                closeonbackgroundclick: false      //the class of a button or element that will close an open modal
            }); 
            var data = $('form').serialize();
            $.ajax({
                url: "FormPanel?",
                type: "POST",
                data: data
            }).done(function() {
                //alert('time has run out');
                clearInterval(timer);
                timer = false;
                prompt_on_leave = 1;
                setTimeout( function(){
                    $('#blankModal').trigger('reveal:close');
                    window.location = baseurl + 'CandidateFormPanel';
                    //window.close();
					//console.log('go to submit');
                }, 5000);
            });
        }
    }
    
    function time(){
        if (sec < 59) {
            sec++;
        }
        else {
            sec = 0;
            mi++;
        }
        if (mi < 60) {
            form_timer.scroller('setValue', [mi, sec], false, 0.5);
        }
        else {
            clearInterval(timer);
            timer = false;
        }
    }

   /* function start() {
        //if the test is timed down to 00:00
        if(form_timer.attr('name') == 'timer_down'){
            countDown();
        }
        //if the test is timed from 00:00
        else if (form_timer.attr('name') == 'timer_up'){
            time();
        }
        timer = setInterval(function() {
            //scroll();
            if(form_timer.attr('name') == 'timer_down'){
                countDown();
            }
            //if the test is timed from 00:00
            else if (form_timer.attr('name') == 'timer_up'){
                time();
            }
        }, 1000);
    }
    
    function stop() {
        clearInterval(timer);
        timer = false;
        if(form_timer.attr('name') == 'timer_down'){
            if(begin_time.length == 0){
                form_timer.scroller('setValue', [30, 0], false);
                mi = 30;
                sec = 0;
            }
            
        }
        else if (form_timer.attr('name') == 'timer_up'){
            if(begin_time.length == 0){
                form_timer.scroller('setValue', [0, 0], false);
                mi = 0;
                sec = 0;
            }
        }
    }
    */
    
    $("#progressbar").progressbar({
        value: 0
    });
//    if('.$formData'){
        $('.footer').show();
        $('.previous').hide();
        $('.form_navigators').show();

        $('#submit').hide();
        $('.begin').hide();
        //$('.next').show();
        //$('.formData').show();

        //Makes the date fields into date picker fields
        $(".date_field").find(':input').scroller({
            preset: 'date',
            invalid: {daysOfWeek: [0, 6], daysOfMonth: ['5/1', '12/24', '12/25']},
            theme: 'default',
            dateOrder: 'Mddyy',
            endYear: 2050,
            display: 'modal',
            mode: 'clickpick',
            width: 20
        });
        /*hide the follow up questions*/
        var followUpQues = $('[class*="If_Other"]');
        followUpQues.each(function () {
            var fUpQues = ($(this).find(':input'));
            if (fUpQues.val() === "") {
               fUpQues.closest('div').hide();
            }
            else {
               fUpQues.closest('div').show();
            }
        });
		
		/*Hide the If_Yes questions*/
		var if_yesfollowUpQues = $('[class*="If_Yes"]');
        if_yesfollowUpQues.each(function () {
            var fUpQues = ($(this).find(':input'));
            if (fUpQues.val() === "") {
               fUpQues.closest('div').hide();
            }
            else {
               fUpQues.closest('div').show();
            }
        });
        //This hides all the sections but the first and deals with if there is one section
        var section = $('.section');
        if (section.size() <= 1) {
            $('.next').hide();
            $('#submit').show();
        }
        else {
            //$('#sumbit').show();
            for (var i = 1; i<section.size(); i++){
                $('.formData').find('.section').eq(i).hide();
           } 
        }
        
        var progress_val = $("#progressbar").find('span.progress_bar');
        progress_val.html('Section '+ (current+1)+' of '+ section.length);
        
        
        var last_section = (section.size()-1);
        var current_date_field = section.eq(last_section).find('div.date_field');
        var today = $('div.form_date').html();
        current_date_field.find(':input').val(today);
        
        
        //handle numbering of questions for the IAQ
        var number_list = $('.formData').find('ul');
        var questions_to_number = number_list.find('div.ques_row');
        var num = 1;
        questions_to_number.each(function(){
            var span_to_number = $(this).find('span.ques_num');
            if($(this).find('dl').length == 0){
                span_to_number.html(num+'.');
                num ++;
            }              
        });
        
       
        
//        //This makes the time fields datepickers
//        var endTimes = section.find('.end_time');
//        var startTimes = section.find('.start_time');
//        endTimes.each(function(){
//            var current = this;
//            $(this).find(':input').scroller({
//                preset: 'time',
//                theme: 'default',
//                display: 'modal',
//                mode: 'clickpick',
//                cancelText: 'Not Available',
//                onCancel: function(){
//                      $(current).find(':input').val("N/A");
//                      $(current).find(':input').change();
//                },
//                stepMinute: 30
//            });
//            
//        });
//        startTimes.each(function(){
//            var current = this;
//            $(this).find(':input').scroller({
//                preset: 'time',
//                theme: 'default',
//                display: 'modal',
//                mode: 'clickpick',
//                cancelText: 'Not Available',
//                onCancel: function(){
//                      $(current).find(':input').val("N/A");
//                      $(current).find(':input').change();
//                },
//                stepMinute: 30
//            });
//
//        });
        
        
//        var scrollers = $('.scroller');
//        scrollers.each(function(){
//            if($(this).closest('div').hasClass('time_input')){
//                console.log($(this));
//                $(this).onChange(function()){
//                    
//                }
//            }
//        })
        
        //----------------------------------
        //handles the changes made on the start times
        var start_times = $('.start_time');
        start_times.find(':input').change(function(){
            $('.error_message').remove();
            if($(this).val() == 'N/A'){
                changeToNotAvailable($(this).closest('div.start_time'));
                var err = $(this).closest('div.start_time').find('div.time_error');
                if(err.length > 0){
                    err.fadeOut(200,function(){$(this).remove()});
                }
            }
            else if($(this).val() != ""){
                checkEndTime($(this).closest('div.start_time')); 
            }
        });

        //handles the changes made on the end times
        var end_times = $('.end_time');
        end_times.find(':input').change(function(){
            $('.error_message').remove();
            if($(this).val() == 'N/A'){
                changeToNotAvailable($(this).closest('div.end_time'));
                var err = $(this).closest('div.question').find('div.start_time');
                err = err.find('div.time_error');
                if(err.length > 0){
                    err.fadeOut(200,function(){
                        $(this).remove()
                    });
                }
            }
            else if($(this).val() != ""){
                checkStartTime($(this).closest('div.end_time')); 
            }
        });


        //Next Button
        //handles the Next Button click
        $(".next").click(function (){
            var has_error = false;
            var t_error_found = false;
            var time_error  = section.eq(current).find('div.time_error');
            var hour_error = section.eq(current).find('div.time_minimum_error');
            var errors = section.eq(current).find('div.formError')
            errors.each(function(){                
                var tm_err = $(this).hasClass('time_minimum_error');
                var t_err = $(this).hasClass('time_error'); 
                if ((tm_err) || (t_err)){}
                else{
                    $(this).remove();
                }
            });
            if((time_error.length > 0) || (hour_error.length > 0)){
                t_error_found = true;
            } 
            $(':input').closest('div').removeClass('form-field error');
            
			if(  document.getElementById('user_type').value	!=	"Admin")	{
            has_error = validateSection(section);
			}
		//	console.log(has_error, "the errors");
		//    has_error = false;
            if ((has_error) || (t_error_found)){
                //find the first error on the page and scroll to it
               var section_errors = section.eq(current).find('.formError');
                if (section_errors.length > 0){
                    $('html, body').animate({
                        scrollTop: section_errors.eq(0).closest('div.question').offset().top});
                }
                //removes the error that is placed on the screen by clicking on it
                var form_errors = section.find('div.formError');
                form_errors.each(function(){
                    $(this).click(function (){
                        $(this).fadeOut(200,function(){
                            $(this).remove();
                        });
                    });
                });                
            }
            else{
                $('.global_error').remove();
                var top = $('div.form_title');
                $('html,body').animate({
                    scrollTop: top.position().top});
                var next = current + 1;
                
                if (next == section.size()-1){
                    //$("input[type='submit']").show();
                    $('#submit').show();
                    $('#cancel').show();
                    $('.next').hide();
    
                }
                if (next != section.size()){ 
                    $('div.save-status').show();
                    section.eq(current).hide();
                    section.eq(next).show();    	
                    current = next;
                    $('.previous').show();
                    var increment = (current/section.length)*100;
                    $("#progressbar").progressbar({ 
                        value: increment
                    });
                    progress_val = $("#progressbar").find('span.progress_bar');
                    progress_val.html('Secton '+ (current+1)+' of '+section.length);
                    //make ajax call to do partial submit
                    var data = 'final="0"&'+ $('form').serialize();
                    
                    $.ajax({
                       url: "FormPanel?",
                        type: "POST",
                        data: data
                    }).done(function() {
                        $('div.save-status').hide();
                    });
                }
            }
        });
        
        //Previous Button
        $(".previous").click(function () {
            $('.global_error').remove();
            var top = $('div.form_title');
            $('html,body').animate({
                scrollTop: top.position().top});
            var previous = current -1;
            if (previous == 0){
                $(".previous").hide();
            }
            if (current != section.size()){
                //$("input[type='submit']").hide();
                $('#submit').hide();
                $('.next').show();
            }
            section.eq(current).hide();
            section.eq(previous).show();    	
            current = previous;
            var increment = (current/section.length)*100;
            $("#progressbar").progressbar({ 
                value: increment
            });
            progress_val = $("#progressbar").find('span.progress_bar');
            progress_val.html('Secton '+ (current+1)+' of '+section.length);
        });

        //handles the submit button being clicked
        $('input[type="submit"]').click(function(){
            var has_error = false;
            section.eq(current).find('div.formError').remove();
            
            $('.error_message').remove();
            $('.global_error').remove();-
            $(':input').closest('div').removeClass('form-field error');
            $('.unique_error').hide();
            has_error = validateSection(section);
            if(has_error){
                var section_errors = section.eq(current).find('.formError');
                if (section_errors.length > 0){
                    $('html, body').animate({
                        scrollTop: section_errors.eq(0).closest('div.question').offset().top});
                }
                //removes the error that is placed on the screen by clicking on it
                var form_errors = section.find('div.formError');
                form_errors.each(function(){
                    $(this).click(function (){
                        $(this).fadeOut(200,function(){
                            $(this).remove()
                        });
                    });
                });
            }
            else{
                var top = $('div.form_title');
                $('html,body').animate({
                    scrollTop: top.position().top}, 'fast', function(){
//                        $('#blank_modal').html('You will now fill out the second part of the Aptitude Test');
//                        $('#blankModal').reveal({
//                            animation: 'fade',                 //fade, fadeAndPop, none
//                            animationspeed: 300,               //how fast animtions are
//                            closeonbackgroundclick: false      //the class of a button or element that will close an open modal
//                        });
                    });
                $("#progressbar").progressbar({ 
                    value: 100
                });
                var labels = section.find('label');
                labels.each(function(){
                    if($(this).text().indexOf('Province') != -1){
                        var province = $(this).closest('div').find(':input');
                        var pr = province.val();
                        if (pr != ""){
                            province.val(pr.toUpperCase());
                        }
                    }
                });
                prompt_on_leave = 1;
                if(form_id == 12){
                    //store the time past for the aptitude test in the session
                    //document.cookie = "time_left=" + mi + ":" + sec;
                    //stop();
                    clearInterval(timer);
                    timer = false;
//                    var data = 'next=0&'+ $('form').serialize();
//                    $.ajax({
//                        url: "FormPanel?",
//                        type: "POST",
//                        data: data
//                    }).done(function() {
////                        setTimeout( function(){
//////                            $('#blankModal').trigger('reveal:close');
//////                            location.reload(true);
//                            //location.href = "formpanel?next=0";
////                        }, 1000);
//                    });
                    $("[name='uploadForm']").unbind('submit', handler);
                }
                else{
                    stop();
                    $('#blank_modal').html('Submitting..');                    
                    $('#blankModal').reveal();
                    $("[name='uploadForm']").unbind('submit', handler);					
				}  
            }
        });
        
        var dropDown = $('select');
        dropDown.each(function (){
           $(this).change(function(){
              //$(this).closest('div.question').removeClass('error_textbox');
              $(this).closest('div.question').find('div.formError').fadeOut(200,function(){$(this).remove()});
           }); 
        });
        
        
        $('.date_field').each(function(){
            $(this).change(function(){
//               if($(this).closest('div').hasClass('form-field error')){
//                   $(this).closest('div').removeClass('form-field error');
//               }
                if($(this).children().hasClass('formError')){
                    $(this).find('div.formError').fadeOut(200,function(){$(this).remove()});
                }
                
            });
        });
        
        //handles when there is a change on input text boxes
        $('input[type="text"]').each(function(){
            if($(this).closest('div').hasClass('time_input')){
                $(this).change(function(){
                    if($(this).closest('div').hasClass('form-field error')){
                       $(this).closest('div').removeClass('form-field error');
                    }
                    var question = $(this).closest('div.question');
                    var input_times = question.find('input[type="text"]');
                    var has_empty_time = false;
                    input_times.each(function(){
                        if($(this).val == "") {
                            has_empty_time = true;
                        }
                    });
                    if (!has_empty_time){
                        //calculateTotalTime($(this).closest('div.question'));
                        if (question.find('div.time_empty_error').length > 0){
                            question.find('div.time_empty_error').fadeOut(200,function(){$(this).remove()});
                        }                        
                    }
                }).change();
            }
            else{
                $(this).change(function(){
                    if($(this).closest('div').children().hasClass('formError')){
                        $(this).closest('div').find('div.formError').fadeOut(200,function(){$(this).remove()});
                    }
                });
            }            
        });
        
        $('input[type="number"]').each(function(){
            $(this).keyup(function(){
//               if($(this).closest('div').hasClass('form-field error')){
//                   $(this).closest('div').removeClass('form-field error');
                if($(this).closest('div').children().hasClass('formError')){
                    $(this).closest('div').find('div.formError').fadeOut(200,function(){$(this).remove()});
                }
//               } 
            });
        });
        
        
        $('input[type="email"]').each(function(){
            $(this).keyup(function(){
//               if($(this).closest('div').hasClass('form-field error')){
//                   $(this).closest('div').removeClass('form-field error');
                if($(this).closest('div').children().hasClass('formError')){
                    $(this).closest('div').find('div.formError').fadeOut(200,function(){$(this).remove()});
                }
//               } 
            });
        });
        
        
        $('textarea').each(function(){
            $(this).keyup(function(){
//               if($(this).closest('div').hasClass('form-field error')){
//                   $(this).closest('div').removeClass('form-field error');
//                   $(this).removeClass('error_textbox');
//               } 
                if($(this).closest('div').children().hasClass('formError')){
                    $(this).closest('div').find('div.formError').fadeOut(200,function(){$(this).remove()});
                }
            });
        });
        
        //deals with check boxes being clicked
        var checkBoxQuestions = section.find("input[type='checkbox']");
        checkBoxQuestions.each(function(){
            $(this).change(function(){
                var label = $(this).closest('label').text();
                var question = $(this).closest('div.question');
                var errorToRemove;
                if(label.indexOf("Other") != -1){                   
                    var foll_up = question.find('div.If_Other');
                    if (foll_up.length >0){
                        var is_checked = $(this).is(':checked');
                        if (is_checked){
                           foll_up.fadeIn(200,function(){$(this).show();
						   $(this).addClass('required');
						   });
						   
                        }
                        else{
                            foll_up.find(':input').val('');
                            foll_up.fadeOut(200,function(){$(this).hide()});
                            errorToRemove = question.find('div.formError');
                            if (errorToRemove.length > 0){                   
                                errorToRemove.fadeOut(200,function(){$(this).remove()});
                            }
                       }
                   }
               }
			   if(label.indexOf("Yes") != -1){                   
                    var foll_up = question.find('div.If_Yes');
                    if (foll_up.length >0){
                        var is_checked = $(this).is(':checked');
                        if (is_checked){
                           foll_up.fadeIn(200,function(){$(this).show();
						   $(this).addClass('required');
						   });
						   
                        }
                        else{
                            foll_up.find(':input').val('');
                            foll_up.fadeOut(200,function(){$(this).hide()});
                            errorToRemove = question.find('div.formError');
                            if (errorToRemove.length > 0){                   
                                errorToRemove.fadeOut(200,function(){$(this).remove()});
                            }
                       }
                   }
               }
               errorToRemove = question.find('div.formError');
               if (errorToRemove.length > 0){                   
                  errorToRemove.fadeOut(200,function(){$(this).remove()});
               }  
           });
        });
        
        //deals with Input Numbers being clicked
        var inputNumberQuestions = section.find("input[type='number']");
        inputNumberQuestions.each(function(){
            $(this).change(function(){
				var label = $(this).closest('label').text();
                var question = $(this).closest('div.question');
                var errorToRemove;
                if(label.indexOf("Other") != 0){ 
					var foll_up = question.find('div.If_Other');
                    if (foll_up.length >0){
						if(($(this).val()	!=	""))
						{
                        var is_checked = true;
						}
                        if (is_checked){
                           foll_up.fadeIn(200,function(){$(this).show();
						   $(this).addClass('required');
						   });
						   
                        }
                        else{
                            foll_up.find(':input').val('');
                            foll_up.fadeOut(200,function(){$(this).hide()});
                            errorToRemove = question.find('div.formError');
                            if (errorToRemove.length > 0){                   
                                errorToRemove.fadeOut(200,function(){$(this).remove()});
                            }
                       }
                   }
               }
		       errorToRemove = question.find('div.formError');
               if (errorToRemove.length > 0){                   
                  errorToRemove.fadeOut(200,function(){$(this).remove()});
               }  
           });
        });
        
		 //deals with Select being clicked
        var selectQuestions = section.find("select");
        selectQuestions.each(function(){
            $(this).change(function(){
				var label = $(this).closest('label').text();
                var question = $(this).closest('div.question');
                var errorToRemove;
               if(label.indexOf("1.") != 0){ 
					var foll_up = question.find('div.If_Other');
                    if (foll_up.length >0){
						if(($(this).val()	==	"Other"))
						{
                        var is_checked = true;
						}
                        if (is_checked){
                           foll_up.fadeIn(200,function(){$(this).show();
						   $(this).addClass('required');
						   });
						   
                        }
                        else{
                            foll_up.find(':input').val('');
                            foll_up.fadeOut(200,function(){$(this).hide()});
                            errorToRemove = question.find('div.formError');
                            if (errorToRemove.length > 0){                   
                                errorToRemove.fadeOut(200,function(){$(this).remove()});
                            }
                       }
                   }
               }
			   
		       errorToRemove = question.find('div.formError');
               if (errorToRemove.length > 0){                   
                  errorToRemove.fadeOut(200,function(){$(this).remove()});
               }  
           });
        });
        /*this handles the follow up questions being hidden or shown amd removing selectiong errors*/
        var radioQuestions = section.find("input[type='radio']");
        radioQuestions.each(function(){          
            $(this).change(function(){
                var errorsToRemove;
                if($(this).closest('div.radio_button').hasClass('error_position')){
                    errorsToRemove = $(this).closest('div.question').find("input[type='radio']");
                    errorsToRemove.each(function(){
                        $(this).closest('div.radio_button').removeClass('error_positions');
                        $(this).closest('div.question').find('div.radioformError').fadeOut(200,function(){$(this).remove()});
                    });
                }
                else if(($(this).closest('div.radio_pair_left').hasClass('error_position') || ($(this).closest('div.radio_pair_right').hasClass('error_position')))){
                    errorsToRemove = $(this).closest('div.question').find("input[type='radio']");
                    errorsToRemove.each(function(){
                        $(this).closest('div.radio_button').removeClass('error_position');
                        $(this).closest('div.question').find('div.radioformError').fadeOut(200,function(){$(this).remove()});
                    });
                }
                var radId = $(this).attr('name');
                var radioId = radId.substring((radId.lastIndexOf('[')+1),(radId.lastIndexOf(']')));                
                var radioOption = $(this).closest('div').find('label').text();
                var fuy = "followUp_yes";
                var fun = "followUp_no";
                var fuo = "followUp_other";
                followUpQues.each(function(){
                    var temp;
                    if($(this).find('input[type="text"]').length>0){
                        var followUpInput = $(this).find('input[type="text"]');
                        var fInputId = followUpInput.attr('name');
                        temp = fInputId.substring(0,(fInputId.lastIndexOf('[')));
                        var followUpInputId = temp.substring((temp.lastIndexOf('[')+1),(temp.lastIndexOf(']')));
                    }
                    else if($(this).find('textarea').length >0){
                        var fTextId = $(this).find('textarea').attr('name');
                        temp = fTextId.substring(0,(fTextId.lastIndexOf('[')));
                        var followUpTextId = temp.substring((temp.lastIndexOf('[')+1),(temp.lastIndexOf(']')));
                    }
                    
                    var changeOp = $(this).attr('class');
                    var changeOption = changeOp.slice(0,changeOp.indexOf(' '));
                    if((radioId == followUpTextId) || (radioId == followUpInputId)) { 
                        if((changeOption == fuy)  && (radioOption == 'Yes')){
                            $(this).fadeIn(200,function(){$(this).show()});
                        }
                        else if((changeOption == fun) && (radioOption == 'No')){
                            $(this).fadeIn(200,function(){$(this).show()});
                        }
                        else if((changeOption == fuo) && (radioOption == 'Other')){
                            $(this).fadeIn(200,function(){$(this).show()});
                        }
                        else{
                            $(this).fadeOut(200,function(){$(this).hide()});
                            $(this).find(":input").val('');
                            ($(this).find('span')).remove();
                            $(this).find(":input").removeClass('error_textbox');
                            if ($(this).find('div.formError').length >0){
                                $(this).find('div.formError').fadeOut(200,function(){$(this).remove()});
                            }
                        }
                    }
                })
            })
        });
//    }
});

/**
Validates the current section of the form that is displayed on the screen
@return The boolean value of whether the section has an error
@param section The current section displayed on the screen
@pre The user has clicked the Next or Submit button to begin validation
@post The error status is returned
*/
function validateSection(section){
    var has_error = false;
	var contextParent = section.eq(current);
    var context = section.eq(current).find('.question div:not(".require-one-in-set"):visible');
	var contextRadioCheck	=	section.eq(current).find('.question div:(".require-one-in-set"):visible');
	//$(context).validate();
	
    //Validate text boxes, and this includes time inputs
    var inputTypeText = context.find("input[type='text']");
    if (inputTypeText.length > 0){
        var text_error = validateInputTexts(inputTypeText);
    }    
    
    //Validate text area inputs
    var inputTypeTextArea = context.find("textarea");
    if (inputTypeTextArea.length > 0){
        var text_area_error = validateTextAreas(inputTypeTextArea);
    }  
  
    //Validate phone number inputs
    var phoneInput = contextParent.find(".Phone");
	if (phoneInput.length > 0){
        var phone_error = validatePhoneNumbers(phoneInput);
    }
	
	//Validate Email
	var inputTypeEmail = contextParent.find(".Email");
    if (inputTypeEmail.length > 0){
        var email_error = validateEmailInputs(inputTypeEmail.find('input'));
    }
    	
	//Validate drop downs
    //var dropDown = section.eq(current).find('select').closest('div.question');
	var dropDown = context.find("select");
    var drop_down_error;
    drop_down_error = validateDropDowns(dropDown);

	//Validatate date inputs
    var inputTypeDate_div = section.eq(current).find('div.date_field');
	  var inputTypeDate = inputTypeDate_div.find("input[type='text']:visible");
//    var inputTypeDate = section.eq(current).find("input[type='date']:visible");
    if (inputTypeDate.length > 0){
        var date_error = validateDateInputs(inputTypeDate);
    }    
    

    //Validate numbers
	var inputTypeNumber = context.find("input[type='number']");
    if (inputTypeNumber.length > 0){
        var number_error =  validateNumbers(inputTypeNumber);
    }
    

    //Validate radio buttons
	var inputTypeRadio = contextRadioCheck.find("input[type='radio']");
    var radioChecked = contextRadioCheck.find("input[type='radio']:checked");
    if (inputTypeRadio.length > 0){
        var radio_error =  validateRadios(inputTypeRadio,radioChecked);
    } 
    
	//Validate check boxes
    var inputTypeCheckbox = contextRadioCheck.find("input[type='checkbox']");
    var radioChecked = contextRadioCheck.find("input[type='checkbox']:checked");
    if (inputTypeCheckbox.length > 0){
        var check_box_error =  validateCheckBoxes(inputTypeCheckbox);
    } 
	
        //Validate signature
    var inputTypeSignature = contextParent.find(".sigPad");
    if (inputTypeSignature.length>0)
        {
            var signature_error =  validateSigPad(inputTypeSignature);
        }
        
    //return error
    if(text_error){
        return text_error;
    }
    else   if(text_area_error){
        return text_area_error;
    }
    else if(text_area_error){
        return text_area_error;
    }
    else if(phone_error){
        return phone_error;
    }
    else if(drop_down_error){
        return drop_down_error;
    }
 //   else if(date_error){
   //     return date_error;
  //  }
    else if(email_error){
        return email_error;
    }
    else if(number_error){
        return number_error;
    }

    else if(radio_error){
        return radio_error;
    }
    else if(check_box_error){
        return check_box_error;
    }
    else if (signature_error) {
        return signature_error;
    }
   else{
        return has_error;
    }
}

/**
Validates all text inputs including time inputs in the current section.
@return has_error Boolean of the error status of all input text types of the current section.
@param text_inputs Array of all the input text types in the current section.
@pre User has clicked on the Next or Submit button to beging the validation.
@post Returns the errror status of all the input text types in the current section.
*/
function validateInputTexts(text_inputs){
    var has_error = false;
    var empty_error = false;
    var no_white_space = /^\s+$/;
	var valid_numbers	=	/^([0-9 ]*)$/;	//	For Checking No_Letters
	var valid_letters	=	/^([a-zA-Z .]*)$/;	// 	For Checking No_Numbers
	var valid_pcode = /(^s*([a-z](\s)?\d(\s)?){3}$)s*/i;	//For Checking Postal Code
	
	       
    var empty_error_places = {};
    text_inputs.each(function(){
	
		 //Field not Null
		if (($(this).val()) != "" ){
			
			//Validate Input Only Consists of Numbers
			var validNumber;
            validNumber= valid_numbers.test($(this).val());
            if (!(validNumber)){
		    if ($(this).closest('div').hasClass('No_Letters')){
				$(this).closest('div').append('<div class="No_LettersformError formError redPopup" style="position: relative; float:left; top: 0px; left: 170px; margin-top: -45px; opacity: 0.87; "><div class="formErrorContent"> *This field requires only Numbers</div></div>');
				has_error = true;
					}
				}
				
			//Validate Input Only Consists of Letters
			var validLetter;
            validLetter= valid_letters.test($(this).val());
            if (!(validLetter)){
		    if ($(this).closest('div').hasClass('No_Numbers')){
				$(this).closest('div').append('<div class="No_LNumbersformError formError redPopup" style="position: relative; float:left; top: 0px; left:  170px; margin-top: -45px; opacity: 0.87; "><div class="formErrorContent"> *This field requires only Letters</div></div>');
				has_error = true;
					}
				}				
		}			
	
        if(!($(this).closest('div').hasClass('date_field'))){
            var has_whitespaces = no_white_space.test($(this).val());
            if(!($(this).closest('div').hasClass('time_input'))){
                if ((($(this).val()) === "" || (has_whitespaces)) && ($(this).closest('div').hasClass('required'))){
                    $(this).closest('div').append('<div class="textformError formError redPopup" style="position: relative; float:left; top: 0px; left:  170px; margin-top: -40px; opacity: 0.87; "><div class="formErrorContent"> *This field is required</div></div>');
                    //$(this).closest('div').addClass('form-field error');
                    //$(this).parent().append('<span class="error_message">Enter text</span>');
                    has_error = true;
                }
                else{
                    var label = $(this).closest('div').find('label').text();
                    if(label == "Postal Code"){
                        var postal_code =  $(this).val();
                        if(!valid_pcode.test(postal_code)){
                            $(this).closest('div').append('<div class="provinceformError formError redPopup" style="position: relative; float:left; top: 0px; left:  170px; margin-top: -40px; opacity: 0.87; "><div class="formErrorContent"> *Enter a valid postal code</div></div>');
                            has_error = true;
                        }
                    
                    }
                    else if(label.indexOf('Province') != -1){
                        var province = $(this).val();
                        if (province.length  != 2){
                            $(this).closest('div').append('<div class="provinceformError formError redPopup" style="position: relative; float:left; top: 0px; left:  170px; margin-top: -40px; opacity: 0.87; "><div class="formErrorContent"> *Enter a valid province</div></div>');
                            has_error  = true;
                        }
                    }
                }
            }
            else {
                if ((($(this).val()) === "" || (has_whitespaces)) && ($(this).closest('div').hasClass('required'))){
                    $(this).closest('div').addClass('form-field error');
                    var ques_id = $(this).attr('name');
                    ques_id = ques_id.substring(ques_id.indexOf('[')+1, ques_id.lastIndexOf(']'));
                    ques_id = ques_id.substring(ques_id.indexOf('[')+1, ques_id.lastIndexOf(']'));
                
                    //$(this).closest('div.start_time').append('<span class="error_message">Put in a time</span>');
                    //$(this).closest('div.end_time').append('<span class="error_message">Put in a time</span>');
                    empty_error = true;
                    empty_error_places[ques_id] = $(this).closest('div.question');
                }
            }
        }        
    });
	
    if (empty_error){
        has_error = empty_error;
        var ep;
        for( ep in empty_error_places){
            $(empty_error_places[ep]).append('<div class="time_empty_error formError redPopup" style="position: relative; float:left; top: 0px; left: 300px; margin-top: -70px; opacity: 0.87; "><div class="formErrorContent"> *All time fields are required</div></div>');
        }
    }
    return has_error;
}

/**
Validates all text area inputs in the current section.
@return has_error Boolean of the error status of all text area types of the current section.
@param text_area_inputs Array of all the text area types in the current section.
@pre User has clicked on the Next or Submit button to beging the validation.
@post Returns the errror status of all the text area types in the current section.
*/
function validateTextAreas(text_area_inputs){
    var has_error = false;
    var no_white_space = /^\s+$/;
    text_area_inputs.each(function(){
        var has_whitespaces = no_white_space.test($(this).val());
        if ((($(this).val()) === "" || (has_whitespaces)) && ($(this).closest('div').hasClass('required'))){
            $(this).closest('div').append('<div class="textareaformError formError redPopup" style="position: relative; float:left; top: 0px; left: 170px; margin-top: -45px; opacity: 0.87; "><div class="formErrorContent"> *This field is required</div></div>');
            //$(this).addClass('error_textbox');
            //$(this).closest('div.input_text_area').addClass('form-field error');
            has_error = true;
        }
    });
    return has_error;
}

/**
Validates input text boxes being used for phone numbers in the current section.
@return has_error Boolean of the error status of all phone number types of the current section.
@param phone_inputs Array of all the phone input types in the current section.
@pre User has clicked on the Next or Submit button to beging the validation.
@post Returns the errror status of all the phone number types in the current section.
*/
function validatePhoneNumbers(phone_inputs){
    var has_error = false;
    var valid_phone_number = /^\(?([0-9]{3})\)?[-. ]?([0-9]{3})[-. ]?([0-9]{4})$/;
    var no_white_space = /^\s+$/;
    var input;
    phone_inputs.each(function(){
        input  = $(this).find(':input').val()
        var has_whitespaces = no_white_space.test(input);
        if (((input) != "") && (!has_whitespaces)){
            var validPhoneNumber = valid_phone_number.test($(this).find(':input').val());
            if(validPhoneNumber){
                var formattedPhoneNumber = $(this).find(':input').val().replace(valid_phone_number, "($1) $2-$3");
                $(this).find(':input').val(formattedPhoneNumber);
            }
            else{ 
                $(this).closest('div').append('<div class="phoneNumFormError formError redPopup" style="position: relative; float:left; top: 0px; left:  170px; margin-top: -45px; opacity: 0.87; "><div class="formErrorContent"> *Enter a valid phone number</div></div>');
                has_error = true;
            }
        }
    });
    return has_error
}

/**
Validates all drop down inputs in the current section.
@return has_error Boolean of the error status of all input text types of the current section.
@param drop_down_inputs Array of all the drop downs in the current section.
@pre User has clicked on the Next or Submit button to beging the validation.
@post Rturns the errror status of all the drop down types in the current section.
*/
function validateDropDowns(drop_down_ques){
    var has_error = false;
    var number_type = $(this).closest('div');
	
    drop_down_ques.each(function(){
        var selected = $(this).find('option:selected').text();

	if($(this).closest('div').hasClass('required')){
		
        if (selected.indexOf('Select') != -1){
            var error_shown = $(this).closest('div.question').find('div.formError');
            if(error_shown.length == 0){
                var s_time = $(this).find('div.start_time');
                var e_time = $(this).find('div.end_time');
                if((s_time.length > 0) || (e_time.length > 0)){
                    $(this).append('<div class="time_empty_error formError redPopup" style="position: relative; float:left; top: 0px; left: 300px; margin-top: -70px; opacity: 0.87; "><div class="formErrorContent"> *All time fields are required</div></div>');
                }
                else{
                    $(this).closest('div.question').append('<div class="selectFormError formError redPopup" style="position: relative; float:left;  top: 0px; left: 340px; margin-top: -40px; opacity: 0.87; "><div class="formErrorContent"> *Make a selection</div></div>');
                }                
            }            
            has_error = true;
        }
	}	
    });
    return has_error;
}

/**
Validates all drop down inputs in the current section.
@return has_error Boolean of the error status of all input text types of the current section.
@param date_inpputs Array of all the date inputs in the current section.
@pre User has clicked on the Next or Submit button to beging the validation.
@post Returns the errror status of all the date input types in the current section.
*/
function validateDateInputs(date_inputs){
    var has_error = false;
    var no_white_space = /^\s+$/;
    date_inputs.each(function(){
		if($(this).closest('div').hasClass('required')){
        var has_whitespaces = no_white_space.test($(this).val());
        if (($(this).val()) === "" || (has_whitespaces)){
            $(this).closest('div.date_field').append('<div class="dateformError formError redPopup" style="position: relative; float:left; top: 0px; left: 140px; margin-top: -40px; opacity: 0.87; "><div class="formErrorContent"> *This field is required</div></div>');
            //$(this).closest('div.date_field').addClass('form-field error');
            //$(this).parent().append('<span class="error_message">Select a date</span>');
            has_error = true;
        }
		}
    });
    return has_error;
}

/**
Validates all email inputs in the current section.
@return has_error Boolean of the error status of all input text types of the current section.
@param email_inputs Array of all the email inputs in the current section.
@pre User has clicked on the Next or Submit button to beging the validation.
@post Returns the errror status of all the email input types in the current section.
*/
function validateEmailInputs(email_inputs){
    var has_error = false;
    var no_white_space = /^\s+$/;
	var valid_email	= /^[a-z0-9,!#\$%&'\*\+/=\?\^_`\{\|}~-]+(\.[a-z0-9,!#\$%&'\*\+/=\?\^_`\{\|}~-]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*\.([a-z]{2,})$/;
    //var valid_email = /^[a-zA-Z0-9.!#$%&'*+/=?^_`{|}~-]+@[a-zA-Z0-9-]+(?:\.[a-zA-Z-]+)*$/ ;
    email_inputs.each(function(){
        var has_whitespaces = no_white_space.test($(this).val());
        if ((($(this).val()) === "" || (has_whitespaces))){
		    if ($(this).closest('div').hasClass('required')){
				$(this).closest('div').append('<div class="emailformError formError redPopup" style="position: relative; float:left; top: 0px; left:  170px; margin-top: -45px; opacity: 0.87; "><div class="formErrorContent"> *This field is required</div></div>');
				has_error = true;
			}
        }
        else{
            var validEmail;
            validEmail= valid_email.test($(this).val());
            if (!(validEmail)){
//                $(this).closest('div.email_text').addClass('form-field error');
//                $(this).closest('div.email_text').append('<span class="error_message">Enter a valid email</span>');
                $(this).closest('div').append('<div class="emailformError formError redPopup" style="position: relative; float:left; top: 0px; left:  170px; margin-top: -45px; opacity: 0.87; "><div class="formErrorContent"> *Enter a valid email address</div></div>');
                has_error = true;
            }
        }
    });
    return has_error;
}


/**
Validates all number inputs in the current section.
@return has_error Boolean of the error status of all input text types of the current section.
@param number_inputs Array of all the number inputs in the current section.
@pre User has clicked on the Next or Submit button to beging the validation.
@post Returns the errror status of all number inputs in the current section.
*/
function validateNumbers(number_inputs){
    var has_error = false;
    var inputs = {};
    number_inputs.each(function(){
        //$('.unique_error').hide(); 
        var val = $(this).val();
        var isNum = jQuery.isNumeric(val);
        var number_type = $(this).closest('div');
        
		if(number_type.hasClass('oneToSixUnique')){
        
			if($(this).val() > 6){
                //$(this).closest('div').addClass('form-field error');
                number_type.append('<div class="numberFormError formError redPopup" style="position: relative; top: 0px; left: 115px; margin-top: -26px; opacity: 0.87; "><div class="formErrorContent"> *Number must be less than 7</div></div>');
                has_error = true;
            }
            else if($(this).val() < 1){
                number_type.append('<div class="numberFormError formError redPopup" style="position: relative; top: 0px; left: 115px; margin-top: -26px; opacity: 0.87; "><div class="formErrorContent"> *Number must be more than 0</div></div>');
                has_error = true;
            }
            else {
                //get the question id form the input box
                var ques_id = $(this).attr('name');
                ques_id = ques_id.substring(ques_id.indexOf('[')+1, ques_id.lastIndexOf(']'));
                ques_id = ques_id.substring(ques_id.indexOf('[')+1, ques_id.lastIndexOf(']'));
                inputs[ques_id] = $(this).closest('div.question') ;
            }
        }
		
		else if((number_type.attr('class').indexOf('oneToTen') != -1) || (number_type.attr('class').indexOf('oneToFive') != -1) )	{
          
		  //	Validate the input numbers who have a Required class
			if (($(this).closest('div').hasClass('required')))	{
											
				if(($(this).val()	==	""))	//for null
					{
						has_error = true; 
						number_type.append('<div class="numberFormError formError redPopup" style="position: relative; top: 0px; left: 170px; margin-top: -26px; opacity: 0.87; "><div class="formErrorContent"> *This field is required</div></div>');
					}
				else if (isNum == false) 		//for not a number
					{       
						has_error = true; 
						number_type.append('<div class="numberFormError formError redPopup" style="position: relative; top: 0px; left: 170px; margin-top: -26px; opacity: 0.87; "><div class="formErrorContent"> *This field only requires a Number</div></div>');
					}
				else
					{	//Validate input number in valid range
						var theMaxVal = (number_type.attr('class').indexOf('oneToTen') != -1) ? 10 : 5;
						if($(this).val() > theMaxVal)	{
							number_type.append('<div class="numberFormError formError redPopup" style="position: relative; top: 0px; left: 170px; margin-top: -26px; opacity: 0.87; "><div class="formErrorContent"> *Number must be less than or equal to ' + theMaxVal + '</div></div>');
							has_error = true;
						}
						else if($(this).val() < 1)	{
							number_type.append('<div class="numberFormError formError redPopup" style="position: relative; top: 0px; left: 170px; margin-top: -26px; opacity: 0.87; "><div class="formErrorContent"> *Number must be more than 0 and less than ' +theMaxVal +'</div></div>');
							has_error = true;
						}
						else if(number_type.attr('class').indexOf('Unique') != -1)	{
							//get the question id form the input box
           					var ques_id = $(this).attr('name');
							ques_id = ques_id.substring(ques_id.indexOf('[')+1, ques_id.lastIndexOf(']'));
							ques_id = ques_id.substring(ques_id.indexOf('[')+1, ques_id.lastIndexOf(']'));
							inputs[ques_id] = $(this).closest('div.question') ;
							//    console.log(inputs);
						}
					}
		   }	
		   else	{
				 //	Validate the input numbers who have a Required class
				if(($(this).val()	!=	""))	//not null
					{
						if (isNum == false) 	//not a number
						{                
							has_error = true; 
							number_type.append('<div class="numberFormError formError redPopup" style="position: relative; top: 0px; left: 170px; margin-top: -26px; opacity: 0.87; "><div class="formErrorContent"> *This field only requires a Number</div></div>');
						}
						else
						{
							//Validate input number in valid range
						var theMaxVal = (number_type.attr('class').indexOf('oneToTen') != -1) ? 10 : 5;
						if($(this).val() > theMaxVal){
							number_type.append('<div class="numberFormError formError redPopup" style="position: relative; top: 0px; left: 170px; margin-top: -26px; opacity: 0.87; "><div class="formErrorContent"> *Number must be less than or equal to ' + theMaxVal + '</div></div>');
							has_error = true;
							}
						else if($(this).val() < 1){
							number_type.append('<div class="numberFormError formError redPopup" style="position: relative; top: 0px; left: 170px; margin-top: -26px; opacity: 0.87; "><div class="formErrorContent"> *Number must be more than 0 and less than ' +theMaxVal +'</div></div>');
							has_error = true;
							}
						}	
					}
			}
		   }
		else if(number_type.hasClass('oneToOneHundred'))	{
				
				if($(this).val() > 100)	{
						//$(this).closest('div').addClass('form-field error');
						number_type.append('<div class="numberFormError formError redPopup" style="position: relative; top: 0px; left: 115px; margin-top: -26px; opacity: 0.87; "><div class="formErrorContent"> *Number must be less than 101</div></div>');
						has_error = true;
				}
				else if($(this).val() < 0)	{
						number_type.append('<div class="numberFormError formError redPopup" style="position: relative; top: 0px; left: 115px; margin-top: -26px; opacity: 0.87; "><div class="formErrorContent"> *Must be a positive number</div></div>');
						has_error = true;
				}          
        }       
	});
    
	if (!has_error){
        var elem;
        var isUnique;
        for (elem in inputs){
            isUnique = checkForUnique($(inputs[elem]));
            if(!isUnique){
                has_error = true;
            }
        } 
    }
    return has_error;    
}


/**
Validates all radio buttons in the current section.
@return has_error Boolean of the error status of all input text types of the current section.
@param radios Array of all radio buttons in the current section.
@param checked_radios Array of all the checked radio buttons in the current section.
@pre User has clicked on the Next or Submit button to beging the validation.
@post Returns the errror status of all radio buttons in the current section.
*/
function validateRadios(radios, checked_radios){
    var has_error = false;
    //container for rNames
    var rNames = {};
    //find all the questions that have radio buttons
    	
    radios.each(function() {
		if (($(this).closest('div').hasClass('require-one-in-set')))
	{
		var radId = $(this).attr('name');
        var radioId = radId.substring((radId.lastIndexOf('[')+1),(radId.lastIndexOf(']')));
        rNames[radioId] = $(this).closest('div.question');
    }});
    //find all the radio buttons that are checked, and delete them from rNames
    
    checked_radios.each(function() {
        var radId = $(this).attr('name');
        var radioId = radId.substring((radId.lastIndexOf('[')+1),(radId.lastIndexOf(']')));
        delete rNames[radioId];
    });
    //label the unselected radio button groups with an error warning
    var x;
		
    for (x in rNames){
        if($(rNames[x]).find("[class*='radio_pair']").length > 0){
            $(rNames[x]).find("[class*='radio']").addClass('error_position');
            $(rNames[x]).children().eq(1).append('<div class="radioformError formError redPopup" style="position: relative; float:left; top: 10px; left: -170px; margin-top: -40px; opacity: 0.87; ">\n\
                            <div class="formErrorContent"> *Please select an option</div></div>');
            has_error = true;
        }
        else{
            $(rNames[x]).find("[class*='radio']").addClass('error_position');
            var error_radios = $(rNames[x]).find("[class*='radio']");
            error_radios.eq(0).append('<div class="radioformError formError redPopup" style="position: relative; float:left; top: 10px; left: 140px; margin-top: -40px; opacity: 0.87; ">\n\
                            <div class="formErrorContent"> *Please select an option</div></div>');
            has_error = true;
        }        
    }
	
    return has_error;
}



/**
Validates all chec boxes in the current section.
@return has_error Boolean of the error status of all input text types of the current section.
@param check_boxes Array of all radio buttons in the current section.
@pre User has clicked on the Next or Submit button to beging the validation.
@post Returns the errror status of all check boxes in the current section.
*/
function validateCheckBoxes(check_boxes){
    var has_error = false;
    var check_box_ques = {};
    check_boxes.each(function(){
		
		if (($(this).closest('div').hasClass('require-one-in-set')))
	{
        var ques_id = $(this).attr('name');
        ques_id = ques_id.substring(ques_id.indexOf('[')+1, ques_id.lastIndexOf(']'));
        ques_id = ques_id.substring(ques_id.indexOf('[')+1, ques_id.lastIndexOf(']'));
        check_box_ques[ques_id] = $(this).closest('div.question'); 
    }});
    var chk;
	
	
    for (chk in check_box_ques){
        var unchecked_count = 0;
        var c_boxes = $(check_box_ques[chk]).find("input[type='checkbox']");
        c_boxes.each(function(){
            if(!($(this).is(':checked'))){
                unchecked_count++;
            } 
					
        });
        if(unchecked_count == c_boxes.length){
            $(check_box_ques[chk]).children().eq(1).append('<div class="radioError formError redPopup" style="position: relative; float:left; top: 10px; left: 160px; margin-top: -40px; opacity: 0.87; ">\n\
                            <div class="formErrorContent"> *Select one or more options</div></div>');
            has_error = true;
        }
    }
	
    return has_error;
    
}

/**
Validates all signature pads in the current section.
@return has_error Boolean of the error status of all input text types of the current section.
@pre User has clicked on the Next or Submit button to beging the validation.
@post Returns the errror status of all signature pads in the current section.
*/
function validateSigPad(inputTypeSignature){
    var has_error = false;
    //console.log(inputTypeSignature);
    inputTypeSignature.each(function(){
        //console.log($(this));
        var signature=$(this).children('div').children('input').val();
        //console.log(signature);
        if ($(this).closest('div').hasClass('required') && !signature){
            $(this).closest('div').append('<div class="signatureError formError redPopup" style="position: relative; float:left; top: -20px; left: 310px; margin-top: -45px; opacity: 0.87; "><div class="formErrorContent"> *Signature is required, please sign here.</div></div>');
            has_error=true;
        }
        else if (!signature){
            //do nothing
        }
        else try {
            JSON.parse(inflateToJsonSignature(signature));
        }
        catch (err) {
           console.log (err);
           $(this).closest('div').append('<div class="signatureError formError redPopup" style="position: relative; float:left; top: -20px; left: 310px; margin-top: -45px; opacity: 0.87; "><div class="formErrorContent"> *Invalid Signature, please sign again (Do not draw across the box).</div></div>');
           has_error=true;
        }
    });
    return has_error;
}


/**
Checks to see if a set of numbers, which are input by the user, are unique.
@return flag A boolean value indicating whether the values are unique.
@param question An HTML element which holds the question which has the set of values to check.
@pre All the number fields have input.
@post The flag containing the boolean value of the uniqueness of the values is returned.
*/
function checkForUnique(question){
     var numberValues=[];
    var flag = true;
    var input = question.find(':input');
    input.each(function(){
        if ($.inArray(this.value, numberValues) != -1){
            $(this).closest('div').append('<div class="uniqueFormError formError redPopup" style="position: relative; top: 0px; left: 115px; margin-top: -26px; opacity: 0.87; "><div class="formErrorContent"> *This number has been chosen</div></div>');
                flag = false;
        }        
        numberValues.push(this.value);	
    });
    return flag;
}

/**
Compares the the start_time parameter being passed, to its paired end time.
@return none.
@param start_time A string value containing a user selected time.
@pre start_time (and paired end time) contains a string.
@post If the paired end time is less than the start_time,an error is displayed 
to the user.
 */
function checkEndTime(start_time){
    var endT = start_time.closest('div.question').find(('[class="end_time"]'));
    var ques_id = start_time.find('select').attr('name');
    ques_id = ques_id.substring(ques_id.indexOf('[')+1, ques_id.lastIndexOf(']'));
    ques_id = ques_id.substring(ques_id.indexOf('[')+1, ques_id.lastIndexOf(']'));
    var s_time = convertStringToTime(start_time.find('select').val());
    var start_label = start_time.find('label').text();       
    var start_day = start_label.substring(0,start_label.indexOf(' '));
    endT.each(function(){         
        var end_label = $(this).find('label').text();
        var end_day = end_label.substring(0,end_label.indexOf(' '));
        if(start_day == end_day) {            
            if ($(this).find('select').val() == ""){}
            else{
                if (start_time.children().hasClass('formError')){
                    start_time.find('div.formError').remove();
                }
                var e_time = convertStringToTime($(this).find('select').val());
                if(e_time <= s_time){
                    start_time.append('<div class="time_error formError redPopup" style="position: relative; float:left; top: -80px; left: -5px; margin-top: -5px; opacity: 0.87; width:20px; height:5px; ">\n\
                        <div class="formErrorContent"> *Time Conflict</div></div>');
                } 
            }               
        }
    });
}

/**
Compares the the end_time parameter being passed, to its paired start time.
@return none.
@param end_time A string value containing a user selected time.
@pre end_time (and paired start time) contains a string.
@post If the start time that is being compared is greater than the end_time, 
an error is displayed to the user.
 */
function checkStartTime(end_time){
    var startT = end_time.closest('div.question').find('[class="start_time"]');
    if (startT.length > 0){
        var ques_id = end_time.find('select').attr('name');
        ques_id = ques_id.substring(ques_id.indexOf('[')+1, ques_id.lastIndexOf(']'));
        ques_id = ques_id.substring(ques_id.indexOf('[')+1, ques_id.lastIndexOf(']'));
        var e_time = convertStringToTime(end_time.find('select').val());
        var end_label = end_time.find('label').text();
        var end_day = end_label.substring(0,end_label.indexOf(' '));
        startT.each(function(){
            var start_label = $(this).find('label').text();
            var start_day = start_label.substring(0,start_label.indexOf(' '));
            if(end_day == start_day) {
                var s_time = convertStringToTime($(this).find('select').val());
                if ($(this).find('select').val() == ""){}
                else{
                    if ($(this).children().hasClass('formError')){
                        $(this).find('div.formError').remove();
                    }
                    if(e_time <= s_time){
                        $(this).append('<div class="time_error formError redPopup" style="position: relative; float:left; top: -80px; left: -5px; margin-top: -5px; opacity: 0.87; width:20px; height:5px; ">\n\
                            <div class="formErrorContent"> *Time Conflict</div></div>');
                    }
                }                  
            }
        });
    }  
}



function changeToNotAvailable(time_div){    
    if(time_div.hasClass('start_time')){
        var start_div = time_div;
        var start_day = start_div.find('label').text();
        start_day = start_day.substring(0, start_day.indexOf(' '));
        var end_times = start_div.siblings('.end_time');
        end_times.each(function(){
            var end_day = $(this).find('label').text();
            end_day = end_day.substring(0, end_day.indexOf(' '));
            if(start_day === end_day){
                $(this).find('select').val("N/A");
            }
        });        
    }
    else if(time_div.hasClass('end_time')){
        var end_div = time_div;
        var end_day = end_div.find('label').text();
        end_day = end_day.substring(0, end_day.indexOf(' '));
        var start_times = end_div.siblings('.start_time');
        start_times.each(function(){
            var start_day = $(this).find('label').text();
            start_day = start_day.substring(0, start_day.indexOf(' '));
            if(start_day === end_day){
                $(this).find('select').val("N/A");
            }
        });
    }
//    var startT = time_div.closest('div.question').find('[class="start_time"]');
//    if (startT.length > 0){
//        var ques_id = end_time.find(':input').attr('name');
//        ques_id = ques_id.substring(ques_id.indexOf('[')+1, ques_id.lastIndexOf(']'));
//        ques_id = ques_id.substring(ques_id.indexOf('[')+1, ques_id.lastIndexOf(']'));            
//        var e_time = convertStringToTime(end_time.find(':input').val());
//        var end_label = end_time.children().attr('name');
//        var end_day = end_label.substring(0,end_label.indexOf(' '));
//        startT.each(function(){
//            var start_label = $(this).children().attr('name');
//            var start_day = start_label.substring(0,start_label.indexOf(' '));
//            if(end_day == start_day) {
//                var s_time = convertStringToTime($(this).find(':input').val());
//                if ($(this).find(':input').val() == ""){}
//                else{
//                    if ($(this).children().hasClass('formError')){
//                        $(this).find('div.formError').remove();
//                    }
//                    if(e_time <= s_time){
//                        $(this).append('<div class="time_error formError redPopup" style="position: relative; float:left; top: -80px; left: -5px; margin-top: -5px; opacity: 0.87; width:20px; height:5px; ">\n\
//                            <div class="formErrorContent"> *Time Conflict</div></div>//');
//                    }
//                }                  
//            }
//        });
//    }  
}

/**
 *converStringToTime: converts a time in the form of a string to a Date format.
 *@param timeString: a time sent in the form of a string.
 *@return a time in the form of a Date.
 */
function convertStringToTime(timeString){
    if ((timeString != "") || (timeString.indexOf('select') != -1)){
        var actualHour;
        var am_pm = timeString.substring((timeString.indexOf(' ')+1), timeString.length);
        var hour = timeString.substring(0,2);
        var minutes = timeString.substring(3, 5);
        var timeFromString = "";
        if ((am_pm == 'PM') && (parseInt(hour,10) != 12)){
            actualHour = parseInt(hour,10) + 12;
            timeFromString = new Date(0,1,1,actualHour,minutes,0,0);
        }
        else if ((am_pm == 'AM') && (parseInt(hour,10) == 12)){
            actualHour = parseInt(hour,10) - 12
            timeFromString = new Date(0,1,1, actualHour,minutes,0,0);
        }
        else{
           actualHour = parseInt(hour,10);
            timeFromString = new Date(0,1,1, actualHour,minutes,0,0);
        }
    return timeFromString;
   }
   return"";
  
}

function calculateTotalTime(element){
    //find all inputs
    var s_time = element.find('div.start_time');
    if (s_time.length > 0){
        var min_minutes = 2400;
        var labels = element.find('label');
        var start_times = [];
        var end_times = [];
        labels.each(function(){
            var input_time = $(this).closest('div').find(':input');
            var label = $(this).attr('name');
            if (label.indexOf('Start') != -1){
               start_times.push(input_time.val());
            }
            else if(label.indexOf('End') != -1 ){
               end_times.push(input_time.val());
            }
        });
        var total_minutes = 0;
        //calculate the total minites for Mon-Fri to meet minimum of 40hrs per week
        for(var i =0; i<(start_times.length-1); i++){
            total_minutes += calculateDailyMinutes(start_times[i], end_times[i]);
        }
        if (total_minutes < min_minutes){
            if(element.find('div.formError').length == 0){
                element.append('<div class="time_minimum_error formError redPopup" style="position: relative; float:left; top: 0px; left: 300px; margin-top: -80px; "><div class="formErrorContent"> *Minimum 40 hours not met</div></div>');   
            }            
        }
        else{
            if(element.find('div.formError').length > 0){
                element.find('div.time_minimum_error').fadeOut(200,function(){$(this).remove()});
            }
        }
    }
}

function calculateDailyMinutes(s_time, e_time){
    
    var start_time = convertStringToTime(s_time);
    var end_time = convertStringToTime(e_time);
    var daily_minutes = ((end_time-start_time)/1000)/60;
    return daily_minutes;
}


//Disables right click
function clickIE4()
{
    if (event.button==2){ 
        alert(message); 
        return false; 
    } 
} 
function clickNS4(e){ 
    if (document.layers||document.getElementById&&!document.all){
        if (e.which==2||e.which==3){alert(message); 
            return false; 
        } 
    } 
} 
if (document.layers){ 
    document.captureEvents(Event.MOUSEDOWN); 
    document.onmousedown=clickNS4; 
} 
else if (document.all&&!document.getElementById){ 
    document.onmousedown=clickIE4; 
} 
//document.oncontextmenu=new Function("return false")
//$(document).keydown(function(e) {
//    var doPrevent;
//    if (e.keyCode == 8) {
//        var d = e.srcElement || e.target;
//        if (d.tagName.toUpperCase() == 'INPUT' || d.tagName.toUpperCase() == 'TEXTAREA') {
//            doPrevent = d.readOnly || d.disabled;
//        }
//        else
//            doPrevent = true;
//    }
//    else
//        doPrevent = false;
//    if (doPrevent)
//        e.preventDefault();
//});
//function onUnload () {
//   alert("reload");
//   console.log('reload');
//}

var prompt_on_leave = 0; //set dont_confirm_leave to 1 when you want the user to be able to leave withou confirmation
var leave_message = 'You are leaving the questionnaire are you sure?'
function goodbye(e) 
{
    if(prompt_on_leave!==1)
    {
        if(!e) e = window.event;
        //e.cancelBubble is supported by IE - this will kill the bubbling process.
        e.cancelBubble = true;
        e.returnValue = leave_message;
        //e.stopPropagation works in Firefox.
        if (e.stopPropagation) 
        {
            e.stopPropagation();
            e.preventDefault();
        }
        //return works for Chrome and Safari
        return leave_message;
    }
}   
window.onbeforeunload=goodbye;

// Hi try this  $('p:input[type=text]').addClass('textbox');