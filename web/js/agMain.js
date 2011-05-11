// This main document ready function determines which other functions will be needed by the current
// page. Determined by the presence of relevant DOM elements.
$(document).ready(function() {
  // Used in scenario/resourcetypes
  var containerElement = $('.inlineListWrapper');
  if(containerElement.length > 0) {
    containerElement.each(function() {equalizeHeight($(this))});
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
      
      
// Watermark stuff for scenario/staffresources
    $(function() {
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

      $(":input[id$='minimum_staff']").Watermark("Min");
      $(":input[id$='maximum_staff']").Watermark("Max");

      //adding forward slash after minimum_staff  input box
      $(":input[id$='minimum_staff']").parent().parent().after("<span style='margin-left:2px; font-size:15px'>/</span>");

    });
// End watermark stuff
      
      
    });
  }
  
});

/**
 * This function is used to check or uncheck a series of checkboxes.
 **/

//$(document).ready(function(){
  // Checking the checkbox w/ id checkAll will check all boxes w/ class chekToggle
  // unchecking checkAll will uncheck all checkToggles.
  $('#checkall').live('click', function () {
    var check = this.checked;
    $('.checkBoxContainer').find('.checkToggle').each(function() {
      this.checked = check;
      $(this).trigger('change');
    });
  });
  // This unsets the check in checkAll if one of the checkToggles are unchecked.
  // or it will set the check on checkAll if all the checkToggles have been checked
  // individually.
  $('.checkToggle').live('change', function(){
    if($('.checkToggle').length == $('.checkToggle:checked').length) {
      $('#checkall').attr('checked', 'checked');
    } else {
      $("#checkall").removeAttr('checked');
    }
  });
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


$().ready(function() {
  $('.addShiftTemplate').click(function() {
    var passId = '#' + $(this).attr('id');
    var $poster = $(this);
    var templates = $('.shiftTemplateCounter').length;
    $(passId).parent().prepend(addShiftTemplate(templates, $(this).attr('href')));
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

  function addSlider(formNumber,
  formRootName,
  stored_time,
  stored_field,
  sliderOptions,
  stepVal
) {
    sComponent = $("#" + formRootName +"_slider" + formNumber).slider({
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
        $("#st_" + formNumber + "_start_time_hours").val(hours);
        $("#st_" + formNumber + "_start_time_minutes").val(minutes);
        $("#st_" + formNumber + "_" + stored_field).val(ui.value);
      }
    });
    addLabels(sComponent,sliderOptions);
  }
                   

  $().ready(function() {

    var stLabels = []; //start time
    stLabels[0]=
      {
      value: '-7200',
      text: '-5days'
    };
    stLabels[1]=
      {
      value: '0',
      text: '0'
    };
    stLabels[2] =
      {
      value: '7200',
      text: '+5days'
    };
    var ttLabels = []; //task time
    ttLabels[0]=
      {
      value: '0',
      text: '0hrs'
    };
    ttLabels[1]=
      {
      value: '360',
      text: '6hrs'
    };
    ttLabels[2] =
      {
      value: '720',
      text: '12hrs'
    };
  
  


//    //these functions rely on variables coming from the
//    addSlider($formNumber,'break_time',$storedBreak,'break_length_minutes',ttOptions,30);
//    //task time label/slider options are equivalent to break time label/slider options
//    addSlider($formNumber,'break_time',$storedTask,'task_length_minutes',ttOptions,30);
//    addSlider(formNumber,'start_time',$storedStart,'minutes_start_to_facility_activation',stOptions,30);

  });

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
* buildModal and then loads and opens the modal dialog.
*
* @return false  Return false is used here to prevent the clicked link from returning and sending
*                the user forward in the browser.
**/
$(document).ready(function() {
  $('.tooltipTrigger').live('click', function() {
    var $dialog = buildTooltip('<div id="tooltipContent"></div>', this, $(this).attr('title'));
    $dialog.load($(this).attr('href'), function() {$dialog.dialog('open')});
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
////

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

    $("#revealable").css( { "left": pos.left + "px", "top":(pos.top + height) + "px" } );

    $("#revealable").fadeToggle();
    $(revealer).html(pointerCheck($(revealer).html()));
    return false;
}

function reloadGroup (reloader) {
  $.post(
    $(reloader).parent().attr('action'),
    { change: true, groupid: $(reloader).siblings('select').val(), groupname: $(reloader).siblings('select').find(':selected').text() },
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
    values[index] = {'frId' : $(this).attr('id').replace('facility_resource_id_', ''),
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
  if(out.length == 1){
    $("#staff_pool_search_search_condition").val(JSON.stringify(out));
  }
  else if(out.length ==0){
    $("#staff_pool_search_search_condition").val('[ ]');
  }
  else{
    var query_c = Array(out.pop());
    if(query_c != undefined){
      $("#staff_pool_search_search_condition").val(JSON.stringify(query_c));
    }
  }
}
