    <?php foreach ($pages as $page): ?>
      <?php echo link_to($page['name'], $page['route'], array('class' => 'navButton')); ?>
    <?php endforeach; ?>