<?php

/**
 * Pet actions stub
 *
 * LICENSE: This source file is subject to LGPLv2.1 license
 * that is available through the world-wide-web at the following URI:
 * http://www.gnu.org/licenses/lgpl-2.1.html
 *
 * @author CUNY SPS
 *
 * Copyright of the Sahana Software Foundation, sahanafoundation.org
 *
 */
class agPetActions extends sfActions
{
  public function executeIndex(sfWebRequest $request)
  {
    $this->ag_pets = Doctrine_Core::getTable('agPet')
      ->createQuery('a')
      ->execute();
  }

  public function executeNew(sfWebRequest $request)
  {
    $this->form = new agPetForm();
        unset($this->form['created_at'], $this->form['updated_at']);
  }

  public function executeCreate(sfWebRequest $request)
  {
    $this->forward404Unless($request->isMethod(sfRequest::POST));

    $this->form = new agPetForm();

    $this->processForm($request, $this->form);

    $this->setTemplate('new');
  }

  public function executeEdit(sfWebRequest $request)
  {
    $this->forward404Unless($ag_pet = Doctrine_Core::getTable('agPet')->find(array($request->getParameter('id'))), sprintf('Object ag_pet does not exist (%s).', $request->getParameter('id')));
    $this->form = new agPetForm($ag_pet);
  }

  public function executeUpdate(sfWebRequest $request)
  {
    $this->forward404Unless($request->isMethod(sfRequest::POST) || $request->isMethod(sfRequest::PUT));
    $this->forward404Unless($ag_pet = Doctrine_Core::getTable('agPet')->find(array($request->getParameter('id'))), sprintf('Object ag_pet does not exist (%s).', $request->getParameter('id')));
    $this->form = new agPetForm($ag_pet);

    $this->processForm($request, $this->form);

    $this->setTemplate('edit');
  }

  public function executeDelete(sfWebRequest $request)
  {
    $request->checkCSRFProtection();

    $this->forward404Unless($ag_pet = Doctrine_Core::getTable('agPet')->find(array($request->getParameter('id'))), sprintf('Object ag_pet does not exist (%s).', $request->getParameter('id')));
    $ag_pet->delete();

    $this->redirect('agPet/index');
  }

  protected function processForm(sfWebRequest $request, sfForm $form)
  {
    $form->bind($request->getParameter($form->getName()), $request->getFiles($form->getName()));
    if ($form->isValid())
    {
      $ag_pet = $form->save();

      $this->redirect('agPet/edit?id='.$ag_pet->getId());
    }
  }
}
