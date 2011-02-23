<?php use_helper('I18N') ?>
<form style="vertical-align: top;top:0;float: right; margin-top:0px; width:260px;" method="post" action="<?php echo url_for("@sf_guard_signin") ?>" name="sf_guard_signin" id="sf_guard_signin">
  <?php
  $form->getWidget('username')->setAttribute('class', 'inputGray');
  $form->getWidget('username')->setLabel('Username');
  $form->getWidget('password')->setAttribute('class', 'inputGray'); ?>
  <ul style="list-style-type: none;vertical-align: top; margin-top:0px; margin:0;">
    <li style="margin-top: 0px;">
      <?php
      echo $form['username']->renderError();
      echo $form['username']->renderLabel(null, array('class' => 'label80'));
      echo $form['username']; ?>
    </li>
    <li style="margin-top: 0px;">
      <?php
      echo $form['password']->renderLabel(null, array('class' => 'label80'));
      echo $form['password']->renderError();
      echo $form['password'];
      echo $form['_csrf_token']; ?>
    </li>
    <!--we could render text to use as the background of the username form... maybe-->
    <li style="margin-top: 1px;">
      <input type="submit"  class="buttonSmall" value="<?php echo __('sign in', array(), 'sfGuardAuth') ?>" />
    </li>
  </ul>
</form>
