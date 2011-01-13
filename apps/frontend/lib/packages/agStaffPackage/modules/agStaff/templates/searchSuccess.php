<?php $referer = $sf_request->getReferer(); ?>
<div id="columnLeft">
  <?php include_partial('agStaff/filter', array('pager' => $pager, 'query' => $query)) ?>
</div>
<div id="columnRight">
  <?php include_partial('agStaff/results', array('pager' => $pager, 'query' => $query)) ?>
</div>
