<?php use_helper('I18N') ?>
<div id="sf_apply_logged_in_as" style="vertical-align: top;top:0;float: right; margin-top:0px; width:260px;">
  <span class="logLabel">Logged in as:</span>
  <span class="logName">
    <?php echo __('%1%',
        array("%1%" => $sf_user->getGuardUser()->getUsername()), 'sfGuardAuth') ?>
  </span><br />
  <?php echo button_to(__('log out', array(), 'sfGuardAuth'),
        '@sf_guard_signout', array("id" => 'logout', "class" => 'buttonSmall')) ?>
    &nbsp;&nbsp;
  <?php # echo button_to(__('Settings', array(), 'sfGuardAuth'),
    #'sfGuardAuth/settings', array("id" => 'settings')) ?>
</div>

