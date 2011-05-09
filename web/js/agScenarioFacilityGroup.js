$(document).ready(function() {
  buildSortList();
  countSorts('.count');
  sortSlide();
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


  $('#available > li').tooltip();
  $('#allocated > li').tooltip({
    bodyHandler: function() {
      return $('#allocated_tip').text();
    },
    showURL: false
  });

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
//return false;
  }

  function highLight(id, highLightClass) {
    $(id).addClass(highLightClass).attr('onfocus', 'emptyHighLight(this)').attr('onkeypress', 'removeHighLight(this, \'' + highLightClass + '\')');
  }

  function emptyHighLight(element) {
    $(element).val('');
  }

  function removeHighLight(element, highLightClass) {
    $(element).removeClass(highLightClass);
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