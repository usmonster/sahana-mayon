<h2>*Name of Event* Facilities Management</h2>
<?php
//note for devs: anytime you see *Name Of Event* it's my placeholder for where you should make
//the app display the name of the event the user is working in.
//
// This page is currently a stub!  The following random string is a marker for the stub.
// PnaODfcm3Kiz4MV4vzbtr4
// PLEASE REMOVE THIS COMMENT BLOCK WHEN YOU DEVELOP THIS PAGE!
?>

<h3>*Group Name*</h3>
<p>*Here will be a list of the facilities in the group with reports on the status of the facilities,
  staff count, client count, and totals.  It's been floated that the percentage of the totals
  should also be included.  Clicking the name of the facility brings you to facilitySuccess.</p>

<?php
//and a button to Return to Group List & one to Return to Event Dashboard.
?>
<h3>Current Facilities for the <span style="color: #ff8f00"><?php echo $eventFacilityGroup->event_facility_group; ?></span></h3><br>
<table class="singleTable">
  <tbody>
    <tr>
      <th><?php echo $eventFacilityGroup->event_facility_group . ' Facilities'; ?></th>
    </tr>
    <?php
      foreach ($eventFacilityGroup->getAgFacilityResource() as $facilityResource) {
        echo '<tr>';
        echo '<th class="head">' . $facilityResource->getAgFacility()->facility_name . ': ' . ucwords($facilityResource->getAgFacilityResourceType()->facility_resource_type) . '</th>';
//        echo '<td>' .
        echo '</tr>';
        
      }
    ?>
  </tbody>
</table>
<br />

