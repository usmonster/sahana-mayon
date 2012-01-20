<?php require 'header.php'; ?>

<div class="info">
    <h2>File &amp; Directory Permissions</h2>
    <table class="requirements">
        <thead>
            <tr>
                <th style="border-bottom: 1px solid #DADADA">Name</th>
                <th style="border-bottom: 1px solid #DADADA">Current</th>
                <th style="border-bottom: 1px solid #DADADA">Required</th>
                <th style="border-bottom: 1px solid #DADADA">Recommended</th>
                <th style="border-bottom: 1px solid #DADADA"></th>
            </tr>
        </thead>
        <tbody>
            <?php $i = 0; ?>
            <?php foreach ($filePerms as $perm): ?>
                <tr class="d<?php echo ($i & 1) ?>">                  
                    <td><?php echo $perm['name'] ?></td>
                    <td class="number"><?php echo $perm['current'] ?></td>
                    <td class="number"><?php echo $perm['required'] ?></td>
                    <td class="number"><?php echo $perm['recommended'] ?></td>  
                    <td><?php echo showStatus($perm['result']) ?></td>
                </tr>
                <?php $i++ ?>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

<?php require 'footer.php'; ?>