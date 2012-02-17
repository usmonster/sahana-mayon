<?php
/**
 * extends agActions for the administration module
 *
 * PHP Version 5.3
 *
 * LICENSE: This source file is subject to LGPLv2.1 license
 * that is available through the world-wide-web at the following URI:
 * http://www.gnu.org/licenses/lgpl-2.1.html
 *
 * @author Clayton Kramer, CUNY SPS
 * @author Charles Wisniewski, CUNY SPS
 * Copyright of the Sahana Software Foundation, sahanafoundation.org
 */


// Check to see if the system requirements have been met before loading Symfony framwork
// libraries and functions

if (isset($_SESSION['install']['reqOK']) && $_SESSION['install']['reqOK'] == true) {
    // Load Symfony framework libs
    require_once (dirname(__FILE__) . '/../../../lib/vendor/symfony/lib/yaml/sfYaml.php');
    require_once (dirname(__FILE__) . '/../../../config/ProjectConfiguration.class.php');
    require_once (dirname(__FILE__) . '/../../../apps/frontend/lib/install/func.inc.php');
    
    // Auto load some symphony stuff
    $configuration = ProjectConfiguration::getApplicationConfiguration('frontend', 'all', false);

    /** these two extended classes are for configuring doctrine */
    class agDoctrineConfigureDatabaseTask extends sfDoctrineConfigureDatabaseTask
    {

        function execute($arguments = array(), $options = array())
        {
            parent::execute($arguments, $options);
        }

    }

    class agDoctrineBuildSqlTask extends sfDoctrineBuildSqlTask
    {

        function execute($arguments = array(), $options = array())
        {
            try {
                parent::execute($arguments, $options);
            } catch (Exception $e) {
                throw new Doctrine_Exception($e->getMessage());
            }
        }

    }

} else {

    // These two functions are needed by the system check
    // TODO: reorganize the relationship between installer and frontend libs for better reuse
    function mem2str($size)
    {
        $prefix = S_B;
        if ($size > 1048576) {
            $size = $size / 1048576;
            $prefix = S_M;
        } elseif ($size > 1024) {
            $size = $size / 1024;
            $prefix = S_K;
        }
        return round($size, 6) . $prefix;
    }

    function str2mem($val)
    {
        $val = trim($val);
        $last = strtolower(substr($val, -1, 1));

        switch ($last) {
            // The 'G' modifier is available since PHP 5.1.0
            case 'g':
                $val *= 1024;
            case 'm':
                $val *= 1024;
            case 'k':
                $val *= 1024;
        }

        return $val;
    }

}
?>
