<div class="infoHolder shiftTemplateCounter" 
     style="width: 750px;"
     id="container<?php echo $number ?>">
       <?php
       if (!$shifttemplateform->getObject()->isNew()) {
         $number = $shifttemplateform['id']->getValue();
       } else{
         $number = $number;
       }
       
       echo $shifttemplateform['id']->render();
       ?>

  <table style="width:750px;">
    <tfoot style="display: none;">
      <tr>
        <td colspan="2">
          <input type="submit" class="continueButton" value="Save" />
        </td>
      </tr>
    </tfoot>
    <tbody>
      <tr>
        <td colspan="2" style="background-color: #E5F7FF;">
          <?php echo $shifttemplateform['staff_resource_type_id']->renderRow() ?>
          <a href="<?php echo url_for('scenario/deleteshifttemplate') ?>" 
             class="smallLinkButton floatRight removeShiftTemplate" id="<?php echo $number ?>">
            - Delete Shift Template</a>
          </span>
          <br/>
          <?php echo $shifttemplateform['facility_resource_type_id']->renderRow(); ?>
        </td>
      </tr>
      <tr colspan="2" style="background-color: #F7F7F7;">
        <td style="text-align:left;">
          <?php
          echo $shifttemplateform['shift_repeats']->renderRow();
          echo '<br />';
          echo $shifttemplateform['max_staff_repeat_shifts']->renderRow();
          echo '<br />';
          echo $shifttemplateform['shift_status_id']->renderRow();
          echo '<br />';
          echo $shifttemplateform['task_id']->renderRow();
          echo '<br />';
          echo $shifttemplateform['deployment_algorithm_id']->renderRow();
          ?>
        </td>
        <td>
          <table style="padding-right:50px;">
            <tr>
              <td></td>
              <td>
                <span class="infoText" style="padding-left: 6px">Hours : Minutes</span>
              </td>
              <td style="text-align: center;">
                <span class="infoText">Facility Activation</span>
              </td>
            </tr>
            <tr>
              <td>
                <span class="rowFormatLabel2">
                  Shifts Start:
                </span>
              </td>
              <td>
                <span style="display:inline;">
                  <?php
                  $start_time =
                      $shifttemplateform['minutes_start_to_facility_activation']->getValue();
                  $start_hours = intval($start_time / 60);
                  $start_minutes = $start_time - ($start_hours * 60);
                  $start_hours = sprintf('%02d', $start_hours);
                  $start_minutes = sprintf('%02d', $start_minutes);
                  ?>
                  <input type="text" id="st_<?php echo $number; ?>_start_time_hours"
                         name="st[<?php echo $number ?>][start_time_hours]"
                         value="<?php echo $start_hours ?>"
                         class="inputGray" style="width:30px; text-align: right;">
                  :
                  <input type="text" id="st_<?php echo $number; ?>_start_time_minutes"
                         name="st[<?php echo $number ?>][start_time_minutes]"
                         value="<?php echo $start_minutes ?>"
                         class="inputGray" style="width:20px;">
                </span>
              </td>
              <td>
                <?php echo $shifttemplateform['minutes_start_to_facility_activation']->render(); ?>
                <div id="start_time<?php echo $number ?>" class="timeslider"></div>
              </td>
            </tr>
            <tr>
              <td></td>
              <td>
                <span class="infoText" style="padding-left: 6px">Hours : Minutes</span>
              </td>
              <td></td>
            </tr>
            <tr>
              <td>
                <span class="rowFormatLabel2">
                  Task Length:
                </span>
              </td>

              <td>
                <span style="display:inline;">
                  <?php

                  $task_time =
                      $shifttemplateform['task_length_minutes']->getValue();
                  $task_hours = intval($task_time / 60);
                  $task_minutes = $task_time - ($task_hours * 60);
                  $task_hours = sprintf('%02d', $task_hours);
                  $task_minutes = sprintf('%02d', $task_minutes);
                  ?>
                  <input type="text" id="st_<?php echo $number; ?>_task_time_hours"
                         name="st[<?php echo $number ?>][task_time_hours]"
                         value="<?php echo $task_hours ?>"
                         class="inputGray" style="width:30px;text-align: right;">
                  :
                  <input type="text" id="st_<?php echo $number; ?>_task_time_minutes"
                         name="st[<?php echo $number ?>][task_time_minutes]"
                         value="<?php echo $task_minutes ?>"
                         class="inputGray" style="width:20px;">
                </span>
              </td>
              <td width="200px">
                <?php echo $shifttemplateform['task_length_minutes']->render(); ?>
                <div id="task_time<?php echo $number ?>" class="timeslider"></div>
              </td>
            </tr>
            <tr>
              <td>
                <span class="rowFormatLabel2">
                  Break Length:
                </span>
              </td>

              <td>
                <span style="display:inline;">
                  <?php

                  $break_time =
                      $shifttemplateform['break_length_minutes']->getValue();
                  $break_hours = intval($break_time / 60);
                  $break_minutes = $break_time - ($break_hours * 60);
                  $break_hours = sprintf('%02d', $break_hours);
                  $break_minutes = sprintf('%02d', $break_minutes);
                  ?>
                  <input type="text" id="st_<?php echo $number; ?>_break_time_hours"
                         name="st[<?php echo $number ?>][break_time_hours]"
                         value="<?php echo $break_hours ?>"
                         class="inputGray" style="width:30px;text-align: right;">
                  :
                  <input type="text" id="st_<?php echo $number; ?>_break_time_minutes"
                         name="st[<?php echo $number ?>][break_time_minutes]"
                         value="<?php echo $break_minutes ?>"
                         class="inputGray" style="width:20px;">

                </span>
              </td>
              <td width="200px">
                <?php echo $shifttemplateform['break_length_minutes']->render(); ?>
                <div id="break_time<?php echo $number ?>" class="timeslider"></div>
              </td>
            </tr>
          </table>
        </td>
      </tr>
    </tbody>
  </table>
</div>