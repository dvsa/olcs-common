<?php

namespace Common;

use Common\Preference\LanguageListener;
use Common\Service\Cqrs\Exception\AccessDeniedException;
use Common\Service\Cqrs\Exception\NotFoundException;
use Common\Service\Helper\TranslationHelperService;
use Dvsa\Olcs\Utils\Translation\MissingTranslationProcessor;
use Olcs\Logging\Log\Logger;
use Zend\EventManager\EventManager;
use Zend\Http\Request;
use Zend\ModuleManager\ModuleEvent;
use Zend\Mvc\Application;
use Zend\Mvc\MvcEvent;
use Zend\ServiceManager\ServiceLocatorInterface;
use Zend\Http\PhpEnvironment\Response;
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
        //  CSRF token check
        $events->attach(MvcEvent::EVENT_DISPATCH, [$this, 'validateCsrfToken'], 100);

        // On dispatch error ot certain CQRS exceptions then change page to a 404
        $events->attach(
            MvcEvent::EVENT_DISPATCH_ERROR,
            function (MvcEvent $e) {
                // If Backend Not found or access denied then display error as a 404 not found
                if ($e->getParam('exception') instanceof NotFoundException
                    || $e->getParam('exception') instanceof AccessDeniedException
                ) {
                    $e->setError(Application::ERROR_CONTROLLER_INVALID);
                    $e->setParam('exceptionNoLog', true);
                }
            },
            100
        );

        $this->setupRequestForProxyHost($app->getRequest());

        $this->setLoggerUser($sm);

        $identifier = $sm->get('LogProcessorManager')
            ->get(\Olcs\Logging\Log\Processor\RequestId::class)
            ->getIdentifier();

        $this->onFatalError($identifier);

        $events->attach(
            MvcEvent::EVENT_RENDER,
            function (MvcEvent $e) use ($identifier) {
                // Inject the log correlation ID into the view
                if ($e->getResult() instanceof ViewModel) {
                    $e->getResult()->setVariable('correlationId', $identifier);
                }
            },
            -100
        );

        /** @var Response $response */
        $response = $e->getResponse();
        $headers = $response->getHeaders();
        $headers->addHeaders(
            ['X-XSS-Protection: 1; mode=block', 'X-Content-Type-Options: nosniff']
        );
    }

    /**
     * Catch fatal error
     *
     * @param string $identifier Identifier
     *
     * @return void;
     */
    public function onFatalError($identifier)
    {
        // Handle fatal errors //
        register_shutdown_function(
            function () use ($identifier) {
                // get error
                $error = error_get_last();

                $minorErrors = [
                    E_WARNING, E_NOTICE, E_USER_NOTICE, E_DEPRECATED, E_USER_DEPRECATED
                ];
                if (null === $error || (isset($error['type']) && in_array($error['type'], $minorErrors))) {
                    return null;
                }

                // check and allow only errors
                // clean any previous output from buffer
                while (ob_get_level() > 0) {
                    ob_end_clean();
                }

                /** @var Response $response */
                $response = new Response();
                $response->getHeaders()
                    ->addHeaderLine('Location', '/error?correlationId='. $identifier .'&src=shutdown');
                $response->setStatusCode(Response::STATUS_CODE_302);
                $response->sendHeaders();

                return $response;
            }
        );
    }


    /**
     * Validate the CSRF token
     *
     * @param MvcEvent $e MVC event
     *
     * @return void
     */
    public function validateCsrfToken(MvcEvent $e)
    {
        /** @var \Zend\Http\PhpEnvironment\Request $request */
        $request = $e->getRequest();
        if ($request->isPost() === false) {
            return;
        }

        $sm = $e->getApplication()->getServiceManager();

        //  whitelisted paths: allow POST without CSRF check
        $cfg = $sm->get('config');
        if (in_array($request->getUri()->getPath(), $cfg['csrf']['whitelist'], true)) {
            return;
        }

        $postDataCnt = $request->getPost()->count();
        if ($postDataCnt === 0) {
            return;
        }

        $name = 'security';
        $token = $request->getPost($name);

        $validator = new \Zend\Validator\Csrf(['name' => $name]);
        if ($validator->isValid($token)) {
            return;
        }

        /** @var TranslationHelperService $translator */
        $hlpFlashMsgr = $sm->get('Helper\FlashMessenger');
        $hlpFlashMsgr->addErrorMessage('csrf-message');

        /** @var \Zend\Http\Response $resp */
        $resp = $e->getResponse();
        $resp->getHeaders()->addHeaderLine('X-CSRF-error', '1');

        $request->setMethod(Request::METHOD_GET);
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
        /** @var \\Zend\I18n\Translator\TranslatorInterface $translator */
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

        // Add a logger so that missing translations can be recorded
        $request = $sm->get('Request');
        if ($request instanceof \Zend\Http\PhpEnvironment\Request) {
            $missingTranslationProcessor->setTranslationLogger(
                new \Dvsa\Olcs\Utils\Translation\TranslatorLogger($sm->get('logger'), $request)
            );
        }

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
