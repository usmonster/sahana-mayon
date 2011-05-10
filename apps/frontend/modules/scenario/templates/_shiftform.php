<div class="infoHolder scenarioshiftCounter" 
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
          <input type="submit" class="linkButton" value="Save" />
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
                  echo $scenarioshiftform['minutes_start_to_facility_activation']->render();
                  $start_time =
                      $scenarioshiftform['minutes_start_to_facility_activation']->getValue();
                  $start_hours = intval($start_time / 60);
                  $start_minutes = $start_time - ($start_hours * 60);
                  if ((strlen((string) $start_hours)) == 1)
                    $start_hours = '0' . $start_hours;
                  if ((strlen((string) $start_minutes)) == 1)
                    $start_minutes = '0' . $start_minutes;
                  ?>
                  <input type="text" id="st_start_time_hours"
                         name="st[start_time_hours]"
                         value="<?php echo $start_hours ?>"
                         class="inputGray" style="width:20px;">
                  :
                  <input type="text" id="st_start_time_minutes"
                         name="st[start_time_minutes]"
                         value="<?php echo $start_minutes ?>"
                         class="inputGray" style="width:20px;">
                </span>
              </td>
              <td>
                <div id="start_time_slider"></div>
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
                  echo $scenarioshiftform['task_length_minutes']->render();
                  $task_time =
                      $scenarioshiftform['task_length_minutes']->getValue();
                  $task_hours = intval($task_time / 60);
                  $task_minutes = $task_time - ($task_hours * 60);
                  if ((strlen((string) $task_hours)) == 1)
                    $task_hours = '0' . $task_hours;
                  if ((strlen((string) $task_minutes)) == 1)
                    $task_minutes = '0' . $task_minutes;
                  ?>
                  <input type="text" id="st_task_time_hours"
                         name="st[task_time_hours]"
                         value="<?php echo $task_hours ?>"
                         class="inputGray" style="width:20px;">
                  :
                  <input type="text" id="st_task_time_minutes"
                         name="st[task_time_minutes]"
                         value="<?php echo $task_minutes ?>"
                         class="inputGray" style="width:20px;">
                </span>
              </td>
              <td width="200px">
                <div id="task_time_slider"></div>
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
                  echo $scenarioshiftform['break_length_minutes']->render();
                  $break_time =
                      $scenarioshiftform['break_length_minutes']->getValue();
                  $break_hours = intval($break_time / 60);
                  $break_minutes = $break_time - ($break_hours * 60);
                  if ((strlen((string) $break_hours)) == 1)
                    $start_hours = '0' . $break_hours;
                  if ((strlen((string) $break_minutes)) == 1)
                    $break_minutes = '0' . $break_minutes;
                  ?>
                  <input type="text" id="st_break_time_hours"
                         name="st[break_time_hours]"
                         value="<?php echo $break_hours ?>"
                         class="inputGray" style="width:20px;">
                  :
                  <input type="text" id="st_break_time_minutes"
                         name="st[break_time_minutes]"
                         value="<?php echo $break_minutes ?>"
                         class="inputGray" style="width:20px;">

                </span>
              </td>
              <td width="200px">
                <div id="break_time_slider"></div>
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
              stComponent = $("#start_time_slider").slider({
                orientation: "horizontal",
                value:<?php
                  if ($start_time == '') {
                    echo '0';
                  } else {
                    echo $start_time;
                  }
                  ?>,
                 min: -4320,
                 max: 4320,
                 step: 60,
                 slide: function( event, ui ) {
                   var hours = Math.floor(ui.value / 60);
                   var minutes = ui.value - (hours * 60);
                   if(hours.length == 1) hours = '0' + hours;
                   if(minutes.length == 1) minutes = '0' + minutes;
                   $("#st__start_time_hours").val(hours);
                   $("#st__start_time_minutes").val(minutes);
                   $("#st__minutes_start_to_facility_activation").val(ui.value);
                 }
               });

               var labelOptions = [];
               labelOptions[0]=
                 {
                 value: '-4320',
                 text: '-3days'
               };
               labelOptions[1]=
                 {
                 value: '0',
                 text: '0day'
               };
               labelOptions[2] =
                 {
                 value: '4320',
                 text: '3days'
               };
               addLabels(stComponent, labelOptions);

               ttComponent = $("#task_time_slider").slider({
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
                         if(hours.length == 1) hours = '0' + hours;
                         if(minutes.length == 1) minutes = '0' + minutes;
                         $("#st__task_time_hours").val(hours);
                         $("#st__task_time_minutes").val(minutes);
                         $("#st__task_length_minutes").val(ui.value);

                       }
                     });

                     var labelOptions = [];
                     labelOptions[0]=
                       {
                       value: '0',
                       text: '0hours'
                     };
                     labelOptions[1]=
                       {
                       value: '360',
                       text: '6hours'
                     };
                     labelOptions[2] =
                       {
                       value: '720',
                       text: '12hours'
                     };

                     addLabels(ttComponent, labelOptions);

                     btComponent = $("#break_time_slider").slider({
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
                         if(hours.length == 1) hours = '0' + hours;
                         if(minutes.length == 1) minutes = '0' + minutes;
                         $("#st__break_time_hours").val(hours);
                         $("#st__break_time_minutes").val(minutes);
                         $("#st__break_length_minutes").val(ui.value);
                       }
                     });
                     //labelOptions are already defined above
                     addLabels(btComponent, labelOptions);

                     $('#removescenarioshift').click(function() {
                       //if there is no id for this record(db_not_exists)
                       var passId = '#' + $(this).attr('id');
                       var $inputs = $('#myForm :input:hidden');
                       //send get/post to call delete
                       $('#container').remove();
                 
<?php if (!$isNewscenarioshift): ?>
                   $('#newscenarioshifts').prepend('<h2 class="overlay">' + removescenarioshift(
  <?php echo $scenarioshiftform['id']->getValue() ?>) + '</h2>');
                                    $('.overlay').fadeIn(1200, function() {
                                      $('.overlay').fadeOut(1200, function() {
                                        $('.overlay').remove();
                                      });
                                    });

<?php endif; ?>


               });
               function removescenarioshift(stId) {
                 var r = $.ajax({
                   type: 'DELETE',
                   url: '<?php
echo url_for('scenario/deletescenarioshift') .
 '?stId='
?>' + stId,
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