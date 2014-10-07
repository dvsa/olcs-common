<?php

namespace CommonTest;

use Zend\Mvc\Service\ServiceManagerConfig;
use Zend\ServiceManager\ServiceManager;

error_reporting(E_ALL | E_STRICT);
chdir(dirname(__DIR__));
date_default_timezone_set('Europe/London');

/**
 * Test bootstrap, for setting up autoloading
 */
class Bootstrap
{
    protected static $serviceManager;

    public static function init()
    {
        // Setup the autloader
        $loader = static::initAutoloader();
        $loader->addPsr4('CommonTest\\', __DIR__ . '/Common/src/Common');
        $loader->addPsr4('CommonComponentTest\\', __DIR__ . '/Component');

        // Grab the application config
        $config = array(
            'modules' => array(
                'Common'
            ),
            'module_listener_options' => array(
                'module_paths' => array(
                    __DIR__ . '/../'
                )
            )
        );

        $serviceManager = new ServiceManager(new ServiceManagerConfig());
        $serviceManager->setService('ApplicationConfig', $config);
        $serviceManager->get('ModuleManager')->loadModules();
        static::$serviceManager = $serviceManager;
    }

    public static function getServiceManager()
    {
        return static::$serviceManager;
    }

    protected static function initAutoloader()
    {
        return require('vendor/autoload.php');
    }
}

Bootstrap::init();
