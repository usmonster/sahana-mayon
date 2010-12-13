<?php
/**
 * Generates the documentation of the project using doxygen.
 *
 * @package    lib
 * @subpackage task
 * @author     Tomasz Ducin <tomasz.ducin@gmail.com>
 */
class sfDoxygenGenerateTask extends sfBaseTask
{
  /**
   * @see sfTask
   */
  protected function configure()
  {
    $this->aliases          = array('doxygen-generate');
    $this->namespace        = 'doxygen';
    $this->name             = 'generate';
    $this->briefDescription = 'Generates documentation of the project';
    $this->detailedDescription = <<<EOF
The [doxygen:generate|INFO] task creates the documentation based on the docblocks
in your sorcecode for the current project:

  [./symfony doxygen:generate|INFO]
EOF;
  }

  /**
   * @see sfTask
   */
  protected function execute($arguments = array(), $options = array())
  {
    $config_file = sfConfig::get('sf_config_dir').'/doxygen/doxygen.cfg';

    // doc/ directory has been removed from symfony since 1.3 version
    $this->getFilesystem()->mkdirs(sfConfig::get('sf_root_dir').'/doc');

    // documentation generation
    $this->getFilesystem()->execute('doxygen '.$config_file);
  }

}

