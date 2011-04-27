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
//  $(function()
//  {
//	$('.scroll-pane').jScrollPane();
//  });
  
  $(function() {
    $("#available tbody tr").bind("dblclick", function(){
      return !$(this).remove().appendTo('#allocated tbody')
    });
    $("#allocated tbody tr").bind("dblclick", function(){
      return !$(this).remove().appendTo('#available tbody')
    });

    $('.available tbody, .allocated tbody' ).sortable({
      connectWith: ".testTable tbody",
      items: 'tr.sort'
//      cancel: 'tr.sortHead'
    });//.disableSelection();

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
//    $('.sortHead').bind('sortover', function() {
//      $(this).find('a').click();
//    })
//    $('#allocated tbody').bind('sortover', function() {
//      if($(this).find('tr').is(':hidden')) {
//        $(this).find('a').click();
//      }
//    })
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
//      out[$(this).attr('id')] = ($(this).find('input')).val();
      out[index] = {'frId' : $(this).attr('id').replace('facility_resource_id_', ''),
                    'actSeq' : ($(this).find('input')).val()}
    });
    $("#ag_scenario_facility_group_values").val(JSON.stringify(out));
  }
//  function serialTran() {
//    var out = Array();
//    $('#allocated > li').each(function(index) {
//      out[index] = $(this).attr('id');
//      $("#ag_scenario_facility_group_ag_facility_resource_order").val(JSON.stringify(out));
//    });
//  }

  function countSorts(countMe) {
    $(countMe).html(function() {
      var counted = $('tbody.' + $(this).parent().attr('id')).children('tr.sort').length;
      if(counted == 0) {
        $('tbody.' + $(this).parent().attr('id')).append('<tr class="countZero"><td colspan="3">No facilities selected for this group.</td></tr>')
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
      $(this).html(pointerCheck($(this).html()));
      return false;
    })
  });
</script>
<noscript>in order to set the activation sequence of resource facilities and add them to the
  facility group, you will need javascript enabled</noscript>
<form name="faciliy_group_form" id="facility_group_form" action="<?php
echo url_for
    ('scenario/fgroup?id=' . $scenario_id) . (!$groupform->getObject()->isNew() ? '?groupid=' . $groupform->getObject()->getId() : '') ?>
      " method="post" <?php $groupform->isMultipart() and print 'enctype="multipart/form-data" ' ?>>

  <div>
    <?php
    if (!$groupform->getObject()->isNew()) {
    ?>
      <input class="linkButton" type="submit" value="Delete" name="Delete"/>
      <input class="linkButton" type="submit" value="Save" id="selecter" onclick="serialTran()"/>
    <?php
    }
    echo $groupform;
    ?>
  </div>
  <div class="bucketHolder" >
    <div class="testTableContainer ">
    <table class="testTable available" cellspacing="0">
      <thead>
        <caption>Available Facility Resources</caption>
      </thead>
      <tbody>
        <tr>
          <th class="left">Facility Code</th>
          <th>Resource Type</th>
          <th class="right">Priority</th>
        </tr>
      <?php foreach ($availableFacilityResources as $availableFacilityResource): ?>
        <tr id="facility_resource_id_<?php echo $availableFacilityResource['fr_id']; ?>" class="sort facility_resource_type_<?php echo $availableFacilityResource['frt_id']; ?>">
          <td class="left" title="<?php echo $availableFacilityResource['f_facility_name'];?>">
            <?php echo $availableFacilityResource['f_facility_code'] ?>
          </td>
          <td title="<?php echo $availableFacilityResource['frt_facility_resource_type'] ?>">
            <?php echo $availableFacilityResource['frt_facility_resource_type_abbr'] ?>
          </td>
          <td class="right">
            <input type="text" class="inputGraySmall">
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
      <th colspan="2" class="left">
        <?php echo ucwords($selectStatus['fras_facility_resource_allocation_status']); ?>
            <a href="#" class="<?php echo ucwords($selectStatus['fras_facility_resource_allocation_status']); ?>">&#9660;</a>
      </th>
      <th class="count right">
            Count:
      </th>
    </tr>
    <tr>
      <td colspan="3" style="height: 0">
        <div class="<?php echo ucwords($selectStatus['fras_facility_resource_allocation_status']); ?>">
          <table class="testTable allocated" cellspacing="0">
            <tbody class="<?php echo $selectStatus['fras_facility_resource_allocation_status']; ?>" title="<?php echo $selectStatus['fras_facility_resource_allocation_status']; ?>">
              <tr>
                <th class="left">Facility Code</th>
                <th>Resource Type</th>
                <th class="right">Priority</th>
              </tr>
            <?php if ($allocatedFacilityResources): ?>
              <?php foreach ($allocatedFacilityResources[$selectStatus['fras_facility_resource_allocation_status']] as $allocatedFacilityResource): ?>
              <tr id="facility_resource_id_<?php echo $allocatedFacilityResource['fr_id']; ?>" class="sort serialIn facility_resource_type_<?php echo $allocatedFacilityResource['frt_id']; ?>">
                <td class="left" title="<?php echo $allocatedFacilityResource['f_facility_name'];?>">
                  <?php echo $allocatedFacilityResource['f_facility_code'] ?>
                </td>
                <td title="<?php echo $allocatedFacilityResource['frt_facility_resource_type'] ?>">
                  <?php echo $allocatedFacilityResource['frt_facility_resource_type_abbr'] ?>
                </td>
                <td class="right">
                  <input type="text" class="inputGraySmall">
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
<!--    <ul id="bavailable" class="bucket">-->
      <?php
//      foreach ($availableFacilityResources as $afr) {
//        echo '<li id="' . $afr['fr_id'] . '" title="' .
//        $afr['frt_facility_resource_type'] . '">' .
//        $afr['f_facility_name'] . ': ' .
//        ucwords($afr['frt_facility_resource_type']) .
//        '</li>'; //we could set the id here to a set of ids
//      }
      ?>

<!--    </ul>-->
<!--    <ul id="allocated" class="bucket">-->
      <?php
//      if ($allocatedFacilityResources) {
//
//        foreach ($allocatedFacilityResources as $facility_resource) {
//          echo '<li id="' . $facility_resource->getId() . '" title="' .
//          $facility_resource->getAgFacilityResourceType() . '">' .
//          $facility_resource->getAgFacility()->getFacilityName() . ': ' .
//          ucwords($facility_resource->getAgFacilityResourceType()->facility_resource_type) .
//          '</li>'; //we could set the id here to a set of ids
//          /**
//           * @todo [$curopt->activation_sequence] needs to still be applied to the list,
//           */
//        }
//      }
      ?>
<!--    </ul>-->
  </div>
  <br/>
  <div class="tooltips" >
    <span id="allocated_tip">
<?php echo "urltowiki/allocated_tooltip"; ?>
    </span>
  </div>
  <br />

  <a href="<?php echo url_for('scenario/review?id=' . $scenario_id) ?>" class="linkButton">Back</a>
  <input class="linkButton" type="submit" value="Save and Create Another" name="Another" onclick="serialTran()"/>
  <input class="linkButton" type="submit" value="Save and Assign Staff Requirements" name="AssignAll" onclick="serialTran()"/>

  <?php
      if ($existingFgroups == true) {
  ?>
 <a href="<?php echo url_for('scenario/staffresources?id=' . $scenario_id) ?>" class="linkButton">Continue</a>
  <?php
      }
  ?>
</form>