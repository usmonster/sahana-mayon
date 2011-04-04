<?php

/**
 * A stub in place for creating persons who are in turn clients in an event
 *
 * LICENSE: This source file is subject to LGPLv2.1 license
 * that is available through the world-wide-web at the following URI:
 * http://www.gnu.org/licenses/lgpl-2.1.html
 *
 * @author     Charles Wisniewski, CUNY SPS
 *
 * Copyright of the Sahana Software Foundation, sahanafoundation.org
 *
 */
class agClientActions extends sfActions
{

  public function executeIndex(sfWebRequest $request)
  {
    $this->ag_clients = Doctrine_Core::getTable('agClient')
            ->createQuery('a')
            ->execute();
  }

  public function executeIn(sfWebRequest $request)
  {
    $this->form = new agClientForm();
  }

  public function executeDuplicate(sfWebRequest $request)
  {

  }

  public function executeCreate(sfWebRequest $request)
  {
    $this->forward404Unless($request->isMethod(sfRequest::POST));

    $this->form = new agClientForm();

    $this->processForm($request, $this->form);

    $this->setTemplate('new');
  }

  public function executeEdit(sfWebRequest $request)
  {
    $this->forward404Unless($ag_client = Doctrine_Core::getTable('agClient')->find(array($request->getParameter('id'))), sprintf('Object ag_client does not exist (%s).', $request->getParameter('id')));
    $this->form = new agClientForm($ag_client);
  }

  public function executeOut(sfWebRequest $request)
  {

  }

  public function executeUpdate(sfWebRequest $request)
  {
    $this->forward404Unless($request->isMethod(sfRequest::POST) || $request->isMethod(sfRequest::PUT));
    $this->forward404Unless($ag_client = Doctrine_Core::getTable('agClient')->find(array($request->getParameter('id'))), sprintf('Object ag_client does not exist (%s).', $request->getParameter('id')));
    $this->form = new agClientForm($ag_client);

    $this->processForm($request, $this->form);

    $this->setTemplate('edit');
  }

  public function executeDelete(sfWebRequest $request)
  {
    $request->checkCSRFProtection();

    $this->forward404Unless($ag_client = Doctrine_Core::getTable('agClient')->find(array($request->getParameter('id'))), sprintf('Object ag_client does not exist (%s).', $request->getParameter('id')));
    $ag_client->delete();

    $this->redirect('agClient/index');
  }

  protected function processForm(sfWebRequest $request, sfForm $form)
  {
    $form->bind($request->getParameter($form->getName()), $request->getFiles($form->getName()));
    if ($form->isValid()) {
      $ag_client = $form->save();

      $this->redirect('agClient/edit?id=' . $ag_client->getId());
    }
  }

}
