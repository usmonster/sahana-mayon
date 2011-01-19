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

  public function setup()
  {
    //$this->appendEnabledModules();
    // plugins are considered 'core' elements, akin to apps/frontend/modules, they should not be
    // disabled
    $this->enablePlugins(
        array('sfDoctrinePlugin',
          'sfDoctrineGuardPlugin',
          'sfDoxygenPlugin',
          'sfFormExtraPlugin',
          'sfPhpExcelPlugin',
          'sfJQueryUIPlugin',
          'ajDoctrineLuceneablePlugin',
          'ioMenuPlugin'
        )
    );



    //packages are specific to the application, though not core and should function standalone
    $this->enablePackages(
        array('agFooPackage',
          'agStaffPackage',
          'agGisPackage')
    );
  }

  /**
   * Define custom hydration.
   * @param Doctrine_Manager $manager
   */
  public function configureDoctrine(Doctrine_Manager $manager)
  {
    $manager->registerHydrator('key_value_pair', 'KeyValuePairHydrator');
    $manager->registerHydrator('combo_set', 'ComboHydrator');
  }

}
