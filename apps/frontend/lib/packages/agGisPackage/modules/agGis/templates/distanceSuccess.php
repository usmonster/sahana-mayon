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

<h3>Calculate Distances</h3>

<p>There are currently <span class="logName"><?php echo $combinationCount; ?></span> combinations of staff and facilities without distances calculated.</p>

<?php echo link_to_function('calculate distances', 'calcCall()', array('class' => 'buttonSmall')) ?>

<br/>
<span id="indicator">indicate!</span>
