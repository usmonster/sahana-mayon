<?php
  use_helper('agTemplate');
?>
<h2>Create Facility Groups: <span class="highlightedText"><?php echo $scenarioName ?> </span></h2>

<?php
  include_partial('wizard', array('wizardDiv' => $wizardDiv));
?>

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
<h4><?php echo $groupAction ?> Facility Group for the <span class="highlightedText"><?php echo $scenarioName;
?> </span> Scenario</h4>
<p>Facility Groups are groupings of facility resources.  To create a facility group: name
the group, assign the group type, select the allocation status, and the order in which is
should be activated (the activation sequence).</p>
<p><strong>Note:</strong> Facility resources should be created in the facility module.  If there are no records
  below use the "Prepare" menu above to reach the Facility menu and add your facilities to Sahana Agasti.</p>
<strong>Note:</strong> Facilities not grouped will not be available for activation when the Scenario is
deployed as an event.
<div>
<?php if ($groupSelector != null): ?>
<br />
<!--<p>Use the list below to select existing facility groups for editing.</p>-->
<form class="formSmall" id="groupSelector" action="<?php echo url_for('scenario/fgroup?id=' . $scenario_id); ?>" method="post">
  <?php echo $groupSelector; ?>
  <input type="button" class="generalButton" value="Change" name="Change Group" onclick="reloadGroup(this)" />
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

<?php
  $contents = $sf_data->getRaw('facilityResourceTypes');
  echo buildCheckBoxTable($contents, 'id', 'facility_resource_type', 'checkBoxTable checkBoxContainer searchParams', 'revealable', 2, 'facility_resource_type_', 'facility_resource_type_abbr', true, true);
?>