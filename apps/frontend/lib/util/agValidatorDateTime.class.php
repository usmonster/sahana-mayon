<?php
/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
class agValidatorDateTime extends sfValidatorDateTime{

  public function convertDateArrayToString($value){
    return parent::convertDateArrayToString($value);
  }

}
?>
