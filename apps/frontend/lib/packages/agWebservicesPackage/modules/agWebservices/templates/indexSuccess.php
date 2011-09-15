<h2 id="web_service">Web Services</h2>

<p>
	More information:
	<?php echo link_to('Launchpad', 'https://blueprints.launchpad.net/sahana-agasti/+spec/web-services')?>
</p>

<h4>Available actions</h4>
<table cellspacing="20">
	<tr>
		<td><?php echo link_to('Staff', '@webservices_list?datapoint=staff', array('class' => 'linkButton width140', 'id' => 'staff_link', 'datapoint' => 'staff')) ?>
		</td>
		<td><?php echo link_to('Organizations', '@webservices_list?datapoint=organizations', array('class' => 'linkButton width140', 'id' => 'organizations_link', 'datapoint' => 'organizations')) ?>
		</td>
	</tr>
</table>
<a href="<?php echo url_for('/') ?>">Home</a>
