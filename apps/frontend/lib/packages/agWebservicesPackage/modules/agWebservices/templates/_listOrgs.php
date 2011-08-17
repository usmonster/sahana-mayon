<h2>Organizations Listing</h2>

<div id="tableContainer">
	<table class="singleTable" style="width: 700px;">
		<thead>
			<tr>
				<th>Id</th>
				<th>Organization</th>
				<th>Description</th>
				<th>Entity Id</th>
				<th>Branch</th>
				<th>Email Contact</th>
				<th>Phone Contact</th>
				<th>Created at</th>
				<th>Updated at</th>
			</tr>
		</thead>
		<tbody>
		<?php if($results): foreach ($results as $key => $organization): ?>
			<tr>
				<td><a
					href="<?php echo url_for('organization/show?id='. $organization['id'])?>"><?php echo $organization['id'] ?>
				</a></td>
				<td><?php echo $organization['organization'] ?></td>
				<td><?php echo $organization['description'] ?></td>
				<td><?php echo $organization['entity_id'] ?></td>
				<td><?php echo $organization['branch'] ?></td>
				<td><?php echo $organization['email'] ?></td>
				<td><?php echo $organization['phone'] ?></td>
				<td><?php echo $organization['created_at'] ?></td>
				<td><?php echo $organization['updated_at'] ?></td>
			</tr>
			<?php endforeach; endif; ?>
		</tbody>
	</table>
</div>
