<h2>Staff Messaging: <span class="highlightedText"><?php echo urldecode($eventName); ?> </span></h2>
<p>Use this page to send messages and receive responses to your staff for <span class="highlightedText"><?php echo urldecode($eventName); ?></span>.</p>
<a href="#" class="generalButton">Export Staff Contact List</a>
<br />
<br />
<a href="#" class="generalButton">Import Staff Responses</a>
<br />
<br />
<a href="#" class="generalButton">Preview Confirmed Staff Pool</a>
<br />
<br />
<a href="<?php echo url_for('event/deploystaff?event=' . urlencode($eventName)); ?>" class="generalButton">Deploy Staff to Facilities</a>