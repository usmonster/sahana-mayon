<?php require 'header.php'; ?>

<div class="info">
    <h2>Configure Database Settings</h2>
    <p>Now you need to configure the database properties where the Sahana RMP information 
        will be stored. This MySQL database must already have been created by your 
        database administrator following the "Installation Manual". You will also need 
        a MySQL username and password setup to access it. The database tables and schema 
        will be generated automatically. Any existing data and schema in the target 
        database will be erased during the installation.</p>
    <table class="definitions">
        <tr>
            <th>Host:</th>
            <td>The host name of the database server. <br/>
                Examples: <i>localhost</i> or <i>dbsrv-01.agency.gov</i> or <i>192.168.0.31</i>
            </td>
        </tr>
        <tr>
            <th>Port:</th>
            <td>The TCP port the MySQL database is listening on. By default MySQL 
                listens on 3306. Leave this field blank if you wish to use default
                or a UNIX socket connection on the local server.</td>
        </tr>
        <tr>
            <th>Database:</th>
            <td>The name of the database. This is sometimes referred to as a catalog by
                database administrators.
            </td>
        </tr>
        <tr>
            <th>Username:</th>
            <td>The MySQL database user account the application will use.</td>
        </tr>
        <tr>
            <th>Password:</th>
            <td>The password created for the application's database user account.</td>
        </tr>
        <tr>
            <td>&nbsp;</td>
            <td><p><span class="redColorText">*</span> Required</p></td>
        </tr>
    </table>

    <?php if (isset($testResult)) : ?>
        <div class=" <?php echo ($failCount == 0) ? "success" : "error" ?>">
            <p>
                <?php echo $testResult ?>
            </p>
        </div>
    <?php endif; ?>


    <br/>
    <form class="configure" style="font-weight: normal; background-color: #FFF; color: #333" method="post" action="<?php echo $rootUri . $resourceUri ?>">
        <b>Connection Settings</b>
        <ul>
            <li>
                <label>Host:</label>
                <input type="text" name="db_host" id="db_host" class="inputGray" 
                       value="<?php echo $dbHost ?>"/> <span class="redColorText">*</span>
            </li>
            <li>
                <label>Port:</label>
                <input type="text" name="db_port" id="db_name" class="inputGray"
                       value="<?php echo $dbPort ?>" /> 
            </li>
            <li>
                <label>Database:</label>
                <input type="text" name="db_name" id="db_name" class="inputGray"
                       value="<?php echo $dbName ?>" /> <span class="redColorText">*</span>
            </li>
            <li>
                <label>Username:</label>
                <input type="text" name="db_user" id="db_user" class="inputGray"
                       value="<?php echo $dbUser ?>" /> <span class="redColorText">*</span>
            </li>
            <li>
                <label>Password:</label>
                <input type="password" name="db_pass" id="db_pass" class="inputGray" autocomplete="off"
                       value="<?php echo $dbPassword ?>" />
            </li>
        </ul>

        <b>Sample Data </b>
        <p>Sample data is provided for developers and evaluators. Enable this option
            if you want the sample data to be loaded after the database is created. 
            <span class="redColorText">DO NOT</span> enable this option for 
            production systems.</p>
        <ul>
            <li>
                <label>Sample Data:</label>
                <input id="sample" type="checkbox" name="sample_data" <?php echo ($sampleData) ? "checked" : "" ?> />
            </li>
            <li>
                <p>The database connection settings will be automatically tested before allowing 
                    you to continue to the next step.</p>
            </li>
            <li>
                <label>&nbsp;</label>
                <input class="generalButton" type="submit" value="Test Connection" name="Submit"/>
            </li>
        </ul>

    </form>
</div>

<?php require 'footer.php'; ?>