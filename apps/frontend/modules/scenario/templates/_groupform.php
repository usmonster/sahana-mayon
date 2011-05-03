<?php use_stylesheets_for_form($groupform) ?>
<?php use_javascript('jquery.ui.custom.js'); ?>
<?php //TODO: see if this is still necessary:
use_javascript('tooltip.js'); ?>
<?php use_javascript('json.serialize.js'); ?>
<?php //use_javascript('jquery.mousewheel.js'); ?>
<?php //use_javascript('jquery.jscrollpane.js'); ?>
<?php //use_javascript('mwheelIntent.js'); ?>
<?php //use_stylesheet('jquery.jscrollpane.css'); ?>
<script type="text/javascript">  
  $(function() {
//    $("#available tbody tr").bind("dblclick", function(){
//      return !$(this).remove().appendTo('#allocated tbody')
//    });
//    $("#allocated tbody tr").bind("dblclick", function(){
//      return !$(this).remove().appendTo('#available tbody')
//    });

    $('.available tbody, .allocated tbody' ).sortable({
      connectWith: ".testTable tbody",
      items: 'tr.sort',
      forcePlaceholderSize: true
    });/*.disableSelection();*/

    $('.allocated tbody').bind('sortover', function(event, ui) {
      if(ui.helper.find('td').length < 3) {
        ui.helper.find('td.right').removeClass('right').addClass('inner');
        ui.helper.append('<td class="right narrow"><input class="inputGraySmall" type="text"></td>');
      }
    });

    $('.available tbody').bind('sortover', function(event, ui) {
      if(ui.helper.find('td').length == 3) {
        ui.helper.find('td.right').remove();
        ui.helper.find('td.inner').removeClass('inner').addClass('right');
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
  });


  $('#available > li').tooltip();
  $('#allocated > li').tooltip({
    bodyHandler: function() {
      return $('#allocated_tip').text();
    },
    showURL: false
  });

  function serialTran() {
    var out = new Object;
    $('.serialIn').each(function(index) {
      out[index] = {'frId' : $(this).attr('id').replace('facility_resource_id_', ''),
                    'actSeq' : ($(this).find('input')).val(),
                    'actStat': ($(this).parents('tbody').attr('title'))
      }
    });
    $("#ag_scenario_facility_group_values").val(JSON.stringify(out));
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
  };

  $(document).ready(function () {
    countSorts('.count');
  })
  
  $(document).ready(function() {
    $('.sortHead th a').click(function(){
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
  });

  $(document).ready(function() {
    $('#revealer').click(function() {
      var pos = $(this).offset();
      var height = $(this).height();
  
      $("#revealable").css( { "left": pos.left + "px", "top":(pos.top + height) + "px" } );
  
      $("#revealable").fadeToggle();
      $(this).html(pointerCheck($(this).html()));
      return false;
    });
  });
</script>
<noscript>in order to set the activation sequence of resource facilities and add them to the
  facility group, you will need javascript enabled</noscript>



<p>Use the checkboxes below to determine which facility resource types are display in the Available list.</p>
<div class="bucketHolder" >
  <form name="faciliy_group_form" id="facility_group_form" action="<?php
echo url_for
    ('scenario/fgroup?id=' . $scenario_id) . (!$groupform->getObject()->isNew() ? '?groupid=' . $groupform->getObject()->getId() : '') ?>
      " method="post" <?php $groupform->isMultipart() and print 'enctype="multipart/form-data" ' ?>>
    <div>
      <?php echo $groupform; ?>
    </div>
    <br />
    <div class="testTableContainer ">
      <table class="testTable available" cellspacing="0">
        <thead>
          <caption><a href="#" id="revealer" title="Facility Resource Type Filter">&#9654;</a>Available Facility Resources</caption>
        </thead>
        <tbody>
          <tr>
            <th class="left">Facility Code</th>
            <th>Resource Type</th>
          </tr>
        <?php foreach ($availableFacilityResources as $availableFacilityResource): ?>
          <tr id="facility_resource_id_<?php echo $availableFacilityResource['fr_id']; ?>" class="sort facility_resource_type_<?php echo $availableFacilityResource['frt_id']; ?>">
            <td class="left" title="<?php echo $availableFacilityResource['f_facility_code'];?>">
              <?php echo $availableFacilityResource['f_facility_name'] ?>
            </td>
            <td class="right" title="<?php echo $availableFacilityResource['frt_facility_resource_type_abbr'] ?>">
              <?php echo $availableFacilityResource['frt_facility_resource_type'] ?>
            </td>
          </tr>
        <?php endforeach; ?>
        </tbody>
      </table>
    </div>

    <div class="testTableContainer">
      <table class="testTableParent" cellspacing="0">
        <thead>
          <caption>Allocated Facility Resources</caption>
        </thead>
        <?php foreach($selectStatuses as $selectStatus): ?>
        <tr class="sortHead" id="<?php echo $selectStatus['fras_facility_resource_allocation_status']; ?>">
          <th class="left">
            <a href="#" class="<?php echo $selectStatus['fras_facility_resource_allocation_status']; ?>" title="Collapse">&#9660;</a>
            <?php echo ucwords($selectStatus['fras_facility_resource_allocation_status']); ?>
          </th>
          <th class="count right">Count:</th>
        </tr>
        <tr>
          <td colspan="3" class="container">
            <div class="<?php echo $selectStatus['fras_facility_resource_allocation_status']; ?>">
              <table class="testTable allocated" cellspacing="0">
                <tbody class="<?php echo $selectStatus['fras_facility_resource_allocation_status']; ?>" title="<?php echo $selectStatus['fras_facility_resource_allocation_status']; ?>">
                  <tr>
                    <th class="left">Facility Code</th>
                    <th class="inner">Resource Type</th>
                    <th class="right narrow">Priority</th>
                  </tr>
                <?php if ($allocatedFacilityResources): ?>
                  <?php foreach ($allocatedFacilityResources[$selectStatus['fras_facility_resource_allocation_status']] as $allocatedFacilityResource): ?>
                  <tr id="facility_resource_id_<?php echo $allocatedFacilityResource['fr_id']; ?>" class="sort serialIn facility_resource_type_<?php echo $allocatedFacilityResource['frt_id']; ?>">
                    <td class="left" title="<?php echo $allocatedFacilityResource['f_facility_code'];?>">
                      <?php echo $allocatedFacilityResource['f_facility_name'] ?>
                    </td>
                    <td class="inner" title="<?php echo $allocatedFacilityResource['frt_facility_resource_type_abbr'] ?>">
                      <?php echo $allocatedFacilityResource['frt_facility_resource_type'] ?>
                    </td>
                    <td class="right narrow">
                      <input type="text" class="inputGraySmall" value="<?php echo $allocatedFacilityResource['sfr_activation_sequence']; ?>">
                    </td>
                  </tr>
                  <?php endforeach; ?>
                <?php endif; ?>
                <tbody>
              </table>
            </div>
          </td>
        </tr>
        <?php endforeach; ?>
      </table>
    </div>
    <br />
    <br />
    <input class="linkButton" type="submit" value="Save and Create Another" name="Another" onclick="serialTran()"/>
    <input class="linkButton" type="submit" value="Save and Assign Staff Requirements" name="AssignAll" onclick="serialTran()"/>
  <?php if ($existingFgroups == true): ?>
    <a href="<?php echo url_for('scenario/staffresources?id=' . $scenario_id) ?>" class="linkButton">Continue</a>
  <?php endif; ?>
  </form>
</div>
<br/>
<div class="tooltips" >
  <span id="allocated_tip">
<?php echo "urltowiki/allocated_tooltip"; ?>
  </span>
</div>
<?php
  $contents = $sf_data->getRaw('facilityResourceTypes');
  echo buildCheckBoxTable($contents, 'id', 'facility_resource_type', 'checkBoxTable searchParams', 'revealable', 2, 'facility_resource_type_', 'facility_resource_type_abbr', true, true);
?>