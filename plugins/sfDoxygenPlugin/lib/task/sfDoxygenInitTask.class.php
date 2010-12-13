<?php
/**
 * Creates doxygen configuration files for the project.
 *
 * @package    lib
 * @subpackage task
 * @author     Tomasz Ducin <tomasz.ducin@gmail.com>
 */
class sfDoxygenInitTask extends sfBaseTask
{
  /**
   * @see sfTask
   */
  protected function configure()
  {
    $this->aliases          = array('doxygen-init');
    $this->namespace        = 'doxygen';
    $this->name             = 'init';
    $this->briefDescription = 'Creates doxygen config files needed to generate the docs';
    $this->detailedDescription = <<<EOF
The [doxygen:init|INFO] task creates the doxygen config directory structure:

  [./symfony doxygen:init|INFO]
EOF;
  }

  /**
   * @see sfTask
   */
  protected function execute($arguments = array(), $options = array())
  {
    // preparing directories
    $doxygen_config_dir = sfConfig::get('sf_config_dir').'/doxygen';
    $plugin_dir = sfConfig::get('sf_plugins_dir').'/sfDoxygenPlugin';
    $plugin_config_dir = $plugin_dir.'/config';

    // creating doxygen config directory
    $this->getFilesystem()->mkdirs($doxygen_config_dir);

    // creating main doxygen config file
    $this->getFilesystem()->execute("doxygen -g {$doxygen_config_dir}/doxygen.cfg");

    // checking if doxygen version is 1.5.7 or above
    $version_string = $this->getFilesystem()->execute("doxygen --version");
    $version_array = explode('.', $version_string);
    if ($version_array[1] >= 6 || ($version_array[1] == 5 && $version_array[2] >=7))
    {
      $this->getFilesystem()->execute("doxygen -l {$doxygen_config_dir}/doxygen.xml");
    }

    // copying additional files to doxygen config dir
    $this->getFilesystem()->copy($plugin_config_dir.'/doxygen.ini', $doxygen_config_dir.'/doxygen.ini');
    $this->getFilesystem()->copy($plugin_config_dir.'/exclude.txt', $doxygen_config_dir.'/exclude.txt');
    $this->getFilesystem()->copy($plugin_config_dir.'/exclude_patterns.txt', $doxygen_config_dir.'/exclude_patterns.txt');

    // mirroring module skeleton directory
    $finder = sfFinder::type('any')->discard('.sf');
    $this->getFilesystem()->mirror($plugin_dir.'/data/skeleton/module', sfConfig::get('sf_data_dir').'/skeleton/module', $finder);
  }
}

