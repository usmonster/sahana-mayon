/* This function is used to launch modal windows. It is currently used by event/shifts
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

/**
*  This function sumbits to the action and also reloads the content of the modal window.
*
*  Once reloaded, a partial loaded with the results of the staff search will be included in
*  the window. The loading of these contents are determined by the presence of $searchquery
*  in staffshiftSuccess.php.
*
*  The $searchquery PHP variable is built and passed to the server by the queryConstruct
*  jQuery function in the _staffshiftform.php partial.
**/
$(document).ready(function() {
  $('.modalSubmit').live('click',function(){
    $('#modalContent').load($('#staffshiftform').attr('action'), $('#' + $(this).parent().attr('name') + ' :input'));
    return false;
  })
});


