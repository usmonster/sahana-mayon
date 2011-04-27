<?php
   use_javascript('jquery.ui.custom.js');
  use_stylesheet('jquery/jquery.ui.custom.css');
  use_javascript('agTooltip.js');

  
//actions.class.php global list actions
//template

($sf_request->getGetParameter('status')) ? $this->statusAppend = '?status=' . $sf_request->getGetParameter('status') : $this->statusAppend = '?status=active';
($sf_request->getGetParameter('sort')) ? $this->sortAppend = '&sort=' . $sf_request->getGetParameter('sort') : $this->sortAppend = '';
($sf_request->getGetParameter('order')) ? $this->orderAppend = '&order=' . $sf_request->getGetParameter('order') : $this->orderAppend = '';
//this piece here could be refactored, the above 3 parsed lines

?>

<?php

$statusTooltip = url_for('@wiki') . '/doku.php?id=tooltip:staff_list_resource_status&do=export_xhtmlbody';

$displayColumns = array(
  'fn' => array('title' => 'First Name', 'sortable' => false),
  'ln' => array('title' => 'Last Name', 'sortable' => false),
  'agency' => array('title' => 'Agency', 'sortable' => true),
  'classification' => array('title' => 'Classification', 'sortable' => true),
  'phones' => array('title' => 'Phone Contact(s)', 'sortable' => false),
  'emails' => array('title' => 'Email Contact(s)', 'sortable' => false),
  'staff_status' => array('title' => 'Status', 'sortable' => false, 'tooltip' => $statusTooltip),
);

//pager comes in from the action

include_partial('global/list', array('sf_request' => $sf_request,
  'displayColumns' => $displayColumns,
  'pager' => $pager,
  'order' => $order,
  'sort' => $sort,
  'status' => $status,
  'target_module' => 'staff',
  'caption' => 'Staff List',
  'widgets' => array($statusWidget)
));

//echo agListForm::eventstafflist($sf_request,'Event Staff Listing', $columns, $pager, $widget);
?>
