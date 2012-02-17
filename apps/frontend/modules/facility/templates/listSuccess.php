<?php

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