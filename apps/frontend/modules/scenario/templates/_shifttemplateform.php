<?php use_stylesheets_for_form($shifttemplateform) ?>
<?php use_javascripts_for_form($shifttemplateform) ?>

<form action="<?php echo url_for('scenario/shifttemplates?id=' . $scenario_id); ?>" method="post">
  <?php #if (!$shifttemplateform->getObject()->isNew()): ?>
  <!--
  <input type="hidden" name="sf_method" value="put" />
  -->
  <?php #endif; ?>
  <div style="overflow:scroll; padding:0px 10px 0px 0px">
  <table>
    <tfoot>
      <tr>
        <td colspan="2">
          &nbsp;<a href="<?php echo url_for('scenario/listshifttemplate') ?>" class="linkButton">Back to list</a>
          <?php #if (!$shifttemplateform->getObject()->isNew()): ?>
          <!--
          &nbsp;<?php #echo link_to('Delete', 'scenario/deleteshifttemp?id='.$shifttemplateform->getObject()->getId(), array('method' => 'delete', 'confirm' => 'Are you sure?', 'class' => 'linkButton')) ?>
          -->
          <?php #endif; ?>
          <input type="submit" class="linkButton" value="Save" />
          <input type="submit" class="linkButton" value="Save, Generate Shifts and Continue" name="Continue" />
        </td>
      </tr>
    </tfoot>
    <tbody><tr><td>
          <?php echo $shifttemplateform ?>
        </td></tr></tbody>
  </table>
  </div>
</form>
