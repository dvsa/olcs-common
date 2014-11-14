<?php

namespace CommonTest;

use Zend\Mvc\Service\ServiceManagerConfig;
use Zend\ServiceManager\ServiceManager;
use \Mockery as m;

error_reporting(-1);
chdir(dirname(__DIR__));
date_default_timezone_set('Europe/London');

/**
 * Test bootstrap, for setting up autoloading
 */
class Bootstrap
{
    protected static $config = array();

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

        self::$config = $config;

        self::getServiceManager();
    }

    public static function getServiceManager()
    {
        $serviceManager = new ServiceManager(new ServiceManagerConfig());
        $serviceManager->setService('ApplicationConfig', self::$config);
        $serviceManager->get('ModuleManager')->loadModules();
        $serviceManager->setAllowOverride(true);

        $config = $serviceManager->get('Config');
        $config['service_api_mapping']['endpoints']['backend'] = 'http://some-fake-backend/';
        $serviceManager->setService('Config', $config);

        /*
        $closure = function ($method, $path, $params) {
            $str = sprintf(
                "Trapped unmocked backend request: %s %s",
                $method, $path
            );
            throw new \Exception($str);
        };

        $serviceManager->setService(
            'ServiceApiResolver',
            m::mock()
            ->shouldReceive('getClient')
            ->andReturn(
                m::mock('\Common\Util\RestClient[request]', [new \Zend\Uri\Http])
                ->shouldReceive('request')
                ->andReturnUsing($closure)
                ->getMock()
            )
            ->getMock()
        );
         */

        return $serviceManager;
    }

    protected static function initAutoloader()
    {
        return require('vendor/autoload.php');
    }
}

Bootstrap::init();
