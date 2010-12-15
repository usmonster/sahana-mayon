<?php use_stylesheets_for_form($form) ?>
<?php use_javascripts_for_form($form) ?>

<script type="text/javascript">
   $().ready(function() {
  $('#add').click(function() {
   return !$('#ag_staff_resource_organization_ag_staff_resource_non_org_list option:selected').remove().appendTo('#ag_staff_resource_organization_ag_staff_resource_organization_list');
  });
  $('#remove').click(function() {
   return !$('#ag_staff_resource_organization_ag_staff_resource_organization_list option:selected').remove().appendTo('#ag_staff_resource_organization_ag_staff_resource_non_org_list');
  });
  $('#addall').click(function() {
    return !$('#ag_staff_resource_organization_ag_staff_resource_organization_list').each(function(){
      $('#ag_staff_resource_organization_ag_staff_resource_non_org_list option').remove().appendTo('#ag_staff_resource_organization_ag_staff_resource_organization_list');
  });
 });
  $('#removeall').click(function() {
    return !$('#ag_staff_resource_organization_ag_staff_resource_organization_list').each(function(){
      $('#ag_staff_resource_organization_ag_staff_resource_organization_list option').remove().appendTo('#ag_staff_resource_organization_ag_staff_resource_non_org_list');
  });
 });
  $('#selectall').click(function() {
    $('#ag_staff_resource_organization_ag_staff_resource_non_org_list').each(function(){
      $('#ag_staff_resource_organization_ag_staff_resource_non_org_list option').attr("selected","selected");
  });
 });
  $('#deselectall').click(function() {
    $('#ag_staff_resource_organization_ag_staff_resource_non_org_list').each(function(){
      $('#ag_staff_resource_organization_ag_staff_resource_non_org_list option').attr("selected","");
  });
 });
  $('#submiter').click(function() {
    $('#ag_staff_resource_organization_ag_staff_resource_organization_list').each(function(){
      $('#ag_staff_resource_organization_ag_staff_resource_organization_list option').attr("selected","selected");
  });
  $('#ag_staff_resource_organization_ag_staff_resource_non_org_list').each(function(){
      $('#ag_staff_resource_organization_ag_staff_resource_non_org_list option').attr("selected","selected");
  });
 });
});

</script>


<noscript><?php echo "Please enable javascript to edit organization."; ?></noscript>


<form name="organization_form" id="organization_form" action="<?php echo url_for('organization/'.($form->getObject()->isNew() ? 'create' : 'update').(!$form->getObject()->isNew() ? '?id='.$form->getObject()->getId() : '')) ?>" method="post" <?php $form->isMultipart() and print 'enctype="multipart/form-data" ' ?>>
  <?php if (!$form->getObject()->isNew()): ?>
    <input type="hidden" name="sf_method" value="put" />

  <?php
    echo $form->renderGlobalErrors();
    echo $form['id']->renderError();
    echo $form['id'];
  ?>
  <?php endif; ?>

  <div class="infoHolder">
    <h3>Organization Information</h3>
    <ul class="neatlist">
      <li>
        <?php
          echo $form['organization']->renderLabel();
          echo $form['organization']->renderError();
          echo $form['organization'];
        ?>
      </li>
      <li>
        <?php
          echo $form['description']->renderLabel();
          echo $form['description']->renderError();
          echo $form['description'];
        ?>
      </li>
    </ul>
  </div>

<!--
  <?php #if (!$form->getObject()->isNew()): ?>
  <div class="infoHolder">
    <h3>Staff Resource Information</h3>

    <div style="width: 45%;float:left;display:inline-block;text-align:right;BORDER-RIGHT: #FFFFFF 20px solid;">
      Available Staff Resources
      <br />
      <?php
        #echo $form['ag_staff_resource_non_org_list']->renderError();
        #echo $form['ag_staff_resource_non_org_list'];
      ?>
    </div>

    <div style="width: 45%;float:left;display:inline-block;text-align: left">
      Assigned Staff Resources
      <br />
      <?php
        #echo $form['ag_staff_resource_organization_list']->renderError();
        #echo $form['ag_staff_resource_organization_list'];
      ?>
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
      <input type="Button" value="deselect all" id="deselectall">
    </div>
  </div>
  <?php #endif; ?>
-->

  <br />

  <table>
    <tfoot>
      <tr>
        <td colspan="2">
          <?php echo $form->renderHiddenFields(false) ?>
          &nbsp;<a href="<?php echo url_for('organization/list') ?>" class="linkButton">Back to list</a>
          <?php if (!$form->getObject()->isNew()): ?>
            &nbsp;<?php echo link_to('Delete', 'organization/delete?id='.$form->getObject()->getId(), array('method' => 'delete', 'confirm' => 'Are you sure?', 'class' => 'linkButton')) ?>
          <?php endif; ?>
          <input type="submit" value="Save" class="linkButton" />
        </td>
      </tr>
    </tfoot>
  </table>
</form>
