<?php
  use_javascript('jquery.ui.custom.js');
  use_stylesheet('jquery/jquery.ui.custom.css');
  
  (isset($status)) ? $status = $status : $status = 'active';
  (isset($sort)) ? $sort = $sort : $sort = '';
  (isset($order)) ? $order = $order : $order = '';

  if($sf_request->getParameter('module') == 'agStaff' || $sf_request->getParameter('module') == 'home' )
  {
    //set tooltips
    $statusTooltip = url_for('@wiki') . '/doku.php?id=tooltip:staff_list_resource_status&do=export_xhtmlbody';
    $orgTooltip = url_for('@wiki') . '/doku.php?id=tooltip:organization&do=export_xhtmlbody';
    $resourceTooltip = url_for('@wiki') . '/doku.php?id=tooltip:staff_resource&do=export_xhtmlbody';

    $displayColumns = $sf_data->getRaw('displayColumns');
    $displayColumns['organization']['tooltip'] = $orgTooltip;
    $displayColumns['resource']['tooltip'] = $resourceTooltip;
    $displayColumns['staff_status']['tooltip'] = $statusTooltip;

    //pager comes in from the action
    include_partial('global/list', array('sf_request' => $sf_request,
                                         'displayColumns' => $displayColumns,
                                         'pager' => $pager,
                                         'data' => $data,
                                         'order' => $order,
                                         'sort' => $sort,
                                         'status' => $status,
                                         'targetModule' => 'staff',
                                         'targetAction' => $targetAction,
                                         'params' => $params,
                                         'caption' => 'Staff Resource List',
                                         'widgets' => array()
                                        ));
  }
  elseif($sf_request->getParameter('module') == 'facility') {
    //pager comes in from the action
    include_partial('global/list', array( 'sf_request' => $sf_request,
                                          'displayColumns' => $displayColumns,
                                          'pager' => $pager,
                                          'data' => $data,
                                          'order' => $order,
                                          'sort' => $sort,
                                          'status' => $status,
                                          'targetModule' => $targetModule,
                                          'targetAction' => $targetAction,
                                          'params' => $params,
                                          'caption' => 'Facility Search Results',
                                          'widgets' => array()
      ));
  }
  elseif($sf_request->getParameter('module') == 'organization') {
//    $displayColumns = array(
//        'id' => array('title' => 'Id', 'sortable' => false),
//        'organization' => array('title' => 'Organization', 'sortable' => true),
//        'description' => array('title' => 'Description', 'sortable' => true)
//    );

    //pager comes in from the action

    include_partial('global/list', array( 'sf_request' => $sf_request,
                                          'displayColumns' => $displayColumns,
                                          'pager' => $pager,
                                          'data' => $data,
                                          'order' => $order,
                                          'sort' => $sort,
                                          'status' => $status,
                                          'targetModule' => $targetModule,
                                          'targetAction' => $targetAction,
                                          'params' => $params,
                                          'caption' => 'Organization Search Results',
                                          'widgets' => array()
      ));
  }
  else{
    include_partial('search/search', array('hits' => $hits, 'searchquery' => $searchquery, 'results' => $results));
  }
?>