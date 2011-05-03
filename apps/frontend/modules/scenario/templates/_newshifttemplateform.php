<table>
  <tfoot style="display:none;">
    <tr>
      <td colspan="2">
        &nbsp;<a href="<?php echo url_for('scenario/listshifttemplate') ?>" class="linkButton">Back to List</a>
        <?php #if (!$shifttemplateform->getObject()->isNew()):  ?>
        <!--
        &nbsp;<?php #echo link_to('Delete', 'scenario/deleteshifttemp?id='.$shifttemplateform->getObject()->getId(), array('method' => 'delete', 'confirm' => 'Are you sure?', 'class' => 'linkButton'))    ?>
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
        echo $shifttemplateform['shift_repeats']->renderRow() . $shifttemplateform['max_staff_repeat_shifts']->renderRow();
        echo '<br />'; //this is only for testing
        echo $shifttemplateform['deployment_algorithm_id']->renderRow();
        ?>
      </td>
      <td>
        <table>
          <tr>
            <td>
              <?php
              echo $shifttemplateform['minutes_start_to_facility_activation']->renderRow();
              ?>
            </td>
            <td width="200px">
              <div id="start_time_slider<?php echo $number ?>"></div>
            </td>
          </tr>
          <tr>
            <td>
              <?php
              echo $shifttemplateform['task_length_minutes']->renderRow();
              ?>
            </td>
            <td width="200px">
              <div id="task_time_slider<?php echo $number ?>"></div>
            </td>
          </tr>
          <tr>
            <td>
            <?php
              echo $shifttemplateform['break_length_minutes']->renderRow();
            ?>
            </td>
          <td width="200px">
          <div id="break_time_slider<?php echo $number ?>"></div>
            </td>
          </tr>
          </table>
<?php

$foo = $shifttemplateform['minutes_start_to_facility_activation']->getValue();

?>
          <script>
            $().ready(function() {
              $("#start_time_slider<?php echo $number ?>").slider({
                orientation: "horizontal",
                value:<?php echo intval($shifttemplateform['minutes_start_to_facility_activation']->getValue() / 60 ); ?>,
                min: -96,
                max: 96,
                step: 1,
                slide: function( event, ui ) {
                  $("#shift_template_<?php echo $number ?>_minutes_start_to_facility_activation").val(ui.value);
                }
              });
              $("#task_time_slider<?php echo $number ?>").slider({
                orientation: "horizontal",
                value:50,
                min: 0,
                max: 100,
                step: 5,
                slide: function( event, ui ) {
                  $("#shift_template_<?php echo $number ?>_task_length_minutes").val(ui.value);
                }
              });
              $("#break_time_slider<?php echo $number ?>").slider({
                orientation: "horizontal",
                value:50,
                min: 0,
                max: 100,
                step: 5,
                slide: function( event, ui ) {
                  $("#shift_template_<?php echo $number ?>_break_length_minutes").val(ui.value);
              }
            });

            $('.removeShiftTemplate').click(function() {
              //if there is no id for this record(db_not_exists)
              var passId = '#' + $(this).attr('id');
              var $inputs = $('#myForm :input:hidden');
              //send get/post to call delete
              $(this).parent().remove();
            });

          });

        </script>


      </td>
    </tr>
  </tbody>
</table>
