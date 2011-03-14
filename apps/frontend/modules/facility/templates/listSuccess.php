<?php
$columns = array(
    'id' => array('title' => 'Id', 'sortable' => false),
    'facility_name' => array('title' => 'Facility Name', 'sortable' => true),
    'services' => array('title' => 'Services', 'sortable' => false),
    'facility_codes' => array('title' => 'Facility Code', 'sortable' => true)
  );

//pager comes in from the action

echo agListForm::facilitylist($sf_request,'Facility Listing', $columns, $pager);
?>