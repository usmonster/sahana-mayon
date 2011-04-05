<?php

//actions.class.php global list actions

//template
(isset($filter)) ? $filterAppend = '&filter=' . $filter : $filterAppend = '';
(isset($sort)) ? $sortAppend = '&sort=' . $sort : $sortAppend = '';
(isset($order)) ? $orderAppend = '&order=' . $order : $orderAppend = '';
?>



<?php
$displayColumns = array(
  'fn' => array('title' => 'First Name', 'sortable' => false),
  'ln' => array('title' => 'Last Name', 'sortable' => false),
  'agency' => array('title' => 'Agency', 'sortable' => true),
  'classification' => array('title' => 'Classification', 'sortable' => true),
  'phones' => array('title' => 'Phone Contact(s)', 'sortable' => true),
  'emails' => array('title' => 'Email Contact(s)', 'sortable' => true),
  'staff_status' => array('title' => 'Status', 'sortable' => false),
);

//pager comes in from the action

include_partial('global/list', array( 'sf_request' => $sf_request,
                                      'displayColumns' => $displayColumns,
                                      'pager' => $pager,
                                      'orderAppend' => $orderAppend,
                                      'sortAppend' => $sortAppend,
                                      'filterAppend' => $filterAppend
  ));

//echo agListForm::eventstafflist($sf_request,'Event Staff Listing', $columns, $pager, $widget);
?>
