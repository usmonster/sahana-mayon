<div class="infoHolder  shiftTemplateCounter"
     style="width: 750px;"
     id="container">
       <?php
       $isNewscenarioshift = $scenarioshiftform->getObject()->isNew();
       echo $scenarioshiftform['id']->render()
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
          <?php
          echo $scenarioshiftform['scenario_facility_resource_id']->renderRow();
          ?>
        </td>
      </tr>
      <tr>
        <td colspan="2" style="background-color: #E5F7FF;">
          <?php
          echo $scenarioshiftform['staff_resource_type_id']->renderRow();
          ?>
        </td>
      </tr>
      <tr colspan="2" style="background-color: #F7F7F7;">
        <td style="text-align:left;">
          <?php
          echo $scenarioshiftform['shift_status_id']->renderRow();
          echo '<br />';
          echo $scenarioshiftform['task_id']->renderRow();
          echo '<br />';
          echo $scenarioshiftform['minimum_staff']->renderRow();
          echo '<br />';
          echo $scenarioshiftform['maximum_staff']->renderRow();
          echo '<br />';
          echo $scenarioshiftform['staff_wave']->renderRow();
          echo '<br />';
          echo $scenarioshiftform['deployment_algorithm_id']->renderRow();
          ?>
        </td>
        <td>
          <table style="padding-right:50px;">
            <tr>
              <td>
                &nbsp;
              </td>
              <td>
                <span class="infoText">Hours : Minutes</span>
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
                      $scenarioshiftform['minutes_start_to_facility_activation']->getValue();
                  $start_hours = intval($start_time / 60);
                  $start_minutes = $start_time - ($start_hours * 60);
                  if ((strlen((string) $start_hours)) == 1)
                    $start_hours = '0' . $start_hours;
                  if ((strlen((string) $start_minutes)) == 1)
                    $start_minutes = '0' . $start_minutes;
                  ?>
                  <input type="text" id="st_0_start_time_hours"
                         name="st[start_time_hours]"
                         value="<?php echo $start_hours ?>"
                         class="inputGray" style="width:20px;">
                  :
                  <input type="text" id="st_0_start_time_minutes"
                         name="st[start_time_minutes]"
                         value="<?php echo $start_minutes ?>"
                         class="inputGray" style="width:20px;">
                </span>
              </td>
              <td>
                <?php echo $scenarioshiftform['minutes_start_to_facility_activation']->render(); ?>
                <div id="start_time0" class="timeslider"></div>
              </td>
            </tr>
            <tr>
              <td>
                <span class="rowFormatLabel2">
                  Shifts Length:
                </span>
              </td>

              <td>
                <span style="display:inline;">
                  <?php

                  $task_time =
                      $scenarioshiftform['task_length_minutes']->getValue();
                  $task_hours = intval($task_time / 60);
                  $task_minutes = $task_time - ($task_hours * 60);
                  if ((strlen((string) $task_hours)) == 1)
                    $task_hours = '0' . $task_hours;
                  if ((strlen((string) $task_minutes)) == 1)
                    $task_minutes = '0' . $task_minutes;
                  ?>
                  <input type="text" id="st_0_task_time_hours"
                         name="st[task_time_hours]"
                         value="<?php echo $task_hours ?>"
                         class="inputGray" style="width:20px;">
                  :
                  <input type="text" id="st_0_task_time_minutes"
                         name="st[task_time_minutes]"
                         value="<?php echo $task_minutes ?>"
                         class="inputGray" style="width:20px;">
                </span>
              </td>
              <td width="200px">
                <?php echo $scenarioshiftform['task_length_minutes']->render();?>
                <div id="task_time0" class="timeslider"></div>
              </td>
            </tr>
            <tr>
              <td>
                <span class="rowFormatLabel2">
                  Breaks Length:
                </span>
              </td>

              <td>
                <span style="display:inline;">
                  <?php
                  $break_time =
                      $scenarioshiftform['break_length_minutes']->getValue();
                  $break_hours = intval($break_time / 60);
                  $break_minutes = $break_time - ($break_hours * 60);
                  if ((strlen((string) $break_hours)) == 1)
                    $start_hours = '0' . $break_hours;
                  if ((strlen((string) $break_minutes)) == 1)
                    $break_minutes = '0' . $break_minutes;
                  ?>
                  <input type="text" id="st_0_break_time_hours"
                         name="st[break_time_hours]"
                         value="<?php echo $break_hours ?>"
                         class="inputGray" style="width:20px;">
                  :
                  <input type="text" id="st_0_break_time_minutes"
                         name="st[break_time_minutes]"
                         value="<?php echo $break_minutes ?>"
                         class="inputGray" style="width:20px;">

                </span>
              </td>
              <td width="200px">
                  <?php echo $scenarioshiftform['break_length_minutes']->render();?>
                <div id="break_time0" class="timeslider"></div>
              </td>
            </tr>
          </table>

        </td>
      </tr>
    </tbody>
  </table>
</div>
<?php 
$shiftlisturl = url_for('scenario/shifts?id=' .$scenario_id);
echo link_to('Back to List', $shiftlisturl, array('class' => 'generalButton'));
?>
<input type="submit"  class="continueButton" value="Save" name="Save"/>
<input type="submit"  class="deleteButton" value="Delete" name="Delete"/>