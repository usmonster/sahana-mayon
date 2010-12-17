
  <style>
    #facility li{
      border:1px solid #000;
      background:#e5f7ff;
      padding: 0.1em;
      margin: 1px 1px 1px 1px;
    }
    .bucket{
      font-size: 1em;
      display:inline-block;
      float:left;
      width:400px;
      height:600px;
      border:1px solid #000;
      background:#ccc;
      list-style-type: none;
      margin: 0;
      padding: 0;

    }
    #staff li{
      border:1px solid #000;
      background:#e5f7ff;
      margin: 1px 1px 1px 1px;
    }
  </style>
<h3>Calculate Distances</h3>

listbox 1 should be all staff members (maybe pared down by agency/other criteria)

listbox 2 should be list of all facilities.<br/>
<?php include_partial('distanceform', array('distanceform' => $distanceform)) ?>
<div class="infoHolder">

  <h3>Facility Group Information</h3>
  <div style="display:inline;">
  <h4>Staff Members(home and work)</h4>
    <ul id="staff" class="bucket">

      <?php
      $names = agPerson::getPersonFullName();

      foreach($ag_staff_geos as $staff_geo){
        echo "<li id=" . $staff_geo->getId() .">" . $names[$staff_geo->getId()]. "</li>"; //we could set the id here to a set of ids
      }
      ?>
    </ul>
    <h4>Facilities(Possible Destination Sites)</h4>
    <ul id="facility" class="bucket">
     <?php $currentoptions = array();
          //if (is_array($ag_allocated_facility_resources)){
            foreach($ag_facility_geos as $curopt)
            {
              /**
               * @todo should i even be bothering with the facility id, or just use the site id?
               */
              echo "<li id=" . $curopt->id .">" . $curopt->facility_name . "</li>"; //we could set the id here to a set of ids
            }
          //}//sweepstakes scratch that, the order they come in = the order they are getting activated.
      ?>
    </ul>
  </div>
  </div>