<?php
  use_javascript('agMain.js');

?>
<h2>Shift Templates: <span class="highlightedText"><?php echo $scenarioName ?></span></h2>
<?php
  include_partial('wizard', array('wizardDiv' => $wizardDiv));
?>
<p>Shift Templates are templates for when responding staff will be scheduled to work.  The times 
  entered are relative, and when an event is created time will become specific to that event.</p>
<form action="<?php echo url_for('scenario/shifttemplates?id=' . $scenario_id); ?>" method="post" name="shift_template">
<?php include_partial('shifttemplateholder', array('shifttemplateforms' => $shifttemplateforms, 'scenario_id' => $scenario_id)) ?>
<div id ="newshifttemplates">
<span class="smallLinkButton addShiftTemplate" id="adder">+ Add Shift Template</span>

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
      });

    });

  </script>
</div>
    <input type="submit" class="linkButton" value="Save, Generate Shifts and Continue" name="Continue" />

</form>
