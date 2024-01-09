<?php

namespace CommonTest;

use Laminas\Mvc\I18n\Translator;
use Laminas\Mvc\Service\ServiceManagerConfig;
use Laminas\ServiceManager\ServiceManager;
use Mockery as m;
use Olcs\Logging\Log\Logger;

error_reporting(E_ALL & ~E_USER_DEPRECATED);
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
        ini_set('intl.default_locale', 'en_GB');
        ini_set('memory_limit', '1500M');
        // Setup the autloader
        $loader = static::initAutoloader();
        $loader->addPsr4('CommonTest\\', __DIR__ . '/Common/src/Common');
        $loader->addPsr4('CommonComponentTest\\', __DIR__ . '/Component');

        // Grab the application config
        $config = array(
            'modules' => array(
                'Common',
            ),
            'module_listener_options' => array(
                'module_paths' => array(
                    __DIR__ . '/../'
                )
            )
        );

        self::$config = $config;

        self::getServiceManager();

        self::setupLogger();
    }

    /**
     * Changed this method to return a mock
     *
     * @return \Laminas\ServiceManager\ServiceManager
     */
    public static function getServiceManager()
    {
        $sm = m::mock('\Laminas\ServiceManager\ServiceManager')
            ->makePartial()
            ->setAllowOverride(true);

        // inject a real string helper
        $sm->setService('Helper\String', new \Common\Service\Helper\StringHelperService());

        return $sm;
    }

    /**
     * Added this method for backwards compatibility
     *
     * @return \Laminas\ServiceManager\ServiceManager
     */
    public static function getRealServiceManager()
    {
        // When we fix our unit tests to mock all dependencies
        // we need to put this line back in to speed up our tests
        //return m::mock('\Laminas\ServiceManager\ServiceManager')->makePartial();

        $serviceManager = new ServiceManager(new ServiceManagerConfig());
        $serviceManager->setService('ApplicationConfig', self::$config);
        $serviceManager->get('ModuleManager')->loadModules();
        $serviceManager->setAllowOverride(true);

        $config = $serviceManager->get('Config');
        $config['service_api_mapping']['endpoints']['backend'] = 'http://some-fake-backend/';
        $serviceManager->setService('Config', $config);

        $translator = m::mock(\Laminas\I18n\Translator\Translator::class)->makePartial();
        /** @var Translator $mvcTranslator */
        $mvcTranslator = m::mock(Translator::class, [$translator])->makePartial();
        $serviceManager->setService('MvcTranslator', $mvcTranslator);

        /*
         * NP 17th Nov 2014
         *
         * Although this is commented out I'd like to leave it in for now;
         * it's a more elegant way to trap unmocked backend requests than
         * setting a fake URL as above. Only trouble is at the moment $path
         * always comes through as null... needs a bit of investigation
         *
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
                m::mock('\Common\Util\RestClient[request]', [new \Laminas\Uri\Http])
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

    public static function setupLogger()
    {
        $logWriter = new \Laminas\Log\Writer\Mock();
        $logger = new \Laminas\Log\Logger();
        $logger->addWriter($logWriter);

        Logger::setLogger($logger);
    }
}

Bootstrap::init();
