<ul>
    <li style="text-align: right; list-style: none">
        <?php if (isset($installSuccess) && $installSuccess == true): ?>
            <a id="nextButton" href="../../" class="generalButton">Go to Login Page</a>
        <?php else : ?>
            <?php if (isset($prevStep)) : ?> 
                <a id="prevButton" href="<?php echo $rootUri . $prevStep ?>" class="generalButton">Previous</a>
                <a id="cancelButton" href="<?php echo $rootUri; ?>/cancel" class="deleteButton">Cancel</a>
            <?php endif; ?>
            <? if (isset($nextStep) && $failCount == 0) : ?>
                <a id="nextButton" href="<?php echo $rootUri . $nextStep ?>" class="continueButton">Next</a>
            <?php endif; ?>
        <?php endif; ?>
    </li>
</ul>

</div>
</div>
</div>
<div id="footer">
    <img src="<?php echo $rootUri ?>/../images/sahana_foundation.png" alt="Sahana Foundation Logo" />
    <img src="<?php echo $rootUri ?>/../images/Seal_of_Pennsylvania.png" alt="State seal of Pennsylvania" />
    <img src="<?php echo $rootUri ?>/../images/Seal_of_Connecticut.png" alt="State seal of Connecticut" />
    <img src="<?php echo $rootUri ?>/../images/Seal_of_New_Jersey.png" alt="State seal of New Jersey" />
    <img src="<?php echo $rootUri ?>/../images/Seal_of_New_York.png" alt="State seal of New York" />
    <img src="<?php echo $rootUri ?>/../images/rcpt_logo.png" alt="Regional Catastrophic Planning Team" />
</div>
</body>
</html>
