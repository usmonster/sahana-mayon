<?php

$configFiles = $this->getConfigPaths('config/lucene.yml');
$config = sfDefineEnvironmentConfigHandler::getConfiguration($configFiles);
    
foreach ($config as $name => $value) {
  sfConfig::set("sf_lucene_{$name}", $value);
}