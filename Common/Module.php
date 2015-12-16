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
        if ($request->getHeaders()->get('xforwardedhost')) {
            Logger::debug(
                'Request host set from xforwardedhost header to '.
                $request->getHeaders()->get('xforwardedhost')->getFieldValue()
            );
            $request->getUri()->setHost(
                $request->getHeaders()->get('xforwardedhost')->getFieldValue()
            );
        }
    }
}
