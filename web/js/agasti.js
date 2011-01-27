$(function() {
  $('#importForm').hide();
});

$(function() {
  $('#import').click(function() {
    $('#importForm').toggle();
    //    $('#importForm').animate({width: 'toggle'});
    return false;
  });
//  $('#file').change(function() {
//    var str = "";
//    $("#file value").each(function() {
//       str += $(this).text() + " ";
//    });
//    $('#show').val = $('#file').val;
//  })
//  .change();
});

$(function() {
  $('#fileUpload').change(function() {
    $('#show').val($('#fileUpload').val());
  })
});