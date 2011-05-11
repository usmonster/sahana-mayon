<?php

foreach ($shifttemplateforms->getEmbeddedForms() as $key => $shifttemplateform) {
  include_partial('shifttemplateform', array('shifttemplateform' => $shifttemplateform, 'scenario_id' => $scenario_id, 'number' => $key));
}

?>
