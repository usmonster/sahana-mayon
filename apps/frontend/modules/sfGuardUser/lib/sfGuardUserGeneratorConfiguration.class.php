<?php

/**
 * sfGuardUser module configuration.
 *
 * @subpackage sfGuardUser
 * @author     Fabien Potencier
 * @version    SVN: $Id: sfGuardUserGeneratorConfiguration.class.php 23319 2009-10-25 12:22:23Z Kris.Wallsmith $
 */
class sfGuardUserGeneratorConfiguration extends BaseSfGuardUserGeneratorConfiguration
{
public function getFilterDisplay()
{
  $display = parent::getFilterDisplay();

  //echo "<pre>";
  //print_r($this->configuration['list']['display']['last_login']);
  //print_r($this->configuration);

  //echo "WTF<br>";
  //echo sfView::getViewName();
  //echo "</pre>";
  return $display;
}

  public function getListDisplay()
  {

      //return array(  0 => '=username',  1 => 'created_at',  2 => 'updated_at');
      return parent::getListDisplay();
  }

  public function getListLayout()
  {
    return parent::getListLayout();
  }
}
