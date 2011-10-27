<?php

use_javascript('jquery.ui.custom.js');
use_stylesheet('jquery/jquery.ui.custom.css');

$statusTooltip = url_for('@wiki') . '/doku.php?id=tooltip:staff_list_resource_status&do=export_xhtmlbody';
$orgTooltip = url_for('@wiki') . '/doku.php?id=tooltip:organization&do=export_xhtmlbody';
$resourceTooltip = url_for('@wiki') . '/doku.php?id=tooltip:staff_resource&do=export_xhtmlbody';

// Adding tooltip info to the displayColumns array to be used in later templates.
$displayColumns = $sf_data->getRaw('displayColumns');
$displayColumns['organization']['tooltip'] = $orgTooltip;
$displayColumns['resource']['tooltip'] = $resourceTooltip;
$displayColumns['staff_status']['tooltip'] = $statusTooltip;

//pager comes in from the action
include_partial('global/list',
                array('sf_request' => $sf_request,
                      'displayColumns' => $displayColumns,
                      'pager' => $pager,
                      'data' => $data,
                      'listParams' => $listParams,
                      'targetModule' => 'staff',
                      'targetAction' => $targetAction,
                      'caption' => 'Staff Resource List',
                      'widgets' => array($statusWidget)
                    ));

if ($targetAction == 'list')
  {
    echo '<a href="' . url_for('staff/new') . '" class="generalButton">Create New</a>';
  }
?>

