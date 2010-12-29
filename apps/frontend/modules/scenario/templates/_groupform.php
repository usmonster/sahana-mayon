<?php use_stylesheets_for_form($groupform) ?>
<?php use_helper('jQuery'); ?>
<?php jq_add_plugins_by_name(array('ui')) ?>
<?php use_javascript('sortList'); ?>
<?php use_javascript('dimensions');?>
<?php use_javascript('tooltip');?>
<?php use_javascript('json.serialize'); ?>
<script type="text/javascript">
	$(function() {
		$( "#available, #allocated" ).sortable({
			connectWith: ".bucket"
		}).disableSelection();
	});

jQuery('#available > li').tooltip();
jQuery('#allocated > li').tooltip({
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
<noscript>in order to set the activation sequence of resource facilities and add them to the facility group, you will need javascript enabled</noscript>

<form name="faciliy_group_form" id="facility_group_form" action="<?php echo url_for('scenario/group'.($groupform->getObject()->isNew() ? 'create' : 'update').(!$groupform->getObject()->isNew() ? '?id='.$groupform->getObject()->getId() : '')) ?>" method="post" <?php $groupform->isMultipart() and print 'enctype="multipart/form-data" ' ?>>

<?php if (!$groupform->getObject()->isNew()): ?>

<input type="hidden" name="sf_method" value="put" />
<?php endif; ?>

<?php 
  $groupFormDeco = new agWidgetFormSchemaFormatterInlineLabels($groupform->getWidgetSchema());
  $groupform->getWidgetSchema()->addFormFormatter('groupFormDeco', $groupFormDeco);
  $groupform->getWidgetSchema()->setFormFormatterName('groupFormDeco');
  echo $groupform;
?>

<div class="infoHolder" style="display:inline-block;">
  <ul id="available" class="bucket">
    <?php
      foreach($ag_facility_resources as $staff_geo){
        echo '<li id="' . $staff_geo->getId() .'" title="' . $staff_geo->getAgFacilityResourceType() . '">' . $staff_geo->getAgFacility()->getFacilityName() . ' : ' . $staff_geo->getAgFacilityResourceType()->facility_resource_type . '</li>'; //we could set the id here to a set of ids
      }
    ?>
  </ul>
  <ul id="allocated" class="bucket">
   <?php $currentoptions = array();
        if ($ag_allocated_facility_resources){
          foreach($ag_allocated_facility_resources as $curopt)
          {
            $currentoptions[$curopt->facility_resource_id] = $curopt->getAgFacilityResource()->getAgFacility()->facility_name . ": " . $curopt->getAgFacilityResource()->getAgFacilityResourceType()->facility_resource_type; //$curopt->getAgFacility()->facility_name . " : " . $curopt->getAgFacilityResourceType()->facility_resource_type;
            /**
             * @todo [$curopt->activation_sequence] needs to still be applied to the list,
             */

            echo "<li id=" . $curopt->facility_resource_id .">" . $currentoptions[$curopt->facility_resource_id] . "</li>"; //we could set the id here to a set of ids
          }
        }
    ?>
  </ul>
</div>

<?php //for shirley's purposes, we need to disable items from a list that already exist in the other list. ?>
<br/>
<div id="tooltips" style="display:none;">
  <span id="allocated_tip">
  <?php echo "urltowiki/allocated_tooltip";?>
  </span>
</div>
<br />
<a href="<?php echo url_for('scenario/listgroup') ?>" class="linkButton">Back to Facility Group List</a>
<?php if (!$groupform->getObject()->isNew()): ?>
  &nbsp;<?php echo link_to('Delete', 'scenario/deletegroup?id='.$groupform->getObject()->getId(), array('method' => 'delete', 'confirm' => 'Are you sure?')) ?>
<?php endif; ?>
<input class="linkButton" type="submit" value="Save" id="selecter" onclick="serialTran()"/>
<input class="linkButton" type="submit" value="Save and Continue" name="Continue" onclick="serialTran()"/>
</form>
