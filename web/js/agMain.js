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
        $(expandToggle).slideToggle('slow');
      });
    } else {
      $(expandToggle).slideToggle('slow');
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
  $('.textToForm').click(function() {
    var passId = '#' + $(this).attr('id');
    $.post($(this).attr('href'), {type: $(this).attr('name'), current: $(this).html(), id: $(this).attr('id')}, function(data) {
      $(passId).parent().html(data);
    });
    return false;
  });
});