<?php
  use_helper('agTemplate');
?>
<h2>Scenario Facility Groups</h2><br>
<?php 
  if(!isset($groupId)) $groupId = 'none';
  $existingFgroups = false;

  if(isset($groupId)){
    if(is_numeric($groupId)){
      $groupAction = 'Edit';
    }
    else{
      $groupAction = 'Create a New';
    }
  }
?>
<h3><?php echo $groupAction ?> Facility Groups for the <span class="highlightedText"><?php echo $scenarioName;
?> </span> Scenario</h3>
<?php
  include_partial('wizard', array('wizardDiv' => $wizardDiv));
?>
<p>Facility Groups are actually groupings of facility resources.  To create a facility group name
the group, assign the group type, allocation status, and the order in which is should be activated 
(activation sequence).</p>
<p><strong>Note:</strong> Facility resources should be created in the facility module.  If there are no records
  below use the "Plan" menu above to reach the Facility menu and add your facilities to Agasti.</p>
<div>
<?php if ($groupSelector != null): ?>
<br />
<!--<p>Use the list below to select existing facility groups for editing.</p>-->
<form class="formSmall" id="groupSelector" action="<?php echo url_for('scenario/fgroup?id=' . $scenario_id); ?>" method="post">
  <?php echo $groupSelector; ?>
  <input type="button" class="linkButton" value="Change" name="Change Group" onclick="reloadGroup(this)" />
</form>
<br />
<?php endif; ?>
  <?php
    include_partial('groupform', array('facilityStatusForm' => $facilityStatusForm ,
                                       'groupform' => $groupform,
                                       'availableFacilityResources' => $availableFacilityResources,
                                       'allocatedFacilityResources' => $allocatedFacilityResources,
                                       'scenario_id' => $scenario_id,
                                       'selectStatuses' => $selectStatuses,
                                       'facilityResourceTypes' => $facilityResourceTypes));
  ?>
</div>
<p>Click "Save" to continue editing this group.  Click "Save and Continue" to save this group and
move to the next step.  Click "Save and Create Another" to save this grouping and create another
grouping.</p>
<strong>Note:</strong> facilities not grouped will not be available for activation when the Scenario is
deployed as an event.
<?php
  $contents = $sf_data->getRaw('facilityResourceTypes');
  echo buildCheckBoxTable($contents, 'id', 'facility_resource_type', 'checkBoxTable checkBoxContainer searchParams', 'revealable', 2, 'facility_resource_type_', 'facility_resource_type_abbr', true, true);
?>