<?php
/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
?>

<h2>Facility Import Status</h2>
<p>Records imported into temporary table: <?php echo $numRecordsImported ?><br/>
    Error count: <?php echo count($errors) ?></p>

<?php if (count($errors) > 0): ?>
    <hr>
    <table>
    <?php foreach ($errors as $error): ?>
        <tr>
            <td><?php echo image_tag('error.png', array('alt' => 'Error', 'width' => '24', 'height' => '24')) ?></td>
            <td><?php echo $error ?></td>
        </tr>
    <?php endforeach; ?>
    </table>
    <br/>
<?php else: ?>
            <table>
                <tr>
                    <td><?php echo image_tag('ok.png', array('alt' => 'Success', 'width' => '24', 'height' => '24')) ?></td>
                    <td>Success!</td>
                </tr>
            </table>
<?php endif; ?>




