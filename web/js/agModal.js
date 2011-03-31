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
function buildModal(element, title) {
  var $dialog = $(element)
  .dialog({
    title: title,
    autoOpen: false,
    resizable: false,
    width: 'auto',
    height: 'auto',
    draggable: false,
    modal: true
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
  $('.modalTrigger').live('click', function() {
    var $dialog = buildModal('<div id="modalContent"></div>', $(this).attr('title'));
    $dialog.load($(this).attr('href'), function() {$dialog.dialog('open')});
    return false;
  });
});

/**
* This unnamed function catches the click event on an element (most likely a form submit) with the
* .modalSubmit class. The complete function of the .ajax call makes a call to showFeedback, which
* handles user feedback and the loading of any other data that may be necessary.
*
* @return false  The click on .modalSubmit returns false to prevent the clicked link from retunring
*                and sending the user forward in the browser.
*
**/
$(document).ready(function() {
  $('.modalSubmit').live('click',function(){
    var $submitter = $(this);
    console.log($submitter);
    $.ajax({
      context: $submitter,
      url: $(this).parent().attr('action'),
      type: "POST",
      data: $('#' + $(this).parent().attr('id') + ' :input'),
      success:
        function (data, $submitter) {
        console.log($submitter);
          showFeedBack(data, $(this), processReturn);
        }
    });
    return false;
  });
  $('#modalContent').bind('dialogclose', function() {
    $('#tableContainer').load(window.location.pathname + ' .singleTable');
  });
});

/**
* showFeedBack is used to confirm to the user that a modal form has successfully submitted to the
* server. It simply appends a new <h2>, fades it in, then out, then drops it, and calls a callback
* function, if necessary.
*
* @param data          The data created by a call to .ajax.
* @param callBackFunc  A function to call after completion of the feedback animation.
*
* @todo  Abstract and parametize this function.
**/
function showFeedBack(data, $submitter, callBackFunc) {
  $('#modalContent').append('<h2 class="overlay">Status Changed</h2>');
  $('.overlay').fadeIn(1200, function() {
    $('.overlay').fadeOut(1200, function() {
      $('.overlay').remove();
      if($.isFunction(callBackFunc)) {
        callBackFunc.call($submitter, data);
      }
    });
  });
}

/**
* returnContent handles the data returned to the browser by an .ajax call. It is used to launch a
* second modal dialog, should the returned data dictate so.
*
* @param data  The returned data from a call to .ajax.
**/
function returnContent(data) {
    var $dialog = buildModal('<div id="#modalFgroup"></div>', 'Set Facility Resource Activation Time');
    $.post(data, function(data) {
      $dialog.html(data);
      $dialog.dialog('open');
    });
}

/**
* @todo refactor so we can pass pattern to this. JSON stuff, probably.
**/
function processReturn(data) {
  pattern = /facilityresource\/[\w\d+%-]*/
  if(data == $(this).attr('id')) {
    returnContent(data);
  }
}