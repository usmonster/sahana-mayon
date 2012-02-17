<?php require 'header.php'; ?>
<script>
    $(document).ready(function() {
        
        $('#nextButton').click(function(){
            showInstallDialog(); 
            $( "#progressbar" ).progressbar();
            var i = 0;
            var interval = setInterval(function() {
                $.getJSON("<?php echo $rootUri ?>/progress",
                {
                    format: "json"
                },
                function(data) {
                    var i = parseInt(data.progress);
                    if ( i > 99 ) {
                        clearInterval( interval );
                    }
                    $("#progressbar").progressbar({
                        value: i,
                        speed: 1500, 
                        easing: 'easeOutBounce'
                    })     
                    $('#step').html("<p>Status: " + data.step + "</p>");
                });              
            }, 1000);
        });
        
        function showInstallDialog(){
            $("#dialog").dialog({
                modal:true
            })
        };
  
    });
</script>
<style>
    .ui-progressbar-value { background-image: url(<?php echo $rootUri ?>/../css/jquery/images/pbar-ani.gif); }
</style>
<div id="dialog" title="Information" style="display:none">
    <p>The database is installing. This process normally takes a few minutes. Please wait.</p>
    <div id="progressbar"></div>
    <div id="step"></div>
</div>
<div class="info">
    <h3>Confirm Installation Settings</h3>
    <p>You are now ready to install the database. Please take a moment to confirm the settings below before clicking the 
        <b><i>Next</i></b> button.</p>
    <table class="requirements">
        <thead>
            <tr>
                <th>Property</th>
                <th>Value</th>
            </tr>
        </thead>
        <tbody>
            <tr class="d0">
                <td>Database Host</td>
                <td><?php echo $dbHost ?></td>
            </tr>
            <tr class="d1">
                <td>Database Port</td>
                <td><?php echo $dbPort ?></td>
            </tr>
            <tr class="d0">
                <td>Database Name</td>
                <td><?php echo $dbName ?></td>
            </tr>
            <tr class="d1">
                <td>Database User</td>
                <td><?php echo $dbUser ?></td>
            </tr>
            <tr class="d0">
                <td>Database Password</td>
                <td><?php echo isset($dbPassword) ? "********" : "" ?></td>
            </tr>
            <tr class="d1">
                <td>Super User Account</td>
                <td><?php echo $superUser ?></td>
            </tr>
            <tr class="d0">
                <td>Super User Password</td>
                <td><?php echo isset($superPassword) ? "********" : "" ?></td>
            </tr>
            <tr class="d1">
                <td>Load Sample Data</td>
                <td><?php echo $sampleData ?></td>
            </tr> 
        </tbody>
    </table>

    <p><img src="<?php echo $rootUri ?>/../images/warn.png" /> <span class="orange" style="font-size: 1.5em; f">WARNING!</span></p>
    <p>The database <b><i><?php echo $dbName ?>@<?php echo $dbHost ?></i></b> will be 
        overwritten by this installation process. Any existing data will be lost. Please 
        make a backup before clicking the <b><i>Next</i></b> button if you wish to keep
        this information.</p>
</div>
<?php require 'footer.php'; ?>