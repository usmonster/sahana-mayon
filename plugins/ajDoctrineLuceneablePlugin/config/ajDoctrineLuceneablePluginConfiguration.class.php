<?php

/** 
 *
 * @package    fpErrorNotifier
 * @subpackage config 
 * 
 * @author     Maksim Kotlyar <mkotlar@ukr.net>
 */
class ajDoctrineLuceneablePluginConfiguration extends sfPluginConfiguration
{
  /**
   * 
   * @return void
   */
  public function initialize()
  {
    $configFiles = $this->configuration->getConfigPaths('config/lucene.yml');
    $config = sfDefineEnvironmentConfigHandler::getConfiguration($configFiles);

    foreach ($config as $name => $value) {
      sfConfig::set("sf_lucene_{$name}", $value);
    }
  }
}