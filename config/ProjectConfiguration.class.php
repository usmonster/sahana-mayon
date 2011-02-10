<?php

require_once dirname(__FILE__) . '/../lib/vendor/symfony/lib/autoload/sfCoreAutoload.class.php';
sfCoreAutoload::register();

class ProjectConfiguration extends sfProjectConfiguration
{

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
      $this->setPluginPath($package, sfConfig::get('sf_app_module_dir') . DIRECTORY_SEPARATOR . $package);
    }
    $this->enablePlugins($packages);
  }

  public function setup()
  {
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
          'agEventPackage')
    );
    // Use the line below to enable indexing by getting Symfony to pick up lucene.yml from the module(s) passed to enableModules();
    $this->enableModules(array('scenario', 'facility'));
  }

  /**
   * Define custom hydration.
   * @param Doctrine_Manager $manager
   */
  public function configureDoctrine(Doctrine_Manager $manager)
  {
    $manager->registerHydrator('key_value_pair', 'KeyValuePairHydrator');
    $manager->registerHydrator('key_value_array', 'KeyValueArrayHydrator');
    $manager->registerHydrator('gis_point_coordinate', 'GisPointCoordinateHydrator');
    $manager->registerHydrator('single_value_array', 'SingleValueArrayHydrator') ;
    $manager->registerHydrator('status_hydrator', 'StatusHydrator') ;
  }

}
