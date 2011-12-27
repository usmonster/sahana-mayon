<?php

////to use the new lister, change this line
//if (2 == 2) {
//
//  $columns = array(
//    'id' => array('title' => 'Id', 'sortable' => false),
//    'facility_name' => array('title' => 'Facility Name <a href="' . url_for('@wiki') .  '/doku.php?id=tooltip:facility_name&do=export_xhtmlbody" class="tooltipTrigger" title="Facility Name">?</a>', 'sortable' => true),
//    'Resources' => array('title' => 'Resources <a href="' . url_for('@wiki') .  '/doku.php?id=tooltip:facility_resource&do=export_xhtmlbody" class="tooltipTrigger" title="Resources">?</a>', 'sortable' => false),
//    'facility_code' => array('title' => 'Facility Code <a href="' . url_for('@wiki') .  '/doku.php?id=tooltip:facility_code&do=export_xhtmlbody" class="tooltipTrigger" title="Facility Code">?</a>', 'sortable' => true)
//  );
//
////pager comes in from the action
//
//echo agListForm::facilitylist($sf_request,'Facility Listing', $columns, $pager);
//
//
//}
//else
//{
//$displayColumns = array(
//    'id' => array('title' => 'Id', 'sortable' => false),
//    'facility_name' => array('title' => 'Facility Name ', 'sortable' => true),
//    'services' => array('title' => 'Services', 'sortable' => false),
//    'facility_code' => array('title' => 'Facility Code', 'sortable' => true)
//);
//
////pager comes in from the action
//
//include_partial('global/list', array( 'sf_request' => $sf_request,
//                                      'displayColumns' => $displayColumns,
//                                      'pager' => $pager,
//                                      'order' => $order,
//                                      'sort' => $sort,
//                                      'filter' => $filter,
//                                      'target_module' => $target_module,
//                                      'caption' => 'Facilitiy Resource List',
//                                      'widgets' => array()
//
//  ));
//}

$facNameTooltip = url_for('@wiki') . '/doku.php?id=tooltip:facility_name&do=export_xhtmlbody';
$facResourceTooltip = url_for('@wiki') . '/doku.php?id=tooltip:facility_resource&do=export_xhtmlbody';
$facCodeTooltip = url_for('@wiki') . '/doku.php?id=tooltip:facility_code&do=export_xhtmlbody';

// Adding tooltip info to the displayColumns array to be used in later templates.
$displayColumns = $sf_data->getRaw('displayColumns');
$displayColumns['facility_name']['tooltip'] = $facNameTooltip;
$displayColumns['resource_type']['tooltip'] = $facResourceTooltip;
$displayColumns['facility_code']['tooltip'] = $facCodeTooltip;


  //pager comes in from the action
  include_partial('global/list',
    array('sf_request' => $sf_request,
          'displayColumns' => $displayColumns,
          'pager' => $pager,
          'data' => $data,
          'listParams' => $listParams,
          'targetModule' => $targetModule,
          'targetAction' => $targetAction,
          'caption' => 'Facility Resource List',
          'widgets' => array(),
        ));

  if ($targetAction == 'list')
  {
    echo link_to('Create New', 'facility/new', array('class' => 'generalButton'));
  }

?>