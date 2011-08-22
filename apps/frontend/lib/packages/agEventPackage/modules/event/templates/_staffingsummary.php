<?php

$results = $sf_data->getRaw('results');
if (!empty($results))
{
 // print_r($results);


  
  foreach( $results as $efg_id => $groupinfo)
  {  echo "<div style=\"background-color:#ccc; display:inline-block;\">";
     echo "<div style=\"display:inline-block;padding:10px 10px 5px; font-size:17px; color:#fff; font-weight:bold \"> Facility Group: ".$groupinfo['group_name'] . "</div><div style=\"display:inline-block;float:right; padding:10px 10px 5px; font-size:17px; color:#fff; font-weight:bold \">Status: ".  $groupinfo['group_status']. "</div>";
     echo '<br>';

     foreach($groupinfo['facilities'] as  $efr_id => $facilityinfo)
     {  echo "<div style=\"background-color:#d8d8d8; display:inline-block; margin:3px 10px;padding:5px;width:auto; border:1px solid #fff; \">";
    // echo $groupinfo['group_name'] . "    ".  $groupinfo['group_status'];
    // echo '<br>';
        echo "<div style=\"display:inline-block; text-align:left;padding:5px 20px 5px 5px;\">Facility: ". $facilityinfo['facility_type'] . "-".  $facilityinfo['facility_code'] . "</div><div style=\"display:inline-block;padding:5px 20px 5px 10px; \">Facility Name: ".  $facilityinfo['facility_name'] . "</div><div style=\"display:inline-block;padding:5px; float:right \">Facility Status: ".  $facilityinfo['facility_status']. "</div>";
        echo '<br>';

        echo "<table class=\"blueTable\" cellpadding=\"5\" style=\"width:auto\">";
        echo "<tr class=\"head\"><th>Staff Type</th><th>Staff Count</th><th>Min / Max Staff</th><th>Shift Status</th><th>Staff Wave</th><th>Shift Start</th><th>Break Start</th><th>Shift End</th><th>Time Zone</th></tr>";
        foreach($facilityinfo['shifts'] as $es_id => $shiftinfo)
        {


          if($shiftinfo['shift_disabled'])
          {
             echo "<tr style=\"background-color:#DAA520; padding:5px;  \">";
          }
          else if($shiftinfo['shift_standby']){
             echo "<tr style=\"background-color:#FFE4B5; padding:5px;  \">";
          }
          else
          {
             echo "<tr style=\"background-color:#fff; padding:5px;  \">";
          }


          echo  "<td>".$shiftinfo['staff_type']. "</td><td><span ";

          if($shiftinfo['staff_count'] == $shiftinfo['maximum_staff'])
          {
            echo "style=\"font-weight:bold; color:dark green\">";
          }
          else if(($shiftinfo['staff_count'] >= $shiftinfo['minimum_staff']) && $shiftinfo['staff_count'] < $shiftinfo['maximum_staff'])
          {
            echo "style=\"font-weight:bold; color:light green\">";
          }
          else if($shiftinfo['staff_count'] < $shiftinfo['minimum_staff'])
          {
            echo "style=\"font-weight:bold; color:red\">";
          }

         echo $shiftinfo['staff_count'];
          
           echo "</span></td><td>".$shiftinfo['minimum_staff']. "/".$shiftinfo['maximum_staff']. "</td><td>".$shiftinfo['shift_status']. "</td><td>";
          echo $shiftinfo['staff_wave']. "</td><td>".$shiftinfo['shift_start']. "</td><td>".$shiftinfo['break_start']. "</td><td>".$shiftinfo['shift_end']. "</td><td>".$shiftinfo['timezone']."</td>";
          echo '</tr>';

        }
        echo "</table>";






        echo '</div><br><br> ';


     }

    echo '</div><br><br><br>';

  }
  

  
}
//print_r($sf_data->getRaw('results'));

?>
