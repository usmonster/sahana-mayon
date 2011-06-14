<?php
   use_javascript('jquery.ui.custom.js');
   use_stylesheet('jquery/jquery.ui.custom.css');
  
(isset($status)) ? $status = $status : $status = 'active';
(isset($sort)) ? $sort = $sort : $sort = '';
(isset($order)) ? $order = $order : $order = '';

if($sf_request->getParameter('module') == 'agStaff' || $sf_request->getParameter('module') == 'home' ) {
  //set tooltips
  $statusTooltip = url_for('@wiki') . '/doku.php?id=tooltip:staff_list_resource_status&do=export_xhtmlbody';
  $orgTooltip = url_for('@wiki') . '/doku.php?id=tooltip:organization&do=export_xhtmlbody';
  $resourceTooltip = url_for('@wiki') . '/doku.php?id=tooltip:staff_resource&do=export_xhtmlbody';
  
  $displayColumns = array(
  'id' => array('title' => '', 'sortable' => false),
  'fn' => array('title' => 'First Name', 'sortable' => true),
  'ln' => array('title' => 'Last Name', 'sortable' => true),
  'organization' => array('title' => 'Organization', 'sortable' => true, 'tooltip' => $orgTooltip),
  'resource' => array('title' => 'Resource', 'sortable' => true, 'tooltip' => $resourceTooltip),
  'phones' => array('title' => 'Phone', 'sortable' => false),
  'emails' => array('title' => 'Email', 'sortable' => false),
  'staff_status' => array('title' => 'Status', 'sortable' => false, 'tooltip' => $statusTooltip),
);


//pager comes in from the action

include_partial('global/list', array( 'sf_request' => $sf_request,
                                      'displayColumns' => $displayColumns,
                                      'pager' => $pager,
                                      'order' => $order,
                                      'sort' => $sort,
                                      'limit' => $limit,
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
elseif($sf_request->getParameter('module') == 'organization') {
$displayColumns = array(
    'id' => array('title' => 'Id', 'sortable' => false),
    'organization' => array('title' => 'Organization', 'sortable' => true),
    'description' => array('title' => 'Description', 'sortable' => true)
);

//pager comes in from the action

include_partial('global/list', array( 'sf_request' => $sf_request,
                                      'displayColumns' => $displayColumns,
                                      'pager' => $pager,
                                      'order' => $order,
                                      'sort' => $sort,
                                      'status' => $status,
                                      'target_module' => $target_module,
                                      'caption' => 'Organization Search Results',
                                      'widgets' => array()
  ));
}

else{
  include_partial('search/search', array('hits' => $hits, 'searchquery' => $searchquery, 'results' => $results));
}

?>