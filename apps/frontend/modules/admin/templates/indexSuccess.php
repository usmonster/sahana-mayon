<h3>System Administration</h3>
Hello, welcome to the system administration management module of Sahana Agasti 2.0, Mayon
<br />
Please select one of the following staff administration actions:<br />
  <fieldset>
    <legend><?php echo image_tag('config.png', array('alt' => 'config gear icon')) ?>Access Control:</legend>

<a href="<?php echo url_for('admin/new') ?>" class="linkButton" title="Create New Account">Create Account</a><br/><br/>
<a href="<?php echo url_for('admin/list') ?>" class="linkButton" title="List Existing Accounts">List Accounts</a><br/><br/>
<a href="<?php echo url_for('admin/cred') ?>" class="linkButton" title="Credential Management">Credential Management</a><br/><br/>

  </fieldset>
  <fieldset>
    <legend><?php echo image_tag('config.png', array('alt' => 'config gear icon')) ?>Configuration:</legend>

    <a href="<?php echo url_for('admin/config') ?>" class="linkButton" title="System Settings">System Settings</a><br/><br/>
    <a href="<?php echo url_for('admin/globals') ?>" class="linkButton" title="System Settings">Global Parameters</a><br/><br/>
    <a href="<?php echo url_for('admin/pacman') ?>" class="linkButton" title="Module Manager">Package Manager</a><br/><br/>
  </fieldset>

    <p class="greyText">This page will allow you to configure your Agasti installation.</p>
        <p class="greyText">Select one of the administration options</p>