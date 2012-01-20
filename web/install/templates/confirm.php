<?php require 'header.php'; ?>
<script>
    $(document).ready(function() {
        
        $('#nextButton').click(function(){
            showInstallDialog();
            
            var iNow = new Date().setTime(new Date().getTime() + 10 * 1000); 
            var iEnd = new Date().setTime(new Date().getTime() + 250 * 1000);
            
            $('#progressbar').anim_progressbar({start: iNow, finish: iEnd, interval: 1000});
        });
        
        
        function showInstallDialog(){
            $("#dialog").dialog({
                modal:true
            })
        };
        
        jQuery.fn.anim_progressbar = function (aOptions) {
            // def values
            var iCms = 1000;
            var iMms = 60 * iCms;
            var iHms = 3600 * iCms;
            var iDms = 24 * 3600 * iCms;

            // def options
            var aDefOpts = {
                start: new Date(), // now
                finish: new Date().setTime(new Date().getTime() + 60 * iCms), // now + 60 sec
                interval: 100
            }
            var aOpts = jQuery.extend(aDefOpts, aOptions);
            var vPb = this;

            // each progress bar
            return this.each(
            function() {
                var iDuration = aOpts.finish - aOpts.start;

                // calling original progressbar
                $(vPb).children('.pbar').progressbar();

                // looping process
                var vInterval = setInterval(
                function(){
                    var iLeftMs = aOpts.finish - new Date(); // left time in MS
                    var iElapsedMs = new Date() - aOpts.start, // elapsed time in MS
                    iDays = parseInt(iLeftMs / iDms), // elapsed days
                    iHours = parseInt((iLeftMs - (iDays * iDms)) / iHms), // elapsed hours
                    iMin = parseInt((iLeftMs - (iDays * iDms) - (iHours * iHms)) / iMms), // elapsed minutes
                    iSec = parseInt((iLeftMs - (iDays * iDms) - (iMin * iMms) - (iHours * iHms)) / iCms), // elapsed seconds
                    iPerc = (iElapsedMs > 0) ? iElapsedMs / iDuration * 100 : 0; // percentages

                    // display current positions and progress
                    $(vPb).children('.percent').html('<b>'+iPerc.toFixed(1)+'%</b>');
                    $(vPb).children('.elapsed').html(iDays+' days '+iHours+'h:'+iMin+'m:'+iSec+'s</b>');
                    $(vPb).children('.pbar').children('.ui-progressbar-value').css('width', iPerc+'%');

                    // in case of Finish
                    if (iPerc >= 100) {
                        clearInterval(vInterval);
                        $(vPb).children('.percent').html('<b>100%</b>');
                        $(vPb).children('.elapsed').html('Finished');
                    }
                } ,aOpts.interval
            );
            });
        }
    });
</script>
<style>
    .pbar .ui-progressbar-value { background-image: url(<?php echo $rootUri ?>/../css/jquery/images/pbar-ani.gif); }
    .pbar .ui-progressbar-value {display:block !important}
    .pbar {overflow: hidden}
    .percent {position:relative;text-align: right;}
    .elapsed {position:relative;text-align: right;}
</style>
<div id="dialog" title="Information" style="display:none">
    <p>The database is installing. This process normally takes a few minutes. Please wait.</p>
    <div id="progressbar">
        <div class="percent"></div>
        <div class="pbar"></div>
        <div class="elapsed"></div>
    </div>
</div>
<div class="info">
    <h3>Confirm Installation Settings</h3>
    <p>Please take a moment to confirm the settings below before clicking the 
        <b>Next</b> button.</p>
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

    <p><span class="warningColor">WARNING!</span></p>
    <p>The database <i><?php echo $dbName ?>@<?php echo $dbHost ?></i> will be 
        overwritten by this installation. Any existing data will be lost. Please 
        make a backup before clicking the <b>Next</b> button if you wish to keep
        this information.</p>
</div>
<?php require 'footer.php'; ?>