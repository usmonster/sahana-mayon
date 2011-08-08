<? echo "<?xml version=\"1.0\" encoding=\"utf-8\" ?>"?>
<results><?php foreach ($results as $k => $entity): ?>

  <entity type="<?php echo $type?>"><?php foreach ($entity as $key => $value): if (is_object($value)): foreach ($value as $v): ?>
  
  <<?php echo $key ?>><?php echo $v ?></<?php echo $key ?>><?php endforeach; ?><?php else: ?>
    
  <<?php echo $key ?>><?php echo $value?></<?php echo $key ?>><?php endif; ?><?php endforeach; ?>
  
  </entity><?php endforeach; ?>
  
</results>