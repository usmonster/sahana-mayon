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

function blorg() {
  $(this).parent().load('/event/boppityboo/groupdetail/ScenA_G01');
//  $(this).parent().load($(this).parent().attr('action'));
}
$(document).ready(function() {
  $('.blorg').live('change', function() {
    //$('#fgroup').load($(this).parent().attr('action'));
    $.post($(this).parent().attr('action'), $('#' + $(this).parent().attr('id') + ' :input') ,function(data) {
       $('#fgroup').html(data);
    });
  })
});