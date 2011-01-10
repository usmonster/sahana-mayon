<h3>Module Manager</h3>
<br />
<table>
  <tr>
    <td>&nbsp;</td>
    <td>Module Name</td>
    <td>Module Version</td>
    <td>Settings</td>
  </tr>
<form action="<?php echo url_for('admin/moduleman') ?>"method="post" enctype="multipart/form-data">
<input type="hidden" name="sf_method" value="put" />

  <?php
  foreach($modules_available as $module)
  {
    echo '<tr><td><input tyepe="checkbox" value="'.  $module['action'] . '" name="' . $module['action'] . '" class="linkButton" /></td><td>' . $module['name'] . '</td><td>version 1<td>' . link_to($module['name'], $module['name'] . '/config') . '</td></tr>';
  }
  ?>
</form>
</table>
