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
          'organization' => new sfWidgetFormInputText(
            array(),
            array('class' => 'inputGray setWidgetsScenario')
          ),
          'description' => new sfWidgetFormTextArea(
            array(),
            array('class' => 'inputGray setWidgetsDesc')
          ),
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
          'organization' => new sfValidatorString(array('trim' => true, 
                                                        'required' => true,
                                                        'max_length' => 128)
          ),
          'description' => new sfValidatorString(array('trim' => true, 'required' => false, 
                                                       'max_length' => 255, 'required' => false)
          ),
        )
    );
    sfProjectConfiguration::getActive()->loadHelpers(array ('Helper','Url', 'Asset', 'Tag'));
    $wikiUrl = url_for('@wiki');

    $this->validatorSchema->setOption('allow_extra_fields', true);
    $this->widgetSchema->setNameFormat('ag_organization[%s]');
    $this->widgetSchema->setLabels(
             array(
               'organization' => 'Organization <a href="' . $wikiUrl .  '/doku.php?id=tooltip:organization_name&do=export_xhtmlbody" class="tooltipTrigger" title="Date of Birth">?</a>',
               'description'  => 'Description <a href="' . $wikiUrl .  '/doku.php?id=tooltip:organization_description&do=export_xhtmlbody" class="tooltipTrigger" title="Date of Birth">?</a>',
             )
           );


    $sectionsDeco = new agWidgetFormSchemaFormatterSection($this->getWidgetSchema());
    $this->getWidgetSchema()->addFormFormatter('section', $sectionsDeco);
    $this->getWidgetSchema()->setFormFormatterName('section');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);
  }

}
