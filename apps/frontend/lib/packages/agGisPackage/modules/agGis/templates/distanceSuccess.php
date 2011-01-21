<?php use_helper('JavascriptBase') ?>

<noscript>
  <p>You have JavaScript enabled.</p>
</noscript>

<h2>Calculate Distances</h2>

<p>In order to deploy staff Agasti must first determine the distance between that staff member and
  facilities they may work at.  To calculate the distances between your staff and facilities click
  the button below.</p>

<p>There are currently <span id="combos" class="logName"><?php echo $combinationCount; ?></span> combinations of staff and facilities without distances calculated.</p>


<a href="#" id="calculate" class="buttonSmall">Calculate all Distances</a>
<br/>
<div id="result" style="display: none;">
  <p><?php echo image_tag('indicator.gif') ?> please wait, calculation taking place</p>
</div>


<script type="text/javascript">
  var start = <?php echo $combinationCount; ?>;
  var totalLeft = start;
  var totalProcessed = 0;

  function calcBatch() {
    var count = 0;
    $.ajax({
      async: false,
      type: "POST",
      url: window.location.pathname,
      success: function(html)
      {
        count = parseInt(html);
      }
    });
    return count;
  }

  $('#calculate').click(function()
  {
    $("#result").show();
    //if(xmlHttp.readyState==4)
    // {
    do {
      totalProcessed += calcBatch();
      totalLeft = start - totalProcessed;
      $("#combos").html(totalLeft);
      $("#result").html('<?php echo image_tag('indicator.gif') ?> done processing '+totalProcessed + " out of "+start+ " records!");
    } while (totalLeft > 0);
    $("#result").html("done processing "+totalProcessed + " out of "+start+ " records!");
    // }
  });

</script>