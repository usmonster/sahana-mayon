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
    $this->scenarioForm = new agEventForm();
    unset($this->scenarioForm['created_at'], $this->scenarioForm['updated_at'], $this->scenarioForm['event_name'], $this->scenarioForm['zero_hour'], $this->scenarioForm['ag_affected_area_list']);
    //we want to only get the scenario here,
  }

  public function executeMeta(sfWebRequest $request)
  {
    $this->metaForm = new agEventForm();
    unset($this->metaForm['created_at'], $this->metaForm['updated_at'], $this->metaForm['ag_affected_area_list'], $this->metaForm['ag_scenario_list']);

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

  public function executeFgroup(sfWebRequest $request)
  {
    
  }

  public function executeFgroupdetail(sfWebRequest $request)
  {
    
  }

  public function executeFacility(sfWebRequest $request)
  {

  }

  public function executeFacilityupdate(sfWebRequest $request)
  {

  }

  public function executeDeploy(sfWebRequest $request)
  {

  }

    public function executeResolution(sfWebRequest $request)
  {

  }

   public function executePost(sfWebRequest $request)
  {

  }

  public function executeCreate(sfWebRequest $request)
  {
    $this->forward404Unless($request->isMethod(sfRequest::POST));

    $this->form = new agEventForm();

    $this->processForm($request, $this->form);

    $this->setTemplate('new');
  }

  public function executeEdit(sfWebRequest $request)
  {
    $this->forward404Unless($ag_event = Doctrine_Core::getTable('agEvent')->find(array($request->getParameter('id'))), sprintf('Object ag_event does not exist (%s).', $request->getParameter('id')));
    $this->form = new agEventForm($ag_event);
  }

  public function executeUpdate(sfWebRequest $request)
  {
    $this->forward404Unless($request->isMethod(sfRequest::POST) || $request->isMethod(sfRequest::PUT));
    $this->forward404Unless($ag_event = Doctrine_Core::getTable('agEvent')->find(array($request->getParameter('id'))), sprintf('Object ag_event does not exist (%s).', $request->getParameter('id')));
    $this->form = new agEventForm($ag_event);

    $this->processForm($request, $this->form);

    $this->setTemplate('edit');
  }

  public function executeDelete(sfWebRequest $request)
  {
    $request->checkCSRFProtection();

    $this->forward404Unless($ag_event = Doctrine_Core::getTable('agEvent')->find(array($request->getParameter('id'))), sprintf('Object ag_event does not exist (%s).', $request->getParameter('id')));
    $ag_event->delete();

    $this->redirect('event/index');
  }

  protected function processForm(sfWebRequest $request, sfForm $form)
  {
    $form->bind($request->getParameter($form->getName()), $request->getFiles($form->getName()));
    if ($form->isValid()) {
      $ag_event = $form->save();

      $this->redirect('event/edit?id=' . $ag_event->getId());
    }
  }

}
