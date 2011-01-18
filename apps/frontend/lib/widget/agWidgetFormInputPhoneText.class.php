<?php

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


