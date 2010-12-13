<?php use_helper('I18N') ?>
<?php if (has_slot('sf_guard_auth_login')): ?>
  <?php include_slot('sf_guard_auth_login') ?>
<?php else: ?>
  <?php if ($loggedIn): ?>
    <?php include_partial('sfGuardAuth/logoutPrompt') ?>
  <?php else: ?>
    <?php include_partial('sfGuardAuth/loginPrompt', array("form" => $form)) ?>
  <?php endif ?>
<?php endif ?>
