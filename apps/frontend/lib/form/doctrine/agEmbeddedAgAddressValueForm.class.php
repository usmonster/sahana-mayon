<?php

/**
 * agEmbeddedAgAddressValueForm.
 *
 * PHP Version 5.3
 *
 * LICENSE: This source file is subject to LGPLv2.1 license
 * that is available through the world-wide-web at the following URI:
 * http://www.gnu.org/licenses/lgpl-2.1.html
 *
 * @author Nils Stolpe, CUNY SPS
 *
 * Copyright of the Sahana Software Foundation, sahanafoundation.org
 * */
class agEmbeddedAgAddressValueForm extends agAddressValueForm
{

  public function setup()
  {
    $this->setWidgets(
        array('id' => new sfWidgetFormInputHidden(),
          'value' => new sfWidgetFormInputText(array(), array('class' => 'inputGray')),
          'address_element_id' => new sfWidgetFormInputHidden())
    );

    $this->setValidators(
        array(
          'id' => new sfValidatorChoice(
              array('choices' => array($this->getObject()->get('id')),
                'empty_value' => $this->getObject()->get('id'),
                'required' => false)
          ),
          'value' => new sfValidatorString(array('max_length' => 255, 'required' => false)),
          'address_element_id' => new sfValidatorDoctrineChoice(array('model' =>
            $this->getRelatedModelName('agAddressElement')))
        )
    );
  }

}
