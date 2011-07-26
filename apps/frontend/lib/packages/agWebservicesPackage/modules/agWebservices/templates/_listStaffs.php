<h2>Staff Listing</h2>

<div id="tableContainer">
	<table class="singleTable" style="width: 700px;">
		<thead>
			<tr>
				<th>Id</th>
				<th>Person Names</th>
				<th>Staff Resource Type</th>
				<th>Date of Birth</th>
				<th>Email</th>
				<th>Phone</th>
				<th>Address</th>
				<th>Created at</th>
				<th>Updated at</th>
			</tr>
		</thead>
		<tbody>
		<?php if($results): foreach ($results as $key => $staff): ?>
			<tr>
				<td><a href="<?php echo url_for('staff/show?id='. $staff['id'])?>"><?php echo $staff['id'] ?>
				</a></td>
				<td>
					<table>
						<tr>
						<?php foreach ($staff['person_names'] as $name): ?>
							<td><?php echo $name ?></td>
							<?php endforeach; ?>
						</tr>
					</table>
				</td>
				<td>
					<table class="singleTable" style="width: 200px;">
						<tr>
						<?php foreach ($staff['staff_resource_type'] as $sr): ?>
							<td><?php echo $sr ?></td>
							<?php endforeach; ?>
						</tr>
						<tr>
						<?php foreach ($staff['staff_resource_type_abbr'] as $sr): ?>
							<td><?php echo $sr ?></td>
							<?php endforeach; ?>
						</tr>
						<tr>
						<?php foreach ($staff['staff_resource_type_description'] as $sr): ?>
							<td><?php echo $sr ?></td>
							<?php endforeach; ?>
						</tr>
					</table>
				</td>
				<td><?php echo $staff['date_of_birth'] ?>
				
				</th>
				<td><?php echo null ?>
				
				</th>
				<td><?php echo null ?>
				
				</th>
				<td><?php echo null ?>
				
				</th>
				<td><?php echo $staff['created_at'] ?></td>
				<td><?php echo $staff['updated_at'] ?></td>
			</tr>
			<?php endforeach; endif; ?>
		</tbody>
	</table>
</div>
