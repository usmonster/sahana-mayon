// This main document ready function determines which other functions will be needed by the current
// page. Determined by the presence of relevant DOM elements.

// Start Initializer
$(document).ready(function() {
  // Used in scenario/resourcetypes
  var containerElement = $('.inlineListWrapper');
  if(containerElement.length > 0) {
    containerElement.each(function() {
      equalizeHeight($(this))
    });
  }

  // Used in scenario/fgroup
  var bucketHolder = $('.bucketHolder');
  if(bucketHolder.length > 0) {
    buildSortList();
    countSorts('.count');
    sortSlide();
  }

  // Used in scenario/staffresources
  var toggleGroup = $('.toggleGroup');
  if(toggleGroup.length > 0) {
    $('.toggleGroup').click(function(){ 
      $(this).nextAll('div:eq(1)').slideToggle("slow");
      $(this).text($(this).text() =='[-]'? '[+]' :'[-]');
    });
    $(".facgroup :input:text").each(function(){
      if( $(this).val() == "")
      {
        $(this).addClass("empty");
        $(this).focus(function(){
          $(this).removeClass("empty");
        });
        $(this).focusout(function(){
          if( $(this).val() == "")
          {
            $(this).addClass("empty");
          }
        })
      }
    });
    // Watermark stuff for scenario/staffresources
    $(":input[id$='minimum_staff']").Watermark("Min");
    $(":input[id$='maximum_staff']").Watermark("Max");

    //adding forward slash after minimum_staff  input box
    $(":input[id$='minimum_staff']").parent().parent().after("<span style='margin-left:2px; font-size:15px'>/</span>");
  // End watermark stuff
  }
  
  var shiftTemplateCounter = $('.shiftTemplateCounter');
  if(shiftTemplateCounter.length > 0) {

      var stLabels = []; //task time
      stLabels[0]=
      {
        value: -7200,
        text: '-5days'
      };
      stLabels[1]=
      {
        value: 0,
        text: '0'
      };
      stLabels[2] =
      {
        value: 7200,
        text: '+5days'
      };

      var ttLabels = []; //task time
      ttLabels[0]=
      {
        value: 0,
        text: '0hrs'
      };
      ttLabels[1]=
      {
        value: 360,
        text: '6hrs'
      };
      ttLabels[2] =
      {
        value: 720,
        text: '12hrs'
      };


  $('.addShiftTemplate').click(function() {
    var passId = '#' + $(this).attr('id');
    var $poster = $(this);
    var templates = $('.shiftTemplateCounter').length;
    $(passId).parent().prepend(addShiftTemplate(templates, $(this).attr('href')));
      var STContainers = $(".shiftTemplateCounter");
      
      var newSTContainer = STContainers[STContainers.length -1];
      $(newSTContainer).find(".timeslider").each(function(){
      
        var elementId = $(this).attr('id');
        var elementNumberIndex = elementId.search('[0-9]');
        var elementNumber = elementId.substr(elementNumberIndex);
        var elementName = elementId.substr(0,elementNumberIndex);
        //var storedElement = $(this).prev(':input:hidden');
        var storedField = $(this).prev().attr('id');
        var storedValue = $(this).prev().val();
      
        if(elementName == 'start_time'){
          addSlider($(this), elementNumber, elementName, storedValue, storedField, stLabels, 60);  
        }
        else{
          addSlider($(this), elementNumber, elementName, storedValue, storedField, ttLabels, 30);  
        }
      
      });


return false;
  });


  $('.removeShiftTemplate').live('click', function() {
    //if there is no id for this record(db_not_exists)
    var passId = '#' + $(this).attr('id');
    //send get/post to call delete
    $('#container' + $(this).attr('id').replace('removeShiftTemplate', '')).remove();
                 
    // if(!$isNewShiftTemplate):
    $('#newshifttemplates').prepend('<h2 class="overlay">'
      + removeShiftTemplate(
        $(this).attr('id'), $(this).attr('href'))
      // $shifttemplateform['id']->getValue()
      + '</h2>');
    $('.overlay').fadeIn(1200, function() {
      $('.overlay').fadeOut(1200, function() {
        $('.overlay').remove();
      });
    });
  });




      $(".timeslider").each(function(){
      
        var elementId = $(this).attr('id');
        var elementNumberIndex = elementId.search('[0-9]');
        var elementNumber = elementId.substr(elementNumberIndex);
        var elementName = elementId.substr(0,elementNumberIndex);
        //var storedElement = $(this).prev(':input:hidden');
        var storedField = $(this).prev().attr('id');
        var storedValue = $(this).prev().val();
      
        if(elementName == 'start_time'){
          addSlider($(this), elementNumber, elementName, storedValue, storedField, stLabels, 60);  
        }
        else{
          addSlider($(this), elementNumber, elementName, storedValue, storedField, ttLabels, 30);  
        }
      
      });
    //});  
  


  //    //these functions rely on variables coming from the
  //    addSlider($formNumber,'break_time',$storedBreak,'break_length_minutes',ttOptions,30);
  ////    //task time label/slider options are equivalent to break time label/slider options
  //    addSlider($formNumber,'break_time',$storedTask,'task_length_minutes',ttOptions,30);
  //    addSlider(formNumber,'start_time',$storedStart,'minutes_start_to_facility_activation',stOptions,30);

    
  }

  // Staff resource type function initialization. Used on the staff creation page.
  var staffResourceAdder = $('.addStaffResource');
  if(staffResourceAdder.length > 0) {
    $('.addStaffResource').click(function() {
      addStaffResource($(this));
      return false;
    });
  }

  var staffResourceRemover = $('.removeStaffResource');
  if(staffResourceRemover.length > 0) {
    $('.removeStaffResource').live('click', function() {
      removeStaffResource($(this));
      return false;
    })
  }
  ///////////////////////////////////////////////////////

  var dateOfBirth = $('#dob');
  if(dateOfBirth.length > 0) {
    createDatePicker();
  }

  var checkAllInit = $('#checkAll');
  if(checkAllInit.length > 0) {
    checkAll();
  }

  var checkToggleInit = $('.checkToggle');
  if(checkToggleInit.length > 0) {
    checkToggle();
  }
});
// End Initializer


/**
 * This function is used to check or uncheck a series of checkboxes.
 **/

//$(document).ready(function(){
// Checking the checkbox w/ id checkAll will check all boxes w/ class chekToggle
// unchecking checkAll will uncheck all checkToggles.

function checkAll() {
  $('#checkAll').live('click', function () {
    var check = this.checked;
    $('.checkBoxContainer').find('.checkToggle').each(function() {
      this.checked = check;
      $(this).trigger('change');
    });
  });
}
// This unsets the check in checkAll if one of the checkToggles are unchecked.
// or it will set the check on checkAll if all the checkToggles have been checked
// individually.
function checkToggle() {
  $('.checkToggle').live('change', function(){
    var a = $('.checkToggle').length;
    var b = $('.checkToggle:checked').length;
    if($('.checkToggle').length == $('.checkToggle:checked').length) {
      $('#checkAll').attr('checked', 'checked');
    } else {
      $("#checkAll").removeAttr('checked');
    }
  });
}
//});

$(document).ready(function() {
  $('.searchParams .checkToggle').live('change', function() {
    if($(this).is(':checked')) {
      $('.available .' + $(this).attr('id')).show();
    } else {
      $('.available .' + $(this).attr('id')).hide();
    }
  });
});

$(document).ready(function() {
  $('.expandAll').live('click', function() {
    $('.expander').each(function(){
      if($('#expandable_' + $(this).attr('id')).children().length == 0) {
        $(this).click();
      }
    });
    return false;
  });
});

$(document).ready(function() {
  $('.collapseAll').live('click', function() {
    $('.expander').each(function(){
      if($('#expandable_' + $(this).attr('id')).children().length != 0) {
        $(this).click();
      }
    });
    return false;
  });
});

$(document).ready(function() {
  $('.expander').click(function() {
    var expandToggle = '#expandable_' + $(this).attr('id');
    if($(expandToggle).children().length == 0) {
      $(expandToggle).load($(this).attr('href'), function() {
        $(expandToggle).slideToggle();
      });
    } else {
      $(expandToggle).slideToggle(function() {
        $(expandToggle).empty();
      });
    }
    $(this).html(pointerCheck($(this).html()));
    return false;
  });
});

$(document).ready(function() {
  $('.textToForm').live('click', function() {
    var passId = '#' + $(this).attr('id');
    var $poster = $(this);
    $.post($(this).attr('href'), {
      type: $(this).attr('name'), 
      current: $(this).html(), 
      id: $(this).attr('id')
    }, function(data) {
      $(passId).parent().append(data);
      $poster.attr('id', 'poster_' + $poster.attr('id'));
      $poster.hide();
      $(passId + ' > .submitTextToForm').focus();
    });

    return false;
  });
});

$(document).ready(function() {
  $('.includeAndAdd').live('click', function() {
    var passId = '#' + $(this).attr('id');
    var $poster = $(this);
    $.post($(this).attr('href'), {
      type: $(this).attr('name'), 
      current: $(this).html(), 
      id: $(this).attr('id')
    }, function(data) {
      $(passId).parent().append(data + '<br />' + $poster.parent().html());
      $poster.attr('id', 'poster_' + $poster.attr('id'));
      $poster.hide();
      $(passId + ' > .submitTextToForm').focus();
    });

    return false;
  });
});


// Disable submitting with enter for .submitTextToForm inputs.
$(document).ready(function() {
  $('.submitTextToForm').live('keypress', function(evt) {
    var charCode = evt.charCode || evt.keyCode;
    if (charCode  == 13) {
      return false;
    }
  });
});

$(document).ready(function() {
  $('.submitTextToForm').live('blur submit', function() {
    var $poster = $(this);

    $.post($(this).parent().attr('action'), $('#' + $(this).parent().attr('id') + ' :input'), function(data) {
      var returned = $.parseJSON(data);
      if(returned.status == 'failure') {
        $poster.css('color', 'red')
        $poster.val(returned.refresh);
      } else {
        var idTransfer = $poster.parent().attr('id');
        $poster.parent().remove();
        $('#poster_' + idTransfer).html(returned.refresh);
        $('#poster_' + idTransfer).show();
        $('#poster_' + idTransfer).attr('id', idTransfer);
      }
    });
    return false;
  });
});

function pointerCheck(pointer) {
  if(pointer == (String.fromCharCode(9654))) {
    return '&#9660;';
  } else if(pointer == (String.fromCharCode(9660))) {
    return '&#9654;';
  } else {
    return null;
  }
}

$(document).ready(function() {
  $("ul.stepperList li").live('mouseover',function(){
    $("li.altLItext").text($(this).attr('title'))
  }).live('mouseout',function(){
    $("li.altLItext").text('')
  });
});

//deleteUrl =
//echo url_for('scenario/deleteshifttemplate') .
function removeShiftTemplate(stId, deleteUrl) {
  var r = $.ajax({
    type: 'DELETE',
    url: deleteUrl + '?stId=' + stId,
    async: false //the above could and should be refactored for re-usability
  }).responseText;
  return r;
}

//addUrl = 
//echo url_for('scenario/addshifttemplate?id=' . $scenario_id)
function addShiftTemplate(formId, addUrl) {
  var r = $.ajax({
    type: 'GET',
    url: addUrl + '?num=' + formId,
    async: false
  }).responseText;
  return r;
}

function addLabels(element, labels){
  var scale = element.append('<ol class="ui-slider-scale ui-helper-reset" role="presentation"></ol>').find('.ui-slider-scale:eq(0)');
  jQuery(labels).each(function(i){
    scale.append('<li style="left:'+ leftVal(i, this.length) +'"><span class="ui-slider-label">'+ this.text +'</span><span class="ui-slider-tic ui-widget-content"></span></li>');
  });

}
function leftVal(i){
  return (i/(2) * 100).toFixed(2)  +'%';
}

function addSlider(
  sliderObject,
  formNumber,
  formRootName,
  stored_time,
  stored_field,
  sliderOptions,
  stepVal
  ) {
    var sComponent;
    var hourTextBox = $("#st_" + formNumber + "_" + formRootName + "_hours");
    var minuteTextBox =  $("#st_" + formNumber + "_" + formRootName + "_minutes");
    var storedInput = $('#' + stored_field);
    
    
  sComponent = sliderObject.slider({
    orientation: "horizontal",
    value: stored_time,
    min: sliderOptions[0].value, //the first
    max: sliderOptions[sliderOptions.length-1].value,
    step: stepVal,
    slide: function( event, ui ) {
      var hours = Math.floor(ui.value / 60);
      var minutes = ui.value - (hours * 60);
      hours = hours.toString();
      minutes = minutes.toString();
      if(hours.length == 1) hours = '0' + hours;
      if(minutes.length == 1) minutes = '0' + minutes;
      hourTextBox.val(hours);
      minuteTextBox.val(minutes);
      storedInput.val(ui.value);
    }
  });
    
    hourTextBox.bind('blur', {sliderObj: sliderObject}, function(e) {
      var sliderObject = e.data.sliderObj
      var setValue = Math.abs($(this).val()) * 60 + Math.abs(minuteTextBox.val());
      sliderObject.slider("value", setValue);
      storedInput.val(setValue);
      
    });
    
    minuteTextBox.bind('blur', {sliderObj: sliderObject}, function(e) {
      var sliderObject = e.data.sliderObj
      var setValue = Math.abs(hourTextBox.val()) * 60 + Math.abs($(this).val());
      sliderObject.slider("value", setValue);//handleIndex, thisIndex);
      storedInput.val(setValue);
      
    });

  addLabels(sComponent,sliderOptions);
}
                   
function equalizeHeight(containerElement) {
  var maxHeight = 0;

  containerElement.children('div.inlineLists').each(function(){
    maxHeight = Math.max(maxHeight, $(this).height());
  });

  containerElement.children('div.inlineLists').height(maxHeight);
}

/**
* buildTooltip creates a jQuery UI modal dialog window to contain the tooltip information.
*
* @param  element  A DOM element, most likely a div, that will display content inside the
*                  modal window.
* @param  title    The title for the modal window.
* @return $dialog  A configured modal dialog.
*
* @todo   Add more params for greater configurability.
**/
function buildTooltip(element, obj, title) {
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
    },
    title: title
  });
  return $dialog;
}

/**
* This unnamed function catches the click of an element with .tooltipTrigger class. It calls
* buildTooltip and then loads and opens the modal dialog.
*
* @return false  Return false is used here to prevent the clicked link from returning and sending
*                the user forward in the browser.
**/
$(document).ready(function() {
  $('.tooltipTrigger').live('click', function() {
    var $dialog = buildTooltip('<div id="tooltipContent"></div>', this, $(this).attr('title'));
    $dialog.load($(this).attr('href'), function() {
      $dialog.dialog('open')
    });
    $(document).find('div.ui-dialog-titlebar').addClass('titleClass');
    return false;
  });
});

function buildSortList() {
  $('.available tbody, .allocated tbody' ).sortable({
    connectWith: ".sortTable tbody",
    items: 'tr.sort',
    forcePlaceholderSize: true
  });

  $('.allocated tbody').bind('beforestop', function(event, ui) {
    if(ui.helper.find('td').length < 3) {
      ui.helper.find('td.right').removeClass('right').addClass('inner');
      ui.helper.append('<td class="right narrow"><input class="inputGraySmall" type="text"></td>');
      ui.helper.css('width', '305px');
    }
  });

  $('.available tbody').bind('beforestop', function(event, ui) {
    if(ui.helper.find('td').length == 3) {
      ui.helper.find('td.right').remove();
      ui.helper.find('td.inner').removeClass('inner').addClass('right');
      ui.helper.css('width', '257px');
    }
  });

  $('.allocated tbody').bind('sortover', function(event, ui) {
    if(ui.helper.find('td').length < 3) {
      ui.helper.find('td.right').removeClass('right').addClass('inner');
      ui.helper.append('<td class="right narrow"><input class="inputGraySmall" type="text"></td>');
      ui.helper.css('width', '305px');
    }
  });

  $('.available tbody').bind('sortover', function(event, ui) {
    if(ui.helper.find('td').length == 3) {
      ui.helper.find('td.right').remove();
      ui.helper.find('td.inner').removeClass('inner').addClass('right');
      ui.helper.css('width', '257px');
    }
  });
  $('.allocated tbody').bind('sortupdate', function () {
    if($(this).find('tr').is(':hidden')) {
      $(this).find('.sort').hide();
    }
    countSorts($('tr#' + $(this).attr('title')).find('.count'));
  });

  $('.allocated tbody').bind('sortreceive', function(event, ui) {
    if(ui.item.hasClass('serialIn') == false) {
      ui.item.addClass('serialIn');
    }
  });

  $('.available tbody').bind('sortreceive', function(event, ui) {
    if(ui.item.hasClass('serialIn') == true) {
      ui.item.removeClass('serialIn');
    }
  });
}

function countSorts(countMe) {
  $(countMe).html(function() {
    var counted = $('tbody.' + $(this).parent().attr('id')).children('tr.sort').length;
    if(counted == 0) {
      $('tbody.' + $(this).parent().attr('id')).append('<tr class="countZero"><td colspan="3">No facilities selected for this status.</td></tr>')
    } else if (counted != 0) {
      $('tbody.' + $(this).parent().attr('id')).children('tr.countZero').remove();
    }
    return 'Count: ' + counted;
  });
}

/**
* These 3 functions are for error reporting/highlighting in the browser.
**/
function highLight(id, highLightClass) {
  $(id).addClass(highLightClass).attr('onfocus', 'emptyHighLight(this)').attr('onkeypress', 'removeHighLight(this, \'' + highLightClass + '\')');
}

function emptyHighLight(element) {
  $(element).val('');
}

function removeHighLight(element, highLightClass) {
  $(element).removeClass(highLightClass);
}
/**************************************************************************************************/

function sortSlide() {
  $('.sortHead th a').live('click', function(){
    $('div.' + $(this).attr('class')).slideToggle();
    var pointer = pointerCheck($(this).html());
    if(pointer == '&#9654;') {
      $(this).attr('title', 'Expand')
    } else if (pointer == '&#9660;') {
      $(this).attr('title', 'Collapse')
    }
    $(this).html(pointer);
    return false;
  })
}

function reveal (revealer) {
  var pos = $(revealer).offset();
  var height = $(revealer).height();

  $("#revealable").css( {
    "left": pos.left + "px", 
    "top":(pos.top + height) + "px"
  } );

  $("#revealable").fadeToggle();
  $(revealer).html(pointerCheck($(revealer).html()));
  return false;
}

function reloadGroup (reloader) {
  $.post(
    $(reloader).parent().attr('action'),
    {
      change: true, 
      groupid: $(reloader).siblings('select').val(), 
      groupname: $(reloader).siblings('select').find(':selected').text()
    },
    function(data) {
      var $response = $(data);
      $('.bucketHolder').replaceWith($response.filter('.bucketHolder'));
      buildSortList();
      countSorts('.count');
    }
    );
}

function serialTran(poster) {
  var values = new Object;
  $('.serialIn').each(function(index) {
    values[index] = {
      'frId' : $(this).attr('id').replace('facility_resource_id_', ''),
      'actSeq' : ($(this).find('input')).val(),
      'actStat': ($(this).parents('tbody').attr('title'))
    }
  });
  $("#ag_scenario_facility_group_values").val(JSON.stringify(values));

  $('#' + $(poster).parent().attr('id') + ' :input[type="button"]').each(function() {
    if($(this).attr('name') != $(poster).attr('name')) {
      $(this).addClass('exclude');
    }
  });

  $.post($(poster).parent().attr('action'), $('#' + $(poster).parent().attr('id') + ' :input:not(.exclude)'), function(data) {
    $('.exclude').removeClass('exclude');
    var response = $.parseJSON(data);
    if(response.redirect == true) {
      window.location.href = response.response;
    } else {
      highLight('#ag_scenario_facility_group_scenario_facility_group', 'redHighLight');
      alert(response.response);
    }
  });
}

/**
* This function is used in scenario/staffpool to construct and save the staff pool query.
**/
function queryConstruct() {
  var out = new Array();
  $('.filter option:selected').each(function(index) {
    conditionObject = new Object();
    if($(this).text() != ''){
      conditionObject.condition = $(this).text();
      conditionObject.field = $(this).parent().attr('id');
      conditionObject.operator = '=';
      out.push(conditionObject);
    }
  //ONLY IF text is NOT empty
  })
  if (out.length == 1) {
    var query_c = Array(out.pop());
    if(query_c != undefined){
      $("#staff_pool_search_search_condition").val(JSON.stringify(query_c));
    }
  }
  else if(out.length == 0) {
    $("#staff_pool_search_search_condition").val('[ ]');
  }
  else if(out.length > 0) {
    $("#staff_pool_search_search_condition").val(JSON.stringify(out));
  }
}


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
* triggerModal is called from the click event of an element (used to be called on those elements
* with the modalTrigger class. It calls buildModal and then loads and opens the modal dialog.
*
* @return false  Return false is used here to prevent the clicked link from returning and sending
*                the user forward in the browser.
**/
function triggerModal(element) {
    var $dialog = buildModal('<div id="modalContent"></div>', $(element).attr('title'));
    $dialog.load($(element).attr('href'), function() {$dialog.dialog('open')});
    return false;
}

/**
* This function is used to render a new staff resource type form on the staff creation page.
**/
function addStaffResource(element) {
  $.ajax({
    type: 'GET',
    url: $(element).attr('href') + '?num=' + $('.staffCounter').length,
    async:false,
    complete: function(data) {
//     $(element).parent().append(data.responseText);
      $(element).parent().find('.staffCounter').filter(':last').after(data.responseText);
    }
  });
}

function removeStaffResource(element) {
  // Only hit the server and delete from the database if an id (set to the db object's id) has been
  // assigned to [element]. The id attribute is also removed from [element], in case it ends up being
  // the only staff resource form on the page and gets reset in the next conditional.
  if($(element).attr('id') != undefined) {
    $.post($(element).attr('href'), {staffResourceId: $(element).attr('id').replace('staff_resource_', '')});
    $(element).removeAttr('id');
  }

  // Reset the select options in the form to the first option, if this was the last staff resource
  // form on the page.
  if($('.staffCounter').length < 2) {
    $(element).parent().find('select').each(function() {
      var p = $(this).val();
      $(this)[0].selectedIndex = 0;
    });
  } else {
    $(element).parent().remove();
  }
}


function createDatePicker() {
  $("#dob").datepicker({
    changeMonth: true,
    changeYear: true,
    defaultDate: new Date($("#dob").val()),
    duration: 'fast',
    minDate: -110*365,
    maxDate: 0,
    yearRange: 'c-110:c'
  });
}

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
    $.ajax({
      context: $submitter,
      url: $(this).parent().attr('action'),
      type: "POST",
      data: $('#' + $(this).parent().attr('id') + ' :input'),
      success:
        function (data) {
          if($('#modalReloadable').length) {
            $('#modalReloadable').replaceWith(data);
          } else {
            $('#modalContent').append(data);
//          showFeedBack(data, $(this), processReturn);
          }
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
    appendContent($(this), data);
//    returnContent(data);
  }
}

function appendContent($submitter, data) {
  $('#modalAppend').load(($submitter).attr('id'), function() {
    $('#modalAppend').slideDown(1000);
  });
}