/**
* This function is used to launch modal windows. It is currently used by event/listgroups
*
* It could be extended later to provide more flexible functionality.
**/

$(document).ready(function() {
  var $dialog = $('<div id="modalContent"></div>')
    .dialog({
      autoOpen: false,
      resizable: false,
      width: 'auto',
      height: 'auto',
      draggable: false,
      modal: true
      });

  $('.modalTrigger').live('click', function() {
    $dialog.dialog("option", "title", $(this).attr('title'));
    $dialog.load($(this).attr('href'), function() {$dialog.dialog('open')});

    return false;
  });
});


$(document).ready(function() {
  $('.modalSubmit').live('click',function(){
    $.ajax({
     url: $(this).parent().attr('action'),
     type: "POST",
     data: $('#' + $(this).parent().attr('name') + ' :input'),
     complete:
       function(data) {
         $('#modalContent').append('<h2 class="overlay">Status Changed</h2>');
         $('.overlay').fadeIn(1200, function() {
           $('.overlay').fadeOut(1200, function() {
             $('.overlay').remove();
             $('#modalContent').load($(this).parent().attr('action'));
           })
         })
         pattern = /event\/[a-zA-Z_0-9\+\%\-]*\/fgroup/;
         var textString = data.responseText;
         result = pattern.exec(textString);
         var b = 5;
         if(data.responseText == "fgroup") {
           var $fgroupDialog = $('<div id="#modalFgroup"></div>')
             .dialog({
               autoOpen: false,
               resizable: false,
               width: 'auto',
               height: 'auto',
               draggable: false,
               modal: true
           });
           $fgroupDialog.dialog("option", "title", "yeah");
           $fgroupDialog.load("fgroup", function() {$fgroupDialog.dialog('open')});
         }
       }
    })
    
    return false;
  });
   $('#modalContent').bind('dialogclose', function() {
     $('#tableContainer').load(window.location.pathname + ' .singleTable');
   });
});

