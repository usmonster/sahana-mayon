<h2>Staff Listing</h2>
<div id="tableContainer">
	<table class="singleTable" style="width: 700px;">
		<thead>
			<tr>
				<th>Id</th>
				<th>Resources Type
					<table class="singleTable" style="width: 296px;">
						<thead>
							<tr>
    						<th>Resource Type</th>
    						<th>Abbr</th>
    						<th>Description</th>
							</tr>
						</thead>
					</table>
				</th>
				<th>Staff Resource Status
					<table class="singleTable" style="width: 269px;">
						<thead>
							<tr>
    						<th>Resource Status</th>
    						<th>Description</th>
    						<th>Available</th>
							</tr>
						</thead>
					</table>
				</th>
				<th colspan="2">Organization</th>
				<th>Created at</th>
				<th>Updated at</th>
			</tr>
		</thead>
		<tbody>
		<?php if($results): foreach ($results as $key => $staff): ?>
			<tr>
				<td><a href="<?php echo url_for('staff/show?id='. $staff['id'])?>"><?php echo $staff['id'] ?></a></td>
				<td>
					<table class="singleTable" style="width: 296px;">
					  <?php foreach ($staff['resources']['resource_type'] as $k => $resource): ?>
						<tr>
    						<td><?php echo $resource['staff_resource_type']; ?></td>
    						<td><?php echo $resource['staff_resource_type_abbr']; ?></td>
    						<td><?php echo $resource['description']; ?></td>
						</tr>
						<?php endforeach; ?>
					</table>
				</td>
				
				<td>
					<table class="singleTable" style="width: 269px;">
					  <?php foreach ($staff['resources']['resource_status'] as $k => $resource): ?>
						<tr>
    						<td><?php echo $resource['staff_resource_status']; ?></td>
    						<td><?php echo $resource['description']; ?></td>
    						<td><?php echo $resource['is_available']; ?></td>
						</tr>
					<?php endforeach; ?>
					</table>
				</td>
				<td colspan="2">
        <?php foreach ($staff['organizations'] as $k => $org): ?>
        	<?php echo $org['id']; ?> - 
        	<?php echo $org['organization']; ?>
        <?php endforeach;?>
				</td>
				<td><?php echo $staff['created_at'] ?></td>
				<td><?php echo $staff['updated_at'] ?></td>
			</tr>
			<?php endforeach; endif; ?>
		</tbody>
	</table>
</div>
