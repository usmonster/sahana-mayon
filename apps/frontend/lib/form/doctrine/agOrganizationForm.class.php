<?php

/**
 * An extension of an organization base form to process the edit and show forms of organization and its related records.
 *
 * PHP Version 5
 *
 * LICENSE: This source file is subject to LGPLv3.0 license
 * that is available through the world-wide-web at the following URI:
 * http://www.gnu.org/copyleft/lesser.html
 *
 * @author Shirley Chan, CUNY SPS
 *
 * Copyright of the Sahana Software Foundation, sahanafoundation.org
 */
class agOrganizationForm extends BaseagOrganizationForm
{
  public function configure()
  {
  /*
   * configure() extends the base method to remove unused fields
   */
    parent::configure();

    unset(
        $this['updated_at'],
        $this['created_at'],
        $this['entity_id'],
        $this['ag_branch_list']
    );
  }

}
