<?php

namespace Common\Preference;

use Laminas\EventManager\EventManagerInterface;
use Laminas\EventManager\ListenerAggregateInterface;
use Laminas\EventManager\ListenerAggregateTrait;
use Laminas\Mvc\MvcEvent;
use Laminas\Router;
use Dvsa\Olcs\Transfer\Util\Annotation\AnnotationBuilder;
use Laminas\Http\Request as HttpRequest;
use Laminas\Http\Response;
use Laminas\ServiceManager\FactoryInterface;
use Laminas\ServiceManager\ServiceLocatorInterface;
use Common\Service\Helper\FlashMessengerHelperService;
use Laminas\I18n\Translator\Translator;
use Interop\Container\ContainerInterface;

/**
 * Language Listener
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class LanguageListener implements ListenerAggregateInterface, FactoryInterface
{
    use ListenerAggregateTrait;

    /**
     * @var Language
     */
    private $languagePref;

    /**
     * @var FlashMessengerHelperService
     */
    private $flashMessenger;

    /**
     * @var Translator
     */
    private $translator;

    public function createService(ServiceLocatorInterface $serviceLocator): LanguageListener
    {
        return $this->__invoke($serviceLocator, LanguageListener::class);
    }

    public function attach(EventManagerInterface $events, $priority = 1)
    {
        $this->listeners[] = $events->attach(MvcEvent::EVENT_ROUTE, array($this, 'onRoute'), $priority);
    }

    public function onRoute(MvcEvent $e)
    {
        $request = $e->getRequest();
        if (!($request instanceof HttpRequest)) {
            return;
        }

        $lang = $request->getQuery('lang');

        if ($lang !== null) {
            try {
                $this->languagePref->setPreference($lang);
            } catch (\Exception $ex) {
                $this->flashMessenger->addCurrentErrorMessage('Only English and Welsh languages are supported');
            }
        }

        $this->translator->setLocale($this->languagePref->getPreference() . '_GB');
    }

    /**
     * @param ContainerInterface $container
     * @param $requestedName
     * @param array|null $options
     * @return $this
     *
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null): LanguageListener
    {
        $this->languagePref = $container->get('LanguagePreference');
        $this->flashMessenger = $container->get('Helper\FlashMessenger');
        $this->translator = $container->get('translator');
        return $this;
    }
}
