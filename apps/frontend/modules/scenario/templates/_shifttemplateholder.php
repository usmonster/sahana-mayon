<?php
use_javascript('jquery.ui.custom.js');
//use_javascript('agMain.js');
use_stylesheet('jquery/jquery.ui.custom.css');
?>
<style>
  #timeframe > div.demo { padding: 10px !important; }
</style>



<form action="<?php echo url_for('scenario/newshifttemplates?id=' . $scenario_id); ?>" method="post">
<?php foreach ($shifttemplateforms as $shifttemplateform): ?>
    <div class="infoHolder shiftTemplateCounter">
<?php include_partial('newshifttemplateform', array('shifttemplateform' => $shifttemplateform, 'scenario_id' => $scenario_id)) ?>

  </div>
<?php endforeach; ?>
</form>
<div class="displayInline shiftTemplateCounter">
  <a href="#" name="groupStatus" class="includeAndAdd linkText" id="staff_id_1">Add Shift Template</a>
  <script>
    function addSlider(element) {
      var slider = $('.value-slider', element).slider({
        orientation: "horizontal",
        value:50,
        min: 0,
        max: 100,
        step: 5,
        slide: function( event, ui ) {
          $("#slider-input").val(ui.value);
        }
      });

    }

    function addShiftTemplate(num) {
      var r = $.ajax({
        type: 'GET',
        url: '<?php echo url_for('scenario/addshifttemplate?id=' . $scenario_id) . '?num=' ?>' + num,
        async: false
      }).responseText;
      return r;
    }
    $().ready(function() {
      $('.linkText').click(function() {
        var passId = '#' + $(this).attr('id');
        var $poster = $(this);
        var templates = $('.shiftTemplateCounter').length
        $(passId).parent().prepend(addShiftTemplate('templates') + '<br \>');
        addSlider($('#timeframe'));
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