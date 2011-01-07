<?php

/**
 * foo actions.
 *
 * @package    AGASTI_CORE
 * @subpackage foo
 * @author     CUNY SPS
 * @version    SVN: $Id: actions.class.php 23810 2009-11-12 11:07:44Z Kris.Wallsmith $
 */
class agFooActions extends sfActions
{
  public function executeIndex(sfWebRequest $request)
  {
    $this->ag_foos = Doctrine_Core::getTable('agFoo')
      ->createQuery('a')
      ->execute();
  }

  public function executeShow(sfWebRequest $request)
  {
    $this->ag_foo = Doctrine_Core::getTable('agFoo')->find(array($request->getParameter('id')));
    $this->forward404Unless($this->ag_foo);
  }

  public function executeNew(sfWebRequest $request)
  {
    $this->form = new agFooForm();
  }

  public function executeCreate(sfWebRequest $request)
  {
    $this->forward404Unless($request->isMethod(sfRequest::POST));

    $this->form = new agFooForm();

    $this->processForm($request, $this->form);

    $this->setTemplate('new');
  }

  public function executeEdit(sfWebRequest $request)
  {
    $this->forward404Unless($ag_foo = Doctrine_Core::getTable('agFoo')->find(array($request->getParameter('id'))), sprintf('Object ag_foo does not exist (%s).', $request->getParameter('id')));
    $this->form = new agFooForm($ag_foo);
  }

  public function executeUpdate(sfWebRequest $request)
  {
    $this->forward404Unless($request->isMethod(sfRequest::POST) || $request->isMethod(sfRequest::PUT));
    $this->forward404Unless($ag_foo = Doctrine_Core::getTable('agFoo')->find(array($request->getParameter('id'))), sprintf('Object ag_foo does not exist (%s).', $request->getParameter('id')));
    $this->form = new agFooForm($ag_foo);

    $this->processForm($request, $this->form);

    $this->setTemplate('edit');
  }

  public function executeDelete(sfWebRequest $request)
  {
    $request->checkCSRFProtection();

    $this->forward404Unless($ag_foo = Doctrine_Core::getTable('agFoo')->find(array($request->getParameter('id'))), sprintf('Object ag_foo does not exist (%s).', $request->getParameter('id')));
    $ag_foo->delete();

    $this->redirect('foo/index');
  }

  protected function processForm(sfWebRequest $request, sfForm $form)
  {
    $form->bind($request->getParameter($form->getName()), $request->getFiles($form->getName()));
    if ($form->isValid())
    {
      $ag_foo = $form->save();

      $this->redirect('foo/edit?id='.$ag_foo->getId());
    }
  }
}
