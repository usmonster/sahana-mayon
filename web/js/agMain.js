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
    var one = $("input.checkToggle").length;
    var two = $("input.checkToggle:checked").length
    if($("input.checkToggle").length == $("input.checkToggle:checked").length) {
      $("#checkall").attr("checked", "checked");
    } else {
      $("#checkall").removeAttr("checked");
    }
  });
});

$(document).ready(function() {
  $('.blorg').live('change', function() {
    //$('#fgroup').load($(this).parent().attr('action'));
    $.post($(this).parent().attr('action'), $('#' + $(this).parent().attr('id') + ' :input') ,function(data) {
       $('#fgroup').html(data);
    });
  })
});

$(document).ready(function() {
  $('.expander').click(function() {
    var expandToggle = '#expandable_' + $(this).attr('id');
    if(expandToggle + ':empty') {
      $(expandToggle).load($(this).attr('href'), function() {
        $(expandToggle).slideToggle();
      });
    } else {
      $(expandToggle).slideToggle();
    }
    if($(this).html() == (String.fromCharCode(9654))) {
      $(this).html('&#9660;');
    } else if($(this).html() == (String.fromCharCode(9660))) {
      $(this).html('&#9654;');
    }
    return false;
  });
});

$(document).ready(function() {
  $('.textToForm').live('click', function() {
    var passId = '#' + $(this).attr('id');
    var $poster = $(this);
    $.post($(this).attr('href'), {type: $(this).attr('name'), current: $(this).html(), id: $(this).attr('id')}, function(data) {
      $(passId).parent().append(data);
      $poster.attr('id', 'poster_' + $poster.attr('id'));
      $poster.hide();
      
    });

    return false;
  });
});

$(document).ready(function() {
  $('.submitTextToForm').live('keypress', function(evt) {
    var charCode = evt.charCode || evt.keyCode;
    if (charCode  == 13) { //Enter key's keycode
      return false;
    }
  });
});

$(document).ready(function() {
  $('.submitTextToForm').live('blur submit', function() {
    var $poster = $(this);
    // The next three lines set passText based on the input type, text or select.
    var textIn = $('#' + $(this).attr('id') + ' option:selected').text();
    var selectIn = $(this).val();
    var passText = (textIn.length == 0) ? selectIn : textIn;

    $.post($(this).parent().attr('action'), $('#' + $(this).parent().attr('id') + ' :input'), function(data) {
      if(data != 'success') {
        $poster.css('color', 'red')
        $poster.val(data);
      } else {
        var idTransfer = $poster.parent().attr('id');
        $poster.remove();
        $('#poster_' + idTransfer).html(passText);
        $('#poster_' + idTransfer).show();
        $('#poster_' + idTransfer).attr('id', idTransfer);
      }
    });
    return false;
  });
});