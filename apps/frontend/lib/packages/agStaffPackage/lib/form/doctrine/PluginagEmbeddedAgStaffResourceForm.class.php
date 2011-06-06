<?php

/**
 * agStaffResource form.
 *
 * @package    AGASTI_CORE
 * @subpackage form
 * @author     CUNY SPS
 * @version    SVN: $Id: sfDoctrinePluginFormTemplate.php 23810 2009-11-12 11:07:44Z Kris.Wallsmith $
 */
class PluginagEmbeddedAgStaffResourceForm extends PluginagStaffResourceForm
{

  public function configure()
  {
    $this->setWidgets(array(
      'id'                       => new sfWidgetFormInputHidden(),
      'staff_resource_type_id'   => new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('agStaffResourceType'), 'add_empty' => false, 'method' => 'getStaffResourceType')),
      'staff_resource_status_id' => new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('agStaffResourceStatus'), 'add_empty' => false, 'method' => 'getStaffResourceStatus')),
      'organization_id'          => new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('agOrganization'), 'add_empty' => false, 'method' => 'getOrganization')),
    ));

    $this->setValidators(array(
      'id'                       => new sfValidatorChoice(array('choices' => array($this->getObject()->get('id')), 'empty_value' => $this->getObject()->get('id'), 'required' => false)),
      'staff_resource_type_id'   => new sfValidatorDoctrineChoice(array('model' => $this->getRelatedModelName('agStaffResourceType'))),
      'staff_resource_status_id' => new sfValidatorDoctrineChoice(array('model' => $this->getRelatedModelName('agStaffResourceStatus'))),
      'organization_id'          => new sfValidatorDoctrineChoice(array('model' => $this->getRelatedModelName('agOrganization'))),
    ));

    sfProjectConfiguration::getActive()->loadHelpers(array ('Helper','Url', 'Asset', 'Tag'));
    $this->wikiUrl = url_for('@wiki');

    $this->getWidget('staff_resource_type_id')->setLabel('Staff Resource Type ' . '<a href="'. url_for('@wiki') . '/doku.php?id=tooltip:staff_resource&do=export_xhtmlbody" class="tooltipTrigger" title="Staff Resource">?</a>');
    $this->getWidget('staff_resource_status_id')->setLabel('Staff Resource Status ' . '<a href="'. url_for('@wiki') . '/doku.php?id=tooltip:staff_resource_status&do=export_xhtmlbody" class="tooltipTrigger" title="Staff Resource Status">?</a>');
    $this->getWidget('organization_id')->setLabel('Organization ' . '<a href="'. url_for('@wiki') . '/doku.php?id=tooltip:organization&do=export_xhtmlbody" class="tooltipTrigger" title="Organization">?</a>');
    $custDeco = new agWidgetFormSchemaFormatterInlineLeftLabel($this->getWidgetSchema());
    $this->getWidgetSchema()->addFormFormatter('custDeco', $custDeco);
    $this->getWidgetSchema()->setFormFormatterName('custDeco');
  }
}
