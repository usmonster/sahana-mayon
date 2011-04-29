<?php #if (!$shifttemplateform->getObject()->isNew()):  ?>
  <!--
  <input type="hidden" name="sf_method" value="put" />
  -->
<?php #endif;  ?>
    <table>
      <tfoot style="display:none;">
        <tr>
          <td colspan="2">
            &nbsp;<a href="<?php echo url_for('scenario/listshifttemplate') ?>" class="linkButton">Back to list</a>
<?php #if (!$shifttemplateform->getObject()->isNew()):  ?>
            <!--
            &nbsp;<?php #echo link_to('Delete', 'scenario/deleteshifttemp?id='.$shifttemplateform->getObject()->getId(), array('method' => 'delete', 'confirm' => 'Are you sure?', 'class' => 'linkButton'))  ?>
            -->
<?php #endif;  ?>
            <input type="submit" class="linkButton" value="Save" />

          </td>
        </tr>
      </tfoot>
      <tbody>
        <tr>
          <td colspan="2">
            <?php
            echo $shifttemplateform['staff_resource_type_id']->renderRow() . $shifttemplateform['facility_resource_type_id']->renderRow();
            ?>
          </td>
        </tr>
        <tr colspan="2" style="background-color: wheat;">
          <td style="text-align:right;">
            <?php
            echo $shifttemplateform['shift_status_id']->renderRow();
            echo '<br />'; //this is only for testing
            echo $shifttemplateform['task_id']->renderRow();
            echo '<br />'; //this is only for testing
            echo $shifttemplateform['deployment_algorithm_id']->renderRow();
            echo '<br />'; //this is only for testing
            echo $shifttemplateform['shift_repeats']->renderRow() . $shifttemplateform['max_staff_repeat_shifts']->renderRow();
            ?>
          </td>
          <td>
            <?php
            echo $shifttemplateform['minutes_start_to_facility_activation']->renderRow();
            echo '<br />'; //this is only for testing
            ?>
            <div class="demo">
              <div id="timeframe"></div>
              <input id="slider-input" type="text">
            </div>
          </td>
        </tr>
      </tbody>
    </table>
