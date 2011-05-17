<?php
//actions.class.php global list actions

//template
(isset($status)) ? $status = $status : $status = 'active';
(isset($sort)) ? $sort = $sort : $sort = '';
(isset($order)) ? $order = $order : $order = '';
?>



<?php

if($sf_request->getParameter('module') == 'agStaff' || $sf_request->getParameter('module') == 'home' ) {
$displayColumns = array(
  'fn'              => array('title' => 'First Name', 'sortable' => false),
  'ln'              => array('title' => 'Last Name', 'sortable' => false),
  'agency'          => array('title' => 'Agency', 'sortable' => true),
  'classification'  => array('title' => 'Classification', 'sortable' => true),
  'phones'          => array('title' => 'Phone Contact(s)', 'sortable' => true),
  'emails'          => array('title' => 'Email Contact(s)', 'sortable' => true),

  'staff_status' => array('title' => 'Status', 'sortable' => false),
);

//pager comes in from the action

include_partial('global/list', array( 'sf_request' => $sf_request,
                                      'displayColumns' => $displayColumns,
                                      'pager' => $pager,
                                      'order' => $order,
                                      'sort' => $sort,
                                      'status' => $status,
                                      'target_module' => $target_module,
                                      'caption' => 'Staff Search Results',
                                      'widgets' => array()
  ));
}
elseif($sf_request->getParameter('module') == 'facility') {
$displayColumns = array(
    'id' => array('title' => 'Id', 'sortable' => false),
    'facility_name' => array('title' => 'Facility Name', 'sortable' => true),
    'services' => array('title' => 'Services', 'sortable' => false),
    'facility_codes' => array('title' => 'Facility Code', 'sortable' => true)
);

//pager comes in from the action

include_partial('global/list', array( 'sf_request' => $sf_request,
                                      'displayColumns' => $displayColumns,
                                      'pager' => $pager,
                                      'order' => $order,
                                      'sort' => $sort,
                                      'status' => $status,
                                      'target_module' => $target_module,
                                      'caption' => 'Facility Search Results',
                                      'widgets' => array()
  ));
}

else{
  include_partial('search/search', array('hits' => $hits, 'searchquery' => $searchquery, 'results' => $results));
}

?>