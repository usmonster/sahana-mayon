<?php
/**
* This widget is used to format values from an agPhoneContact object so they'll
* use that they will use phone number formatting that has been defined in the schema.
*
* PHP Version 5
*
* LICENSE: This source file is subject to LGPLv3.0 license
* that is available through the world-wide-web at the following URI:
* http://www.gnu.org/copyleft/lesser.html
*
* @author Nils Stolpe, CUNY SPS
*
* Copyright of the Sahana Software Foundation, sahanafoundation.org
**/
class agWidgetFormInputPhoneText extends sfWidgetFormInputText
{
  protected function configure($options = array(), $attributes = array())
  {
    parent::configure($options, $attributes);

    $this->addOption('match_pattern', $value = null);
    $this->addOption('replacement_pattern', $value = null);
  }

  public function render($name, $value = null, $attributes = array(), $errors = array())
  {
    return $this->renderTag('input', array_merge(array('type' => $this->getOption('type'),
                                                       'name' => $name,
                                                       'value' => preg_replace(
                                                                    $this->getOption('match_pattern'),
                                                                    $this->getOption('replacement_pattern'),
                                                                    $value
                                                                  )),
                                                 $attributes));
  }
}


