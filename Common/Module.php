<?php

/**
 * ZF2 Module
 */
namespace Common;

use Zend\EventManager\EventManager;
use Zend\Mvc\MvcEvent;
use Zend\ServiceManager\ServiceLocatorInterface;
use Zend\View\Model\ViewModel;
use Zend\I18n\Translator\Translator;
use Common\Preference\LanguageListener;
use Dvsa\Olcs\Utils\Translation\MissingTranslationProcessor;
use Olcs\Logging\Log\Logger;

/**
 * ZF2 Module
 */
class Module
{
    /**
     * Initialize module
     *
     * @param $moduleManager
     */
    public function init($moduleManager)
    {
        $events = $moduleManager->getEventManager();
        $events->attach('loadModules.post', array($this, 'modulesLoaded'));
    }

    public function modulesLoaded($e)
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

    public function onBootstrap(MvcEvent $e)
    {
        $sm = $e->getApplication()->getServiceManager();

        $events = $e->getApplication()->getEventManager();

        $this->setUpTranslator($sm, $events);

        $listener = $e->getApplication()->getServiceManager()->get('Common\Rbac\Navigation\IsAllowedListener');

        $events->getSharedManager()
            ->attach('Zend\View\Helper\Navigation\AbstractHelper', 'isAllowed', array($listener, 'accept'));
        $events->attach(
            $e->getApplication()->getServiceManager()->get('ZfcRbac\View\Strategy\UnauthorizedStrategy')
        );

        $this->setupRequestForProxyHost($e->getApplication()->getRequest());

        $this->setLoggerUser($e->getApplication()->getServiceManager());
    }

    public function getConfig()
    {
        return include __DIR__ . '/config/module.config.php';
    }

    protected function setUpTranslator(ServiceLocatorInterface $sm, $events)
    {
        /** @var Translator $translator */
        $translator = $sm->get('translator');

        $translator->setLocale('en_GB')->setFallbackLocale('en_GB');
        $translator->addTranslationFilePattern('phparray', __DIR__ . '/config/language/', '%s.php');
        $translator->addTranslationFilePattern('phparray', __DIR__ . '/config/sic-codes/', 'sicCodes_%s.php');

        /** @var LanguageListener $languagePrefListener */
        $languagePrefListener = $sm->get('LanguageListener');
        $languagePrefListener->attach($events, 1);

        /** @var  MissingTranslationProcessor $missingTranslationProcessor */
        $missingTranslationProcessor = $sm->get('Utils\MissingTranslationProcessor');
        $missingTranslationProcessor->attach($events);

        $translator->enableEventManager();
        $translator->setEventManager($events);
    }

    /**
     * If the request is coming through a proxy then update the host name on the request
     *
     * @param \Zend\Stdlib\RequestInterface $request
     */
    private function setupRequestForProxyHost(\Zend\Stdlib\RequestInterface $request)
    {
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
     * @param type $serviceManager
     */
    private function setLoggerUser(\Zend\ServiceManager\ServiceManager $serviceManager)
    {
        $authService = $serviceManager->get(\ZfcRbac\Service\AuthorizationService::class);
        $serviceManager->get('LogProcessorManager')->get(\Olcs\Logging\Log\Processor\UserId::class)
            ->setUserId($authService->getIdentity()->getUsername());
    }
}
