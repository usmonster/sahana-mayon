<!-- Nils's & Charles's modifications -->

<?php
//foreach ($toplinks as $toplink) {
//  echo link_to($toplink['name'], $toplink['route'], array('class' => 'navButton'));
////  foreach ($secondlinks as $secondlink) {
////    $a = 4;
////  }
//  foreach ($secondlinks as $secondlink) {
//    if($secondlink['parent'] == $toplink['name']) {
//      echo link_to($secondlink['name'], $secondlink['route'], array('class' => 'navButton'));
//    }
//  }
//}
?>

<?php echo $menu->render(); ?>