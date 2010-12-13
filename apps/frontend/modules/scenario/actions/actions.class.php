<?php

/**
 * extends sfActions for scenario
 *
 * PHP Version 5
 *
 * LICENSE: This source file is subject to LGPLv3.0 license
 * that is available through the world-wide-web at the following URI:
 * http://www.gnu.org/copyleft/lesser.html
 *
 * @author Full Name, Organization
 *
 * Copyright of the Sahana Software Foundation, sahanafoundation.org
 */
class scenarioActions extends sfActions
{

  /**
   *
   * @param sfWebRequest $request
   * generates a list of scenarios that are passed to the view
   */
  public function executeList(sfWebRequest $request)
  {
    $this->ag_scenarios = Doctrine_Core::getTable('agScenario')
            ->createQuery('a')
            ->select('a.*, b.*')
            ->from('agScenario a, a.agScenarioFacilityGroup b')
            ->execute();
  }

  /**
   *
   * @param sfWebRequest $request
   * generates a list of facility groups that are passed to the view
   */
  public function executeListgroup(sfWebRequest $request)
  {
    $this->ag_scenario_facility_groups = Doctrine_Core::getTable('agScenarioFacilityGroup')
            ->createQuery('a')
            ->select('a.*, afr.*, afgt.*, afgas.*, fr.*')
            ->from('agScenarioFacilityGroup a, a.agScenarioFacilityResource afr, a.agFacilityGroupType afgt, a.agFacilityGroupAllocationStatus afgas, a.agFacilityResource fr')
            ->execute();
  }

  /**
   * @todo what's this do?
   * @param sfWebRequest $request
   */
  public function executeIndex(sfWebRequest $request)
  {
    //here we can use $request to better form the index page for scenario
  }

  /**
   *
   * @param sfWebRequest $request
   * generates and passes a new scenario form to the view
   */
  public function executeGrouptype(sfWebRequest $request)
  {
    $this->ag_facility_group_types = Doctrine_Core::getTable('agFacilityGroupType')
            ->createQuery('a')
            ->execute();
    $this->grouptypeform = new agFacilityGroupTypeForm();
  }

  /**
   * @todo what's this do?
   * @param sfWebRequest $request
   */
  public function executeNew(sfWebRequest $request)
  {
    $this->form = new agScenarioForm();
  }

  /**
   *
   * @param sfWebRequest $request
   * generates and passes a new facility group form to the view
   */
  public function executeNewgroup(sfWebRequest $request)
  {
    $this->groupform = new agScenarioFacilityGroupForm();
  }

  /**
   *
   * @param sfWebRequest $request
   * triggers the creation of a scenario
   */
  public function executeCreate(sfWebRequest $request)
  {
    $this->forward404Unless($request->isMethod(sfRequest::POST));

    $this->form = new agScenarioForm();

    $this->processForm($request, $this->form);

    $this->setTemplate('new');
  }

  /**
   *
   * @param sfWebRequest $request
   * triggers the creation of a facility group
   */
  public function executeGroupcreate(sfWebRequest $request)
  {
    $this->forward404Unless($request->isMethod(sfRequest::POST));

    $this->groupform = new agScenarioFacilityGroupForm();

    $this->processGroupform($request, $this->groupform);

    $this->setTemplate('newgroup');
  }

  /**
   * @todo what's this do?
   * @param sfWebRequest $request
   */
  public function executeGrouptypecreate(sfWebRequest $request)
  {
    $this->ag_facility_group_types = Doctrine_Core::getTable('agFacilityGroupType')
            ->createQuery('a')
            ->execute();
    $this->forward404Unless($request->isMethod(sfRequest::POST));

    $this->grouptypeform = new agFacilityGroupTypeForm();

    $this->processGrouptypeform($request, $this->grouptypeform);

    $this->setTemplate('grouptype');
  }

  /**
   *
   * @param sfWebRequest $request
   * creates a scenario form populated with info for the requested scenario
   */
  public function executeEdit(sfWebRequest $request)
  {
    $this->forward404Unless(
        $ag_scenario = Doctrine_Core::getTable('agScenario')->find(array($request->getParameter('id'))), sprintf('Object ag_scenario does not exist (%s).', $request->getParameter('id')));
    $this->ag_scenario_facility_groups = Doctrine_Core::getTable('agScenarioFacilityGroup')
            ->createQuery('a')
            ->select('a.*')
            ->from('agScenarioFacilityGroup a')
            ->where('a.scenario_id = ?', $request->getParameter('id'))
            ->execute();
    $this->form = new agScenarioForm($ag_scenario);

    //check to see if there are any facility groups, if not, pass in a facility group form, set defaults.
//    if(isset($this->ag_scenario_facility_groups))
//    {
//      if(count($this->ag_scenario_facility_groups) < 1)
//      {
//        $this->groupform = new agScenarioFacilityGroupForm();
//        $this->groupform->getObject()->setScenarioId($request->getParameter('id'));
//        $this->groupform->setDefault('scenario_id', $request->getParameter('id'));
//      }
//    }
  }

  /**
   *
   * @param sfWebRequest $request
   * creates a facility group form populated with info for the requested facility group
   */
  public function executeEditgroup(sfWebRequest $request)
  {
    $this->forward404Unless($ag_scenario_facility_group = Doctrine_Core::getTable('agScenarioFacilityGroup')
            ->createQuery('a')
            ->select('a.*, afr.*, afgt.*, afrt.*, afgas.*, fr.*, af.*, s.*')
            ->from('agScenarioFacilityGroup a, a.agScenarioFacilityResource afr, a.agFacilityGroupType afgt, a.agFacilityGroupAllocationStatus afgas, afr.agFacilityResource fr, fr.agFacility af, a.agScenario s, fr.agFacilityResourceType afrt')
            ->where('a.id = ?', $request->getParameter('id'))
            ->fetchOne(), sprintf('Object ag_scenario_facility_group does not exist (%s).', $request->getParameter('id')));

    $this->groupform = new agScenarioFacilityGroupForm($ag_scenario_facility_group);
  }

  /**
   * @todo what's this do?
   * @param sfWebRequest $request
   */
  public function executeEditgrouptype(sfWebRequest $request)
  {
    $this->forward404Unless($ag_facility_group_type = Doctrine_Core::getTable('agFacilityGroupType')->find(array($request->getParameter('id'))), sprintf('Object ag_facility_group_type does not exist (%s).', $request->getParameter('id')));
    $this->ag_facility_group_types = Doctrine_Core::getTable('agFacilityGroupType')
            ->createQuery('a')
            ->execute();
    $this->grouptypeform = new agFacilityGroupTypeForm($ag_facility_group_type);
  }

  /**
   *
   * @param sfWebRequest $request
   * processing the update of a scenario
   */
  public function executeUpdate(sfWebRequest $request)
  {
    $this->forward404Unless($request->isMethod(sfRequest::POST) || $request->isMethod(sfRequest::PUT));
    $this->forward404Unless($ag_scenario = Doctrine_Core::getTable('agScenario')->find(array($request->getParameter('id'))), sprintf('Object ag_scenario does not exist (%s).', $request->getParameter('id')));
    $this->form = new agScenarioForm($ag_scenario);

    $this->processForm($request, $this->form);

    $this->setTemplate('edit');
  }

  /**
   *
   * @param sfWebRequest $request
   * processing the update of a facility group
   */
  public function executeGroupupdate(sfWebRequest $request)
  {
    $this->forward404Unless($request->isMethod(sfRequest::POST) || $request->isMethod(sfRequest::PUT));
    $this->forward404Unless($ag_scenario_facility_group = Doctrine_Core::getTable('agScenarioFacilityGroup')->find(array($request->getParameter('id'))), sprintf('Object ag_scenario_facility_group does not exist (%s).', $request->getParameter('id')));
    $this->groupform = new agScenarioFacilityGroupForm($ag_scenario_facility_group);

    $this->processGroupform($request, $this->groupform);

    $this->setTemplate('editgroup');
  }

  /**
   *
   * @param sfWebRequest $request
   * processing the update of a facility group
   */
  public function executeGrouptypeupdate(sfWebRequest $request)
  {
    $this->forward404Unless($request->isMethod(sfRequest::POST) || $request->isMethod(sfRequest::PUT));
    $this->forward404Unless($ag_facility_group_type = Doctrine_Core::getTable('agFacilityGroupType')->find(array($request->getParameter('id'))), sprintf('Object ag_facility_group_type does not exist (%s).', $request->getParameter('id')));
    $this->grouptypeform = new agFacilityGroupTypeForm($ag_facility_group_type);

    $this->processGrouptypeform($request, $this->grouptypeform);

    $this->setTemplate('grouptype');
  }

  /**
   *
   * @param sfWebRequest $request
   * processing the deletion of a scenario
   */
  public function executeDelete(sfWebRequest $request)
  {
    $request->checkCSRFProtection();

    $this->forward404Unless($ag_scenario = Doctrine_Core::getTable('agScenario')->find(array($request->getParameter('id'))), sprintf('Object ag_scenario does not exist (%s).', $request->getParameter('id')));
    $sfGroups = $ag_scenario->getAgScenarioFacilityGroup();

    //get all facility groups associated with the scenario
    foreach ($sfGroups as $sfRes) {
      $groupRes = $sfRes->getAgScenarioFacilityResource();
      //get all facility resources associated with the facility group
      foreach ($groupRes as $gRes) {
        $gRes->delete();
      }
      $sfRes->delete();
    }
    $ag_scenario->delete();


    $this->redirect('scenario/list');
  }

  /**
   *
   * @param sfWebRequest $request
   * processing the deletion of a facility group
   */
  public function executeDeletegroup(sfWebRequest $request)
  {
    $request->checkCSRFProtection();

    $this->forward404Unless($ag_scenario_facility_group = Doctrine_Core::getTable('agScenarioFacilityGroup')->find(array($request->getParameter('id'))), sprintf('Object ag_scenario_facility_group does not exist (%s).', $request->getParameter('id')));

    $fgroupRes = $ag_scenario_facility_group->getAgScenarioFacilityResource();

    //get all facility resources associated with the facility group
    foreach ($fgroupRes as $fRes) {
      //get all shifts associated with each facility resource associated with the facility group
      $fResShifts = $fRes->getAgScenarioShift();
      foreach ($fResShifts as $resShift) {
        $resShift->delete();
      }
      $fRes->delete();
    }
    $ag_scenario_facility_group->delete();

    $this->redirect('scenario/listgroup');
  }

  /**
   * @todo what's this do?
   * @param sfWebRequest $request
   */
  public function executeDeletegrouptype(sfWebRequest $request)
  {
    $request->checkCSRFProtection();

    $this->forward404Unless($ag_facility_group_type = Doctrine_Core::getTable('agFacilityGroupType')->find(array($request->getParameter('id'))), sprintf('Object ag_facility_group_type does not exist (%s).', $request->getParameter('id')));
    $ag_facility_group_type->delete();

    $this->redirect('scenario/grouptype');
  }

  /**
   *
   * @param sfWebRequest $request
   * @param sfForm $form
   *
   * processing the scenario form
   */
  protected function processForm(sfWebRequest $request, sfForm $form)
  {
    $form->bind($request->getParameter($form->getName()), $request->getFiles($form->getName()));
    if ($form->isValid()) {
      $ag_scenario = $form->save();

      $this->redirect('scenario/edit?id=' . $ag_scenario->getId());
    }
  }

  /**
   *
   * @param sfWebRequest $request
   * @param sfForm $groupform
   *
   * processing the facility group form
   */
  protected function processGroupform(sfWebRequest $request, sfForm $groupform)
  {
    $groupform->bind($request->getParameter($groupform->getName()), $request->getFiles($groupform->getName()));
    if ($groupform->isValid()) {
      $ag_scenario_facility_group = $groupform->save();

      $this->redirect('scenario/editgroup?id=' . $ag_scenario_facility_group->getId());
    }
  }

  protected function processGrouptypeform(sfWebRequest $request, sfForm $grouptypeform)
  {
    $grouptypeform->bind($request->getParameter($grouptypeform->getName()), $request->getFiles($grouptypeform->getName()));
    if ($grouptypeform->isValid()) {
      $ag_facility_group_type = $grouptypeform->save();

      $this->redirect('scenario/grouptype');
    }
  }

}
