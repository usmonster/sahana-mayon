<?php

/**
 * report actions.
 *
 * @package    AGASTI_CORE
 * @subpackage report
 * @author     CUNY SPS
 * @version    SVN: $Id: actions.class.php 23810 2009-11-12 11:07:44Z Kris.Wallsmith $
 */
class reportActions extends sfActions
{
  public function executeIndex(sfWebRequest $request)
  {
    $this->ag_reports = Doctrine_Core::getTable('agReportGenerator')
      ->createQuery('a')
      ->execute();
  }

  public function executeNew(sfWebRequest $request)
  {
    $this->form = new agReportMakerForm();
  }

  public function executeCreate(sfWebRequest $request)
  {
    $this->forward404Unless($request->isMethod(sfRequest::POST));

    $this->form = new agReportMakerForm();

    $this->processForm($request, $this->form);

    $this->setTemplate('new');
  }

  public function executeEdit(sfWebRequest $request)
  {
    $this->forward404Unless($ag_report = Doctrine_Core::getTable('agReport')->find(array($request->getParameter('id'))), sprintf('Object ag_report does not exist (%s).', $request->getParameter('id')));
    $this->form = new agReportMakerForm($ag_report);
  }

  public function executeUpdate(sfWebRequest $request)
  {
    $this->forward404Unless($request->isMethod(sfRequest::POST) || $request->isMethod(sfRequest::PUT));
    $this->forward404Unless($ag_report = Doctrine_Core::getTable('agReport')->find(array($request->getParameter('id'))), sprintf('Object ag_report does not exist (%s).', $request->getParameter('id')));
    $this->form = new agReportForm($ag_report);

    $this->processForm($request, $this->form);

    $this->setTemplate('edit');
  }

  public function executeDelete(sfWebRequest $request)
  {
    $request->checkCSRFProtection();

    $this->forward404Unless($ag_report = Doctrine_Core::getTable('agReport')->find(array($request->getParameter('id'))), sprintf('Object ag_report does not exist (%s).', $request->getParameter('id')));
    $ag_report->delete();

    $this->redirect('report/index');
  }

  public function executeList(sfWebRequest $request)
  {   
     $this->ag_reports = agDoctrineQuery::create()
        ->select('*')
        ->from('agReportGenerator s')
        ->execute(array());
  }

  protected function processForm(sfWebRequest $request, sfForm $form)
  {
    $form->bind($request->getParameter($form->getName()), $request->getFiles($form->getName()));
    if ($form->isValid())
    {
      $ag_report = $form->save();

      $this->redirect('report/edit?id='.$ag_report->getId());
    }
  }
}
