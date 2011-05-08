<div class="infoHolder shiftTemplateCounter" 
     style="width: 750px;"
     id="container<?php echo $number ?>">
<?php
$isNewShiftTemplate = $shifttemplateform->getObject()->isNew();
echo $shifttemplateform['id']->render() ?>
  <table style="width:750px;">
    <tfoot style="display: none;">
      <tr>
        <td colspan="2">
          <input type="submit" class="linkButton" value="Save" />
        </td>
      </tr>
    </tfoot>
    <tbody>
      <tr>
        <td colspan="2" style="background-color: #E5F7FF;">
          <?php echo $shifttemplateform['staff_resource_type_id']->renderRow() ?>
          <span class="smallLinkButton floatRight"
                id="removeShiftTemplate<?php echo $number ?>">
            - Delete Shift Template
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
                  echo $shifttemplateform['minutes_start_to_facility_activation']->render();
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
                <div id="start_time_slider<?php echo $number ?>"></div>
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
                         echo $shifttemplateform['task_length_minutes']->render();
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
                       <div id="task_time_slider<?php echo $number ?>"></div>
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
                         echo $shifttemplateform['break_length_minutes']->render();
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
                       <div id="break_time_slider<?php echo $number ?>"></div>
                     </td>
                   </tr>
                 </table>
                 <script>
                   function addLabels(element, labels){
                     var scale = element.append('<ol class="ui-slider-scale ui-helper-reset" role="presentation"></ol>').find('.ui-slider-scale:eq(0)');
                     jQuery(labels).each(function(i){
                       scale.append('<li style="left:'+ leftVal(i, this.length) +'"><span class="ui-slider-label">'+ this.text +'</span><span class="ui-slider-tic ui-widget-content"></span></li>');
                     });

                   }
                   function leftVal(i){
                     return (i/(2) * 100).toFixed(2)  +'%';
                   }

                   $().ready(function() {
                     stComponent = $("#start_time_slider<?php echo $number ?>").slider({
                       orientation: "horizontal",
                       value:<?php
                         if ($start_time == '') {
                           echo '0';
                         } else {
                           echo $start_time;
                         }
                  ?>,
                        min: -7200,
                        max: 7200,
                        step: 60,
                        slide: function( event, ui ) {
                          var hours = Math.floor(ui.value / 60);
                          var minutes = ui.value - (hours * 60);
                          hours = hours.toString();
                          minutes = minutes.toString();
                          if(hours.length == 1) hours = '0' + hours;
                          if(minutes.length == 1) minutes = '0' + minutes;
                          $("#st_<?php echo $number ?>_start_time_hours").val(hours);
                          $("#st_<?php echo $number ?>_start_time_minutes").val(minutes);
                          $("#st_<?php echo $number ?>_minutes_start_to_facility_activation").val(ui.value);
                        }
                      });

                      var labelOptions = [];
                      labelOptions[0]=
                        {
                        value: '-7200',
                        text: '-5days'
                      };
                      labelOptions[1]=
                        {
                        value: '0',
                        text: '0'
                      };
                      labelOptions[2] =
                        {
                        value: '7200',
                        text: '+5dasys'
                      };
                      addLabels(stComponent, labelOptions);

                      ttComponent = $("#task_time_slider<?php echo $number ?>").slider({
                        orientation: "horizontal",
                        value:<?php
                         if ($task_time == '') {
                           echo '0';
                         } else {
                           echo $task_time;
                         }
                  ?>,
                        min: 0,
                        max: 720,
                        step: 30,
                        slide: function( event, ui ) {
                          var hours = Math.floor(ui.value / 60);
                          var minutes = ui.value - (hours * 60);
                          hours = hours.toString();
                          minutes = minutes.toString();
                          if(hours.length == 1) hours = '0' + hours;
                          if(minutes.length == 1) minutes = '0' + minutes;
                          $("#st_<?php echo $number ?>_task_time_hours").val(hours);
                          $("#st_<?php echo $number ?>_task_time_minutes").val(minutes);
                          $("#st_<?php echo $number ?>_task_length_minutes").val(ui.value);

                        }
                      });

                      var labelOptions = [];
                      labelOptions[0]=
                        {
                        value: '0',
                        text: '0hrs'
                      };
                      labelOptions[1]=
                        {
                        value: '360',
                        text: '6hrs'
                      };
                      labelOptions[2] =
                        {
                        value: '720',
                        text: '12hrs'
                      };

                      addLabels(ttComponent, labelOptions);

                      btComponent = $("#break_time_slider<?php echo $number ?>").slider({
                        orientation: "horizontal",
                        value:<?php
                         if ($break_time == '') {
                           echo '0';
                         } else {
                           echo $break_time;
                         }
                  ?>,
                        min: 0,
                        max: 720,
                        step: 30,
                        slide: function( event, ui ) {
                          var hours = Math.floor(ui.value / 60);
                          var minutes = ui.value - (hours * 60);
                          hours = hours.toString();
                          minutes = minutes.toString();
                          if(hours.length == 1) hours = '0' + hours;
                          if(minutes.length == 1) minutes = '0' + minutes;
                          $("#st_<?php echo $number ?>_break_time_hours").val(hours);
                          $("#st_<?php echo $number ?>_break_time_minutes").val(minutes);
                          $("#st_<?php echo $number ?>_break_length_minutes").val(ui.value);
                 }
               });
               //labelOptions are already defined above
               addLabels(btComponent, labelOptions);

               $('#removeShiftTemplate<?php echo $number ?>').click(function() {
                 //if there is no id for this record(db_not_exists)
                 var passId = '#' + $(this).attr('id');
                 var $inputs = $('#myForm :input:hidden');
                 //send get/post to call delete
                 $('#container<?php echo $number ?>').remove();
                 
<?php if(!$isNewShiftTemplate): ?>
  $('#adder').prepend('<h2 class="overlay">' + removeShiftTemplate(
                 <?php echo $shifttemplateform['id']->getValue()?>) + '</h2>');
  $('.overlay').fadeIn(1200, function() {
    $('.overlay').fadeOut(1200, function() {
      $('.overlay').remove();
    });
  });

<?php endif; ?>


               });
    function removeShiftTemplate(stId) {
      var r = $.ajax({
        type: 'DELETE',
        url: '<?php
              echo url_for('scenario/deleteshifttemplate') .
              '?stId=' ?>' + stId,
        async: false //the above could and should be refactored for re-usability
      }).responseText;
      return r;
    }

             });

          </script>
        </td>
      </tr>
    </tbody>
  </table>
</div>