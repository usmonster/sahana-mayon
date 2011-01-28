<h3>Create New Shift Template
  <span>
    Create new shift template for
  </span>
  <span class="logName">
    <?php echo $scenario_id ?>
  </span>
  <span>
    :
  </span>
</h3>
<br />
<?php 


  include_partial('shifttemplateform', array('formsArray' => $formsArray, 'shifttemplateform' => $shifttemplateform, 'scenario_id' => $scenario_id))
?>



