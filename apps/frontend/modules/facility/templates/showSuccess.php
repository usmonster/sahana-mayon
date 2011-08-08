<table class="singleTable">
  <thead>
  <caption>Facility</caption>
</thead>
<tbody>
  <tr>
    <th class="headLeft">Facility Name:</th>
    <td><?php echo $ag_facility->getFacilityName() ?></td>
  </tr>
</tbody>
</table>

<br>

<table class="singleTable">
  <thead>
  <caption>Facility Resources</caption>
</thead>
<tbody>
  <tr>
    <th class="headLeft">Facility Resource Type</th>
    <th class="headLeft">Facility Code</th>
    <th class="subHead">Status</th>
    <th class="subHead">Description</th>
    <th class="subHead">Capacity</th>
  </tr>
  <?php foreach ($ag_facility->getAgFacilityResource() as $ag_facility_resource): ?>
    <tr>
      <th class="subHead"><?php echo ucwords($ag_facility_resource->getAgFacilityResourceType()->getFacilityResourceType()) ?></th>
      <th class="subHead"><?php echo ucwords($ag_facility_resource->getAgFacility()->getFacilityCode()) ?></th>
      <td><?php echo $ag_facility_resource->getAgFacilityResourceStatus()->getFacilityResourceStatus() ?></td>
      <td><?php echo $ag_facility_resource->getAgFacilityResourceType() ?></td>
      <td><?php echo $ag_facility_resource->getCapacity() ?></td>
    </tr>
  <?php endforeach; ?>
  </tbody>
  </table>

  <br>

  <table class="singleTable">
    <thead>
    <caption>Contact&nbsp;Information</caption>
  </thead>
  </table>

  <table class="singleTable">
    <tbody>
      <tr>
        <th class="headLeft" rowspan="2">Phone:</th>
      <?php
      foreach ($ag_phone_contact_types as $ag_phone_contact_type) {
        echo '<th class="subHead">' . ucwords($ag_phone_contact_type->getPhoneContactType()) . '</th>';
      }
      ?>
    </tr>
    <tr>
      <?php
      foreach ($ag_phone_contact_types as $ag_phone_contact_type) {
        echo "<td>";
        $check = 0;
        $phoneContacts = $ag_facility->getAgSite()->getAgEntity()->getAgEntityPhoneContact();
        foreach ($phoneContacts as $phoneContact) {
          if ($phoneContact->getPhoneContactTypeId() == $ag_phone_contact_type->getId()) {
            echo preg_replace(
                $phoneContact->getAgPhoneContact()->getAgPhoneFormat()->getAgPhoneFormatType()->match_pattern,
                $phoneContact->getAgPhoneContact()->getAgPhoneFormat()->getAgPhoneFormatType()->replacement_pattern,
                $phoneContact->getAgPhoneContact()->phone_contact);
            echo '<br />';
            $check = 1;
          }
        }
        if ($check == 0) {
          echo "----";
        }
        echo "</td>";
      }
      ?>
    </tr>
  </tbody>
</table>

<br>

<table class="singleTable">
  <tbody>
    <tr>
      <th class="headLeft" rowspan="2">Email:</th>
      <?php
      foreach ($ag_email_contact_types as $ag_email_contact_type) {
        echo '<th class="subHead">' . ucwords($ag_email_contact_type->getEmailContactType()) . '</th>';
      }
      ?>
    </tr>
    <tr>
      <?php
      foreach ($ag_email_contact_types as $ag_email_contact_type) {
        echo "<td>";
        $check = 0;
        $emailContacts = $ag_facility->getAgSite()->getAgEntity()->getAgEntityEmailContact();
        foreach ($emailContacts as $emailContact) {
          if ($emailContact->getEmailContactTypeId() == $ag_email_contact_type->getId()) {
            echo '<a href="mailto:' . $emailContact->getAgEmailContact() . '" class="linkText">' . $emailContact->getAgEmailContact() . '</a>' . '<br />';
            $check = 1;
          }
        }
        if ($check == 0) {
          echo "----";
        }
        echo "</td>";
      }
      ?>
    </tr>
  <tbody>
</table>

<br>

<table class="singleTable">
  <thead><caption>Address</caption></thead>
<tbody>
  <?php
      echo '<tr>';
      foreach ($ag_address_contact_types as $ag_address_contact_type) {
        echo '<th class="head">' . ucwords($ag_address_contact_type->getAddressContactType()) . '</th>';
      }
      echo '</tr>';

      $siteContacts = $ag_facility->getAgSite()->getAgEntity()->getAgEntityAddressContact();
      foreach ($siteContacts as $siteContact) {
        $address = $siteContact->getAgAddress();
        $formats = $siteContact->getAgAddress()->getAgAddressStandard()->getAgAddressFormat();
        foreach ($formats as $format) {
          $string = '';

          $addressValues = $address->getAgAddressMjAgAddressValue();
          foreach ($addressValues as $addressValue) {
            if ($addressValue->getAgAddressValue()->getAddressElementId() == $format->getAddressElementId()) {
              if ($format->getLineSequence() <> 1 && $format->getInlineSequence() == 1) {
                $string .= '<br />';
              }
              $string .= $format->getPreDelimiter() . $addressValue->getAgAddressValue() . $format->getPostDelimiter();
            }
          }
          $order[$format->getLineSequence()][$format->getInlineSequence()] = $string;
        }

        $formatted_address = '';
        foreach ($order as $key => $line) {
          foreach ($line as $key2 => $inLine) {
            $formatted_address .= $order[$key][$key2];
          }
        }
        $arrangeAddresses[$siteContact->getAddressContactTypeId()][$siteContact->getPriority()] = $formatted_address;
      }

      if (isset($arrangeAddresses) == 'true') {
        # Reassigning second key values in double array.
        $rowMax = 0;
        foreach ($arrangeAddresses as $key => $innerArray) {
          ksort($innerArray);
          $arrayMax = count($innerArray);
          if ($arrayMax > $rowMax) {
            $rowMax = $arrayMax;
          }
          $reAssignKeyValue = array();
          foreach ($innerArray as $a) {
            array_push($reAssignKeyValue, $a);
          }
          $arrangeAddresses[$key] = $reAssignKeyValue;
        }

        # Displaying address per row per column.
        for ($rowCount = 0; $rowCount < $rowMax; $rowCount++) {
          echo '<tr>';
          $columnCount = 0;
          foreach ($ag_address_contact_types as $ag_address_contact_type) {
            $addressTypeId = $ag_address_contact_type->getId();
            if (array_key_exists($addressTypeId, $arrangeAddresses)) {
              $addressForType = $arrangeAddresses[$addressTypeId];
              if (array_key_exists($rowCount, $addressForType)) {
                echo '<td>' . $addressForType[$rowCount] . '</td>';
              } else {
                echo '<td>---</td>';
              }
            } else {
              echo '<td>---</td>';
            }
            $columnCount++;
          }
          echo '</tr>';
        }
      } else {
        echo '<tr>';
        foreach ($ag_address_contact_types as $ag_address_contact_type) {
          echo '<td>---</td>';
        }
        echo '</tr>';
      }
  ?>
    </tbody>
    </table>

    <br />

    <table class="singleTable">
      <thead>
      <caption>Administrative</caption>
    </thead>
    <tbody>
      <tr>
        <th class="headLeft">Id:</th>
        <td><?php echo $ag_facility->getId() ?></td>
      </tr>
      <tr>
        <th class="headLeft">Created at:</th>
        <td><?php echo $ag_facility->getCreatedAt() ?></td>
      </tr>
      <tr>
        <th class="headLeft">Updated at:</th>
        <td><?php echo $ag_facility->getUpdatedAt() ?></td>
      </tr>
    </tbody>
    </table>

    <hr class="ruleGray" />
<?php echo link_to('Edit', 'facility/edit?id=' . $ag_facility->getId(), array('class' => 'continueButton')); ?>
      &nbsp;
<?php echo link_to('List', 'facility/list', array('class' => 'generalButton')); ?>
