[
<?php $nb = count($results); $i = 0 ; foreach ($results as $url => $entity): ++$i ?>
{
  "type": "<?php echo $type ?>", 
  <?php $nb1 = count($entity); $j = 0; foreach ($entity as $key => $value): ++$j ?>
"<?php echo $key ?>": <?php echo (is_object($value))?json_encode($value->getRawValue()):json_encode($value); echo ($nb1 == $j ? '' : ',') ?> 
  <?php endforeach ?>
}<?php echo $nb == $i ? '' : ',' ?>

<?php endforeach ?>
]