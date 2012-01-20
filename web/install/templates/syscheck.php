<?php require 'header.php'; ?>

<div class="info">
    <h2>PHP System Configuration Requirements</h2>
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
            <?php foreach ($phpReqs as $req): ?>
                <tr class="d<?php echo ($i & 1) ?>">                  
                    <td><?php echo $req['name'] ?></td>
                    <td><?php echo $req['current'] ?></td>
                    <td><?php echo $req['required'] ?></td>
                    <td><?php echo $req['recommended'] ?></td>  
                    <td><?php echo showStatus($req['result']) ?></td>
                </tr>
                <?php $i++ ?>
            <?php endforeach; ?>
        </tbody>
    </table>
    <?php if ($failCount > 0) : ?>
        <h3 style="margin-top: 15px; color: #AA0000">Warning <?php echo $failCount ?> Errors Found</h3>
        <p>These PHP configuration problems must be corrected before continuing with 
            the installation. Please contact your system administrator.</p>
        <ul>
            <?php foreach ($phpReqs as $req) : ?>
                <?php if ($req['result'] < 1) : ?>
                    <li><span class="fail"><?php echo $req['name'] ?></span> : <?php echo $req['error'] ?></li>
                <?php endif; ?>
            <?php endforeach; ?>
        </ul>
    <?php endif; ?>
</div>

<?php require 'footer.php'; ?>