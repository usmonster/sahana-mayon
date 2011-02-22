<?php use_stylesheets_for_form($groupform) ?>
<?php use_javascript('jquery.ui.custom.js'); ?>
<?php //TODO: see if this is still necessary:
use_javascript('tooltip.js'); ?>
<?php use_javascript('json.serialize.js'); ?>
<script type="text/javascript">
  $(function() {
    $("#available li").bind("dblclick", function(){
      return !$(this).remove().appendTo('#allocated')
    });
    $("#allocated li").bind("dblclick", function(){
      return !$(this).remove().appendTo('#available')
    });

    $( "#available, #allocated" ).sortable({
      connectWith: ".bucket"
    }).disableSelection();
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
    <?php }
    echo $groupform;
    ?>
  </div>
  <div class="bucketHolder" >
    <ul id="available" class="bucket">
      <?php
      foreach ($ag_facility_resources as $facility_resource) {
        echo '<li id="' . $facility_resource->getId() . '" title="' .
        $facility_resource->getAgFacilityResourceType() . '">' .
        $facility_resource->getAgFacility()->getFacilityName() . ': ' .
        ucwords($facility_resource->getAgFacilityResourceType()->facility_resource_type) .
        '</li>'; //we could set the id here to a set of ids
      }
      ?>
    </ul>
    <ul id="allocated" class="bucket">
      <?php
      if ($ag_allocated_facility_resources) {

        foreach ($ag_allocated_facility_resources as $facility_resource) {
          echo '<li id="' . $facility_resource->getId() . '" title="' .
          $facility_resource->getAgFacilityResourceType() . '">' .
          $facility_resource->getAgFacility()->getFacilityName() . ': ' .
          ucwords($facility_resource->getAgFacilityResourceType()->facility_resource_type) .
          '</li>'; //we could set the id here to a set of ids
          /**
           * @todo [$curopt->activation_sequence] needs to still be applied to the list,
           */
        }
      }
      ?>
    </ul>
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
  <input class="linkButton" type="submit" value="Save and Assign Staff Requirements to All Facility Groups" name="AssignAll" onclick="serialTran()"/>
  <a href="<?php echo url_for('scenario/staffresources?id=' . $scenario_id) ?>" class="linkButton">Continue</a>
</form>