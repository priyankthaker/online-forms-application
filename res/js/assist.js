var user = {};
$.ctrl = function(key, callback, args) {
    $(document).keydown(function(event) {
       if(!args) args=[]; // for IE
       if(event.keyCode == key.charCodeAt(0) && event.ctrlKey) {
           callback.apply(this, args);
           return false;
       }
    });
};
$.ctrl('D', function(s) {
    if($('#usermsg').prop('disabled') || (isConnected == false)){
        return false;
    }
    else{
        showChat();     
    }
    
});            
var isConnected = false;
var has_error = false;
$(document).ready(function(){
    username = $('#username').val();
//    $('#chat').click(function(){
//        showChat();
//    });
//    if(username != ''){
//        $.ajax({
//            url : 'login',
//            type : 'POST',
//            dataType : 'json',
//            data : {
//                user : ''
//            }
//        }).done(function(data){
//            //create object for socket
//            user = data;
//            //console.log(user);
//        });
//    }
    if (username !== '') {
        initSocket();
    }
//    $('#chat').click(function(){
//        showChat();
//    });
    
    
});



initSocket = function () {
socket = io.connect('http://localhost:801');
socket.on('connect', function () {
    console.log('connected');
	if (typeof username !== 'undefined') {
            socket.emit('client config', {name : username, type : 'client'});
	}
  });
  socket.on('ready', function () {
	console.log('server is ready');
	isConnected = true;

  });  
  socket.on('disconnect', function () {
    console.log("disconnected");
	isConnected = false;
	//delete this;
  });

}

function clearInput(){
//    if($('#usermsg').val() == '<Enter a message to send>'){
    if(has_error  === true){
        $('#usermsg').val('');
        $('#usermsg').css('color','black');
        $('#submitmsg').attr('disabled', false);
        has_error = false;
    }    
}




function sendMsg(){
    var name = username.substr(0, username.indexOf(' '));
    var clientmsg = $("#usermsg").val();
    //$.post("post.php", {text: clientmsg});
    if(clientmsg == ""){
        $('#submitmsg').attr('disabled', true);
        $('#usermsg').css('color','red');
        $('#usermsg').val('<Enter a message to send>');
        has_error = true;
//        setTimeout(function(){
//            $('#usermsg').animate({
//                color: '#fff'
//            }, function(){
//                $(this).val('').css('color', 'black');
//                $('#submitmsg').attr('disabled', false);                
//            },2000);
//        },2000);
    }
    else{
//        var chat_content = $('#chatbox').text();
//        if(chat_content == 'Enter a message to send'){
//            chat_content.te
//        }
//        var new_chat_content = chat_content + "\n You: " + clientmsg; 
//        //console.log(new_chat_content);
//        $('#chatbox').text(new_chat_content + '\n');
//        var user = {};
//        var room, msg;
//        user[name] = username;
//        user[room] = $('#room').text();
//        user[msg] = $('#usermsg').val();

        var onSend = socket.emit('assist', {msg: $('#usermsg').val(),room : $('#room').text()});
        $("#usermsg").val('');
        $("#usermsg").attr('disabled', 'disabled');
        setTimeout(function(){
            $("#usermsg").attr('disabled', false);
        },60000);
        
        
    }
    
//        return false;  
}
function showChat(){
    var name = username.substr(0, username.indexOf(' '));
    var chat = '';
    chat += '<div id="chat-wrapper">';  
    chat += '<div class="panel">';
    chat += '<p class="logout"><a id="exit" onClick="closeChat()">';
    chat += '<span class="label black round" style="position:relative; top:-25px; right:-30px;">X</span></a></p>';
    chat += '<p><span class="label round">Type your message below and click Send</span></p>';
    chat += '<div class="chat_input">';
    chat += '<input name="usermsg" type="text" id="usermsg" onClick="clearInput()" autocomplete="off"/>';
    chat += '</div>'; //chat_input
    chat += '<div class="chat_submit">';
    chat += '<button class="button green small round" id="submitmsg" onClick="sendMsg()">Send</button>';
    chat += '</div>'; //chat_submit
    chat += '<br/><br/>';
    chat += '</div>'; //panel
    chat += '</div>'; //chat-wrapper
    
    //    chat += '</div>';
    $('div#chat-show').html(chat);
//    $('div#chat').hide();
}

function closeChat(){
    if($('#usermsg').prop('disabled')){
        return false;
    }
    else{
        $('div#chat-show').html('')     
    }
    
//    $('div#chat').show();
}


/*
 * 

function clearInput(){
    if($('#usermsg').val() == 'Enter a message to send'){
        $('#usermsg').val('');
        $('#usermsg').css('color','black');
    }    
}

function sendMsg(){
    var name = username.substr(0, username.indexOf(' '));
    var clientmsg = $("#usermsg").val();
    //$.post("post.php", {text: clientmsg});
    if(clientmsg == ""){
        $('#usermsg').css('color','red');
        $('#usermsg').val('Enter a message to send');
        setTimeout(function(){
            $('#usermsg').animate({
                color: '#fff'
            }, function(){
                $(this).val('').css('color', 'black');
            });
        },2000);
    }
    else{
        var chat_content = $('#chatbox').text();
//        if(chat_content == 'Enter a message to send'){
//            chat_content.te
//        }
        var new_chat_content = chat_content + "\n You: " + clientmsg; 
        //console.log(new_chat_content);
        $('#chatbox').text(new_chat_content + '\n');
        $("#usermsg").val('');
        $("#usermsg").attr('disabled', 'disabled');
        setTimeout(function(){
            $("#usermsg").attr('disabled', false);
        },10000);
    }
    
//        return false;  
}


function closeChat(){
    $('div#chat-show').html('')
//    $('div#chat').show();
}

var username, socket, isConnected = false;
$.ctrl = function(key, callback, args) {
    $(document).keydown(function(event) {
       if(!args) args=[]; // for IE
       if(event.keyCode == key.charCodeAt(0) && event.ctrlKey) {
           callback.apply(this, args);
           return false;
       }
    });        
};

$.ctrl('D', function(s) {
    if(isConnected === true) {
        showChat();
//        socket.emit('assist'); 			 
//        console.log('emitting assist');
    }
});


initSocket = function () {
socket = io.connect('http://localhost:801');
socket.on('connect', function () {
    console.log('connected');
	if (typeof username !== 'undefined') {
		socket.emit('client config', {name : username, type : 'client'});
	}
  });
  socket.on('ready', function () {
	console.log('server is ready');
	isConnected = true;

  });  
  socket.on('disconnect', function () {
    console.log("disconnected");
	isConnected = false;
	//delete this;
  });

}
	

//$("#submitmsg").click(function(e){
function sendMsg(){
    var name = username.substr(0, username.indexOf(' '));
    var clientmsg = $("#usermsg").val();
    //$.post("post.php", {text: clientmsg});
    var chat_content = $('#chatbox').text();
    var new_chat_content = chat_content + "\n" + name + ": " + clientmsg; 
    console.log(new_chat_content);
    $('#chatbox').text(new_chat_content + '\n');
    $("#usermsg").val('');  
    //        return false;  
}
function showChat(){
    var name = username.substr(0, username.indexOf(' '));
    var chat = '';
    chat += '<div id="chat-wrapper">';  
    chat += '<div id="menu">';
//    chat += '<p class="welcome">Hi, ' + name + '<b></b></p>';
    chat += '<p class="logout"><a id="exit" href="#" onClick="closeChat()">';
    chat += '<span class="label red radius">Exit</span></a></p>';
    chat += '<div style="clear:both"></div>';
    chat += '</div>';

    chat += '<div id="chatbox"></div>';
    chat += '<div class="chat_input">';
    //    chat += '<div class="twelve columns">';
    //    chat += '<div class="row">';
    //    chat += '<div class="ten columns">';
    chat += '<input name="usermsg" type="text" id="usermsg"/>';
    chat += '</div>';
    chat += '<div class="chat_submit">';
    chat += '<button class="button green small nice radius" id="submitmsg" onClick="sendMsg()">Send</button>';
    chat += '</div';
    chat += '</div';
    chat += '</div>';
    //    chat += '</div>';
    //    chat += '</div>';
    $('div#chat-show').html(chat);
    $('div#chat').hide();
}

function closeChat(){
   
   $('div#chat-show').html('')
   $('div#chat').show();
}
            
            
$(document).ready(function(){
    username = $('#username').val();
	if (username !== '') {
		initSocket();
	}
    $('#chat').click(function(){
        showChat();
    });
});
 * 
 */