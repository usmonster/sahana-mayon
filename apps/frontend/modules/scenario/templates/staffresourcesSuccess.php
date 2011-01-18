<script type="text/javascript">
  $(function(){
    $('.groupLabel').click(function(){
      $(this).parent().find('.facgroup').slideToggle("slow");
    });
  });
</script>

          <h3>
            <span>
              Assign minimum and maximum staff resource requirements to facility groups for the
            </span>
            <span class="logName">
    <?php echo $scenario->scenario ?>
        </span>
        <span>
          scenario:
        </span>
      </h3>
      <br />

<?php //include_partial('staffresourceform', array('staffresourceform' => $staffresourceform, 'ag_staff_resources' => $ag_staff_resources, 'scenario' => $scenario, 'formsArray' => $formsArray)) ?>


<?php
          include_partial('staffresourceform', array(
            'formsArray' => $formsArray,
            //'scenarioFacilityGroupId' => $scenarioFacilityGroup->id,
            'array' => $arrayBool,
            'scenario' => $scenario,
              // 'ag_facility_resources' => $ag_facility_resources,
              // 'ag_allocated_facility_resources' => $ag_allocated_facility_resources
              //is this form modified?
          ));
?>

<?php if(isset($scenarioFacilityGroups)){ ?>
<?php foreach ($scenarioFacilityGroups as $facilityGroup): ?>
<table class="singleTable">
  <thead>
      <caption><?php echo $facilityGroup->scenario_facility_group;?></caption>
  </thead>
  <tbody>
    <?php foreach ($facilityGroup->getAgScenarioFacilityResource() as $scenarioFacilityResource): ?>
    <tr>
      <th class="head" colspan="<?php echo (count($scenarioFacilityResource->getAgFacilityStaffResource()) * 3);?>"><?php echo $scenarioFacilityResource->getAgFacilityResource()->getAgFacility()->facility_name . ': ' . ucwords($scenarioFacilityResource->getAgFacilityResource()->getAgFacilityResourceType()->facility_resource_type); ?></th>
    </tr>
    <tr>
      <?php foreach ($scenarioFacilityResource->getAgFacilityStaffResource() as $key => $staffResourceType): ?>
      <th class="<?php echo (($key == 0) ? 'subHeadLeft' : 'subHeadMid'); ?>"><?php echo ucwords($staffResourceType->getAgStaffResourceType()->staff_resource_type); ?></th>
      <td>Min: <?php echo $staffResourceType->minimum_staff; ?></td>
      <td>Max: <?php echo $staffResourceType->maximum_staff; ?></td>
      <?php endforeach; ?>
    </tr>
    <?php endforeach; ?>
  </tbody>
</table>
<br />
<?php endforeach; ?>
<?php
}
?>
<a class=linkButton href="<?php echo url_for('scenario/newshifttemplate?scenId=' . $scenario->id) ?>" title="View Shift Templates">Create Shift Templates for <span style="color: #ff8f00"><?php echo $scenario->scenario; ?></span> Facilities</a>