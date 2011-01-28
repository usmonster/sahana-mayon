<?php use_helper('I18N') ?>
<?php if (has_slot('staff_search_form')): ?>
  <?php include_slot('staff_search_form') ?>
<?php else: ?>
  <?php if ($loggedIn): ?>
    <?php include_partial('staff/searchForm', array("form" => $form)) ?>
  <?php else: ?>
    <?php //don't do anything! '?>
  <?php endif ?>
<?php endif ?>