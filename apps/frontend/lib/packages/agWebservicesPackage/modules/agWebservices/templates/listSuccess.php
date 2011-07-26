<?php
if ($type=='organizations') {
  include_partial('listOrgs', array('results' => $results));
} elseif ($type=='staff') {
  include_partial('listStaffs', array('results' => $results));
}
?>

<a href="<?php echo url_for('webservices_index') ?>">Back to available actions</a>
