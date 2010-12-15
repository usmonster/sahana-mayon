<h3>Calculate Distances</h3>
<?php include_partial('form', array('form' => $form)) ?>


listbox 1 should be all staff members (maybe pared down by agency/other criteria)

listbox 2 should be list of all facilities.



Drag from here...<br />
<div style="display:inline;">
<ul class="drag" style="display:inline-block;display: inline-block;width:200px;height:200px;border:1px solid #ccc;background:#ccc;">
<?php foreach($ag_facility_geos as $staff_geo){
  echo "<li>" . $staff_geo->getFacilityName() . "</li>"; //we could set the id here to a set of ids
}
?>
</ul>
<ul id="sort" style="display: inline-block;width:200px;height:200px;border:1px solid #ccc;background:#ccc;">
</ul>
</div>
<script type="text/javascript">
<!-- we run in the footer so no need to use onload -->
jQuery('.drag > li').draggable({helper:'original',connectToSortable:'#sort'});
jQuery('#sort').sortable();
</script>
<?php //for shirley's purposes, we need to disable items from a list that already exist in the other list. ?>