<?php

namespace Common;

use Common\Exception\ResourceNotFoundException;
use Common\Preference\LanguageListener;
use Common\Service\Cqrs\Exception\AccessDeniedException;
use Common\Service\Cqrs\Exception\NotFoundException;
use Common\Service\Helper\TranslationHelperService;
use Dvsa\Olcs\Utils\Translation\MissingTranslationProcessor;
use Laminas\ServiceManager\ServiceManager;
use Olcs\Logging\Log\Logger;
use Laminas\EventManager\EventManager;
use Laminas\Http\Request;
use Laminas\I18n\Translator\Translator;
use Laminas\ModuleManager\ModuleEvent;
use Laminas\Mvc\Application;
use Laminas\Mvc\MvcEvent;

use Laminas\Http\PhpEnvironment\Response;
use Laminas\View\Model\ViewModel;
use Laminas\ModuleManager\Feature\ConfigProviderInterface;
use Laminas\ModuleManager\Feature\ServiceProviderInterface;

/**
 * ZF2 Module
 */
class Module implements ConfigProviderInterface, ServiceProviderInterface
{
    public static string $dateFormat = 'd/m/Y';
    public static string $dateTimeFormat = 'd/m/Y H:i';
    public static string $dateTimeSecFormat = 'd/m/Y H:i:s';
    public static string $dbDateFormat = 'Y-m-d';

    /**
     * Initialize module
     *
     * @param \Laminas\ModuleManager\ModuleManager $moduleManager Module manager
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

        self::$dateFormat = $config['date_settings']['date_format'] ?? self::$dateFormat;
        self::$dateTimeFormat = $config['date_settings']['datetime_format'] ?? self::$dateTimeFormat;
        self::$dateTimeSecFormat = $config['date_settings']['datetimesec_format'] ?? self::$dateTimeSecFormat;
        self::$dbDateFormat = $config['date_settings']['db_date_format'] ?? self::$dbDateFormat;
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
            \Laminas\View\Helper\Navigation\AbstractHelper::class,
            'isAllowed',
            [$listener, 'accept']
        );

        //  RBAC behaviour if user not authorised
        $events->attach(MvcEvent::EVENT_DISPATCH_ERROR, [$sm->get(\LmcRbacMvc\View\Strategy\RedirectStrategy::class), 'onError']);
        //  CSRF token check
        $events->attach(MvcEvent::EVENT_DISPATCH, [$this, 'validateCsrfToken'], 100);

        // On dispatch error ot certain CQRS exceptions then change page to a 404
        $events->attach(
            MvcEvent::EVENT_DISPATCH_ERROR,
            function (MvcEvent $e) {
                // If Backend Not found or access denied then display error as a 404 not found
                if ($e->getParam('exception') instanceof NotFoundException
                    || $e->getParam('exception') instanceof AccessDeniedException
                    || $e->getParam('exception') instanceof ResourceNotFoundException
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
        /** @var \Laminas\Http\PhpEnvironment\Request $request */
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

        $validator = new \Laminas\Validator\Csrf(['name' => $name]);
        if ($validator->isValid($token)) {
            return;
        }

        /** @var TranslationHelperService $translator */
        $hlpFlashMsgr = $sm->get('Helper\FlashMessenger');
        $hlpFlashMsgr->addErrorMessage('csrf-message');

        /** @var \Laminas\Http\Response $resp */
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
        return include __DIR__ . '/../config/module.config.php';
    }

    /**
     * Setup the translator service
     *
     * @param ServiceLocatorInterface         $sm           Service manager
     * @param \Laminas\EventManager\EventManager $eventManager Event manager
     *
     * @return void
     */
    protected function setUpTranslator(ServiceManager $sm, $eventManager)
    {
        /**
         * @var Translator $translator
         */
        $cache = $sm->get('default-cache');
        $translator = $sm->get('translator');
        $translator->setCache($cache);

        /** @var LanguageListener $languagePrefListener */
        $languagePrefListener = $sm->get('LanguageListener');
        $languagePrefListener->attach($eventManager, 1);

        /** @var  MissingTranslationProcessor $missingTranslationProcessor */
        $missingTranslationProcessor = $sm->get('Utils\MissingTranslationProcessor');
        $missingTranslationProcessor->attach($eventManager);

        $translator->enableEventManager();
        $translator->setEventManager($eventManager);
    }

    /**
     * If the request is coming through a proxy then update the host name on the request
     *
     * @param \Laminas\Stdlib\RequestInterface $request Request
     *
     * @return void
     */
    private function setupRequestForProxyHost(\Laminas\Stdlib\RequestInterface $request)
    {
        if (!$request instanceof \Laminas\Http\PhpEnvironment\Request) {
            // if request is not \Laminas\Http\PhpEnvironment\Request we must be running from CLI so do nothing
            return;
        }

        /* @var $request \Laminas\Http\PhpEnvironment\Request */
        if ($request->getHeaders()->get('x-forwarded-host')) {
            $host = $request->getHeaders()->get('x-forwarded-host')->getFieldValue();

            $hosts = explode(',', $host);
            if (!empty($hosts)) {
                $host = trim($hosts[0]);
            }

            Logger::debug(
                sprintf(
                    'Request host set from x-forwarded-host header to %s setting host to %s',
                    $request->getHeaders()->get('x-forwarded-host')->getFieldValue(),
                    $host
                )
            );
            $request->getUri()->setHost($host);
        }

        // if X-Forwarded-Proto Header exists (ie from AWS ELB) then set the request as this so that route
        // generated URLS will have the correct scheme
        if ($request->getHeaders()->get('x-forwarded-proto')) {
            $proto = $request->getHeaders()->get('x-forwarded-proto')->getFieldValue();

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
    private function setLoggerUser(ServiceManager $serviceManager)
    {
        $authService = $serviceManager->get(\LmcRbacMvc\Service\AuthorizationService::class);
        $serviceManager->get('LogProcessorManager')->get(\Olcs\Logging\Log\Processor\UserId::class)
            ->setUserId($authService->getIdentity()->getUsername());
    }

    public function getServiceConfig()
    {
        return [];
    }
}
