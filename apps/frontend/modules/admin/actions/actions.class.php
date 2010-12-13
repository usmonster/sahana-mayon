<?php

/** 
* 
*
* PHP Version 5
*
* LICENSE: This source file is subject to LGPLv3.0 license
* that is available through the world-wide-web at the following URI:
* http://www.gnu.org/copyleft/lesser.html
*
* @author Full Name, Organization
* Copyright of the Sahana Software Foundation, sahanafoundation.org
*/

/**
* @todo add description of class in header
*/

class adminActions extends sfActions
{
/**
* 
* @todo add description of function above and details below
* @param $request (add description)
*/
  public function executeIndex(sfWebRequest $request)
  {
    /**
     *
     * @param sfWebRequest $request is what the user is asking of the server
     */
  }
  public function executeModuleman(sfWebRequest $request)
  {
    /**
     *
     * @param sfWebRequest $request is what the user is asking of the server
     */
    $this->modules_available = agPluginManager::getPlugins();
    if($request->getParameter('enable'))
    {
      sfSymfonyPluginManager::enablePlugin('agGisPlugin' , sfConfig::get('sf_config_dir'));
    }
      if($request->getParameter('disable'))
    {
      sfSymfonyPluginManager::disablePlugin('agGisPlugin' , sfConfig::get('sf_config_dir'));
      //disabling the plugin/module turns it off, we should remove any menu items associated with this
      //module/plugin, but this mapping needs to be stored somewhere.  maybe routing should exist in the plugin?
    }


  }
  public function executeConfig(sfWebRequest $request)
  {
    /**
     *
     * @param sfWebRequest $request is what the user is asking of the server
     */
  }
  public function executeDisplay(sfWebRequest $request)
  {     /**
      *
      * @param sfWebRequest $request should be passing in information that was submitted in the form created
      * $this->processForm($request, $this->form);
      */
    $this->form = new agReligionForm();
    $this->form->setWidgets(array('ag_religion_list' => new sfWidgetFormDoctrineChoice(array('multiple' => true, 'model' => 'agReligion'))));
    $this->form->setValidators(array(new sfValidatorDoctrineChoice(array('model' => $this->form->getModelName(),'column' => 'app_display'))));
    $food = $this->form->getObject();
    $foog = $this->form->getObject()->getReligion();
    $garb = $this->form->getObject()->id;
    //if submitted
    $this->form->bind($request->getParameter($this->form->getName()));
    if($this->form->isValid()){
      foreach($foog as $religion)
      {
        $food = $this->form->getObject();
        $this->form->getObject()->app_display[$religion] = 1;
      }
      $this->form->save();
   }
    
  }
  public function executeList(sfWebRequest $request)
  {
    $this->ag_accounts = Doctrine::getTable('agAccount')
      ->createQuery('a')
      ->execute();
  }
/**
* 
* @todo add description of function above and details below
* @param $request (add description)
*/
  public function executeShow(sfWebRequest $request)
  {
    $this->ag_account = Doctrine::getTable('agAccount')->find(array($request->getParameter('id')));
    $this->forward404Unless($this->ag_account);
  }
/**
* 
* @todo add description of function above and details below
* @param $request (add description)
*/
  public function executeNew(sfWebRequest $request)
  {
    $this->form = new agAccountForm();
  }
/**
* 
* @todo add description of function above and details below
* @param $request (add description)
*/
  public function executeCreate(sfWebRequest $request)
  {
    $this->forward404Unless($request->isMethod(sfRequest::POST));

    $this->form = new agAccountForm();

    $this->processForm($request, $this->form);

    $this->setTemplate('new');
  }
/**
* 
* @todo add description of function above and details below
* @param $request (add description)
*/
  public function executeEdit(sfWebRequest $request)
  {
    $this->forward404Unless($ag_account = Doctrine::getTable('agAccount')->find(array($request->getParameter('id'))), sprintf('Object ag_account does not exist (%s).', $request->getParameter('id')));
    $this->form = new agAccountForm($ag_account);
  }
/**
* 
* @todo add description of function above and details below
* @param $request (add description)
*/
  public function executeUpdate(sfWebRequest $request)
  {
    $this->forward404Unless($request->isMethod(sfRequest::POST) || $request->isMethod(sfRequest::PUT));
    $this->forward404Unless($ag_account = Doctrine::getTable('agAccount')->find(array($request->getParameter('id'))), sprintf('Object ag_account does not exist (%s).', $request->getParameter('id')));
    $this->form = new agAccountForm($ag_account);

    $this->processForm($request, $this->form);

    $this->setTemplate('edit');
  }
/**
* 
* @todo add description of function above and details below
* @param $request (add description)
*/
  public function executeDelete(sfWebRequest $request)
  {
    $request->checkCSRFProtection();

    $this->forward404Unless($ag_account = Doctrine::getTable('agAccount')->find(array($request->getParameter('id'))), sprintf('Object ag_account does not exist (%s).', $request->getParameter('id')));
    $mj = $ag_account->getAgAccountMjSfGuardUser()->getFirst();
    $sf = $mj->getSfGuardUser();
    $sf_user = Doctrine::getTable('sfGuardUser')->find(array($sf->id));
    $mj->delete();
    $sf->delete();
    $ag_account->delete();



    $this->redirect('admin/list');
  }

  protected function processForm(sfWebRequest $request, sfForm $form)
  {
    $form->bind($request->getParameter($form->getName()), $request->getFiles($form->getName()));
    if ($form->isValid())
    {
      $ag_account = $form->save();

      $this->redirect('admin/edit?id='.$ag_account->getId());
    }
  }
}