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
    var ble = $(this).parent().attr('name') + ' :input';
    var blere = '#' + $(this).parent().attr('id') + ' :input';
    $.ajax({
     url: $(this).parent().attr('action'),
     type: "POST",
     data: $('#' + $(this).parent().attr('id') + ' :input'),
     complete:
       function(data) {
         var boo = data.responseText;
         $('#modalContent').append('<h2 class="overlay">Status Changed</h2>');
         $('.overlay').fadeIn(1200, function() {
           $('.overlay').fadeOut(1200, function() {
             $('.overlay').remove();
//             pattern = /event\/[a-zA-Z_0-9\+\%\-]*\/facilitygroups/;
             pattern = /event\/[a-zA-Z_0-9\+\%\-]*\/facilityresource\/[a-zA-Z_0-9\+\%\-]*/;
             var theer = pattern.exec(data.responseText);
             if(data.responseText == pattern.exec(data.responseText)) {
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
//               Get the name in there somehow.
               $.post('facilityresource/FF11EC', function(data) {
                 $fgroupDialog.html(data);
                 $fgroupDialog.dialog('open');
               });
             }
             $('#modalContent').load($(this).parent().attr('action'));
           })
         })
       }
    });

    return false;
  });
   $('#modalContent').bind('dialogclose', function() {
     $('#tableContainer').load(window.location.pathname + ' .singleTable');
   });
});