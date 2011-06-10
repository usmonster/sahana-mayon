$(function() {
  $('#descText').show(); //default
});

$(function() {
  $('#import').click(function() {
    $('#descText').toggle();

    return false;
  });

});
 