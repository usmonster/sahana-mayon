<?php
  $wizardOp = array('step' => 2);
  $sf_response->setCookie('wizardOp', json_encode($wizardOp));
  include_partial('wizard', array('wizardDiv' => $wizardDiv));
?>
<br />
<script language="Javascript" type="text/javascript"> 
    $(document).ready(function(){
        var maxHeight=0;

        $('div.inlineLists').each(function(){
          maxHeight = Math.max(maxHeight, $(this).height());
        });

       $('div.inlineLists').height(maxHeight);
    })

</script>
<h2>Edit Required Resource Types for Scenario</h2> <br />
<p>Scenarios are plans for emergency responders.  Using the Scenario Creator you'll add facilities,
  staff, and other resources to your plan.  For now, name the Scenario and give it a brief
  description.</p>

<?php include_partial('resourceForm', array('resourceForm' => $resourceForm,'scenario_id' => $scenario_id)) ?>

<p> After you've completed the description, click "Save and Continue" to move to the next step in
  creating the Scenario.</p>