<?php

/**
 * agEmbeddedFacilityStaffResourceForm extends agFacilityStaffResource form base class, used primarily as an embedded form for
 *   agStaffResourceRequiremenForm
 *
 * PHP Version 5
 *
 * LICENSE: This source file is subject to LGPLv3.0 license
 * that is available through the world-wide-web at the following URI:
 * http://www.gnu.org/copyleft/lesser.html
 *
 * @author Charles Wisniewski, CUNY SPS
 *
 * Copyright of the Sahana Software Foundation, sahanafoundation.org
 */
class agEmbeddedFacilityStaffResourceForm extends BaseagFacilityStaffResourceForm
{
  public function configure()
  {
    parent::configure();

    /**
     * Remove unused fields
     */
    unset(
        $this['created_at'],
        $this['updated_at']
        );
  }

  public function setup()
  {
    /**
     * Remove unused fields
     */
    unset(
        $this['created_at'],
        $this['updated_at']
        );
    parent::setup();

    $resDeco = new agWidgetFormSchemaFormatterRow($this->getWidgetSchema());
    $this->getWidgetSchema()->addFormFormatter('row', $resDeco);
    $this->getWidgetSchema()->setFormFormatterName('row');

  }

  public function getModelName()
  {
    return 'agFacilityStaffResource';
  }

}