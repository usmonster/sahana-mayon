<?php

/**
 * message actions.
 *
 * @package    AGASTI_CORE
 * @subpackage message
 * @author     CUNY SPS
 * @version    SVN: $Id: actions.class.php 23810 2009-11-12 11:07:44Z Kris.Wallsmith $
 */
class messageActions extends sfActions
{
  public function executeIndex(sfWebRequest $request)
  {
    $this->ag_message_templates = Doctrine_Core::getTable('agMessageTemplate')
      ->createQuery('a')
      ->execute();
  }

  public function executeNew(sfWebRequest $request)
  {
    $this->form = new agMessageTemplateForm();
  }

  public function executeCreate(sfWebRequest $request)
  {
    $this->forward404Unless($request->isMethod(sfRequest::POST));

    $this->form = new agMessageTemplateForm();

    $this->processForm($request, $this->form);

    $this->setTemplate('new');
  }

  public function executeEdit(sfWebRequest $request)
  {
    $this->forward404Unless($ag_message_template = Doctrine_Core::getTable('agMessageTemplate')->find(array($request->getParameter('id'))), sprintf('Object ag_message_template does not exist (%s).', $request->getParameter('id')));
    $this->form = new agMessageTemplateForm($ag_message_template);
  }

  public function executeUpdate(sfWebRequest $request)
  {
    $this->forward404Unless($request->isMethod(sfRequest::POST) || $request->isMethod(sfRequest::PUT));
    $this->forward404Unless($ag_message_template = Doctrine_Core::getTable('agMessageTemplate')->find(array($request->getParameter('id'))), sprintf('Object ag_message_template does not exist (%s).', $request->getParameter('id')));
    $this->form = new agMessageTemplateForm($ag_message_template);

    $this->processForm($request, $this->form);

    $this->setTemplate('edit');
  }

  public function executeDelete(sfWebRequest $request)
  {
    $request->checkCSRFProtection();

    $this->forward404Unless($ag_message_template = Doctrine_Core::getTable('agMessageTemplate')->find(array($request->getParameter('id'))), sprintf('Object ag_message_template does not exist (%s).', $request->getParameter('id')));
    $ag_message_template->delete();

    $this->redirect('message/index');
  }

  protected function processForm(sfWebRequest $request, sfForm $form)
  {
    $form->bind($request->getParameter($form->getName()), $request->getFiles($form->getName()));
    if ($form->isValid())
    {
      $ag_message_template = $form->save();

      $this->redirect('message/edit?id='.$ag_message_template->getId());
    }
  }
}
