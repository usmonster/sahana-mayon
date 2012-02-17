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
</div>
<?php if ($failCount == 0) : ?>
    <div class="success">
        <p>
            Thank you. Please click <b><i>Next</i></b> to continue.
        </p>
    </div>
<?php else : ?>
    <br />
<?php endif; ?>

<form id="form0" class="configure" method="post" action="<?php echo $rootUri . $resourceUri ?>">
    <label for="agree">I agree</label>
    <input class="checkbox" type="checkbox" value="yes" name="agree" id="agree" <?php echo $licenseAgreement ?>/>
</form>

<?php require 'footer.php'; ?>
