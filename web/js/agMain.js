/**
 * This function is used to check or uncheck a series of checkboxes.
 **/
$(document).ready(function(){
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
  $('.blorg').live('change', function() {
    //$('#fgroup').load($(this).parent().attr('action'));
    $.post($(this).parent().attr('action'), $('#' + $(this).parent().attr('id') + ' :input') ,function(data) {
      $('#fgroup').html(data);
    });
  })
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
    var templates = $('.shiftTemplateCounter').length
    $(passId).parent().prepend(addShiftTemplate(templates));
  });


  $('.removeShiftTemplate').click(function() {
    //if there is no id for this record(db_not_exists)
    var passId = '#' + $(this).attr('id');
    //send get/post to call delete
    $('#container' + $(this).attr('id')).remove();
                 
    // if(!$isNewShiftTemplate):
    $('#newshifttemplates').prepend('<h2 class="overlay">'
      + removeShiftTemplate(
    $(this).attr('id'))
    // $shifttemplateform['id']->getValue()
      + '</h2>');
    $('.overlay').fadeIn(1200, function() {
      $('.overlay').fadeOut(1200, function() {
        $('.overlay').remove();
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
  
  


    //these functions rely on variables coming from the 
    //addSlider($formNumber,'break_time',$storedBreak,'break_length_minutes',ttOptions,30);
    //task time label/slider options are equivalent to break time label/slider options
    //addSlider($formNumber,'break_time',$storedTask,'task_length_minutes',ttOptions,30);                    
    //addSlider(formNumber,'start_time',$storedStart,'minutes_start_to_facility_activation',stOptions,30);

  });