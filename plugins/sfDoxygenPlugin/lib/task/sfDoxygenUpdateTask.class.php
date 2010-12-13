<?php
/**
 * Updates doxygen configuration.
 *
 * @package    lib
 * @subpackage task
 * @author     Tomasz Ducin <tomasz.ducin@gmail.com>
 */
class sfDoxygenUpdateTask extends sfBaseTask
{
  /**
   * @see sfTask
   */
  protected function configure()
  {
    $this->aliases          = array('doxygen-update');
    $this->namespace        = 'doxygen';
    $this->name             = 'update';
    $this->briefDescription = 'Updates doxygen config file';
    $this->detailedDescription = <<<EOF
The [doxygen:update|INFO] updates doxygen config file using
doxygen.ini file (run doxygen:init before):

  [./symfony doxygen:update|INFO]
EOF;
  }

  /**
   * @see sfTask
   */
  protected function execute($arguments = array(), $options = array())
  {
    $doxygen_dir = sfConfig::get('sf_config_dir').'/doxygen';
    $root_dir = sfConfig::get('sf_root_dir');

    // read the doxygen.ini file
    $ini_file = $doxygen_dir.'/doxygen.ini';
    $ini_content = parse_ini_file($ini_file, true);

    // add INPUT option
    $ini_content['INPUT'] = sfConfig::get('sf_root_dir');
    // add EXCLUDE option
    $ini_content['EXCLUDE'] = '';
    $exclude_file = $doxygen_dir.'/exclude.txt';
    $exclude_lines = file($exclude_file, FILE_IGNORE_NEW_LINES);
    foreach($exclude_lines as $line)
    {
        $ini_content['EXCLUDE'] .= $root_dir.'/'.$line.' ';
    }
    // add EXCLUDE_PATTERNS option
    $ini_content['EXCLUDE_PATTERNS'] = '';
    $exclude_file = $doxygen_dir.'/exclude_patterns.txt';
    $exclude_lines = file($exclude_file, FILE_IGNORE_NEW_LINES);
    foreach($exclude_lines as $line)
    {
        $ini_content['EXCLUDE_PATTERNS'] .= $line.' ';
    }

    // read the doxygen.cfg file
    $config_file = $doxygen_dir.'/doxygen.cfg';

    // modify doxygen.cfg content
    $config_content = "";
    $config_lines = file($config_file, FILE_IGNORE_NEW_LINES);
    foreach($config_lines as $line)
    {
      if ($line[0] == '#')
      {
        $config_content .= $line."\n";
      }
      else
      {
        $opts = explode('=', $line);
        if (isset($ini_content[trim($opts[0])]))
        {
          $opt = trim($opts[0]);
          if ($opt == 'EXCLUDE' || $opt == 'EXCLUDE_PATTERNS')
          {
            $config_content .= $opts[0].'= '.$ini_content[trim($opts[0])]."\n";
          }
          else
          {
            $config_content .= $opts[0].'= "'.$ini_content[trim($opts[0])]."\"\n";
          }
        }
        else
        {
          $config_content .= $line."\n";
        }
      }
    }

    // save new doxygen.cfg content
    file_put_contents($config_file, $config_content);
  }

}

