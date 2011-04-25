<?php use_stylesheets_for_form($groupform) ?>
<?php use_javascript('jquery.ui.custom.js'); ?>
<?php //TODO: see if this is still necessary:
use_javascript('tooltip.js'); ?>
<?php use_javascript('json.serialize.js'); ?>
<script type="text/javascript">
  $(function() {
    $("#available tbody tr").bind("dblclick", function(){
      return !$(this).remove().appendTo('#allocated tbody')
    });
    $("#allocated tbody tr").bind("dblclick", function(){
      return !$(this).remove().appendTo('#available tbody')
    });

    $( "#available tbody, #allocated tbody" ).sortable({
      connectWith: ".testTable tbody",
      items: 'tr.sort'
//      cancel: 'tr.sortHead'
    }).disableSelection();

    $('#allocated tbody').bind('sortupdate', function () {
      if($(this).find('tr').is(':hidden')) {
        $(this).find('.sort').hide();
      }
      countSorts($(this).find('.count'));
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
    var out = Array();
    $('#allocated > li').each(function(index) {
      out[index] = $(this).attr('id');
      $("#ag_scenario_facility_group_ag_facility_resource_order").val(JSON.stringify(out));
    });
  }

  function countSorts(countMe) {
    $(countMe).html(function() {
      var counted = $(this).parent().siblings('tr.sort').length;
      if(counted == 0) {
        $(this).parents('tbody').append('<tr class="countZero"><td colspan="3">No facilities selected for this group.</td></tr>')
      } else if (counted != 0) {
        $(this).parent().siblings('tr.countZero').remove();
      }
      return 'Count: ' + counted;
    });
  };

  $(document).ready(function () {
    countSorts('.count');
  })

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
    <table class="testTable" id="available" cellspacing="0">
      <tbody>
        <tr>
          <th>Facility Code</th>
          <th>Resource Type</th>
          <th>Activation Sequence</th>
        </tr>
      <?php foreach ($availableFacilityResources as $availableFacilityResource): ?>
        <tr class="sort facility_resource_type_<?php echo $availableFacilityResource['frt_id']; ?>">
          <td title="<?php echo $availableFacilityResource['f_facility_name'];?>">
            <?php echo $availableFacilityResource['f_facility_code'] ?>
          </td>
          <td title="<?php echo $avfr['frt_facility_resource_type'] ?>">
            <?php echo $availableFacilityResource['frt_facility_resource_type_abbr'] ?>
          </td>
          <td>
            <input type="text">
          </td>
        </tr>
      <?php endforeach; ?>
      </tbody>
    </table>

    <table class="testTable" id="allocated"  cellspacing="0">
    <?php foreach($selectStatuses as $selectStatus): ?>
      <tbody id="fras_id_<?php echo $selectStatus['fras_id']; ?>">
        <tr class="sortHead">
          <th colspan="2">
            <?php echo ucwords($selectStatus['fras_facility_resource_allocation_status']); ?>
            <a href="#">&#9660;</a>
          </th>
          <th class="count">
            Count:
          </th>
        </tr>
        <tr>
          <th>Facility Code</th>
          <th>Resource Type</th>
          <th>Activation Sequence</th>
        </tr>
      <?php if ($allocatedFacilityResources): ?>
        <?php foreach ($allocatedFacilityResources[$selectStatus['fras_facility_resource_allocation_status']] as $allocatedFacilityResource): ?>
        <tr class="sort facility_resource_type_<?php echo $allocatedFacilityResource['frt_id']; ?>">
          <td title="<?php echo $allocatedFacilityResource['f_facility_name'];?>">
            <?php echo $allocatedFacilityResource['f_facility_code'] ?>
          </td>
          <td title="<?php echo $allocatedFacilityResource['frt_facility_resource_type'] ?>">
            <?php echo $allocatedFacilityResource['frt_facility_resource_type_abbr'] ?>
          </td>
          <td>
            <input type="text">
          </td>
        </tr>
        <?php endforeach; ?>
      <?php endif; ?>
      </tbody>
    <?php endforeach; ?>
    </table>
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