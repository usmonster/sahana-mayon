/**
* This function is used to check or uncheck a series of checkboxes.
**/
$(document).ready(function(){
  // Checking the checkbox w/ id checkAll will check all boxes w/ class chekToggle
  // unchecking checkAll will uncheck all checkToggles.
  $("#checkall").live('click', function () {
    $('.checkToggle').attr('checked', this.checked);
  });
  // This unsets the check in checkAll if one of the checkToggles are unchecked.
  // or it will set the check on checkAll if all the checkToggles have been checked
  // individually.
  $(".checkToggle").live('click', function(){
    if($(".checkToggle").length == $(".checkToggle:checked").length) {
      $("#checkall").attr("checked", "checked");
    } else {
      $("#checkall").removeAttr("checked");
    }
  });
});

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
         if(data.responseText == pattern.exec(textString)) {
//         if(textString == result) {
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
           $.post('fgroup', function(data) {
             $fgroupDialog.html(data);
             $fgroupDialog.dialog('open');
           })
         }
       }
    })
    
    return false;
  });
   $('#modalContent').bind('dialogclose', function() {
     $('#tableContainer').load(window.location.pathname + ' .singleTable');
   });
});

