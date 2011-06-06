<?php

//to use the new lister, change this line
if (2 == 2) {

  $columns = array(
    'id' => array('title' => 'Id', 'sortable' => false),
    'facility_name' => array('title' => 'Facility Name <a href="' . url_for('@wiki') .  '/doku.php?id=tooltip:facility_name&do=export_xhtmlbody" class="tooltipTrigger" title="Date of Birth">?</a>', 'sortable' => true),
    'Resources' => array('title' => 'Resources <a href="' . url_for('@wiki') .  '/doku.php?id=tooltip:facility_resource&do=export_xhtmlbody" class="tooltipTrigger" title="Resources">?</a>', 'sortable' => false),
    'facility_codes' => array('title' => 'Facility Code <a href="' . url_for('@wiki') .  '/doku.php?id=tooltip:facility_code&do=export_xhtmlbody" class="tooltipTrigger" title="Date of Birth">?</a>', 'sortable' => true)
  );

//pager comes in from the action

echo agListForm::facilitylist($sf_request,'Facility Listing', $columns, $pager);


}
else
{
$displayColumns = array(
    'id' => array('title' => 'Id', 'sortable' => false),
    'facility_name' => array('title' => 'Facility Name ', 'sortable' => true),
    'services' => array('title' => 'Services', 'sortable' => false),
    'facility_codes' => array('title' => 'Facility Code', 'sortable' => true)
);

//pager comes in from the action

include_partial('global/list', array( 'sf_request' => $sf_request,
                                      'displayColumns' => $displayColumns,
                                      'pager' => $pager,
                                      'order' => $order,
                                      'sort' => $sort,
                                      'filter' => $filter,
                                      'target_module' => $target_module,
                                      'caption' => 'List Facilities',
                                      'widgets' => array()

  ));
}
?>