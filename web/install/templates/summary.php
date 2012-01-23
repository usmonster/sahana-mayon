<?php require 'header.php'; ?>
<div class="info">
    <h3>Installation Summary</h3>
    <p>The installation is complete. Click the <b><i>Go to Login Page</i></b> button to go the 
    Sahana Resource Management Program login page.</p>
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