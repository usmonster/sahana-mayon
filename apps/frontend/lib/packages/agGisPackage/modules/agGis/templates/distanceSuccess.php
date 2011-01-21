<?php use_helper('JavascriptBase') ?>

<noscript>
  <p>You have JavaScript enabled.</p>
</noscript>

<h2>Calculate Distances</h2>

<p>In order to deploy staff Agasti must first determine the distance between that staff member and
  facilities they may work at.  To calculate the distances between your staff and facilities click
  the button below.</p>

<p>There are currently <span class="logName"><?php echo $combinationCount; ?></span> combinations of staff and facilities without distances calculated.</p>


<a href="#" id="calculate" class="buttonSmall">Calculate all Distances</a>
<br/>
<div id="result"></div>


<script type="text/javascript">
  $('#calculate').click(function()
  {
    $('#result').html('<p>please wait, calculation taking place <?php echo image_tag('indicator.gif') ?></p>');
    //if(xmlHttp.readyState==4)
    // {
    $.post('http://trunk/gis/distance', function(datum) {
      $('#result').html(datum);
    });

    // }

    //and when this is DONE, it should be set back to nothing. or a success image

  });
</script>

<?php if(isset($awesomecool))
      {
        echo $awesomecool;
      } ?>