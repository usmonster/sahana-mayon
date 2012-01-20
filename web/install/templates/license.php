<?php require 'header.php'; ?>
<script>
    $(document).ready(function() {
        $("#agree").click(function() {
            $("#form0").submit();      
        });
    });
</script>
<div class="info" style="height: 400px; overflow: auto;"> 
    <?php echo nl2br($license) ?>
</div><br />
<form id="form0" class="configure" method="post" action="<?php echo $rootUri . $resourceUri ?>">
    <label for="agree">I agree</label>
    <input class="checkbox" type="checkbox" value="yes" name="agree" id="agree" <?php echo $licenseAgreement ?>/>
</form>

<?php require 'footer.php'; ?>
