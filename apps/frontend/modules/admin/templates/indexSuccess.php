<h3>System Administration</h3>
<p class="greyText">Hello, welcome to the system administration management module of Sahana Agasti 2.0, Mayon
<br />
Please select one of the following staff administration actions:</p>
<div class="configure adminConfig">
  <fieldset>
    <legend><?php echo image_tag('config.png', array('alt' => 'config gear icon')) ?>Access Control:</legend>

<a href="<?php echo url_for('admin/new') ?>" class="continueButton" title="Create New Account">Create Account</a><br/><br/>
<a href="<?php echo url_for('admin/list') ?>" class="continueButton" title="List Existing Accounts">List Accounts</a><br/><br/>
<a href="<?php echo url_for('admin/cred') ?>" class="continueButton" title="Credential Management">Credential Management</a><br/><br/>

  </fieldset>
  <fieldset>
    <legend><?php echo image_tag('config.png', array('alt' => 'config gear icon')) ?>Configuration:</legend>

    <a href="<?php echo url_for('admin/config') ?>" class="continueButton" title="System Settings">System Settings</a><br/><br/>
    <a href="<?php echo url_for('admin/globals') ?>" class="continueButton" title="System Settings">Global Parameters</a><br/><br/>
    <a href="<?php echo url_for('admin/pacman') ?>" class="continueButton" title="Module Manager">Package Manager</a><br/><br/>
  </fieldset>
  <fieldset>
    <legend><?php echo image_tag('config.png', array('alt' => 'config gear icon')) ?>Custom Management:</legend>

<?php
  $exportUrl = url_for('admin/disablestaff') ;
    echo link_to('Disable All Staff Resources', $exportUrl, array('method' => 'post', 'confirm' => 'Are you sure you want to inactivate all staff?', 'class' => 'deleteButton', 'title' => 'Inactivate all Staff Resources'));
//all links on this page should use the link_to helper
  ?>
  </fieldset>
</div>
  <br />
    <p class="greyText">This page will allow you to configure your Agasti installation.</p>
        <p class="greyText">Select one of the administration options</p>