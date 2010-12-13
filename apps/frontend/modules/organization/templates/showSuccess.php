<script type="text/javascript">
  //On scrolling of DIV tag.

function OnDivScroll()
{
    var staffList = document.getElementById("staffList");

    if (staffList.options.length > 10)
    {
        staffList.size=staffList.options.length;
    }
    else
    {
        staffList.size=10;
    }
}

//On focus of Selectbox

function OnSelectFocus()
{

    if (document.getElementById("divAssocStaff").scrollLeft != 0)
    {
        document.getElementById("divAssocStaff").scrollLeft = 0;
    }

    var staffList = document.getElementById('staffList');

    if( staffList.options.length > 10)
    {
        staffList.focus();
        staffList.size=10;
    }
}
</script>

<noscript><?php echo "Please enable javascript to view list of staffs associating to the organization."; ?></noscript>

<table class="singleTable">
  <thead>
    <caption>Organization</caption>
  </thead>
  <tbody>
    <tr>
      <th class="headLeft">Organization:</th>
      <td><?php echo $ag_organization->getOrganization() ?></td>
    </tr>
    <tr>
      <th class="headLeft">Description:</th>
      <td><?php echo $ag_organization->getDescription() ?></td>
    </tr>
  </tbody>
</table>

<br>

<table class="singleTable">
  <thead>
    <caption>Staff Count</caption>
  </thead>
  <tbody>
<?php foreach($staffResourceTypes as $stfResTypeId => $stfResType): ?>
    <tr>
      <th class="headLeft"><?php echo $stfResType . ':'; ?></th>
      <td>
<?php
  $uniqueStaffCountArray = $sf_data->getRaw('uniqueStaffCount');
  $orgId = $ag_organization->getId();

  if (array_key_exists($orgId, $uniqueStaffCountArray))
  {
    if (array_key_exists($stfResTypeId, $uniqueStaffCountArray[ $orgId ]))
    {
      echo $uniqueStaffCountArray[ $orgId ][ $stfResTypeId ];
    } else {
      echo 0;
    }
  } else {
    echo 0;
  }
?>
      </td>
    </tr>
<?php endforeach; ?>
    <tr>
      <th class="headLeft">Total Unique Staff:</th>
      <td><?php  echo $totalStaffCount; ?></td>
    </tr>
  </tbody>
</table>

<br>
<!--
<div class="infoHolder">
  <h3>Associated Staff Information</h3>
  <ul class="neatlist">
    <li>
-->
<h3>Associated Staff Information</h3>

      <div id='divAssocStaff' style="OVERFLOW: auto; WIDTH: 304px; HEIGHT: 147px" onscroll="OnDivScroll();">
        <SELECT id='staffList' size="10" multiple onfocus="OnSelectFocus();">

<!--
       <select id="staffList" name="staffList" size="10" multiple="multiple">
-->

        <?php
          print_r($staffResource);
          foreach($organizationStaffResources as $orgStfRes)
          {
            if ($orgStfRes['staff_resource_organization_id'] == NULL) {
              echo '<option value="none">none</option>';
              break;
            } else {
              $staffResourceString = $personFullName[ $orgStfRes['person_id'] ] . ' (' . $orgStfRes['staff_id'] . ') : ' . $staffResourceTypes[ $orgStfRes['staff_resource_type_id'] ];
              echo '<option value="' . $orgStfRes['staff_resource_organization_id'] . '">' . $staffResourceString .'</option>';
            }
          }
        ?>
        </SELECT>
      </div>
<!--
    </li>
  </ul>
</div>
-->

<hr />

<a href="<?php echo url_for('organization/edit?id='.$ag_organization->getId()) ?>" class="linkButton" >Edit</a>
&nbsp;
<a href="<?php echo url_for('organization/list') ?>" class="linkButton" >List</a>
