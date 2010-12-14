<h3>Staff GIS List</h3>

<table class="staffTable">
  <thead>
    <tr>
      <th>Staff Member</th>
      <th>Home Address</th>
      <th>Work Address</th>
      <th>Updated at</th>
    </tr>
  </thead>
  <tbody>
    <?php foreach ($ag_staff_geos as $ag_staff_geo): ?>
    <tr>
      <td>
        <a href="<?php echo url_for('staff/edit?id='.$ag_staff_geo->getId()) ?>">
          <?php
          foreach ($ag_person_name_types as $agPersonNameType) {
            $names = $ag_staff_geo->getAgPersonMjAgPersonName();
            foreach ($names as $name) {
              if ($name->getPersonNameTypeId() == $agPersonNameType->getId()) {
                echo $name->getAgPersonName() . ' ';
              }
            }
          }?>
        </a>
      </td>
      <td>
      <?php
      //maybe the below 9 lines should be in actions.class
      //the below lines return a properly ordered array of:
      //street number, street name, city, state, zip

    $siteContacts = $ag_staff_geo;

      $siteContacts = $ag_staff_geo->getAgEntity()->getAgEntityAddressContact();
      foreach ($siteContacts as $siteContact) {
        $address = $siteContact->getAgAddress();
        $addressValues = $address->getAgAddressMjAgAddressValue();
        $formats = $address->getAgAddressStandard()->getAgAddressFormat();
        $staffAddress[$siteContact->getAddressId()] = '';
        foreach ($formats as $format) {
          $string = '';
          $addressValues = $address->getAgAddressMjAgAddressValue();
          foreach ($addressValues as $addressValue) {
            if ($addressValue->getAgAddressValue()->getAddressElementId()  == $format->getAddressElementId()) {
              $staffAddress[$siteContact->getAddressId()][] = $addressValue->getAgAddressValue()->value; //okay, i have to have these in order.
              //the array needs to be reset, crazy concat
            }
          }
          $order[$format->getLineSequence()][$format->getInlineSequence()] = $string;
        }

        $formatted_address = '';
        $address_id = $siteContact->getAddressId();
        foreach ($order as $key => $line) {
          foreach ($line as $key2 => $inLine) {
            $formatted_address .= $order[$key][$key2];
          }
        }

        $fooaddress[] =  $formatted_address;
        //making a second array with the same indices as the above (hackish, needs redo)
        $fooaddressid[] = $address_id;
      }
//we should put a check here for 'work' and 'home' address and only retrieve those, currently it is 0 and 1 (first and second contact
      $addressKeys = array_keys($staffAddress);
      if(isset($staffAddress[$addressKeys[0]]))
      {
        foreach($staffAddress[$addressKeys[0]] as $addressPart){
          echo $addressPart . ' '; //echo the entire address
        }
        $streetnumber = substr($staffAddress[$addressKeys[0]][0], 0,strpos($staffAddress[$addressKeys[0]][0],' '));// agGis::parse_address($staffAddress[$addressKeys[0]][0]);
        //parse address should be better, i want it to return streetname AND number!
        $streetname = substr($staffAddress[$addressKeys[0]][0], strpos($streetnumber['number'], $staffAddress[$addressKeys[0]][0]));
        $zip = $staffAddress[$addressKeys[0]][3];
        echo '<a href="' . url_for('gis/geocode') . '?number=' . $streetnumber['number'] . '&street=' . $streetname  . '&zip=' . $zip . '&address_id=' . $addressKeys[0] . '">geo</a>';
      }?>
      </td>
      <td>
      <?php
        if(isset($staffAddress[$addressKeys[1]]))
        {
          foreach($staffAddress[$addressKeys[1]] as $addressPart){
            echo $addressPart . ' '; //echo the entire address
          }
          $streetnumber = substr($staffAddress[$addressKeys[1]][0], 0,strpos($staffAddress[$addressKeys[1]][0],' '));
          //parse address should be better, i want it to return streetname AND number!
          $streetname = substr($staffAddress[$addressKeys[1]][0], strpos($streetnumber['number'], $staffAddress[$addressKeys[1]][0]));
          $zip = $staffAddress[$addressKeys[1]][3];
          echo '<a href="' . url_for('gis/geocode') . '?number=' . $streetnumber['number'] . '&street=' . $streetname  . '&zip=' . $zip . '&address_id=' . $addressKeys[1] . '">geo</a>';
        }?>
      </td>
      <td><?php echo $ag_staff_geo->getUpdatedAt() ?></td>
    </tr>

    <?php
    endforeach;
    ?>
  </tbody>
</table>

  <a href="<?php echo url_for('gis/new') ?>">New</a>