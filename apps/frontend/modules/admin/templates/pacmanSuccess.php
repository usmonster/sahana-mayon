<?php use_javascript('json.serialize'); ?>
<script type="text/javascript">

function pacTran() {
var out = Array();
$('input:checkbox:not(:checked)').each(function(index) {
   out[index] = $(this).val();
   $("#disable").val(JSON.stringify(out));

});
$('input:checkbox:checked').each(function(index) {
   out[index] = $(this).val();
  $("#enable").val(JSON.stringify(out));
});
}
</script>

<h3>Package Manager</h3>
<br />
<table>
  <tr>
    <td>Enabled?</td>
    <td>Package Name</td>
    <td>Package Version</td>
    <td>Settings</td>
  </tr>
<form action="<?php echo url_for('admin/pacman') ?>"method="post" enctype="multipart/form-data">
<input type="hidden" id="disable" name="disable"/>
<input type="hidden" id="enable" name="enable" />

  <?php
  foreach($packages_available as $package)
  {
    ($package['action'] == 0) ? $checked = 'unchecked' : $checked = 'checked';
    echo '<tr><td><input type="checkbox" checked="' . $checked . '"class="paclist" value="' . $package['name'] . '"/></td><td>' . $package['name'] . '</td><td>version 1<td>' . link_to($package['name'], $package['name'] . '/config') . '</td></tr>';
  }
  ?>
<tr>
  <td>
    <input class="linkButton" type="submit" value="Update" id="selecter" onclick="pacTran()"/>
  </td>
</tr>

</form>
</table>
