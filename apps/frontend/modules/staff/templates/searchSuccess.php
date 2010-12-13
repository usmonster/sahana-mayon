<?php $referer = $sf_request->getReferer(); ?>
<div id="columnLeft">
  <?php include_partial('staff/filter', array('pager' => $pager, 'query' => $query)) ?>
</div>
<div id="columnRight">
  <?php include_partial('staff/results', array('pager' => $pager, 'query' => $query)) ?>
</div>
