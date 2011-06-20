<h1>Ag foos List</h1>

<table>
  <thead>
    <tr>
      <th>Id</th>
      <th>Foo</th>
      <th>Bar</th>
    </tr>
  </thead>
  <tbody>
    <?php foreach ($ag_foos as $ag_foo): ?>
    <tr>
      <td><a href="<?php echo url_for('foo/show?id='.$ag_foo->getId()) ?>"><?php echo $ag_foo->getId() ?></a></td>
      <td><?php echo $ag_foo->getFoo() ?></td>
      <td><?php echo $ag_foo->getBar() ?></td>
    </tr>
    <?php endforeach; ?>
  </tbody>
</table>

  <a href="<?php echo url_for('foo/new') ?>">New</a>


<!-- Below are for testing purposes only.  Delete after testing -->

<br />

<br />

<?php #include_partial('exportXls', array('exportPath' => $exportPath, 'count' => $count)) ?>

<?php 
//echo link_to('Export Nonprocessed Staff File',
//                   'foo/export',
//                   array('exportFile' => $exportFile,
//                         'exportFileName' => $exportFileName
//                        ),
//                   'class' => 'buttonText',
//                   'target' => 'blank'
//                  );
        ?>
<a class="buttonText" href="<?php echo url_for('foo/export') ?>" target="_blank">Export Error File</a>

<!--<form action="<?php #echo url_for('foo/index')?>" method="post" name="foo">
  <table>
    <tr>
      <th>Count</th>
      <td><?php #echo $count; ?></td>
    </tr>
      <th>File</th>
      <td>
        <input type="submit" value="Export Hello to Xls" id ="export" name="exportXls" class="buttonText"/>
        <input type="hidden" value="<?php #echo $exportPath; ?>" id ="filePath" name="filePath" />
        <input type="hidden" value="<?php #echo $count; ?>" id="count" name="count" />
      </td>
    </tr>
  </table>
</form>-->
