
<script type="text/javascript">
  $(function(){
    $('.groupLabel').click(function(){
      $(this).parent().find('.facgroup').slideToggle("fast");
    });
  });
</script>

<script language="javascript" type="text/javascript">
$(document).ready(function() {
    $('.next-column').click(function(event) {
        event.preventDefault();
        $('.table-container').animate({scrollLeft:'+=231'}, 'fast');
    });
    $('.previous-column').click(function(event) {
        event.preventDefault();
        $('.table-container').animate({scrollLeft:'-=231'}, 'fast');
    });

  $(function() {
      $(":input:text").each(function(){
         if( $(this).val() == "")
         {
             $(this).addClass("empty");
             $(this).focus(function(){
                     $(this).removeClass("empty");
                 })
         }
      });
   });
});
</script>


<h2>Staff Resource Requirements</h2><br>
<?php
  include_partial('wizard', array('wizardDiv' => $wizardDiv));
?>
<h3>
  <span>
    Assign minimum and maximum Staff Resource Requirements to Facility Groups for the
  </span>
  <span class="logName">
    <?php echo $scenario->scenario ?>
  </span>
  <span>
    Scenario:
  </span>
</h3>
<br />
<p>Staff Resources are a combination of staff records and their associated skill, called
  'Resources'.  Below, assign minimum and maximum of the staff resource types to the facilities
  you defined in the previous step.</p>
<strong>Note:</strong> If a facility does not require any of a staff resource type leave the min and max
blank.  <strong>A facility resource must have at least one staff resource entered.</strong>

<?php //include_partial('staffresourceform', array('staffresourceform' => $staffresourceform, 'ag_staff_resources' => $ag_staff_resources, 'scenario' => $scenario, 'formsArray' => $formsArray)) ?>


<?php
    include_partial('staffresourceform', array(
      'formsArray' => $formsArray,
      //'scenarioFacilityGroupId' => $scenarioFacilityGroup->id,
      'facilityStaffResourceContainer' => $facilityStaffResourceContainer,
      'array' => $arrayBool,
      'scenario' => $scenario,
    ));

?>
    <br>
    
<p>Click "Save" to save any updates and continue editing on this page.  Click "Create Shift 
  Templates" to save and move to the next step.</p>