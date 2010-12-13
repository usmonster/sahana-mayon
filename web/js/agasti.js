/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
$(document).ready(function() {
  $('#importForm').hide();
});

$(document).ready(function() {
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

$(document).ready(function() {
  $('#fileUpload').change(function() {
    $('#show').val($('#fileUpload').val());
  })
});