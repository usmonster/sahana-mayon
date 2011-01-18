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


          //the URL below should only appear if we have filled out this sufficiently
          ?>



      <a class=linkButton href="<?php echo url_for('scenario/newshifttemplate?scenId=' . $scenario->id) ?>" title="View Shift Templates">Create Shift Templates for <span style="color: #ff8f00"><?php echo $scenario->scenario; ?></span> Facilities</a>