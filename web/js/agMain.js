/***************************************************************************************************
* Initializer function. Calls all of the initialize functions to determine which functions will be *
* used on the page.                                                                                *
***************************************************************************************************/
$(document).ready(function initialize(){
  initEqualizeHeight();
  initSortableTables();
  initVerticalTabs();
  initCheckBoxToggling();
  initReveal();
  initCollapseAll();
  initExpandAll();
  initExpand();
  initTextToForm();
  initTriggerModal();
  initDeployStaff();
  initFileImportReplacer();
  initStatusPolling();
  fileUploadBrowseHover();
});

/** Start Initializer Section *********************************************************************/

/**
 *Initialize the deploy staff button for staff deployment
 */

function initDeployStaff() {

  var dsElement = $('#deploystaff');
  if(dsElement.length) {
    var start = 5;//$("#result").children('#totalstart')
    var totalLeft = start;
    var totalProcessed = 0;

    $('#deploystaff').click(function()
    {
      var startTime, endTime, totalProcessed, totalTimeElapsed = 0, averageTime = 0, estimateTimeLeft = 0;
      $("#result").show();
      //if(xmlHttp.readyState==4)
      // {
      do {
        // Start timing.
        startTime = new Date().getTime();

        var recordProcessed = calcBatch();
        totalProcessed += recordProcessed;
        // End Timing.
        endTime = new Date().getTime();

        totalLeft = start - totalProcessed;

        // Time elapsed for batch processing.
        intervalTimeElapsed = endTime - startTime;
        totalTimeElapsed += endTime - startTime;
        if (totalProcessed != 0) {
          averageTime = totalTimeElapsed / totalProcessed;
          estimateTimeLeft = averageTime * totalLeft;
        }
        //@TODO remove hardcoding of image
        $("#combos").html(totalLeft);
        $("#result").html('<img src="../images/indicator.gif"> done processing '+totalProcessed + " out of "+start+ " records!<BR>Total time elapsed to process " + totalProcessed + " records: "+ (totalTimeElapsed / 1000) + 's<BR>Estimated time left to process ' + totalLeft + ' records: ' + (estimateTimeLeft / 1000) + 's');
      } while (totalLeft > 0);
      $("#result").html('<a class="generalButton" id="deploystaff" href="#">Export Staff Deployment</a>'
        +
        "done processing "+totalProcessed + " out of "+start+ " records!");
    // }
    });
  }
}

/***************************************************************************************************
* Initializes the fileImportReplacer() function. Used on scenario/[scenario_id]/review.                                                                *
***************************************************************************************************/
function initFileImportReplacer() {
  var replacer = $('#fileImportReplacer');
  if(replacer.length) {
    fileImportReplacer();
  }
}
/***************************************************************************************************
* Initializes the textToForm(), configSubmitTextToForm(), and submitTextToForm() functions. Used   *
* on event/[event_name]/listgroups.                                                                *
***************************************************************************************************/
function initTextToForm() {
  var ttfElement = $('.textToForm');
  if(ttfElement.length) {
    textToForm();
    configSubmitTextToForm();
    submitTextToForm();
  }
}
/***************************************************************************************************
* Initializes the equalizeHeight() function. Used on scenario/[scenario_id]/resourcetypes.         *
***************************************************************************************************/
function initEqualizeHeight() {
  var containerElement = $('.inlineListWrapper');
  if(containerElement.length) {
    equalizeHeight(containerElement);
  }
}
/***************************************************************************************************
* Initializes the buildSortList(), countSorts(), and sortSlide() functions. Used on                *
* scenario/[scenario_id]/fgroup.                                                                   *
***************************************************************************************************/
function initSortableTables() {
  var bucketHolder = $('.bucketHolder');
  if(bucketHolder.length) {
    buildSortList();
    countSorts('.count');
    sortSlide();
  }
}
/***************************************************************************************************
* Initializes the verticalTabs() function. Used on /respond. This should be removed at some point  *
* as there is an overabundance of code behind this that does nothing but create extaneous UI       *
* elements that don't fit with the rest of the applications overall UI design.                     *
***************************************************************************************************/
function initVerticalTabs() {
  var verticalTab = $('#textExample');
  if(verticalTab.length)
  {
    $("#textExample").verticaltabs();
  }
}
/***************************************************************************************************
* Initializes the checkToggle(), and checkAll() functions. Used on scenario/[scenario_id]/fgroup.  *
***************************************************************************************************/
function initCheckBoxToggling() {
  var checkAllInit = $('#checkAll');
  if(checkAllInit.length) {
    checkAll();
  }

  var checkToggleInit = $('.checkToggle');
  if(checkToggleInit.length) {
    checkToggle();
  }
}
/***************************************************************************************************
* Initializes the reveal() function. Used on scenario/[scenario_id]/fgroup.                        *
***************************************************************************************************/
function initReveal() {
  var revealerInit = $('#revealer');
  if(revealerInit.length) {
    reveal();
  }
}
/***************************************************************************************************
* Initializes the expandAll() function. Used on event/[event_name]/listgroups.                     *
***************************************************************************************************/
function initExpand() {
  var expandInit = $('.expander');
  if(expandInit.length) {
    expand();
  }
}
/***************************************************************************************************
* Initializes the expandAll() function. Used on event/[event_name]/listgroups.                     *
***************************************************************************************************/
function initExpandAll() {
  var expandAllInit = $('.expandAll');
  if(expandAllInit.length) {
    expandAll();
  }
}
/***************************************************************************************************
* Initializes the expandAll() function. Used on event/[event_name]/listgroups.                     *
***************************************************************************************************/
function initCollapseAll() {
  var collapseAllInit = $('.collapseAll');
  if(collapseAllInit.length) {
    collapseAll();
  }
}

function initTriggerModal() {
  var modalTrigger = $('.modalTrigger');
  if(modalTrigger.length) {
    triggerModal();
  }
}
/** End Initializer Section ***********************************************************************/

/***************************************************************************************************
* fileImportReplacer() switches the content of a td to a file upload form.                         *
***************************************************************************************************/
function fileImportReplacer() {
  $('#fileImportReplacer').click(function () {
    if($('#importForm').length < 1) {
      var $importForm = '<form id="importForm" style="position: relative; display: inline-block" action="' + $('#fileImportReplacer').attr('href') + '" method="post" enctype="multipart/form-data" target="_blank"><div style="position: absolute; top: 0px; left: 0px; width: 250px"><input  style="display: inline-block; color: #848484" class="inputGray" id="show" /><a class="continueButton fileUploadBrowse" style="padding: 5px;margin-left: 5px;">Browse</a></div><input type="file" name="import" id="inputfileupload2" style="height:25px" onchange="javascript:document.getElementById(\'show\').value = this.value;"/><input type="submit" name="submit" value="Submit" class="submitLinkButton" style="position:absolute; top:0px; left: 199px" onclick="return confirm(\'To begin importing click &quot;OK&quot; and a new tab will open.  Do not close the new tab until import is complete.\');" /></form>';

      $('#replaceMe').hide();
      $('#replaceMe').parent().append($importForm);

      

           $('input#inputfileupload2').mouseover(function()
    {
        $('a.fileUploadBrowse').addClass("fileUploadHover").removeClass("continueButton");
    });
     $('input#inputfileupload2').mouseout(function()
    {
        $('a.fileUploadBrowse').removeClass("fileUploadHover").addClass("continueButton");
    });



    } else {
      $('#importForm').remove();
      $('#replaceMe').show();
    }
    return false;
  });
}
/***************************************************************************************************
* reveal() reveals whatever content is within #revealable.                                         *
***************************************************************************************************/
function reveal() {
  $('#revealer').click(function() {
    var pos = $(this).offset();
    var height = $(this).height();

    $("#revealable").css( {
      "left": pos.left + "px",
      "top":(pos.top + height) + "px"
    } );

    $("#revealable").fadeToggle();
    $(this).html(pointerCheck($(this).html()));
    return false;
  });
}
/***************************************************************************************************
* checkAll() and checkToggle() are used in conjunction to check or uncheck a group of checkboxes.  *
***************************************************************************************************/
function checkAll() {
  $('#checkAll').live('click', function () {
    var check = this.checked;
    $('.checkBoxContainer').find('.checkToggle').each(function() {
      this.checked = check;
      $(this).trigger('change');
    });
  });
}
/***************************************************************************************************
* checkAll() and checkToggle() are used in conjunction to check or uncheck a group of checkboxes.  *
***************************************************************************************************/
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
/***************************************************************************************************
* expandAll() expands all collapsed elements that are subject to an .expander class.               *
***************************************************************************************************/
function expandAll() {
  $('.expandAll').live('click', function() {
    $('.expander').each(function(){
      if($('#expandable_' + $(this).attr('id')).children().length == 0) {
        $(this).click();
      }
    });
    return false;
  });
}
/***************************************************************************************************
* collapseAll() collapses all expanded elements that are subject to an .expander class.            *
***************************************************************************************************/
function collapseAll() {
  $('.collapseAll').live('click', function() {
    $('.expander').each(function(){
      if($('#expandable_' + $(this).attr('id')).children().length != 0) {
        $(this).click();
      }
    });
    return false;
  });
}
/***************************************************************************************************
* expand() expands elements that are subject to an .expander class.
***************************************************************************************************/
function expand() {
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
}
/***************************************************************************************************
* textToForm() converts an anchor to a form populated with the anchor's value.                     *
***************************************************************************************************/
function textToForm() {
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
}
/***************************************************************************************************
* configSubmitTextToForm() disables submit from the keyboard on .submitTextToForm elements.        *
***************************************************************************************************/
function configSubmitTextToForm() {
  $('.submitTextToForm').live('keypress', function(evt) {
    var charCode = evt.charCode || evt.keyCode;
    if (charCode  == 13) {
      return false;
    }
  });
}
/***************************************************************************************************
* submitTextToForm() submits .submitTextToForm forms and returns their value. Then the form        *
* reverts to the original anchor.                                                                  *
***************************************************************************************************/
function submitTextToForm() {
  $('.submitTextToForm').live('blur submit', function() {
    var $poster = $(this);
    var b = $(this).parent().attr('action');
    var c = $('#' + $(this).parent().attr('id') + ' :input');
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
}
/***************************************************************************************************
* pointerCheck is called by a number of other functions to check the state of an arrow used for    *
* expanding or showing UI elements in order to set the pointer to the correct value after          *
* expansion, collapse, revealing or hiding.                                                        *
***************************************************************************************************/
function pointerCheck(pointer) {
  if(pointer == (String.fromCharCode(9654))) {
    return '&#9660;';
  } else if(pointer == (String.fromCharCode(9660))) {
    return '&#9654;';
  } else {
    return null;
  }
}
/***************************************************************************************************
* equalizeHeight() sets two div elements with the .inlineLists class to the height of whichever is *
* taller.                                                                                          *
***************************************************************************************************/
function equalizeHeight(containerElement) {
  var maxHeight = 0;

  containerElement.children('div.inlineLists').each(function(){
    maxHeight = Math.max(maxHeight, $(this).height());
  });

  containerElement.children('div.inlineLists').height(maxHeight);
}
/***************************************************************************************************
* buildSortList() sets up the sortable lists used on scenario/[scenario_id]/fgroup.                *
***************************************************************************************************/
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
/***************************************************************************************************
* countSorts() counts the elements in each allocated sort group on scenario/[scenario_id]/fgroup.  *
* It is used in conjunction with buildSortLists(), so is initialized with initSortableTables();    *
***************************************************************************************************/
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
/***************************************************************************************************
* triggerModal is called from the click event of an element (used to be called on those elements   *
* with the modalTrigger class. It calls buildModal and then loads and opens the modal dialog.      *
*                                                                                                  *
* @return false  Return false is used here to prevent the clicked link from returning and sending  *
*                the user forward in the browser.                                                  *
***************************************************************************************************/
function triggerModal() {
  $('.modalTrigger').live('click', function() {
    var $dialog = buildModal('<div id="modalContent"></div>', $(this).attr('title'));
    $dialog.load($(this).attr('href'), function() {
      $dialog.dialog('open')
      });
    return false;
  });
}
/***************************************************************************************************
* buildModal creates a jQuery UI modal dialog window.                                              *
*                                                                                                  *
* @param  element  A DOM element, most likely a div, that will display content inside the          *
*                  modal window.                                                                   *
* @param  title    The title for the modal window.                                                 *
* @return $dialog  A configured modal dialog.                                                      *
*                                                                                                  *
* @todo   Add more params for greater configurability.                                             *
***************************************************************************************************/
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
$(document).ready(function() {
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


        //start p-code

        $(":input[id$='maximum_staff']").focusout(function(){
            if( $(this).val() != "")
            {
                $(this).parent().parent().parent().children(':first-child').children(':first-child').children(':first-child').addClass('inputEmpty');
                $(this).parent().parent().parent().children(':first-child').children(':first-child').children(':first-child').focusout(function(){
                {
                    if( $(this).val() != "")
                    {
                        $(this).removeClass('inputEmpty');
                        $(this).parent().parent().parent().children(':nth-child(3)').children(':nth-child(1)').children(':first-child').removeClass('inputEmpty');
                    }
                }
                });


        }
        });

        

    $(":input[id$='minimum_staff']").focusout(function(){
        if( $(this).val() != "")
        {
            $(this).removeClass('inputEmpty');

            $(this).parent().parent().parent().children(':nth-child(3)').children(':nth-child(1)').children(':first-child').addClass('inputEmpty');
           
            $(this).parent().parent().parent().children(':nth-child(3)').children(':nth-child(1)').children(':first-child').focusout(function(){
            {
                if( $(this).val() != "")
                {
                    $(this).removeClass('inputEmpty');
                    $(this).parent().parent().parent().children(':first-child').children(':first-child').children(':first-child').removeClass('inputEmpty');
                }
              
            }
            });
    }
    });

//end p-code



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
      $('#newshifttemplates').append(addShiftTemplate(templates, $(this).attr('href')));
//      $(passId).parent().append(addShiftTemplate(templates, $(this).attr('href')));
      var STContainers = $(".shiftTemplateCounter");
      
      var newSTContainer = STContainers[STContainers.length -1];
      $(newSTContainer).find(".timeslider").each(function(){
      
        var elementId = $(this).attr('id');
        var elementNumberIndex = elementId.search('[0-9]');
        var elementNumber = elementId.substr(elementNumberIndex);
        var elementName = elementId.substr(0,elementNumberIndex);
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
      if($(this).parents('#newshifttemplates').length) {
        $(this).parents('.shiftTemplateCounter').fadeOut(1200, function() {
          $(this).parents('.shiftTemplateCounter').remove();
        });
        return false;
      } else {
        var containerId = $(this).parents('.shiftTemplateCounter').attr('id');
        $.post($(this).attr('href'), { stId: $(this).attr('id') }, function() {
          $('#' + containerId).fadeOut(1200, function() {
            $('#' + containerId).remove();
          });
        })
      }





//      //if there is no id for this record(db_not_exists)
//      var passId = '#' + $(this).attr('id');
//      //send get/post to call delete
//      $('#container' + $(this).attr('id').replace('removeShiftTemplate', '')).remove();
//
//      $('#newshifttemplates').prepend('<h2 class="overlay">'
//        + removeShiftTemplate(
//          $(this).attr('id'), $(this).attr('href'))
//
//        + '</h2>');
//      $('.overlay').fadeIn(1200, function() {
//        $('.overlay').fadeOut(1200, function() {
//          $('.overlay').remove();
//        });
//      });
      return false;
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
  

  var dateOfBirth = $('#dob');
  if(dateOfBirth.length > 0) {
    createDatePicker();
  }
 
  var eventWatcher = $('#eventWatcher');
  if(eventWatcher.length > 0) {
    
//$(eventWatcher).html(getActiveEvents($('#urlHolder').attr(href)));
    
}
});




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



$(document).ready(function() {
  $("ul.stepperList li").live('mouseover',function(){
    $("li.altLItext").text($(this).attr('title'))
  }).live('mouseout',function(){
    $("li.altLItext").text('')
  });
});

function getActiveEvents(eventURL) {
  var r = $.ajax({
    type: 'get',
    url: eventURL,
    async: false //the above could and should be refactored for re-usability
  }).responseText;
  return r;
}


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
    
  hourTextBox.bind('blur change keyup', {
    sliderObj: sliderObject
  }, function(e) {
    var sliderObject = e.data.sliderObj
    var sliderValue = Math.abs($(this).val()) * 60 + Math.abs(minuteTextBox.val());
    var setValue = $(this).val() > 0 ? sliderValue : -1*sliderValue;
    sliderObject.slider("value", setValue);
    storedInput.val(setValue);
      
  });
    
  minuteTextBox.bind('blur change keyup', {
    sliderObj: sliderObject
  }, function(e) {
    var sliderObject = e.data.sliderObj
    var setValue = Math.abs(hourTextBox.val()) * 60 + Math.abs($(this).val());
    sliderObject.slider("value", setValue);//handleIndex, thisIndex);
    storedInput.val(setValue);
      
  });

  addLabels(sComponent,sliderOptions);
}

/***************************************************************************************************
* buildTooltip creates a jQuery UI modal dialog window to contain the tooltip information.
*
* @param  element  A DOM element, most likely a div, that will display content inside the
*                  modal window.
* @param  title    The title for the modal window.
* @return $dialog  A configured modal dialog.
*
* @todo   Add more params for greater configurability.
***************************************************************************************************/
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
/***************************************************************************************************
* This function is used to render a new staff resource type form on the staff creation page.       *
***************************************************************************************************/
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
    $.post($(element).attr('href'), {
      staffResourceId: $(element).attr('id').replace('staff_resource_', '')
      });
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
  if($("#dob").val().length == 0) {
    var curDate = new Date();
    var useDate = (curDate.getMonth()+1) + '/' + curDate.getDate() + '/' + (curDate.getFullYear()-36);
  }
  else {
    var useDate = $("#dob").val();
  }


  $("#dob").datepicker({
    changeMonth: true,
    changeYear: true,
    defaultDate: new Date(useDate),
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
  pattern = /facilityresource\/[\w\d+%-]*/;
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
function calcBatch() {
  var count = 0;
  $.ajax({
    async: false,
    type: "POST",
    url: window.location.pathname,
    success: function(html)
    {
      count = parseInt(html);
    }
  });
  return count;
}

function initStatusPolling() {
  if ($('#infobar').size()) {
    updateStatus(status_url);
  }
}

function updateStatus(url) {
  $.getJSON(url, function(data) {
    $('#infobar').text(data);
    setTimeout("updateStatus(url)", 5000);
    return false;
  });
}

function fileUploadBrowseHover() {
    $('#fileUpload').mouseover(function()
    {
        $('a.fileUploadBrowse').addClass("fileUploadHover").removeClass("continueButton");
    });
     $('#fileUpload').mouseout(function()
    {
        $('a.fileUploadBrowse').removeClass("fileUploadHover").addClass("continueButton");
    });
}

/***************************************************************************************************
*                                                                *
***************************************************************************************************/
function shiftTemplateIdVal(stId) {
  var eleId = $('#deleteShiftTemplateId');
  if($('#deleteShiftTemplateId').length) {
   eleId.val(stId);
  }
}