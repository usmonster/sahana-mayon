<?php
  use_javascript('agMain.js');

?>
<h2>Scenario Shift Templates</h2>
<?php
  include_partial('wizard', array('wizardDiv' => $wizardDiv));
?>
<p>Shift Templates are templates for when responding staff will be scheduled to work.  The times 
  entered are relative, and when an event is created time will become specific to that event.</p>
<h3>Shift Templates for
  <span class="highlightedText"">
  <?php echo $scenarioName ?>
  </span>
  Scenario:
</h3>

<?php include_partial('shifttemplateholder', array('shifttemplateforms' => $shifttemplateforms, 'scenario_id' => $scenario_id)) ?>
<div id ="newshifttemplates">
<span class="smallLinkButton addShiftTemplate" id="adder">+ Add Shift Template</span> <hr />

  <script>

    function addShiftTemplate(num) {
      var r = $.ajax({
        type: 'GET',
        url: '<?php echo url_for('scenario/addshifttemplate?id=' . $scenario_id) . '?num=' ?>' + num,
        async: false
      }).responseText;
      return r;
    }
    $().ready(function() {
      $('.addShiftTemplate').click(function() {
        var passId = '#' + $(this).attr('id');
        var $poster = $(this);
        var templates = $('.shiftTemplateCounter').length
        $(passId).parent().prepend(addShiftTemplate(templates) + '<br \>');
        $("#shift_template_" + templates + "_staff_resource_type_id").focus();
        $("#start_time_slider" + templates).slider({
                  orientation: "horizontal",
        value:50,
        min: -750,
        max: 750,
        step: 5,
        slide: function( event, ui ) {
          $("#shift_template_" + templates + "_minutes_start_to_facility_activation").val(ui.value);
        }
        });
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
</div>