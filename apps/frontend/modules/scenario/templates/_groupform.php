<?php use_stylesheets_for_form($groupform) ?>
<?php use_javascripts_for_form($groupform) ?>

<script type="text/javascript">
 $().ready(function() {
  $('#up').click(function() {
   return !$($('#ag_scenario_facility_group_ag_facility_resource_order option:selected').prev("option")).before($('#ag_scenario_facility_group_ag_facility_resource_order option:selected'));
  });
  $('#down').click(function() {
   return !$($('#ag_scenario_facility_group_ag_facility_resource_order option:selected').next("option")).after($('#ag_scenario_facility_group_ag_facility_resource_order option:selected'));
  });
  $('#add').click(function() {
   return !$('#ag_scenario_facility_group_ag_facility_resource_list option:selected').remove().appendTo('#ag_scenario_facility_group_ag_facility_resource_order');
  });
  $('#remove').click(function() {
   return !$('#ag_scenario_facility_group_ag_facility_resource_order option:selected').remove().appendTo('#ag_scenario_facility_group_ag_facility_resource_list');
  });
  $('#addall').click(function() {
    return !$('#ag_scenario_facility_group_ag_facility_resource_list').each(function(){
      $('#ag_scenario_facility_group_ag_facility_resource_list option').remove().appendTo('#ag_scenario_facility_group_ag_facility_resource_order');
  });
 });
  $('#removeall').click(function() {
    return !$('#ag_scenario_facility_group_ag_facility_resource_order').each(function(){
      $('#ag_scenario_facility_group_ag_facility_resource_order option').remove().appendTo('#ag_scenario_facility_group_ag_facility_resource_list');
  });
 });
  $('#selectall').click(function() {
    $('#ag_scenario_facility_group_ag_facility_resource_list').each(function(){
      $('#ag_scenario_facility_group_ag_facility_resource_list option').attr("selected","selected");
  });
 });
  $('#selectalloc').click(function() {
    $('#ag_scenario_facility_group_ag_facility_resource_order').each(function(){
      $('#ag_scenario_facility_group_ag_facility_resource_order option').attr("selected","selected");
  });
 });
  $('#deselectalloc').click(function() {
    $('#ag_scenario_facility_group_ag_facility_resource_order').each(function(){
      $('#ag_scenario_facility_group_ag_facility_resource_order option').attr("selected","");
  });
 });
  $('#deselectall').click(function() {
    $('#ag_scenario_facility_group_ag_facility_resource_list').each(function(){
      $('#ag_scenario_facility_group_ag_facility_resource_list option').attr("selected","");
  });
 });
  $('#submiter').click(function() {
    $('#ag_scenario_facility_group_ag_facility_resource_list').each(function(){
      $('#ag_scenario_facility_group_ag_facility_resource_list option').attr("selected","selected");
  });
  $('#ag_scenario_facility_group_ag_facility_resource_order').each(function(){
      $('#ag_scenario_facility_group_ag_facility_resource_order option').attr("selected","selected");
  });
 });
});
</script>

<noscript><?php echo "in order to set the activation sequence of resource facilities and add them to the facility group, you will need javascript enabled"; ?></noscript>

<form name="faciliy_group_form" id="facility_group_form" action="<?php echo url_for('scenario/group'.($groupform->getObject()->isNew() ? 'create' : 'update').(!$groupform->getObject()->isNew() ? '?id='.$groupform->getObject()->getId() : '')) ?>" method="post" <?php $groupform->isMultipart() and print 'enctype="multipart/form-data" ' ?>>

<?php if (!$groupform->getObject()->isNew()): ?>

<input type="hidden" name="sf_method" value="put" />
<?php endif; ?>

      <?php echo $groupform->renderGlobalErrors() ?>
  <?php echo $groupform['id']->renderError();
        echo $groupform['id'];
  ?><br />

<div class="infoHolder">
  <h3>Facility Group Information</h3>
  <ul class="neatlist">
    <li><?php
        echo $groupform['scenario_id']->renderLabel();
        echo $groupform['scenario_id']->renderError();
        echo $groupform['scenario_id'];
        ?>
    </li>
    <li><?php
        echo $groupform['scenario_facility_group']->renderLabel();
        echo $groupform['scenario_facility_group']->renderError();
        echo $groupform['scenario_facility_group'];
        ?>
    </li>
    <li><?php
        echo $groupform['facility_group_type_id']->renderLabel();
        echo $groupform['facility_group_type_id']->renderError();
        echo $groupform['facility_group_type_id'];
        ?>
    </li>
    <li><?php
        echo $groupform['facility_group_allocation_status_id']->renderLabel();
        echo $groupform['facility_group_allocation_status_id']->renderError();
        echo $groupform['facility_group_allocation_status_id'];
        ?>
    </li>
    <li><?php
        echo $groupform['activation_sequence']->renderLabel();
        echo $groupform['activation_sequence']->renderError();
        echo $groupform['activation_sequence'];
        ?>
    </li>
  </ul>
</div>
<div class="infoHolder">
  <h3>Facility Group Information</h3>
  <div style="width: 40%;float:left;display:inline-block;text-align:right;">
  Available Services
  <br />
          <?php echo $groupform['ag_facility_resource_list']->renderError();
                echo $groupform['ag_facility_resource_list'];
          ?>
  </div>
  <div style="width: 40%;float:left;display:inline-block;">
  Assigned Facility Services
  <br />
          <?php echo $groupform['ag_facility_resource_order']->renderError();
                echo $groupform['ag_facility_resource_order'];
          ?>
    <div style="display:inline-block;float:left;text-align:left;">
      <input type="Button" value="&#9652;" id="up"><br/>
      <input type="Button" value="&#9662;" id="down">
    </div>
  </div>
  <br />
  <div style="display:block;text-align:center;clear:both;">
  </div>
  <div style="text-align: center;">
  <input type="Button" value="add &gt;&gt;" id="add">
  <input type="Button" value="remove &lt;&lt;" id="remove">
  <br />
  <input type="Button" value="add all &gt;&gt;" id="addall">
  <input type="Button" value="remove all &lt;&lt;" id="removeall">
  <br />
  <input type="Button" value="select all" id="selectall">
  <input type="Button" value="select allocated" id="selectalloc">
  
  <br />
  <input type="Button" value="deselect all" id="deselectall">
  <input type="Button" value="deselect allocated" id="deselectalloc">
  </div>
</div>

<table>
<tfoot>
      <tr>
        <td colspan="2">
          <?php echo $groupform->renderHiddenFields(false) ?>
          &nbsp;<a href="<?php echo url_for('scenario/listgroup') ?>">Back to facility group list</a>
          <?php if (!$groupform->getObject()->isNew()): ?>
            &nbsp;<?php echo link_to('Delete', 'scenario/deletegroup?id='.$groupform->getObject()->getId(), array('method' => 'delete', 'confirm' => 'Are you sure?')) ?>
          <?php endif; ?>
          <input type="submit" value="Save" id="selecter" onclick="f_selectAll('ag_scenario_facility_group[ag_facility_resource_order][]')"/>
        </td>
      </tr>
    </tfoot>
  </table>
</form>
