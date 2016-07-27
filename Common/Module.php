<?php

/**
 * ZF2 Module
 */
namespace Common;

use Common\Preference\LanguageListener;
use Dvsa\Olcs\Utils\Translation\MissingTranslationProcessor;
use Olcs\Logging\Log\Logger;
use Zend\EventManager\EventManager;
use Zend\I18n\Translator\Translator;
use Zend\ModuleManager\ModuleEvent;
use Zend\Mvc\MvcEvent;
use Zend\ServiceManager\ServiceLocatorInterface;
use Zend\View\Model\ViewModel;

/**
 * ZF2 Module
 */
class Module
{
    /**
     * Initialize module
     *
     * @param \Zend\ModuleManager\ModuleManager $moduleManager Module manager
     *
     * @return void
     */
    public function init($moduleManager)
    {
        /** @var EventManager $events */
        $events = $moduleManager->getEventManager();
        $events->attach('loadModules.post', array($this, 'modulesLoaded'));
    }

    /**
     * Modules loaded event
     *
     * @param ModuleEvent $e Module event
     *
     * @return void
     */
    public function modulesLoaded(ModuleEvent $e)
    {
        $moduleManager = $e->getTarget();

        if ($moduleManager->getModule('Olcs')) {
            $config = $moduleManager->getModule('Olcs')->getConfig();
        } else {
            $config = [];
        }
        if (!defined('DATE_FORMAT')) {
            define(
                'DATE_FORMAT',
                isset($config['date_settings']['date_format']) ?
                $config['date_settings']['date_format'] : 'd/m/Y'
            );
        }
        if (!defined('DATETIME_FORMAT')) {
            define(
                'DATETIME_FORMAT',
                isset($config['date_settings']['datetime_format']) ?
                $config['date_settings']['datetime_format'] : 'd/m/Y H:i'
            );
        }
        if (!defined('DATETIMESEC_FORMAT')) {
            define(
                'DATETIMESEC_FORMAT',
                isset($config['date_settings']['datetimesec_format']) ?
                $config['date_settings']['datetimesec_format'] : 'd/m/Y H:i:s'
            );
        }
        if (!defined('DB_DATE_FORMAT')) {
            define('DB_DATE_FORMAT', 'Y-m-d');
        }
    }

    /**
     * Bootstrap
     *
     * @param MvcEvent $e MVC Event
     *
     * @return void
     */
    public function onBootstrap(MvcEvent $e)
    {
        $app = $e->getApplication();
        $sm = $app->getServiceManager();
        $events = $app->getEventManager();

        $this->setUpTranslator($sm, $events);

        //  Navigation:Check ability to access an item
        $listener = $sm->get(\Common\Rbac\Navigation\IsAllowedListener::class);

        $events->getSharedManager()->attach(
            \Zend\View\Helper\Navigation\AbstractHelper::class,
            'isAllowed',
            [$listener, 'accept']
        );

        //  RBAC behaviour if user not authorised
        $events->attach($sm->get(\ZfcRbac\View\Strategy\UnauthorizedStrategy::class));

        $this->setupRequestForProxyHost($app->getRequest());

        $this->setLoggerUser($sm);
    }

    /**
     * Get config
     *
     * @return array
     */
    public function getConfig()
    {
        return include __DIR__ . '/config/module.config.php';
    }

    /**
     * Setup the translator service
     *
     * @param ServiceLocatorInterface         $sm           Service manager
     * @param \Zend\EventManager\EventManager $eventManager Event manager
     *
     * @return void
     */
    protected function setUpTranslator(ServiceLocatorInterface $sm, $eventManager)
    {
        /** @var \Common\Util\TranslatorDelegator $translator */
        $translator = $sm->get('translator');

        $translator->setLocale('en_GB')->setFallbackLocale('en_GB');
        $translator->addTranslationFilePattern('phparray', __DIR__ . '/config/language/', '%s.php');
        $translator->addTranslationFilePattern('phparray', __DIR__ . '/config/sic-codes/', 'sicCodes_%s.php');
        $translator->addTranslationFile('phparray', __DIR__ . '/config/language/cy_GB_refdata.php', 'default', 'cy_GB');

        /** @var LanguageListener $languagePrefListener */
        $languagePrefListener = $sm->get('LanguageListener');
        $languagePrefListener->attach($eventManager, 1);

        /** @var  MissingTranslationProcessor $missingTranslationProcessor */
        $missingTranslationProcessor = $sm->get('Utils\MissingTranslationProcessor');
        $missingTranslationProcessor->attach($eventManager);

        // Log all messages where the translation is missing, only use these lines in dev environments
        //$request = $sm->get('Request');
        //if ($request instanceof \Zend\Http\PhpEnvironment\Request) {
        //    $missingTranslationProcessor->setTranslationLogger(
        //        new \Dvsa\Olcs\Utils\Translation\TranslatorLogger('/tmp/missing-translations.csv', $request)
        //    );
        //}

        $translator->enableEventManager();
        $translator->setEventManager($eventManager);
    }

    /**
     * If the request is coming through a proxy then update the host name on the request
     *
     * @param \Zend\Stdlib\RequestInterface $request Request
     *
     * @return void
     */
    private function setupRequestForProxyHost(\Zend\Stdlib\RequestInterface $request)
    {
        if (!$request instanceof \Zend\Http\PhpEnvironment\Request) {
            // if request is not \Zend\Http\PhpEnvironment\Request we must be running from CLI so do nothing
            return;
        }

        /* @var $request \Zend\Http\PhpEnvironment\Request */
        if ($request->getHeaders()->get('xforwardedhost')) {

            $host = $request->getHeaders()->get('xforwardedhost')->getFieldValue();

            $hosts = explode(',', $host);
            if (!empty($hosts)) {
                $host = trim($hosts[0]);
            }

            Logger::debug(
                sprintf(
                    'Request host set from xforwardedhost header to %s setting host to %s',
                    $request->getHeaders()->get('xforwardedhost')->getFieldValue(),
                    $host
                )
            );
            $request->getUri()->setHost($host);
        }

        // if X-Forwarded-Proto Header exists (ie from AWS ELB) then set the request as this so that route
        // generated URLS will have the correct scheme
        if ($request->getHeaders()->get('xforwardedproto')) {

            $proto = $request->getHeaders()->get('xforwardedproto')->getFieldValue();

            Logger::debug(
                sprintf(
                    'Request scheme set from xforwardedproto header to %s',
                    $proto
                )
            );
            $request->getUri()->setScheme($proto);
        }
    }

    /**
     * Set the user ID in the log processor so that it can be included in the log files
     *
     * @param ServiceLocatorInterface $serviceManager Service manager
     *
     * @return void
     */
    private function setLoggerUser(ServiceLocatorInterface $serviceManager)
    {
        $authService = $serviceManager->get(\ZfcRbac\Service\AuthorizationService::class);
        $serviceManager->get('LogProcessorManager')->get(\Olcs\Logging\Log\Processor\UserId::class)
            ->setUserId($authService->getIdentity()->getUsername());
    }
}
