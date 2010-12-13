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
      <td><a href="<?php echo url_for('staff/edit?id='.$ag_staff_geo->getId()) ?>"><?php echo $ag_staff_geo->getId() ?></a></td>
      <td><?php

    $siteContacts = $ag_staff_geo;

    $siteContacts = $siteContacts->getAgEntity()->getAgEntityAddressContact();
      foreach ($siteContacts as $siteContact) {
        $address = $siteContact->getAgAddress();
        $formats = $siteContact->getAgAddress()->getAgAddressStandard()->getAgAddressFormat();
        foreach ($formats as $format) {
          $string = '';
          $addressValues = $address->getAgAddressMjAgAddressValue();
          foreach ($addressValues as $addressValue) {
            if ($addressValue->getAgAddressValue()->getAddressElementId() == $format->getAddressElementId()) {
              if ($format->getLineSequence() <> 1 && $format->getInlineSequence() == 1) {
                $string .= ' ';
              }
              $string .= $format->getPreDelimiter() . $addressValue->getAgAddressValue() . $format->getPostDelimiter();
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
      if(isset($fooaddress[0]))
      {
        echo $fooaddress[0];

        $addressfull = new agGis();
        $addressparts['street'] =
        $addressparts = $addressfull->parse_address($fooaddress[0]);

        echo '<a href="' . url_for('gis/geocode') . '?number=' . $addressparts['number'] . '&street=' . $addressparts['street']  . '&zip=' . $addressparts['zip'] . '&address_id=' . $fooaddressid[0] . '">geo</a>';
      }       ?></td>
      <td><?php 
      if(isset($fooaddress[1]))
      {
        echo $fooaddress[1];

        //take the address, parse it into the pieces needed for geocoding
        $addressfull = new agGis();
        $addressparts = $addressfull->parse_address($fooaddress[1]);

        echo '<a href="' . url_for('gis/geocode') . '?number=' . $addressparts['number'] . '&street=' . $addressparts['street']  . '&zip=' . $addressparts['zip'] . '&address_id=' . $fooaddressid[1] . '">geo</a>';
      }?></td>
      <td><?php echo $ag_staff_geo->getUpdatedAt() ?></td>
    </tr>

    <?php
    $fooaddress = "";
    endforeach;
    ?>
  </tbody>
</table>

  <a href="<?php echo url_for('gis/new') ?>">New</a>