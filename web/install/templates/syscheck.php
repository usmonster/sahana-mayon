<?php require 'header.php'; ?>

<div class="info">
    <h2>PHP System Configuration Requirements</h2>
    <p>The Sahana Resource Management Program requires a set of minimum PHP modules and
        web application environment settings. You many need to contact the web server
        system administrator to correct any problems discovered by the system check.</p>
    <ul>
        <li><span class="green">Success</span> - Recommended value met or exceeded.</li>
        <li><span class="orange">Passed</span> - Minimum requirement found.</li>
        <li><span class="fail">Failed</span> - Module or setting does not meet minimum requirement.</li>
    </ul>
    <table class="requirements">
        <thead>
            <tr>
                <th style="border-bottom: 1px solid #DADADA">Name</th>
                <th style="border-bottom: 1px solid #DADADA">Server Value</th>
                <th style="border-bottom: 1px solid #DADADA">Minimum</th>
                <th style="border-bottom: 1px solid #DADADA">Recommended</th>
                <th style="border-bottom: 1px solid #DADADA"></th>
            </tr>
        </thead>
        <tbody>
            <?php $i = 0; ?>
            <?php foreach ($phpReqs as $req): ?>
                <tr class="d<?php echo ($i & 1) ?>">                  
                    <td><span class="<?php echo ($req['result'] == 0) ? "fail" : "" ?>"><?php echo $req['name'] ?></span></td>
                    <td><span class="<?php echo ($req['result'] == 0) ? "fail" : "" ?>"><?php echo $req['current'] ?></span></td>
                    <td><?php echo $req['required'] ?></td>
                    <td><?php echo $req['recommended'] ?></td>  
                    <td><?php echo showStatus($req['result']) ?></td>
                </tr>
                <?php $i++ ?>
            <?php endforeach; ?>
        </tbody>
    </table>
    <?php if ($failCount > 0) : ?>
        <div class="error">
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
        </div>
    <?php else : ?>
        <div class="success">
            <p>
                The system has passed. Click <b><i>Next</i></b> to continue.
            </p>
        </div>

    <?php endif; ?>
</div>

<?php require 'footer.php'; ?>