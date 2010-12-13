<h3>Edit Scenario</h3>
<?php

include_partial('form', array('form' => $form, 'ag_scenario_facility_groups' => $ag_scenario_facility_groups));
//if($ag_scenario_facility_groups->count() < 1)
//{
//  echo "In order to continue this process, you need to <a href=" . url_for('scenario/newgroup') . ">define at least one facility group</a> for this scenario.
//<br />After you have defined the facility groups you wish to use for this scenario, you will be able to create a series of shift templates.
//<br />These shift templates will then be used to generate the staff schedules for each of your facilities, for a type of staff member." ;
//
//echo "<h3>Edit Facility Group</h3>";
//
//include_partial('groupform', array('groupform' => $groupform));
//
//}

?>

