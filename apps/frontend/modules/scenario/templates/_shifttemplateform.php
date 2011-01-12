<?php use_stylesheets_for_form($shifttemplateform) ?>
<?php use_javascripts_for_form($shifttemplateform) ?>

<form action="<?php echo url_for('scenario/createshifttemplate?id=' . $scenario_id); ?>" method="post">
<?php #if (!$shifttemplateform->getObject()->isNew()): ?>
<!--
<input type="hidden" name="sf_method" value="put" />
-->
<?php #endif; ?>
  <table>
    <tfoot>
      <tr>
        <td colspan="2">
          &nbsp;<a href="<?php echo url_for('scenario/listshifttemplate') ?>" class="linkButton">Back to list</a>
          <?php #if (!$shifttemplateform->getObject()->isNew()): ?>
            <!--
            &nbsp;<?php #echo link_to('Delete', 'scenario/deleteshifttemp?id='.$shifttemplateform->getObject()->getId(), array('method' => 'delete', 'confirm' => 'Are you sure?')) ?>
            -->
          <?php #endif; ?>
          <input type="submit" class="linkButton" value="Save" />
        </td>
      </tr>
    </tfoot>
    <tbody><tr><td>
      <?php echo $shifttemplateform ?>
    </td></tr></tbody>
  </table>
</form>
