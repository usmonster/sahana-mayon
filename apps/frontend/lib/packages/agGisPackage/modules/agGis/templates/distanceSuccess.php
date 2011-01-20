<?php use_helper('JavascriptBase') ?>
<script language="Javascript">
  function calcCall()
  {
    document.getElementById("indicator").innerHTML =
      '<?php
echo image_tag('indicator.gif')
//and when this is DONE, it should be set back to nothing. or a success image
?>'

      }
</script>
<noscript>
  <p>You have JavaScript enabled.</p>
</noscript>

<h2>Calculate Distances</h2>

<p>In order to deploy staff Agasti must first determine the distance between that staff member and
facilities they may work at.  To calculate the distances between your staff and facilities click
the button below.</p>

<p>There are currently <span class="logName"><?php echo $combinationCount; ?></span> combinations of staff and facilities without distances calculated.</p>

<?php echo link_to_function('Calculate Distances', 'calcCall()', array('class' => 'buttonSmall')) ?>

<br/>
<span id="indicator">indicate!</span>
