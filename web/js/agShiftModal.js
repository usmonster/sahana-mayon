/* This function is used to launch modal windows. It is currently used by event/listgroups
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

	$('.modalTrigger').click(function() {
                $dialog.dialog("option", "title", $(this).attr('title'));
		$dialog.load($(this).attr('href'), function() {$dialog.dialog('open')});

		return false;
	});
});

$(document).ready(function() {
  $('.modalSubmit').live('click',function(){
    $('#modalContent').load($('#staffshiftform').attr('action'), $('#' + $(this).parent().attr('name') + ' :input'));
//    $('#modalContent').load($('#staffshiftform').attr('action'));
    return false;
  })
});

//$(document).ready(function() {
//  $('.modalSubmit').live('click',function(){
//    $.ajax({
//     url: $(this).parent().attr('action'),
//     dataType: 'xml',
//     type: "GET",
//     data: $('#' + $(this).parent().attr('name') + ' :input'),
//     complete:
//       function(data) {
////         $('#modalContent').load('/');
//var thing = $(data.responseText);
////         $('#modalContent').load(data.responseText);
//         $('#modalContent').load($('#staffshiftform').attr('action'));
//       }
//    })
//    return false;
//  });
////   $('div#modalContent').bind('dialogclose', function(event) {
//////     var path = window.location.pathname;
////   $('.modalReloadable').load(window.location.pathname + ' .modalReloadable > ');
////     location.reload();
////   });
//});


