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
<div id="result">
  <p><?php echo image_tag('indicator.gif') ?> please wait, calculation taking place</p>
</div>


<script type="text/javascript">
  var start = <?php echo $todeployCount; ?>;
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
    var startTime, endTime, totalProcessed, totalTimeElapsed = 0, averageTime = 0, estimateTimeLeft = 0;
    $("#result").show();
    //if(xmlHttp.readyState==4)
    // {
    do {
      // Start timing.
      startTime = new Date().getTime();

      var recordProcessed = calcBatch();
      totalProcessed += recordProcessed;
      // End Timing.
      endTime = new Date().getTime();

      totalLeft = start - totalProcessed;

      // Time elapsed for batch processing.
      intervalTimeElapsed = endTime - startTime;
      totalTimeElapsed += endTime - startTime;
      if (totalProcessed != 0) {
        averageTime = totalTimeElapsed / totalProcessed;
        estimateTimeLeft = averageTime * totalLeft;
      }

      $("#combos").html(totalLeft);
      $("#result").html('<?php echo image_tag('indicator.gif') ?> done processing '+totalProcessed + " out of "+start+ " records!<BR>Total time elapsed to process " + totalProcessed + " records: "+ (totalTimeElapsed / 1000) + 's<BR>Estimated time left to process ' + totalLeft + ' records: ' + (estimateTimeLeft / 1000) + 's');
    } while (totalLeft > 0);
    $("#result").html("done processing "+totalProcessed + " out of "+start+ " records!");
    // }
  });

</script>