<?php require 'header.php'; ?>
<div class="info">

    <h2>Super User Account Settings</h2>
    <p>The super user is the first user account created. It also serves as the 
        override account for password recovery or system maintenance.</p>

    <table class="definitions">
        <tr>
            <th>Full Name:</th>
            <td>The full name of the administrator.</td>
        </tr>
        <tr>
            <th>Email:</th>
            <td>The email address the system will use for the administrator account.</td>
        </tr>
        <tr>
            <th>Login:</th>
            <td>This is the account name of the super user. It is recommended that you use
                something other than <i>root</i>, <i>admin</i> or <i>Administrator</i> since
                these are commonly guessed.
            </td>
        </tr>
        <tr>
            <th>Password:</th>
            <td>The password created for the super user account. Use of a complex string
                of upper and case letters, numbers and special characters is highly encouraged.</td>
        </tr>
        <tr>
            <td>&nbsp;</td>
            <td><p><span class="redColorText">*</span> Required</p></td>
        </tr>
    </table>
    <br/>
    <?php if ($failCount > 0 && count($errors) > 0) : ?>
        <div class="error">
            <ul>
                <?php foreach ($errors as $e) : ?>
                    <li><?php echo $e ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
    <?php endif; ?>
    <b>Settings</b>
    <form class="configure" style="font-weight: normal; background-color: #FFF; color: #333"  method="post" action="<?php echo $rootUri . $resourceUri ?>">
        <ul>
            <li>
                <label>Friendly Name:</label>
                <input type="text" name="admin_name" id="admin_name" class="inputGray"
                       value="<?php echo $adminName ?>" />
            </li>
            <li>
                <label>Email:</label>
                <input type="text" name="admin_email" id="admin_email" class="inputGray"
                       value="<?php echo $adminEmail ?>" />
            </li>
            <li>
                <label>Login:</label>
                <input type="text" name="admin_user" id="admin_user" class="inputGray"
                       value="<?php echo $superUser ?>" /> <span class="redColorText">*</span>
            </li>
            <li>
                <label>Password:</label>
                <input type="password" name="admin_pass" id="admin_pass" class="inputGray" autocomplete="off"
                       value="<?php echo $superPassword ?>" /> <span class="redColorText">*</span>
            </li>
            <li>
                <label>&nbsp;</label>
                <input class="generalButton" type="submit" value="Save Settings" name="Submit"/>
            </li>
        </ul>              
    </form>

</div>
<?php require 'footer.php'; ?>