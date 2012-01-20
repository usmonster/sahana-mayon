<?php require 'header.php'; ?>
<div class="info">
    <h3>Installation Summary</h3>
    <table class="requirements">
        <thead>
            <tr>
                <th>Action</th>
                <th>Result</th>
            </tr>
        </thead>
        <tbody>
            <?php $i = 0; ?>
            <?php foreach ($results as $result) : ?>
                <tr class="d<?php echo ($i & 1) ?>">
                    <td><?php echo $result[0] ?></td>
                    <td><?php echo showStatus($result[1]) ?></td>
                </tr>
                <?php $i++ ?>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>
<?php require 'footer.php'; ?>