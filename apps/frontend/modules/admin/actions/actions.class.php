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
  public function executePacman(sfWebRequest $request)
  {
    /**
     *
     * @param sfWebRequest $request is what the user is asking of the server
     */

    $this->packages_available = agPluginManager::getPackages();
    if($request->getParameter('enable'))
    {
      $enable_array = json_decode($request->getParameter('enable'));
      $this->getContext()->getConfiguration()->enablePackages($enable_array);
      //sfSymfonyPluginManager::enablePlugin('agGisPlugin' , sfConfig::get('sf_config_dir'));
    }
    if($request->getParameter('disable'))
    {
      $disable_array = json_decode($request->getParameter('disable'));
      $this->getContext()->getConfiguration()->disablePackages($disable_array);

      //sfSymfonyPluginManager::disablePlugin('agGisPlugin' , sfConfig::get('sf_config_dir'));
      //disabling the plugin/module turns it off, we should remove any menu items associated with this
      //module/plugin, but this mapping needs to be stored somewhere.  maybe routing should exist in the plugin?
    }


  }

  public function executeGlobals(sfWebRequest $request)
  {
    if($ag_global_param = Doctrine_Core::getTable('agGlobalParam')->find(array($request->getParameter('param')))){
      $this->paramform = new agGlobalParamForm($ag_global_param);
    }
    else{
      $this->paramform = new agGlobalParamForm();
    }
    $this->ag_global_params = Doctrine_Core::getTable('agGlobalParam')
        ->createQuery('a')
        ->execute();

    if($request->getParameter('delete'))
    {
      //$request->checkCSRFProtection();

      $this->forward404Unless($ag_global_param = Doctrine_Core::getTable('agGlobalParam')->find(array($request->getParameter('deleteparam'))), sprintf('There is no such parameter (%s).', $request->getParameter('deleteparam')));
      $ag_global_param->delete();

      $this->redirect('admin/globalparams');
    }

    if($request->getParameter('update'))
    {
      $this->forward404Unless($request->isMethod(sfRequest::POST) || $request->isMethod(sfRequest::PUT));
      //$this->forward404Unless($ag_global_param = Doctrine::getTable('agGlobalParam')->findAll()->getFirst(), sprintf('Object ag_account does not exist (%s).', $request->getParameter('id')));

      //are we editing or creating a new param
      $this->processParam($request, $this->paramform);
    }
  }
  public function executeConfig(sfWebRequest $request)
  {
    /**
     * @param sfWebRequest $request is what the user is asking of the server
     */
    
    if($request->getParameter('saveconfig'))
    {
        $file = sfConfig::get('sf_config_dir') . '/config.yml';
      $config_array = sfYaml::load($file);
        $config_array['admin']['admin_name'] = $_POST['admin_name'];
        $config_array['admin']['admin_email'] = $_POST['admin_email'];
        $config_array['admin']['auth_method']['value'] = $_POST['auth_method'];

        // update config.yml
        try {
          file_put_contents($file, sfYaml::dump($config_array, 4));
        } catch (Exception $e) {
          echo "hey, something went wrong:" . $e->getMessage();
        }

        $file = sfConfig::get('sf_app_dir') . '/config/app.yml';
        $appConfig = sfYaml::load($file);
        
        if($_POST['auth_method'] == 'bypass'){
          $appConfig['all']['sf_guard_plugin'] =
            array('check_password_callable'
              => array('agSudoAuth', 'authenticate'));
          $appConfig['all']['sf_guard_plugin_signin_form'] = 'agSudoSigninForm';
        }
        else{
          $appConfig['all'] = '';
        }
    $appConfig['all']['.array']['menu_top'] =
        array(
          'homepage' => array('label' => 'Home', 'route' => '@homepage'),
          'prepare' => array('label' => 'Prepare', 'route' => '@homepage'),
          'respond' => array('label' => 'Respond', 'route' => '@homepage'),
          'recover' => array('label' => 'Recover', 'route' => '@homepage'),
          'mitigate' => array('label' => 'Mitigate', 'route' => '@homepage'),
          'foo' => array('label' => 'Foo', 'route' => '@foo'),
          'modules' => array('label' => 'Modules', 'route' => '@homepage'),
          'admin' => array('label' => 'Administration', 'route' => '@admin'),
          'help' => array('label' => 'Help', 'route' => '@about'));

    $appConfig['all']['.array']['menu_children'] =
        array(
          'facility' => array('label' => 'Facilities', 'route' => '@facility', 'parent' => 'prepare'),
          'org' => array('label' => 'Organizations', 'route' => '@org', 'parent' => 'prepare'),
          'scenario' => array('label' => 'Scenarios', 'route' => '@scenario', 'parent' => 'prepare'),
          'activate' => array('label' => 'Activate a Scenario', 'route' => '@scenario', 'parent' => 'respond'),
          'event' => array('label' => 'Manage Events', 'route' => '@homepage', 'parent' => 'respond'),
          'event_active' => array('label' => 'Manage [Active Event]', 'route' => '@homepage', 'parent' => 'respond'));

    $appConfig['all']['.array']['menu_grandchildren'] =
        array(
          'facility_new' => array('label' => 'Add New Facility', 'route' => 'facility/new', 'parent' => 'facility'),
          'facility_list' => array('label' => 'List Facilities', 'route' => 'facility/list', 'parent' => 'facility'),
          'facility_import' => array('label' => 'Import Facilities', 'route' => '@facility', 'parent' => 'facility'),
          'facility_export' => array('label' => 'Export Facilities', 'route' => '@facility', 'parent' => 'facility'),
          'facility_types' => array('label' => 'Manage Facility Types', 'route' => '@facility', 'parent' => 'facility'),
          'org_new' => array('label' => 'Add New Organization', 'route' => 'organization/new', 'parent' => 'org'),
          'org_list' => array('label' => 'List Organizations', 'route' => 'organization/list', 'parent' => 'org'),
          'scenario_create' => array('label' => 'Build New Scenario', 'route' => '@staff_create', 'parent' => 'scenario'),
          'scenario_list' => array('label' => 'List Scenarios', 'route' => 'scenario/list', 'parent' => 'scenario'),
          'scenario_facilitygrouptypes' => array('label' => 'Edit Facility Group Types', 'route' => 'scenario/grouptype', 'parent' => 'scenario'),
          'scenario_active' => array('label' => '[Active Scenario]', 'route' => '@scenario', 'parent' => 'scenario'),
          'event_active_staff' => array('label' => 'Staff', 'route' => '@homepage', 'parent' => 'event_active'),
          'event_active_facilities' => array('label' => 'Facilities', 'route' => '@homepage', 'parent' => 'event_active'),
          'event_active_clients' => array('label' => 'Clients', 'route' => '@homepage', 'parent' => 'event_active'),
          'event_active_reporting' => array('label' => 'Reporting', 'route' => '@homepage', 'parent' => 'event_active'));
        // update config.yml
        try {
          file_put_contents($file, sfYaml::dump($appConfig, 4));
        } catch (Exception $e) {
          echo "hey, something went wrong:" . $e->getMessage();
          return false;
        }

        $caches = array('all','dev','prod');

        foreach($caches as $cachedir){
          $cacheDir = sfConfig::get('sf_cache_dir') . '/frontend/' . $cachedir . '/';
          $cache = new sfFileCache(array('cache_dir' => $cacheDir));

          $cache->clean();
        }


      //check to see if auth_method is bypass, if so, modify app.yml accordingly... AFTER agSaveSetup
//        if(!agSaveSetup($config_array)) return 'fail';
        //agSaveSetup($config_array);
    }
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
* @todo learn how description should be explained properly
* @param $request (add description)
*/
  public function executeNew(sfWebRequest $request)
  {
    $this->form = new agAccountForm();
  }
  public function executeCred(sfWebRequest $request)
  {

        $this->sf_guard_permissions = Doctrine_Core::getTable('sfGuardPermission')
                        ->createQuery('a')
                        ->execute();
        $this->sf_guard_group_permissions = Doctrine_Core::getTable('sfGuardGroupPermission')
                        ->createQuery('a')
                        ->execute();


        $this->form = new agCredForm();
        //if a user has entered information, process said information
        if($request->isMethod(sfRequest::POST)){
          $this->forward404Unless($request->isMethod(sfRequest::POST));
          $this->processCredform($request, $this->form);
        
          $this->setTemplate('cred');
        }


  }
/**
* 
* @todo add description of function above and details below
* @param $request (add description)
*/
  public function executeCreate(sfWebRequest $request)
  {
    $this->forward404Unless($request->isMethod(sfRequest::POST));

    $this->form = new agAccountForm();//, $options, $CSRFSecret)agAccountForm();

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
    $this->forward404Unless($ag_account = Doctrine::getTable('sfGuardUser')->find(array($request->getParameter('id'))), sprintf('Object ag_account does not exist (%s).', $request->getParameter('id')));
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
  protected function processParam(sfWebRequest $request, sfForm $paramform)
  {
    $paramform->bind($request->getParameter($paramform->getName()), $request->getFiles($paramform->getName()));
    if ($paramform->isValid())
    {
      $paramform->save();

      $this->redirect('admin/config');
    }
  }
  protected function processCredform(sfWebRequest $request, sfForm $credform) {
      $credform->bind($request->getParameter($credform->getName()), $request->getFiles($credform->getName()));
      if ($credform->isValid()) {
          $cred_result = $credform->save();
          //do i really need to set a variable?
          $this->redirect('admin/cred');
      }
  }
}
