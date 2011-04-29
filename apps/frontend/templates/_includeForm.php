<form action="<?php echo url_for($url); ?>" id="<?php echo $id; ?>" method="post">
  <?php echo $form; ?>
  <?php if (isset($button)) : ?>
    <br />
    <input type="submit" name="<?php echo $button['name'];?>" value="<?php echo $button['value'];?>" class="<?php echo $button['class'];?>">
  <?php endif; ?>
</form>

