<?php

/**
 * extends agActions for the administration module
 *
 * PHP Version 5.3
 *
 * LICENSE: This source file is subject to LGPLv2.1 license
 * that is available through the world-wide-web at the following URI:
 * http://www.gnu.org/licenses/lgpl-2.1.html
 *
 * @author Charles Wisniewski, CUNY SPS
 * Copyright of the Sahana Software Foundation, sahanafoundation.org
 */
class adminActions extends agActions
{
  protected $_searchedModels = array('agScenario', 'agStaff', 'agFacility',
    'agScenarioFacilityGroup', 'agOrganization');

  /**
   * necessary function which triggers rendering of the indexSuccess template
   * @param sfWebRequest $request is what the user is asking of the server
   */
  public function executeIndex(sfWebRequest $request)
  {
    $this->enable_cache_clear = agGlobal::getParam('enable_clear_cache');
  }

  /**
   * Method provided to disable staff.
   * @param sfWebRequest $request is what the user is asking of the server
   */
  public function executeDisablestaff(sfWebRequest $request)
  {
    $foo = agStaffResource::disableAllStaff();
    $this->redirect('admin/index');
  }

  /**
   * Magic button to reindex search data
   * @param sfWebRequest $request
   * @todo Place these models somewhere special or dynamically generate the list so it's not hard-
   * coded in here.
   */
  public function executeSearchreindex(sfWebRequest $request)
  {
    $models = array();

    $this->dispatcher->notify(new sfEvent($this, 'import.do_reindex'));
    //agLuceneIndex::indexModels($models);
    $this->redirect('admin/index');
  }

  public function executeClearcache(sfWebRequest $request)
  {
    if (agGlobal::getParam('enable_clear_cache') == 1) {
      apc_clear_cache();
      apc_clear_cache('user');
      apc_clear_cache('opcode');

      $oldDir = getcwd();
      try {
        chdir(sfConfig::get('sf_root_dir'));
        $task = new sfCacheClearTask($this->context->getEventDispatcher(), new sfFormatter());
        $task->run();
      } catch(Exception $e) {
        $this->sfContext->getLogger()->warning("Failed to clear the symfony cache: \n" . $e->getMessage());
      }
      chdir($oldDir);
    }
    $this->redirect('admin/index');
  }

  /** Pacman is the basic shell for package management, it is currently NOT STABLE
   *
   * @param sfWebRequest $request is what the user is asking of the server
   */
  public function executePacman(sfWebRequest $request)
  {
    $this->packages_available = agPluginManager::getPackages();
    if ($request->getParameter('enable')) {
      $enable_array = json_decode($request->getParameter('enable'));
      $this->getContext()->getConfiguration()->enablePackages($enable_array);
      //sfSymfonyPluginManager::enablePlugin('agGisPlugin' , sfConfig::get('sf_config_dir'));
    }
    if ($request->getParameter('disable')) {
      $disable_array = json_decode($request->getParameter('disable'));
      $this->getContext()->getConfiguration()->disablePackages($disable_array);

      //sfSymfonyPluginManager::disablePlugin('agGisPlugin' , sfConfig::get('sf_config_dir'));
      //disabling the plugin/module turns it off, we should remove any menu items associated with this
      //module/plugin, but this mapping needs to be stored somewhere.  maybe routing should exist in the plugin?
    }
  }

  /** Globals is the portal for management of global parameters. used throughout the application
   *
   * @param sfWebRequest $request is what the user is asking of the server
   */
  public function executeGlobals(sfWebRequest $request)
  {
    $ag_global_param = Doctrine_Core::getTable('agGlobalParam')
            ->find(array($request->getParameter('param')));
    if (isset($ag_global_param)) {
      $this->paramform = new agGlobalParamForm($ag_global_param);
    } else {
      $this->paramform = new agGlobalParamForm();
    }
    $this->ag_global_params = agDoctrineQuery::create()
            ->select('gp.*')
            ->from('agGlobalParam gp')
            ->orderBy('gp.datapoint ASC')
            ->execute();

    if ($request->hasParameter('update') /* && $request->hasParameter('ag_global_param') */) {
      $this->forward404Unless($request->isMethod(sfRequest::POST) || $request->isMethod(sfRequest::PUT));
      //$this->forward404Unless($ag_global_param = Doctrine::getTable('agGlobalParam')->findAll()->getFirst(), sprintf('Object ag_account does not exist (%s).', $request->getParameter('id')));
      //are we editing or creating a new param
      $this->processParam($request, $this->paramform);
    }
  }

  /** Config gives the administrator a place to see and configure system settings
   *
   * @param sfWebRequest $request is what the user is asking of the server
   */
  public function executeConfig(sfWebRequest $request)
  {

    require_once (sfConfig::get('sf_app_lib_dir') . '/install/func.inc.php');
    //OR sfProjectConfiguration::getActive()->loadHelpers(array('Install)); ^

    if ($request->getParameter('saveconfig')) {
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
      //try to create the default app.yml file, function called from func.inc.php
      if ($_POST['auth_method'] == 'bypass') {
        $authMethod = NULL;
      } else {
        $authMethod = '';
      }

      $appYmlWriteResult = writeAppYml($authMethod);
      //write app.yml file

      $caches = array('all', 'dev', 'prod');

      // clear the cache after changing the app.yml file
      foreach ($caches as $cachedir) {
        $cacheDir = sfConfig::get('sf_cache_dir') . '/frontend/' . $cachedir . '/';
        $cache = new sfFileCache(array('cache_dir' => $cacheDir));
        $cache->clean();
      }
      $this->result = $appYmlWriteResult;
    }
  }

  /**
   *
   * Display is the stub for managing display options in select lists, it is NOT STABLE
   * @param sfWebRequest $request should be passing in information that was submitted in the form created
   */
  public function executeDisplay(sfWebRequest $request)
  {

    $this->form = new sfForm();

    $this->form->getWidgetSchema()->setNameFormat('display[%s]');
    $this->form->setWidget(
        'agProfession',
        new sfWidgetFormDoctrineChoice(
            array('multiple' => true, 'expanded' => true, 'model' => 'agProfession')
        )
    );

    //$this->form->setValidator(array(new sfValidatorDoctrineChoice(array('model' => $this->form->getModelName(), 'column' => 'app_display'))));
    //if submitted
    if ($request->isMethod(sfRequest::POST)) {

      //$this->form->bind($request->getParameter($this->form->getName()));
//      if ($this->form->isValid()) {
      foreach ($request->getParameter('display') as $process_display) {
        //$array_of_all = get
        $profession = new agProfession;
        $profession->setAppDisplay(1);
        $profession->save();
        //$process_display =
      }
//      }
    }
  }

  /**
   * provides a list of sfguardusers to the listSuccess template
   * @param sfWebRequest $request
   */
  public function executeList(sfWebRequest $request)
  {
    $this->ag_accounts = Doctrine::getTable('sfGuardUser')
            ->createQuery('a')
            ->execute();
  }

  /**
   * provides sfguarduser information to display on the showSuccess template
   * @param $request (add description)
   */
  public function executeShow(sfWebRequest $request)
  {
    $this->ag_account = Doctrine::getTable('sfGuardUser')->find(array($request->getParameter('id')));
    $this->forward404Unless($this->ag_account);
  }

  /**
   * provides the newSuccess template with an agAccountForm for creating a user account
   * @param $request (add description)
   */
  public function executeNew(sfWebRequest $request)
  {
    $this->form = new agAccountForm();
  }

  /**
   * provides the credSuccess template with credential management information and forms.
   * all CRUD operations for credentials are performed through this action
   * @todo have all CRUD functions operate through their respective action function
   * @param sfWebRequest $request
   */
  public function executeCred(sfWebRequest $request)
  {
    //get data needed for the list on credSuccess page
    $this->sf_guard_permissions = Doctrine_Core::getTable('sfGuardUserPermission')
            ->createQuery('a')
            ->execute();
    $this->sf_guard_group_permissions = Doctrine_Core::getTable('sfGuardGroupPermission')
            ->createQuery('a')
            ->execute();
    $credObject = null;

    //get our post parameters
    $cred_id = $request->getParameter('id');
    if (isset($cred_id)) {
      $credObject = Doctrine::getTable('sfGuardPermission')->find(array($cred_id));
    }

    $this->form = new agCredForm($credObject);
//CREATE/UPDATE

    if ($request->isMethod(sfRequest::POST) || $request->isMethod(sfRequest::PUT)) {
      $this->form->bind($request->getParameter($this->form->getName()), $request->getFiles($this->form->getName()));
      if ($this->form->isValid()) {
        $cred_result = $this->form->save();
        //do i really need to set a variable?
        $this->redirect('admin/cred?id=' . $cred_result->getId());
      }

//      $this->setTemplate('cred');
    }
  }

  /**
   * provides the editSuccess template with an agAccountForm bound to the requested user id
   * @todo collapse this functionality all into executeUser or executeAccount function
   * @param $request (add description)
   */
  public function executeCreate(sfWebRequest $request)
  {
    $this->forward404Unless($request->isMethod(sfRequest::POST));

    $this->form = new agAccountForm(); //, $options, $CSRFSecret)agAccountForm();

    $this->processForm($request, $this->form);

    $this->setTemplate('new');
  }

  /**
   * provides the editSuccess template with an agAccountForm bound to the requested user id
   * @todo collapse this functionality all into executeUser or executeAccount function
   * @param $request (add description)
   */
  public function executeEdit(sfWebRequest $request)
  {
    $this->forward404Unless($ag_account = Doctrine::getTable('sfGuardUser')->find(array($request->getParameter('id'))), sprintf('Object ag_account does not exist (%s).', $request->getParameter('id')));
    $this->form = new agAccountForm($ag_account);
  }

  /**
   * this function is only used to perform updates to a user account
   * @todo collapse this functionality all into the executeUser or executeAccount function
   * @param $request (add description)
   */
  public function executeUpdate(sfWebRequest $request)
  {
    $this->forward404Unless($request->isMethod(sfRequest::POST) || $request->isMethod(sfRequest::PUT));
    $this->forward404Unless($ag_account = Doctrine::getTable('sfGuardUser')->find(array($request->getParameter('id'))), sprintf('Object sf_guard_user does not exist (%s).', $request->getParameter('id')));
    $this->form = new agAccountForm($ag_account);

    $this->processForm($request, $this->form);

    $this->setTemplate('edit');
  }

  /**
   * this function is used only to delete a user account
   * @todo the functionality in here should be collapsed into the executeUser or executeAccount action
   * @param $request (add description)
   */
  public function executeDelete(sfWebRequest $request)
  {
    $request->checkCSRFProtection();

    $this->forward404Unless($ag_account = Doctrine::getTable('sfGuardUser')->find(array($request->getParameter('id'))), sprintf('Object ag_account does not exist (%s).', $request->getParameter('id')));
//    $mj = $ag_account->getAgAccountMjSfGuardUser()->getFirst();
//    $sf = $mj->getSfGuardUser();
//    $sf_user = Doctrine::getTable('sfGuardUser')->find(array($sf->id));
//    $mj->delete();
//    $sf->delete();
    $ag_account->delete();

    $this->redirect('admin/list');
  }

  /**
   * the processform function only processes a form
   * @todo collapse this functionality all into a module-wde processForm function, taking in another
   *       parameter: the redirect.
   * @param sfWebRequest $request
   * @param sfForm $form the form to be processed
   */
  protected function processForm(sfWebRequest $request, sfForm $form)
  {
    $form->bind($request->getParameter($form->getName()), $request->getFiles($form->getName()));
    if ($form->isValid()) {
      $ag_account = $form->save();
      if ($request->hasParameter('Continue')) {
        /** @todo pass the previously created username to the new template for verification */
        $this->redirect('admin/new');
      } else {
        $this->redirect('admin/list'); //edit?id=' . $ag_account->getId());
      }
    }
  }

  /**
   * the processParam function only processes a paramform for modifying global params
   * @todo collapse this functionality all into a module-wde processForm function, taking in another
   *       parameter: the redirect.
   * @param sfWebRequest $request
   * @param sfForm $paramform the form to be processed
   */
  protected function processParam(sfWebRequest $request, sfForm $paramform)
  {
    $values = $request->getParameter('ag_global_param');
    if (empty($values['id'])) {

      if (!is_null($values['id']))
      { $values['id'] = NULL; }

      $param = new agGlobalParam();
    } else {
      $param = agDoctrineQuery::create()
              ->select()
              ->from('agGlobalParam')
              ->where('id = ?', $values['id'])
              ->fetchOne();
    }
    $param->synchronizeWithArray($values);

    $param->save();
    $this->redirect('admin/globals');
  }

}
