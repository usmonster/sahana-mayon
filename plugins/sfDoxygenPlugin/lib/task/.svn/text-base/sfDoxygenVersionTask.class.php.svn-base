<?php
/**
 * Checks the version of doxygen installed on local machine.
 *
 * @package    lib
 * @subpackage task
 * @author     Tomasz Ducin <tomasz.ducin@gmail.com>
 */
class sfDoxygenVersionTask extends sfBaseTask
{
  /**
   * @see sfTask
   */
  protected function configure()
  {
    $this->aliases          = array('doxygen-version');
    $this->namespace        = 'doxygen';
    $this->name             = 'version';
    $this->briefDescription = 'Checks the version of doxygen installed';
    $this->detailedDescription = <<<EOF
The [doxygen:version|INFO] checks the version of doxygen installed on
local machine:

  [./symfony doxygen:version|INFO]
EOF;
  }

  /**
   * @see sfTask
   */
  protected function execute($arguments = array(), $options = array())
  {
      echo "DUPA";
    $result = $this->getFilesystem()->execute('doxygen --version');
    echo 'Doxygen version: '.$result[0];
  }

}

