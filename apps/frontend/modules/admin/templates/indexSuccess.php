<h3>System Administration</h3>
Hello, welcome to the system administration management module of Sahana Agasti 2.0, Mayon
<br />
Please select one of the following staff administration actions:<br />
  <fieldset>
    <legend><img src="<?php echo url_for('images/config.png') ?>" alt="config gear icon" />Access Control:</legend>
<?php
echo '<a href="' . url_for('admin/new') . '" class="buttonText" title="Create New Account">Create Account<a/><br/>';
echo '<a href="' . url_for('admin/list') . '" class="buttonText" title="List Existing Accounts">List Accounts</a><br/>';
echo '<a href="' . url_for('staff/cred') . '" class="buttonText" title="Credential Management">Credential Management</a><br/>';
?>
  </fieldset>
  <fieldset>
    <legend><img src="<?php echo url_for('images/config.png') ?>" alt="config gear icon" />Configuration:</legend>
<?php
echo '<a href="' . url_for('admin/config') . '" class="buttonText" title="System Settings">System Settings<a/><br/>';
echo '<a href="' . url_for('admin/pacman') . '" class="buttonText" title="Module Manager">Package Manager</a><br/>';
?>
  </fieldset>

    <p style="color: #848484">This page will allow you to configure your Agasti installation.</p>
        <p style="color: #848484">Select one of the administration options</p>