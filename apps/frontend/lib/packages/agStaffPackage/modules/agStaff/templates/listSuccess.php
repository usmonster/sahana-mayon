<?php

use_javascript('jquery.ui.custom.js');
use_stylesheet('jquery/jquery.ui.custom.css');

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

include_partial('global/list',
                array('sf_request' => $sf_request,
  'displayColumns' => $displayColumns,
  'pager' => $pager,
  'order' => $order,
  'limit' => $limit,
  'sort' => $sort,
  'status' => $status,
  'target_module' => 'staff',
  'caption' => 'Staff List',
  'widgets' => array($statusWidget)
));
?>