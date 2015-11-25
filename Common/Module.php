<?php

/**
 * ZF2 Module
 */
namespace Common;

use Dvsa\Olcs\Utils\Auth\AuthHelper;
use Zend\EventManager\EventManager;
use Zend\Mvc\MvcEvent;
use Zend\ServiceManager\ServiceLocatorInterface;
use Zend\View\Model\ViewModel;
use Zend\I18n\Translator\Translator;
use Common\Preference\LanguageListener;
use Dvsa\Olcs\Utils\Translation\MissingTranslationProcessor;

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
        if (AuthHelper::isOpenAm() === false) {
            $events->attach(
                $e->getApplication()->getServiceManager()->get('ZfcRbac\View\Strategy\RedirectStrategy')
            );
        }
    }

    public function getConfig()
    {
        $config = include __DIR__ . '/config/module.config.php';

        if (AuthHelper::isOpenAm() === false) {
            unset($config['zfc_rbac']['identity_provider']);
            $config['zfc_rbac']['redirect_strategy'] = [
                'redirect_when_connected'           => false,
                'redirect_to_route_disconnected'    => 'zfcuser/login',
                'append_previous_uri'               => true,
                'previous_uri_query_key'            => 'redirectTo',
            ];
            $config['zfcuser']['auth_identity_fields'] = ['username'];
            $config['service_manager']['delegators']['zfcuser_user_mapper'] = [
                \Common\Rbac\UserProviderDelegatorFactory::class
            ];
            $config['view_manager']['template_path_stack']['zfcuser'] = __DIR__ . '/../../view';
            $config['service_manager']['factories']['AnonQuerySender']
                = \Common\Service\Cqrs\Query\AnonQuerySender::class;
            $config['service_manager']['factories']['AnonCqrsRequest']
                = \Common\Service\Cqrs\AnonRequestFactory::class;
            $config['service_manager']['factories']['AnonQueryService']
                = \Common\Service\Cqrs\Query\AnonQueryServiceFactory::class;
        }

        return $config;
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
}
