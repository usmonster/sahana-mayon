/**
* buildModal creates a jQuery UI modal dialog window.
*
* @param  element  A DOM element, most likely a div, that will display content inside the
*                  modal window.
* @param  title    The title for the modal window.
* @return $dialog  A configured modal dialog.
*
* @todo   Add more params for greater configurability.
**/
function buildTooltip(element, obj) {
  var $dialog = $(element)
  .dialog({
    dialogClass: 'tooltipDialog',
    autoOpen: false,
    resizable: false,
    position: {
      my: 'left',
      at: 'right',
      of: obj,
      offset: "20 65"
    }
  });
  return $dialog;
}

/**
* This unnamed function catches the click of an element with .modalTrigger class. It calls
* buildModal and then loads and opens the modal dialog.
*
* @return false  Return false is used here to prevent the clicked link from returning and sending
*                the user forward in the browser.
**/
$(document).ready(function() {
  $('.tooltipTrigger').live('click', function() {
    var $dialog = buildTooltip('<div id="tooltipContent"></div>', this);
    $dialog.load($(this).attr('href'), function() {$dialog.dialog('open')});
    $(document).find('div.ui-dialog-titlebar').addClass('titleClass');
    return false;
  });
});