<?php
/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
class agPluginManager extends sfSymfonyPluginManager
{
  static public function getPlugins()
  {
    if ($handle = opendir(sfConfig::get('sf_plugins_dir'))) {
      while (false !== ($file = readdir($handle))) {
          //if file is not . or .. OR file/directory is not an Agasti module, disallow it from being meddled with here.
        $i = 0;
        if ($file != "." && $file != ".." && (strpos($file, 'ag') === 0)) {
              $module_array[$i]['name']  = $file;
              $module_array[$i]['action'] = agPluginManager::pluginStatus($file, sfConfig::get('sf_config_dir'));
              //if the module is enabled, set the action to disable, etc.
              $i++;
          }
      }
      closedir($handle);
    }
    return $module_array;
  }
  static public function pluginStatus($plugin, $configDir)
  {
    if (!$configDir)
    {
      throw new sfPluginException('You must provide a "config_dir" option.');
    }
    $file = $configDir.'/ProjectConfiguration.class.php';
    $source = file_get_contents($file);
    if(strpos($source, $plugin) === 0){
      return 'enable';
    }
    else{
      return 'disable';
    }
    //is the plugin/module in the enabled array? return true. else, return false
  }
}