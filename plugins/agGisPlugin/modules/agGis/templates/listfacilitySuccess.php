<h3>Facility GIS List</h3>

<table class="staffTable">
  <thead>
    <tr>
      <th>Facility</th>
      <th>Address</th>
      <th>Updated at</th>
    </tr>
  </thead>
  <tbody>
    <?php foreach ($ag_facility_geos as $ag_facility_geo): ?>
    <tr>
      <td>
        <a href="<?php echo url_for('facility/show?id='.$ag_facility_geo->getId()) ?>">
        <?php    echo $ag_facility_geo->getFacilityName()         ?>
        </a>
      </td>
      <td>
      <?php
      //maybe the below 9 lines should be in actions.class
      //the below lines return a properly ordered array of:
      //street number, street name, city, state, zip

      $siteContacts = $ag_facility_geo->getAgSite()->getAgEntity()->getAgEntityAddressContact();
      foreach ($siteContacts as $siteContact) {
        $address = $siteContact->getAgAddress();
        $addressValues = $address->getAgAddressMjAgAddressValue();
        $formats = $address->getAgAddressStandard()->getAgAddressFormat();
        $facilityAddress[$siteContact->getAddressId()] = '';
        foreach ($formats as $format) {
          $string = '';
          $addressValues = $address->getAgAddressMjAgAddressValue();
          foreach ($addressValues as $addressValue) {
            if ($addressValue->getAgAddressValue()->getAddressElementId()  == $format->getAddressElementId()) {
              $facilityAddress[$siteContact->getAddressId()][] = $addressValue->getAgAddressValue()->value; //okay, i have to have these in order.
              //the array needs to be reset, crazy concat
            }
          }
          $order[$format->getLineSequence()][$format->getInlineSequence()] = $string;
        }
      }
      //we should put a check here for 'work' and 'home' address and only retrieve those, currently it is 0 and 1 (first and second contact
      $addressKeys = array_keys($facilityAddress);
      if(isset($facilityAddress[$addressKeys[0]]))
      {
        foreach($facilityAddress[$addressKeys[0]] as $addressPart){
          echo $addressPart . ' '; //echo the entire address
        }
        $streetnumber = substr($facilityAddress[$addressKeys[0]][0], 0,strpos($facilityAddress[$addressKeys[0]][0],' '));// agGis::parse_address($facilityAddress[$addressKeys[0]][0]);
        //parse address should be better, i want it to return streetname AND number!
        $streetname = substr($facilityAddress[$addressKeys[0]][0], strpos($streetnumber['number'], $facilityAddress[$addressKeys[0]][0]));
        $zip = $facilityAddress[$addressKeys[0]][3];
        echo '<a class=linkButton href="' . url_for('gis/geocode') . '?number=' . $streetnumber['number'] . '&street=' . $streetname  . '&zip=' . $zip . '&address_id=' . $addressKeys[0] . '">geo</a>';
      }?>
      </td>
      <td><?php echo $ag_facility_geo->getUpdatedAt() ?></td>
    </tr>

    <?php
    endforeach;
    ?>
  </tbody>
</table>

  <a class="linkButton" href="<?php echo url_for('gis/new') ?>">New</a>