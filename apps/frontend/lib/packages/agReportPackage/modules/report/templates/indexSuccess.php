<h2>Report List</h2>

<table>
  <thead>
    <tr>
      <th>Id</th>
      <th>Report name</th>
      <th>Report description</th>
      <th>Created at</th>
      <th>Updated at</th>
    </tr>
  </thead>
  <tbody>
    <?php foreach ($ag_reports as $ag_report): ?>
    <tr>
      <td><a href="<?php echo url_for('report/edit?id='.$ag_report->getId()) ?>"><?php echo $ag_report->getId() ?></a></td>
      <td><?php echo $ag_report->getReportName() ?></td>
      <td><?php echo $ag_report->getReportDescription() ?></td>
      <td><?php echo $ag_report->getCreatedAt() ?></td>
      <td><?php echo $ag_report->getUpdatedAt() ?></td>
    </tr>
    <?php endforeach; ?>
  </tbody>
</table>

  <a href="<?php echo url_for('report/new') ?>">New</a>
