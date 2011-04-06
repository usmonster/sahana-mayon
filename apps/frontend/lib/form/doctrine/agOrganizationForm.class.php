<?php

/**
 * An extension of an organization base form to process the edit and
 * show forms of organization and its related records.
 *
 * PHP Version 5.3
 *
 * LICENSE: This source file is subject to LGPLv2.1 license
 * that is available through the world-wide-web at the following URI:
 * http://www.gnu.org/licenses/lgpl-2.1.html
 *
 * @author Shirley Chan, CUNY SPS
 *
 * Copyright of the Sahana Software Foundation, sahanafoundation.org
 */
class agOrganizationForm extends BaseagOrganizationForm
{

  /** @method setup()
   * Unset widgets that are auto-filled, unnecessary, or whose relations
   * are not properly defined without using embedded forms.
   */
  public function setup()
  {
    unset(
        $this['updated_at'],
        $this['created_at'],
        $this['ag_branch_list']
    );
  }

  /** @method configure()
   * In the method it is creating and setting both widgets and
   * validtion for the form.
   */
  public function configure()
  {
    /*
     * configure() extends the base method to remove unused fields
     */

    unset(
        $this['id'],
        $this['updated_at'],
        $this['created_at'],
        $this['ag_branch_list']
    );

    $this->setWidgets(
        array(
          'id' => new sfWidgetFormInputHidden(),
          'entity_id' => new sfWidgetFormInputHidden(),
          'organization' => new sfWidgetFormInputText(),
          'description' => new sfWidgetFormInputText(),
        )
    );

    $this->setValidators(
        array(
          'id' => new sfValidatorChoice(
              array(
                'choices' => array($this->getObject()->get('id')),
                'empty_value' => $this->getObject()->get('id'),
                'required' => false)
          ),
          'organization' => new sfValidatorString(
              array('required' => true, 'max_length' => 128)
          ),
          'description' => new sfValidatorString(
              array('required' => false, 'min_length' => 1, 'max_length' => 255)
          ),
        )
    );

    $this->validatorSchema->setOption('allow_extra_fields', true);
    $this->widgetSchema->setNameFormat('ag_staff_resource_organization[%s]');

    $custDeco = new agWidgetFormSchemaFormatterInline($this->getWidgetSchema());
    $this->getWidgetSchema()->addFormFormatter('custDeco', $custDeco);
    $this->getWidgetSchema()->setFormFormatterName('custDeco');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);
  }

}
