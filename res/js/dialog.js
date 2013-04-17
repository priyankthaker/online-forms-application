/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
var myDialog;

$(document).ready(function() {
        myDialog = $('<div></div>')
                .html('???')
                .dialog({
                    autoOpen: false,
                    modal:true,
                    closeOnEscape: false,
                    open: function(event, ui) { $(".ui-dialog-titlebar-close", ui.dialog || ui).hide(); }
            });
});

/* show message dialog */
function showMsgDialog(element,title,msg,mode)
{   
        if (msg)
            element.html(msg);
        if (title)
            element.dialog('option','title',title);
        
        /*information dialog*/
        if(mode==1)
            {
                element.dialog({
                            buttons: {"OK": function() { 
                                      $(this).dialog("close");
                                      return true;
                                 }
                                 }
                            }
                        );
                element.dialog("open");
            }
        /*in progress, disable all buttons */
        else if (mode==2)
            {
                element.dialog({buttons: {  }});
                element.dialog("open");
            }
        /*close dialog*/    
        else
            element.dialog("close");
}