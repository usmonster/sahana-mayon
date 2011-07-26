<?php

/**
 * Project form base class.
 *
 * PHP Version 5.3
 *
 * LICENSE: This source file is subject to LGPLv2.1 license
 * that is available through the world-wide-web at the following URI:
 * http://www.gnu.org/licenses/lgpl-2.1.html
 *
 * @author Charles Wisniewski, CUNY SPS
 * @author Chad Heuschober, CUNY SPS
 * @author Usman Akeju, CUNY SPS
 *
 * Copyright of the Sahana Software Foundation, sahanafoundation.org
 */
require_once dirname(__FILE__) . '/../lib/vendor/symfony/lib/autoload/sfCoreAutoload.class.php';
sfCoreAutoload::register();

class ProjectConfiguration extends sfProjectConfiguration
{

  static protected $zendLoaded = false;

  static public function registerZend()
  {
    if (self::$zendLoaded) {
      return;
    }

    set_include_path(sfConfig::get('sf_plugins_dir') . '/ajDoctrineLuceneablePlugin/data/vendor' .
        PATH_SEPARATOR . get_include_path());
    require_once sfConfig::get('sf_plugins_dir') .
        '/ajDoctrineLuceneablePlugin/data/vendor/Zend/Loader/Autoloader.php';
    Zend_Loader_Autoloader::getInstance();
    self::$zendLoaded = true;
  }

  public function enablePackages($packages)
  {
    if (!is_array($packages)) {
      if (func_num_args() > 1) {
        $packages = func_get_args();
      } else {
        $packages = array($packages);
      }
    }
    foreach ($packages as $package) {
      $this->setPluginPath($package, sfConfig::get('sf_app_lib_dir') . '/packages/' . $package);
    }
    $this->enablePlugins($packages);
  }

  public function disablePackages($packages)
  {
    if (!is_array($packages)) {
      if (func_num_args() > 1) {
        $packages = func_get_args();
      } else {
        $packages = array($packages);
      }
    }
    foreach ($packages as $package) {
      $this->setPluginPath($package, sfConfig::get('sf_app_lib_dir') . '/packages/' . $package);
    }
    $this->disablePlugins($packages);
  }

  public function enableModules($packages)
  {
    if (!is_array($packages)) {
      if (func_num_args() > 1) {
        $packages = func_get_args();
      } else {
        $packages = array($packages);
      }
    }
    foreach ($packages as $package) {
      $this->setPluginPath($package,
          sfConfig::get('sf_app_module_dir') . DIRECTORY_SEPARATOR . $package);
    }
    //$this->enablePlugins($packages);
  }

  public function setup()
  {
    // redefines the upload and download directories to be in a secure (i.e. web-inaccessible) location
    sfConfig::set('sf_upload_dir', sfConfig::get('sf_data_dir') . DIRECTORY_SEPARATOR . 'uploads');
    sfConfig::set('sf_download_dir', sfConfig::get('sf_data_dir') . DIRECTORY_SEPARATOR . 'downloads');

    //$this->appendEnabledModules();
    // plugins are considered 'core' elements, akin to apps/frontend/modules, they should not be
    // disabled
    $this->enablePlugins(
        array(
          'sfDoctrinePlugin',
          'sfDoctrineGuardPlugin',
          'sfDoxygenPlugin',
          'sfPhpExcelPlugin',
          'ajDoctrineLuceneablePlugin',
          'ioMenuPlugin'
        )
    );

    //packages are specific to the application, though not core and should function standalone
    $this->enablePackages(
        array('agFooPackage',
          'agStaffPackage',
          'agClientPackage',
          'agGisPackage',
          'agEventPackage',
          'agReportPackage',
          'agPetPackage',
          'agEventPackage',
          'agWebservicesPackage',
          'agMessagePackage')
    );

    // enables indexing by getting symfony to see lucene.yml for each module in the array
    $this->enableModules(array('scenario', 'facility'));

    //register zend
    $this->registerZend();

    // registers event listeners
    $this->dispatcher->connect('import.staff_responses', array('agMessageResponseHandler', 'consumeResponses'));
    $this->dispatcher->connect('import.file_ready', array('agImportXLS', 'processFile'));
    $this->dispatcher->connect('deploy.do_deployment', array('agEventStaffDeploymentHelper', 'doDeployment'));
    $this->dispatcher->connect('import.start', array('agImportNormalization', 'processImportEvent'));
    $this->dispatcher->connect('import.do_reindex', array('agLuceneIndex', 'indexAll'));
    $this->dispatcher->connect('global_param.param_updated', array('agGlobal', 'loadParams'));
  }

  /**
   * Define custom hydration.
   * @param Doctrine_Manager $manager
   */
  public function configureDoctrine(Doctrine_Manager $manager)
  {
    // set up proper null handling
    $masterConn = $manager->setAttribute(Doctrine_Core::ATTR_ORACLE_NULLS,
            Doctrine_Core::NULL_NATURAL);

    // register hydrators
    $manager->registerHydrator('key_value_pair', 'KeyValuePairHydrator');
    $manager->registerHydrator('key_value_array', 'KeyValueArrayHydrator');
    $manager->registerHydrator('assoc_three_dim', 'AssociativeThreeDimHydrator');
    $manager->registerHydrator('assoc_two_dim', 'AssociativeTwoDimHydrator');
    $manager->registerHydrator('assoc_one_dim', 'AssociativeOneDimHydrator');
    $manager->registerHydrator('single_value_array', 'SingleValueArrayHydrator');

    // extend where appropriate
    $manager->setAttribute(Doctrine_Core::ATTR_QUERY_CLASS, 'agDoctrineQuery');
    $manager->setAttribute(Doctrine_Core::ATTR_TABLE_CLASS, 'agDoctrineTable');

    // enable the APC cache
    $manager->setAttribute(Doctrine_Core::ATTR_QUERY_CACHE, new Doctrine_Cache_Apc());
    $manager->setAttribute(Doctrine_Core::ATTR_RESULT_CACHE, new Doctrine_Cache_Apc());
  }

}
