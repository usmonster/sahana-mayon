<?php

/**
 * profile actions.
 *
 * @package    AGASTI_CORE
 * @subpackage profile
 * @author     CUNY SPS
 * @version    SVN: $Id: actions.class.php 23810 2009-11-12 11:07:44Z Kris.Wallsmith $
 */
class profileActions extends agActions
{
  public function executeIndex(sfWebRequest $request)
  {
    $r = $request;
    $this->sf_guard_user_profiles = Doctrine_Core::getTable('sfGuardUserProfile')
      ->createQuery('a')
      ->execute();
  }

  public function executeNew(sfWebRequest $request)
  {
    $this->form = new sfGuardUserProfileForm();
  }

  public function executeCreate(sfWebRequest $request)
  {
    $this->forward404Unless($request->isMethod(sfRequest::POST));

    $this->form = new sfGuardUserProfileForm();
    unset($this->form['created_at']);
    unset($this->form['updated_at']);

    $this->processForm($request, $this->form);

    $this->setTemplate('new');
  }

  public function executeEdit(sfWebRequest $request)
  {
    $this->forward404Unless($sf_guard_user_profile = Doctrine_Core::getTable('sfGuardUserProfile')->find(array($request->getParameter('id'))), sprintf('Object sf_guard_user_profile does not exist (%s).', $request->getParameter('id')));
    $this->form = new sfGuardUserProfileForm($sf_guard_user_profile);
  }

  public function executeUpdate(sfWebRequest $request)
  {
    $this->forward404Unless($request->isMethod(sfRequest::POST) || $request->isMethod(sfRequest::PUT));
    $this->forward404Unless($sf_guard_user_profile = Doctrine_Core::getTable('sfGuardUserProfile')->find(array($request->getParameter('id'))), sprintf('Object sf_guard_user_profile does not exist (%s).', $request->getParameter('id')));
    $this->form = new sfGuardUserProfileForm($sf_guard_user_profile);

    $this->processForm($request, $this->form);

    $this->setTemplate('edit');
  }

  public function executeDelete(sfWebRequest $request)
  {
    $request->checkCSRFProtection();

    $this->forward404Unless($sf_guard_user_profile = Doctrine_Core::getTable('sfGuardUserProfile')->find(array($request->getParameter('id'))), sprintf('Object sf_guard_user_profile does not exist (%s).', $request->getParameter('id')));
    $sf_guard_user_profile->delete();

    $this->redirect('profile/index');
  }

  protected function processForm(sfWebRequest $request, sfForm $form)
  {

    $form->bind($request->getParameter($form->getName()), $request->getFiles($form->getName()));
    $valid = $form->isValid();
    $errors = $form->hasErrors();
    $err = $form->getErrorSchema();
    $globals = $form->getGlobalErrors();
    if ($form->isValid())
    {
      $sf_guard_user_profile = $form->save();

      $this->redirect('profile/edit?id='.$sf_guard_user_profile->getId());
    } else {

    }
  }
}
