<?php use_helper('I18N') ?>
<form class="loginPromptForm" method="post" action="<?php echo url_for("@sf_guard_signin") ?>" name="sf_guard_signin" id="sf_guard_signin">
  <?php
  $form->getWidget('username')->setAttribute('class', 'inputGray');
  $form->getWidget('username')->setLabel('Username');
  $form->getWidget('password')->setAttribute('class', 'inputGray'); ?>
  <ul>
    <li>
      <?php
      echo $form['username']->renderError();
      echo $form['username']->renderLabel(null, array('class' => 'label80'));
      echo $form['username']; ?>
    </li>
    <li>
      <?php
      echo $form['password']->renderLabel(null, array('class' => 'label80'));
      echo $form['password']->renderError();
      echo $form['password'];
      echo $form['_csrf_token']; ?>
    </li>
    <!--we could render text to use as the background of the username form... maybe-->
    <li class="spacer">
      <input type="submit"  class="buttonSmall" value="<?php echo __('sign in', array(), 'sfGuardAuth') ?>" />
    </li>
  </ul>
</form>
