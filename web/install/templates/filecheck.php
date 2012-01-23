<?php require 'header.php'; ?>

<div class="info">
    <h2>File &amp; Directory Permissions</h2>
    <p>The Sahana Resource Management Program requires the web server have read and write privileges
        to the following directories. You many need to contact the web server
        system administrator to correct any problems discovered by the permissions check.</p>
    <ul>
        <li><span class="green">Success</span> - Recommended value met or exceeded.</li>
        <li><span class="orange">Passed</span> - Minimum requirement found.</li>
        <li><span class="fail">Failed</span> - Module or setting does not meet minimum requirement.</li>
    </ul>
    <table class="requirements">
        <thead>
            <tr>
                <th style="border-bottom: 1px solid #DADADA">Directory Name</th>
                <th style="border-bottom: 1px solid #DADADA">Server Value</th>
                <th style="border-bottom: 1px solid #DADADA">Minimum Required</th>
                <th style="border-bottom: 1px solid #DADADA">Recommended Permission</th>
                <th style="border-bottom: 1px solid #DADADA"></th>
            </tr>
        </thead>
        <tbody>
            <?php $i = 0; ?>
            <?php foreach ($filePerms as $perm): ?>
                <tr class="d<?php echo ($i & 1) ?>">                  
                    <td><?php echo $perm['name'] ?></td>
                    <td style="text-align:center"><?php echo $perm['current'] ?></td>
                    <td style="text-align:center"><?php echo $perm['required'] ?></td>
                    <td style="text-align:center"><?php echo $perm['recommended'] ?></td>  
                    <td><?php echo showStatus($perm['result']) ?></td>
                </tr>
                <?php $i++ ?>
            <?php endforeach; ?>
        </tbody>
    </table>
    <?php if ($failCount > 0) : ?>
        <div class="error">
            <h3 style="margin-top: 15px; color: #AA0000">Warning <?php echo $failCount ?> Errors Found</h3>
            <p>These PHP file permission problems must be corrected before continuing with 
                the installation. Please contact your system administrator.</p>
        </div>
    <?php else : ?>
        <div class="success">
            <p>
                The permissions check has passed. Click <b><i>Next</i></b> to continue.
            </p>
        </div>

    <?php endif; ?>
</div>

<?php require 'footer.php'; ?>