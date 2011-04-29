<?php
  use_javascript('agMain.js');
  use_helper('agTemplate');
?>
<h2>Scenario Facility Groups</h2><br>
<?php
  $wizardOp = array('step' => 3);
  $sf_response->setCookie('wizardOp', json_encode($wizardOp));
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
  $a = $ag_facility_resources;
?>

<h3><?php echo $groupAction ?> Facility Group for the <span class="highlightedText"><?php echo $scenarioName;
?> </span> Scenario</h3>
<p>Facility Groups are actually groupings of facility resources.  To create a facility group name
the group, assign the group type, allocation status, and the order in which is should be activated 
(activation sequence).</p>
<p><strong>Note:</strong> Facility resources should be created in the facility module.  If there are no records
  below use the "Plan" menu above to reach the Facility menu and add your facilities to Agasti.</p>
<div>
  <?php
    include_partial('groupform', array('facilityStatusForm' => $facilityStatusForm ,
                                       'groupform' => $groupform,
                                       'availableFacilityResources' => $availableFacilityResources,
                                       'allocatedFacilityResources' => $allocatedFacilityResources,
                                       'scenario_id' => $scenario_id,
                                       'existingFgroups' => $existingFgroups,
                                       'selectStatuses' => $selectStatuses,
                                       'facilityResourceTypes' => $facilityResourceTypes))
  ?>
</div>
<p>Click "Save" to continue editing this group.  Click "Save and Continue" to save this group and
move to the next step.  Click "Save and Create Another" to save this grouping and create another
grouping.</p>
<strong>Note:</strong> facilities not grouped will not be available for activation when the Scenario is
deployed as an event.