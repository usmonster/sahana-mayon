<form action="<?php echo url_for('admin/globalparams') ?>" method="post" class="configure adminConfigLegend">

Here you can set up global parameters (variables) that can be used throughout the application.  This keeps all settings in a single place, click on a global param link to edit that item or add a new one at the bottom
  <fieldset>
    <legend>Global Parameters</legend>
<table>
  <thead>
    <tr>
      <th>Global Param</th>
      <th>Description</th>
    </tr>
  </thead>
  <tbody>
    <?php foreach ($ag_global_params as $ag_global_param): ?>
    <tr>
      <td><a href="<?php echo url_for('admin/globals?param='.$ag_global_param->getId()) ?>"><?php echo $ag_global_param->getDatapoint() ?></a></td>
      <td><?php echo $ag_global_param->getValue() ?></td>
    </tr>
    <?php endforeach; ?>
  </tbody>
</table>

<?php
//this still needs some fixing
include_partial('paramform', array('paramform' => $paramform)) ?>



  </fieldset>
</form>