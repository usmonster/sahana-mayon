<?php

/**
 * event actions.
 *
 * @package    AGASTI_CORE
 * @subpackage event
 * @author     CUNY SPS
 * @version    SVN: $Id: actions.class.php 23810 2009-11-12 11:07:44Z Kris.Wallsmith $
 */
class eventActions extends sfActions
{

  public function executeIndex(sfWebRequest $request)
  {
    $this->scenarioForm = new sfForm();
    $this->scenarioForm->setWidgets(array(
      'ag_scenario_list' => new sfWidgetFormDoctrineChoice(array('multiple' => false, 'model' => 'agScenario'))
    ));
    
    $this->scenarioForm->getWidgetSchema()->setLabel('ag_scenario_list', false);
  }

  public function executeDeploy(sfWebRequest $request)
  {
    $this->eventName = Doctrine::getTable('agEvent')
            ->findByDql('id = ?', $request->getParameter('id'))
            ->getFirst()->event_name;
  }

  public function executeMeta(sfWebRequest $request)
  {
    if ($request->isMethod(sfRequest::POST) && !$request->getParameter('ag_scenario_list')) {
      $this->form = new PluginagEventDefForm();
      $this->form->bind($request->getParameter($this->form->getName()), $request->getFiles($this->form->getName()));
      if ($this->form->isValid()) {
        $ag_event = $this->form->save();

        $ag_event_scenario = new agEventScenario();
        $ag_event_scenario->setScenarioId($request->getParameter('scenario_id'));
        $ag_event_scenario->setEventId($ag_event->getId());
        $ag_event_scenario->save();
        if (isset($updating)) { //replace with usable check to update
          $eventStatusObject = Doctrine_Query::create()
                  ->from('agEventStatus a')
                  ->where('a.id =?', $ag_event->getId())
                  ->execute()->getFirst();
        }
        $ag_event_status = isset($eventStatusObject) ? $eventStatusObject : new agEventStatus();

        $ag_event_status->setEventStatusTypeId(1);
        $ag_event_status->setEventId($ag_event->getId());
        $ag_event_status->time_stamp = new Doctrine_Expression('NOW()');
        $ag_event_status->save();

        $ag_event_status = new agEventStatus();
        

        //have to do this for delete also, i.e. delete the event_scenario object

        $this->redirect('event/deploy?id=' . $ag_event->getId());
      }
    } else {
      //get scenario information passed from previous form
      //we should save the scenario that this event is based on
      if ($request->getParameter('ag_scenario_list')) {
        $this->scenario_id = $request->getParameter('ag_scenario_list');
        $this->scenarioName = Doctrine::getTable('agScenario')
                ->findByDql('id = ?', $this->scenario_id)
                ->getFirst()->scenario;
      }
      $this->metaForm = new PluginagEventDefForm();
    }
    //as a rule of thumb, actions should post to themself and then redirect
  }

  public function migrateScenarioToEvent(integer $scenario_id)
  {
    // 1. Regenerate staff pool
    // 2. Copy over staff pool
    // Regenerate scenario shift

    // Copy over scenario shift

  }

  public function executeList(sfWebRequest $request)
  {
    $this->ag_events = Doctrine_Core::getTable('agEvent')
            ->createQuery('a')
            ->select('a.*')
            ->from('agEvent a')
            ->execute();

    /**
     * @todo make query better above to reduce db calls
     */
  }

  public function executeReview(sfWebRequest $request)
  {

  }

  public function executeGis(sfWebRequest $request)
  {
    
  }

  public function executeDashboard(sfWebRequest $request)
  {
    
  }

  public function executeActive(sfWebRequest $request)
  {
    
  }

  public function executeStaff(sfWebRequest $request)
  {
    
  }

  public function executeStaffin(sfWebRequest $request)
  {

  }

  public function executeInconfirm(sfWebRequest $request)
  {

  }

  public function executeStaffout(sfWebRequest $request)
  {
    
  }

  public function executeOutconfirm(sfWebRequest $request)
  {

  }

  public function executeListgroups(sfWebRequest $request)
  {
    $query = Doctrine_Core::getTable('agEventFacilityGroup')
            ->createQuery('a')
            ->select('a.*, afr.*, afgt.*, fr.*')
            ->from('agEventFacilityGroup a, a.agEventFacilityResource afr, a.agFacilityGroupType afgt, a.agFacilityResource fr');

    if ($request->hasParameter('id')) {
      $query->where('a.event_id = ?', $request->getParameter('id'));
      $this->event = Doctrine_Query::create()
                      ->select()
                      ->from('agEvent')
                      ->where('id = ?', $request->getParameter('id'))
                      ->execute()->getFirst();
    }

    $this->ag_event_facility_groups = $query->execute();
//$this->forward($module, $action) i think we need to forward here instead of just listfacilitygroup template because we have to
    //$this->setTemplate(sfConfig::get('sf_app_template_dir') . DIRECTORY_SEPARATOR . 'listFacilityGroup');
  }

  public function executeGroupdetail(sfWebRequest $request)
  {
    $this->eventFacilityGroup = Doctrine_Query::create()
        ->select()
        ->from('agEventFacilityGroup')
        ->where('id = ?', $request->getParameter('id'))
        ->fetchOne();
    $this->event = Doctrine_Query::create()
        ->select()
        ->from('agEvent')
        ->where('id = ?', $request->getParameter('eid'))
        ->fetchOne();
    $this->blorg = array();
    foreach($this->eventFacilityGroup->getAgFacilityResource() as $facRes) {
      $q = Doctrine_Query::create()
          ->select('id')
          ->from('agEventFacilityResource')
          ->where('facility_resource_id = ?', $facRes->id)
          ->andWhere('event_facility_group_id = ?', $this->eventFacilityGroup->id)
          ->execute()->getFirst()->id;
       $this->blorg[] = $this->foo($q);
    }
  }

  private function foo($frId)
  {
    $staffMinCount = array();
    $staffMaxCount = array();
    $query = Doctrine_Query::create()
        ->select('SUM(a.minimum_staff) as foo')
        ->from('agEventShift a')
        ->where('a.event_facility_resource_id = ?', $frId)
        ->andWhere('a.shift_status_id = ?', 2)
        ->execute();
    return $query[0]['foo'];
  }

  public function executeFacility(sfWebRequest $request)
  {
    
  }

  public function executeReport(sfWebRequest $request)
  {

  }

  public function executeFacilityupdate(sfWebRequest $request)
  {

  }

  public function executeResolution(sfWebRequest $request)
  {
    
  }

  public function executePost(sfWebRequest $request)
  {
    
  }

  public function executeDelete(sfWebRequest $request)
  {
    $request->checkCSRFProtection();

    $this->forward404Unless($ag_event = Doctrine_Core::getTable('agEvent')->find(array($request->getParameter('id'))), sprintf('Object ag_event does not exist (%s).', $request->getParameter('id')));
    $ag_event->delete();

    $this->redirect('event/index');
  }

}
