<h1>Ag pets List</h1>

<table>
  <thead>
    <tr>
      <th>Id</th>
      <th>Pet name</th>
      <th>Event</th>
      <th>Sex</th>
      <th>Species</th>
      <th>Age</th>
      <th>Age date recorded</th>
      <th>Physical description</th>
      <th>Created at</th>
      <th>Updated at</th>
    </tr>
  </thead>
  <tbody>
    <?php foreach ($ag_pets as $ag_pet): ?>
    <tr>
      <td><a href="<?php echo url_for('agPet/edit?id='.$ag_pet->getId()) ?>"><?php echo $ag_pet->getId() ?></a></td>
      <td><?php echo $ag_pet->getPetName() ?></td>
      <td><?php echo $ag_pet->getEventId() ?></td>
      <td><?php echo $ag_pet->getSexId() ?></td>
      <td><?php echo $ag_pet->getSpeciesId() ?></td>
      <td><?php echo $ag_pet->getAge() ?></td>
      <td><?php echo $ag_pet->getAgeDateRecorded() ?></td>
      <td><?php echo $ag_pet->getPhysicalDescription() ?></td>
      <td><?php echo $ag_pet->getCreatedAt() ?></td>
      <td><?php echo $ag_pet->getUpdatedAt() ?></td>
    </tr>
    <?php endforeach; ?>
  </tbody>
</table>

  <a href="<?php echo url_for('agPet/new') ?>">New</a>
