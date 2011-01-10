<?php use_javascript('json.serialize'); ?>
<script type="text/javascript">

function pacTran() {
var out = Array();
$('.paclist :not(:checked)').each(function(index) {
   out[index] = $(this).attr('id');
   $("#disable").val(JSON.stringify(out));

});
$('.paclist :checked').each(function(index) {
   out[index] = $(this).attr('id');
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
<input type="hidden" name="sf_method" value="put" />
<input type="hidden" name="disable" value="" />
<input type="hidden" name="enable" value="" />

  <?php
  foreach($packages_available as $package)
  {
    ($package['action'] == 0) ? $checked = 'unchecked' : $checked = 'checked';
    echo '<tr><td><input type="checkbox" checked="' . $checked . '"class="paclist" /></td><td>' . $package['name'] . '</td><td>version 1<td>' . link_to($package['name'], $package['name'] . '/config') . '</td></tr>';
  }
  ?>
<tr>
  <td>
    <input class="linkButton" type="submit" value="Update" id="selecter" onclick="pacTran()"/>
  </td>
</tr>

</form>
</table>
