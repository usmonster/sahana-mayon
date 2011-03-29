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
     data: $('#' + $(this).parent().attr('id') + ' :input'),
     complete: function (data) { returnText(data); }
    });

    return false;
  });
   $('#modalContent').bind('dialogclose', function() {
     $('#tableContainer').load(window.location.pathname + ' .singleTable');
   });
});

function returnText(data) {
 var boo = data.responseText;
 $('#modalContent').append('<h2 class="overlay">Status Changed</h2>');
 $('.overlay').fadeIn(1200, function() {
   $('.overlay').fadeOut(1200, function() {
     $('.overlay').remove();
//             pattern = /event\/[a-zA-Z_0-9\+\%\-]*\/facilitygroups/;
//             pattern = /event\/[a-zA-Z_0-9\+\%\-]*\/facilityresource\/[a-zA-Z_0-9\+\%\-]*/;
     pattern = /facilityresource\/[\w\d+%-]*/;
     var goop = pattern.exec(data.responseText);
     if(data.responseText == pattern.exec(data.responseText)) {
       var $facResDialog = $('<div id="#modalFgroup"></div>')
         .dialog({
           autoOpen: false,
           resizable: false,
           width: 'auto',
           height: 'auto',
           draggable: false,
           modal: true
       });
       $facResDialog.dialog("option", "title", "Set Facility Resource Activation Time");
       
       $.post(data.responseText, function(data) {
         $facResDialog.html(data);
         $facResDialog.dialog('open');
       });
     }
     $('#modalContent').load($(this).parent().attr('action'));
   })
 })
}