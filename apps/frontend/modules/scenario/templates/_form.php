<?php use_stylesheets_for_form($form) ?>
<?php use_javascripts_for_form($form) ?>

<script type="text/javascript">
$().ready(function() {
  $('#up').click(function() {
   return !$($('#ag_scenario_scenario_facility_group_new_ag_facility_resource_order option:selected').prev("option")).before($('#ag_scenario_scenario_facility_group_new_ag_facility_resource_order option:selected'));
  });
  $('#down').click(function() {
   return !$($('#ag_scenario_scenario_facility_group_new_ag_facility_resource_order option:selected').next("option")).after($('#ag_scenario_scenario_facility_group_new_ag_facility_resource_order option:selected'));
  });
  $('#add').click(function() {
   return !$('#ag_scenario_scenario_facility_group_new_ag_facility_resource_list option:selected').remove().appendTo('#ag_scenario_scenario_facility_group_new_ag_facility_resource_order');
  });
  $('#remove').click(function() {
   return !$('#ag_scenario_scenario_facility_group_new_ag_facility_resource_order option:selected').remove().appendTo('#ag_scenario_scenario_facility_group_new_ag_facility_resource_list');
  });
  $('#addall').click(function() {
    return !$('#ag_scenario_scenario_facility_group_new_ag_facility_resource_list').each(function(){
      $('#ag_scenario_scenario_facility_group_new_ag_facility_resource_list option').remove().appendTo('#ag_scenario_scenario_facility_group_new_ag_facility_resource_order');
  });
 });
  $('#removeall').click(function() {
    return !$('#ag_scenario_scenario_facility_group_new_ag_facility_resource_order').each(function(){
      $('#ag_scenario_scenario_facility_group_new_ag_facility_resource_order option').remove().appendTo('#ag_scenario_scenario_facility_group_new_ag_facility_resource_list');
  });
 });
  $('#selectall').click(function() {
    $('#ag_scenario_scenario_facility_group_new_ag_facility_resource_list').each(function(){
      $('#ag_scenario_scenario_facility_group_new_ag_facility_resource_list option').attr("selected","selected");
  });
 });
  $('#selectalloc').click(function() {
    $('#ag_scenario_scenario_facility_group_new_ag_facility_resource_order').each(function(){
      $('#ag_scenario_scenario_facility_group_new_ag_facility_resource_order option').attr("selected","selected");
  });
 });
  $('#deselectalloc').click(function() {
    $('#ag_scenario_scenario_facility_group_new_ag_facility_resource_order').each(function(){
      $('#ag_scenario_scenario_facility_group_new_ag_facility_resource_order option').attr("selected","");
  });
 });
  $('#deselectall').click(function() {
    $('#ag_scenario_scenario_facility_group_new_ag_facility_resource_list').each(function(){
      $('#ag_scenario_scenario_facility_group_new_ag_facility_resource_list option').attr("selected","");
  });
 });

 $(".shifts").each(
   function(index) {
     var buttonname = $(this).find("input:first").attr('id').substr(0, $(this).find("input:first").attr('id').length - 15);
     var divname = buttonname;
     $(this).before('<input type="button" value="preview" class="preview" name="' + buttonname.valueOf() + '"\>');
     $(this).after('<div class="shiftpreview" id="' + divname + 'div"></div>');
   });


 $(".preview").click(function() {
    //this gets all of our 'select' elements
    var foofoo = $('select[id*="' + $(this).attr('name') + '"]').each(function(index){
      return $(this).attr('id');
    })
    var barbar =  $('input[id*="' + $(this).attr('name') + '"]').each(function(index){
      return $(this).attr('id');
    })
    var zap = '#' + $(this).attr('name') + 'div';
    $(zap).html('<small>here is a preview of your scenario shifts</small><ul id="' + $(this).attr('name') + 'ul" class="estimate"></ul>');
    var zoo = $(this);
      $.each(foofoo,
      function( intIndex, objValue )
        //make zap be an unordered list inside the div
        { $('#' + zoo.attr('name') + 'ul').append(
        //then just append list items
            $("<li>" + objValue.value + "</li>"));
        //do number crunching here.

        //assume calc is good, how to pass this(ajax?) to doctrine to save?
        }
        );
      $.each(barbar,
      function( intIndex, objValue )
        //make zap be an unordered list inside the div
        { $('#' + zoo.attr('name') + 'ul').append(
        //then just append list items
            $("<li>" + objValue.value + "</li>"));
        //do number crunching here.

        }
        );
    });
  });

</script>

<noscript>in order to work with scenario shifts you will need javascript enabled</noscript>

<form action="<?php echo url_for('scenario/'.($form->getObject()->isNew() ? 'create' : 'update').(!$form->getObject()->isNew() ? '?id='.$form->getObject()->getId() : '')) ?>" method="post" <?php $form->isMultipart() and print 'enctype="multipart/form-data" ' ?>>
<?php if (!$form->getObject()->isNew()): ?>
<input type="hidden" name="sf_method" value="put" />
<?php endif; ?>
  <table>
    <tfoot>
      <tr>
        <td colspan="2">
          <?php echo $form->renderHiddenFields(false) ?>
          &nbsp;<a href="<?php echo url_for('scenario/list') ?>">Back to list</a>
          <?php if (!$form->getObject()->isNew()): ?>
            &nbsp;<?php echo link_to('Delete', 'scenario/delete?id='.$form->getObject()->getId(), array('method' => 'delete', 'confirm' => 'Are you sure?')) ?>
          <?php endif; ?>
          <input type="submit" value="Save" />
        </td>
      </tr>
    </tfoot>
    <tbody>
      <tr>
        <td>
      <?php echo $form ?>
        </td>
      <?php
//      if(isset($ag_scenario_facility_groups))
//      {
//        if(count($ag_scenario_facility_groups) < 1)
//        {
//          echo "<td>";
//          echo $groupform;
//          echo "</td>";
//        }
//      }
      ?>



      </tr>

    </tbody>
  </table>
</form>



<?php
//ALL OF THESE BUTTONS NEED TO BE CREATED 'on the fly' via javascript...
//for each facility group form
?>
    <div style="display:inline-block;float:left;text-align:left;">
      <input type="Button" value="&#9652;" id="up"><br/>
      <input type="Button" value="&#9662;" id="down">
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
