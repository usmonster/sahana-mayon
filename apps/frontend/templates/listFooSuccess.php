<?php
$columns = array(
    'id' => array('title' => 'Id', 'sortable' => false),
    'foo' => array('title' => 'Name', 'sortable' => true),
    'bar' => array('title' => 'Drink', 'sortable' => true),
  );

//pager comes in from the action

//echo agListForm::facilitylist($sf_request,'Foo Listing', $columns, $pager);
echo agListForm::foolist($sf_request,'Foo Listing', $columns, $pager);
?>