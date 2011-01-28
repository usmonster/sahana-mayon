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
    //$this->scenarioForm->widgetSchema->setNameFormat('scenario[%s]');
    //$this->scenarioForm = new agEventForm();
    //unset($this->scenarioForm['created_at'], $this->scenarioForm['updated_at'], $this->scenarioForm['event_name'], $this->scenarioForm['zero_hour'], $this->scenarioForm['ag_affected_area_list']);
    //we want to only get the scenario here,
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

        //have to do this for delete also, i.e. delete the event_scenario object

        $this->redirect('event/deploy?id=' . $ag_event->getId());
      }
    } else {
      //get scenario information passed from previous form
      //we should save the scenario that this event is based on
      if($request->getParameter('ag_scenario_list')){
        $this->scenario_id = $request->getParameter('ag_scenario_list');
        $this->scenarioName = Doctrine::getTable('agScenario')
            ->findByDql('id = ?', $this->scenario_id)
            ->getFirst()->scenario;
      }
      $this->metaForm = new PluginagEventDefForm();

    }
    //as a rule of thumb, actions should post to themself and then redirect
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

  public function executeFgroup(sfWebRequest $request)
  {
    
  }

  public function executeFgroupdetail(sfWebRequest $request)
  {
    
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
