<?php

use_javascript('jquery.ui.custom.js');
//use_javascript('agMain.js');
use_stylesheet('jquery/jquery.ui.custom.css');
use_stylesheet('jquery/mayon.jquery.ui.css');
?>

<?php

foreach ($shifttemplateforms->getEmbeddedForms() as $key => $shifttemplateform) {
  include_partial('newshifttemplateform', array('shifttemplateform' => $shifttemplateform, 'scenario_id' => $scenario_id, 'number' => $key));
}

?>
