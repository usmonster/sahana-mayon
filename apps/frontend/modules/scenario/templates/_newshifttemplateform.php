<?php
use_stylesheets_for_form($newshifttemplateform);
use_javascripts_for_form($newshifttemplateform);
use_javascript('jquery.ui.custom.js');
use_javascript('agMain.js');
use_stylesheet('jquery/jquery.ui.custom.css');
?>
<style>
  #timeframe > div.demo { padding: 10px !important; }
</style>
<script>
  var SLIDE = $(function() {
    $( "#timeframe" ).slider({
      min: 0,
      max: 150,
      value: 50,
      slide: function(event, ui) {
        $("#slider-input").val(ui.value);
      }
    }
  );

    $(document).ready(SLIDE);
  });

</script>






<form action="<?php echo url_for('scenario/newshifttemplates?id=' . $scenario_id); ?>" method="post">
<?php #if (!$shifttemplateform->getObject()->isNew()):  ?>
  <!--
  <input type="hidden" name="sf_method" value="put" />
  -->
<?php #endif;  ?>
    <table>
      <tfoot>
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
            echo $newshifttemplateform['staff_resource_type_id']->renderRow() . $newshifttemplateform['facility_resource_type_id']->renderRow();
            ?>
          </td>
        </tr>
        <tr colspan="2" style="background-color: wheat;">
          <td style="text-align:right;">
            <?php
            echo $newshifttemplateform['shift_status_id']->renderRow();
            echo '<br />'; //this is only for testing
            echo $newshifttemplateform['task_id']->renderRow();
            echo '<br />'; //this is only for testing
            echo $newshifttemplateform['deployment_algorithm_id']->renderRow();
            echo '<br />'; //this is only for testing
            echo $newshifttemplateform['shift_repeats']->renderRow() . $newshifttemplateform['max_staff_repeat_shifts']->renderRow();
            ?>
          </td>
          <td>
            <?php
            echo $newshifttemplateform['minutes_start_to_facility_activation']->renderRow();
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
</form>
