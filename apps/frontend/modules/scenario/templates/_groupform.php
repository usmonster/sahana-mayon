<?php use_javascript('json.serialize.js'); ?>
<noscript>in order to set the activation sequence of resource facilities and add them to the
  facility group, you will need javascript enabled</noscript>

<div id="bucketHolder" class="bucketHolder" >
  <form name="faciliy_group_form" id="facility_group_form" action="<?php
echo url_for
    ('scenario/fgroup?id=' . $scenario_id) . (!$groupform->getObject()->isNew() ? '?groupid=' . $groupform->getObject()->getId() : '') ?>" method="post" <?php $groupform->isMultipart() and print 'enctype="multipart/form-data" ' ?>>
    <div class="headerWrap">
      <?php echo $groupform; ?>
    </div>
    <p class="highlightedText">Drag and Drop facilities to add to the group</p>
    <div class="selectionFilter">
      <div><a href="#" id="revealer" title="Available Resource Type Filter">&#9654;</a>Available Resource Type Filter <a href="<?php echo url_for('@wiki'); ?>/doku.php?id=tooltip:resource_type_filter&do=export_xhtmlbody" class="tooltipTrigger" title="Resource Type Filter">?</a></div>
    </div>
    <div class="sortTableContainer">
      <table class="sortTable available" cellspacing="0">
        <thead>
          <caption>Available Facility Resources</caption>
        </thead>
        <tbody>
          <tr>
            <th class="left">Facility Code</th>
            <th>Resource Type</th>
          </tr>
        <?php foreach ($availableFacilityResources as $availableFacilityResource): ?>
          <tr id="facility_resource_id_<?php echo $availableFacilityResource['fr_id']; ?>" class="sort facility_resource_type_<?php echo $availableFacilityResource['frt_id']; ?>">
            <td class="left" title="<?php echo $availableFacilityResource['f_facility_code'];?>">
              <?php echo $availableFacilityResource['f_facility_name'] ?>
            </td>
            <td class="right" title="<?php echo $availableFacilityResource['frt_facility_resource_type_abbr'] ?>">
              <?php echo $availableFacilityResource['frt_facility_resource_type'] ?>
            </td>
          </tr>
        <?php endforeach; ?>
        </tbody>
      </table>
    </div>
    <div class="sortTableContainer">
      <table class="sortTableParent" cellspacing="0">
        <thead>
          <caption>Allocated Facility Resources</caption>
        </thead>
        <?php foreach($selectStatuses as $selectStatus): ?>
        <tr class="sortHead" id="<?php echo $selectStatus['fras_facility_resource_allocation_status']; ?>">
          <th class="left">
            <a href="#" class="<?php echo $selectStatus['fras_facility_resource_allocation_status']; ?>" title="Collapse">&#9660;</a>
            <?php echo ucwords($selectStatus['fras_facility_resource_allocation_status']); ?>
          </th>
          <th class="count right">Count:</th>
        </tr>
        <tr>
          <td colspan="3" class="container">
            <div class="<?php echo $selectStatus['fras_facility_resource_allocation_status']; ?>">
              <table class="sortTable allocated" cellspacing="0">
                <tbody class="<?php echo $selectStatus['fras_facility_resource_allocation_status']; ?>" title="<?php echo $selectStatus['fras_facility_resource_allocation_status']; ?>">
                  <tr>
                    <th class="left">Facility Code</th>
                    <th class="inner">Resource Type</th>
                    <th class="right narrow">Priority</th>
                  </tr>
                <?php if ($allocatedFacilityResources): ?>
                  <?php foreach ($allocatedFacilityResources[$selectStatus['fras_facility_resource_allocation_status']] as $allocatedFacilityResource): ?>
                  <tr id="facility_resource_id_<?php echo $allocatedFacilityResource['fr_id']; ?>" class="sort serialIn facility_resource_type_<?php echo $allocatedFacilityResource['frt_id']; ?>">
                    <td class="left" title="<?php echo $allocatedFacilityResource['f_facility_code'];?>">
                      <?php echo $allocatedFacilityResource['f_facility_name'] ?>
                    </td>
                    <td class="inner" title="<?php echo $allocatedFacilityResource['frt_facility_resource_type_abbr'] ?>">
                      <?php echo $allocatedFacilityResource['frt_facility_resource_type'] ?>
                    </td>
                    <td class="right narrow">
                      <input type="text" class="inputGraySmall" value="<?php echo $allocatedFacilityResource['sfr_activation_sequence']; ?>">
                    </td>
                  </tr>
                  <?php endforeach; ?>
                <?php endif; ?>
                <tbody>
              </table>
            </div>
          </td>
        </tr>
        <?php endforeach; ?>
      </table>
    </div>
    <br />
    <br />
    <input class="continueButton" type="button" value="Save and Create Another" name="Another" onclick="serialTran(this)"/>
    <input class="continueButton" type="button" value="Save and Continue" name="AssignAll" onclick="serialTran(this)"/>
       <p style="width: 670px">Click "Save and Continue" to save this group and move to the next step.
  Click "Save and Create Another" to save this grouping and create another grouping.</p>
     <?php
      if (!$groupform->getObject()->isNew()) {
        echo link_to('Delete', 'scenario/facilityGroupDelete?groupId=' . $groupform->getObject()->getId(), array('method' => 'delete', 'confirm' => 'Are you sure?', 'class' => 'deleteButton'));
      }
    ?>
  </form>
</div>
<br />
<br />
<?php if ($allocatedFacilityResources == true): ?>
  <a href="<?php echo url_for('scenario/staffresources?id=' . $scenario_id) ?>" class="generalButton">Skip & Continue</a>
<?php endif; ?>
  <a href="<?php echo url_for('scenario/listgroup?id=' . $scenario_id); ?>" class="generalButton">Back to Facility Group List</a>
<div class="tooltips" >
  <span id="allocated_tip">
<?php echo "urltowiki/allocated_tooltip"; ?>
  </span>
</div>